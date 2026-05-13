<?php

namespace App\Services\Payments;

use App\Models\Course;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PayPalCheckoutService
{
    public function isConfigured(): bool
    {
        $mode = (string) config('paypal.mode', 'sandbox');
        $block = $mode === 'live'
            ? (array) config('paypal.live', [])
            : (array) config('paypal.sandbox', []);

        $clientId = trim((string) ($block['client_id'] ?? ''));
        $clientSecret = trim((string) ($block['client_secret'] ?? ''));

        if ($clientId === '' || $clientSecret === '') {
            return false;
        }

        // Keep PayPal visible if keys are present; real validity is verified by OAuth call.
        if (hash_equals($clientId, $clientSecret)) {
            Log::warning('PayPal credentials look suspicious (client_id matches client_secret)', [
                'paypal_mode' => $mode,
            ]);
        }

        return true;
    }

    public function createApprovalUrl(Payment $payment, Course $course, string $returnUrl, string $cancelUrl): string
    {
        $provider = $this->client();
        $value = number_format((float) $payment->amount, 2, '.', '');

        $response = $provider->createOrder([
            'intent' => 'CAPTURE',
            'purchase_units' => [
                [
                    'reference_id' => (string) $payment->id,
                    'custom_id' => 'payment:'.$payment->id,
                    'amount' => [
                        'currency_code' => strtoupper((string) ($payment->currency ?: 'USD')),
                        'value' => $value,
                    ],
                    'description' => Str::limit((string) $course->title, 127),
                ],
            ],
            'application_context' => [
                'return_url' => $returnUrl,
                'cancel_url' => $cancelUrl,
                'brand_name' => (string) config('app.name'),
                'user_action' => 'PAY_NOW',
            ],
        ]);

        if (isset($response['error'])) {
            Log::error('PayPal create order failed', [
                'response' => $response,
                'paypal_mode' => config('paypal.mode'),
            ]);

            throw new \RuntimeException('PayPal order creation failed.');
        }

        $orderId = (string) ($response['id'] ?? '');
        $approve = collect($response['links'] ?? [])->firstWhere('rel', 'approve');
        $href = $approve['href'] ?? null;

        if ($orderId === '' || ! is_string($href) || $href === '') {
            Log::error('PayPal create order invalid response', ['response' => $response]);

            throw new \RuntimeException('PayPal approval link missing.');
        }

        $payment->update([
            'gateway_checkout_id' => $orderId,
        ]);

        return $href;
    }

    /**
     * @return array{ok: bool, issue?: string, message?: string, debug_id?: string}
     */
    public function captureOrder(string $orderId): array
    {
        $provider = $this->client();
        $response = $provider->capturePaymentOrder($orderId);

        if (isset($response['error'])) {
            Log::error('PayPal capture failed', ['response' => $response]);

            $error = is_array($response['error'] ?? null) ? $response['error'] : [];
            $details = is_array($error['details'] ?? null) ? $error['details'] : [];
            $firstDetail = is_array($details[0] ?? null) ? $details[0] : [];

            return [
                'ok' => false,
                'issue' => (string) ($firstDetail['issue'] ?? ''),
                'message' => (string) ($firstDetail['description'] ?? ($error['message'] ?? '')),
                'debug_id' => (string) ($error['debug_id'] ?? ''),
            ];
        }

        $status = (string) ($response['status'] ?? '');

        return [
            'ok' => $status === 'COMPLETED',
        ];
    }

    private function client(): PayPalClient
    {
        $provider = new PayPalClient;
        $token = $provider->getAccessToken();

        if (! is_array($token) || isset($token['error']) || empty($token['access_token'])) {
            $paypalError = is_array($token['error'] ?? null) ? $token['error'] : [];
            $errorCode = (string) ($paypalError['error'] ?? 'unknown');
            $errorDescription = (string) ($paypalError['error_description'] ?? '');

            Log::error('PayPal OAuth token failed', [
                'response' => $token,
                'paypal_mode' => config('paypal.mode'),
            ]);

            throw new \RuntimeException(
                sprintf(
                    'PayPal OAuth failed (%s%s). Check PAYPAL_MODE and matching client id/secret in .env or config/paypal.php.',
                    $errorCode,
                    $errorDescription !== '' ? ': '.$errorDescription : ''
                )
            );
        }

        return $provider;
    }
}
