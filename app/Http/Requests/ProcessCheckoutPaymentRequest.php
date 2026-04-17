<?php

namespace App\Http\Requests;

use App\Services\Payments\PayPalCheckoutService;
use App\Services\Payments\StripeCheckoutService;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ProcessCheckoutPaymentRequest extends FormRequest
{
    /**
     * @return list<string>
     */
    public static function availablePaymentMethods(): array
    {
        $methods = [];

        $stripeOn = app(StripeCheckoutService::class)->isConfigured();
        $paypalOn = app(PayPalCheckoutService::class)->isConfigured();

        if ($stripeOn) {
            $methods[] = 'stripe';
        }

        if ($paypalOn) {
            $methods[] = 'paypal';
        }

        $hasRealGateway = $stripeOn || $paypalOn;
        $demoAllowed = (bool) config('payments.demo_enabled');

        // Demo is only for environments without a real gateway (never alongside Stripe/PayPal on checkout).
        if ($demoAllowed && ! $hasRealGateway) {
            $methods[] = 'demo';
        }

        return $methods;
    }

    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'payment_method' => ['required', 'string', $this->paymentMethodRule()],
            'discount_code' => ['nullable', 'string', 'max:64'],
            'accept_terms' => ['accepted'],
        ];
    }

    public function attributes(): array
    {
        return [
            'accept_terms' => __('terms of purchase'),
            'payment_method' => __('Payment method'),
        ];
    }

    /**
     * @return \Closure(string, mixed, \Closure(string): void): void
     */
    private function paymentMethodRule(): \Closure
    {
        return function (string $attribute, mixed $value, \Closure $fail): void {
            $allowed = $this->allowedPaymentMethods();
            if ($allowed === []) {
                $fail(__('No payment provider is available. Configure Stripe or PayPal, or enable demo payments for testing.'));

                return;
            }

            if (! is_string($value) || ! in_array($value, $allowed, true)) {
                $fail(__('Select a valid payment method.'));
            }
        };
    }

    /**
     * @return list<string>
     */
    public function allowedPaymentMethods(): array
    {
        return self::availablePaymentMethods();
    }
}
