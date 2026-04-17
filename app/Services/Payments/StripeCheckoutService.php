<?php

namespace App\Services\Payments;

use App\Models\Course;
use App\Models\Payment;
use Illuminate\Support\Str;
use Stripe\Checkout\Session;

class StripeCheckoutService
{
    public function isConfigured(): bool
    {
        $secret = config('payments.stripe.secret');

        return is_string($secret) && $secret !== '';
    }

    public function createCheckoutRedirectUrl(
        Payment $payment,
        Course $course,
        string $successUrl,
        string $cancelUrl,
        ?string $customerEmail = null,
    ): string {
        $stripe = $this->stripeClient();

        $amountCents = (int) round((float) $payment->amount * 100);
        if ($amountCents < 50) {
            throw new \InvalidArgumentException('Amount below Stripe minimum (USD 0.50).');
        }

        $sessionParams = [
            'mode' => 'payment',
            'success_url' => $successUrl.'?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => $cancelUrl,
            'locale' => $this->checkoutLocale(),
            'client_reference_id' => (string) $payment->id,
            'metadata' => [
                'payment_id' => (string) $payment->id,
                'user_id' => (string) $payment->user_id,
                'course_id' => (string) $payment->course_id,
            ],
            'line_items' => [[
                'quantity' => 1,
                'price_data' => [
                    'currency' => strtolower((string) ($payment->currency ?: 'usd')),
                    'unit_amount' => $amountCents,
                    'product_data' => array_filter([
                        'name' => Str::limit((string) $course->title, 120),
                        'description' => Str::limit((string) ($course->description ?? ''), 200),
                        'images' => $this->stripeProductImages($course),
                    ]),
                ],
            ]],
        ];

        $email = is_string($customerEmail) ? trim($customerEmail) : '';
        if ($email !== '') {
            $sessionParams['customer_email'] = $email;
        }

        /** @var Session $session */
        $session = $stripe->checkout->sessions->create($sessionParams);

        $payment->update([
            'gateway_checkout_id' => $session->id,
        ]);

        return (string) $session->url;
    }

    public function retrievePaidSession(string $sessionId): ?Session
    {
        $stripe = $this->stripeClient();
        /** @var Session $session */
        $session = $stripe->checkout->sessions->retrieve($sessionId);

        if (($session->payment_status ?? '') !== 'paid') {
            return null;
        }

        return $session;
    }

    private function stripeClient(): \Stripe\StripeClient
    {
        return new \Stripe\StripeClient((string) config('payments.stripe.secret'));
    }

    private function checkoutLocale(): string
    {
        $tag = strtolower(str_replace('_', '-', (string) app()->getLocale()));
        $supported = [
            'auto', 'bg', 'cs', 'da', 'de', 'el', 'en', 'en-gb', 'es', 'es-419', 'et', 'fi', 'fil',
            'fr', 'fr-ca', 'hr', 'hu', 'id', 'it', 'ja', 'ko', 'lt', 'lv', 'ms', 'mt', 'nb', 'nl',
            'pl', 'pt', 'pt-br', 'ro', 'ru', 'sk', 'sl', 'sv', 'th', 'tr', 'vi', 'zh', 'zh-hk', 'zh-tw',
        ];

        if (in_array($tag, $supported, true)) {
            return $tag;
        }

        $primary = explode('-', $tag)[0] ?? 'en';

        return in_array($primary, $supported, true) ? $primary : 'auto';
    }

    /**
     * @return list<string>
     */
    private function stripeProductImages(Course $course): array
    {
        $url = trim((string) ($course->img_link ?? ''));
        if ($url === '' || ! str_starts_with($url, 'https://')) {
            return [];
        }

        return [$url];
    }
}
