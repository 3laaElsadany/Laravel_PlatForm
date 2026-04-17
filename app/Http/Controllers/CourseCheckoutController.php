<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProcessCheckoutPaymentRequest;
use App\Models\Course;
use App\Models\DiscountCode;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Services\CoursePricingService;
use App\Services\Payments\PayPalCheckoutService;
use App\Services\Payments\StripeCheckoutService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CourseCheckoutController extends Controller
{
    public function __construct(
        private CoursePricingService $pricing,
        private StripeCheckoutService $stripe,
        private PayPalCheckoutService $paypal,
    ) {}

    public function show(Course $course): View|RedirectResponse
    {
        $this->authorize('enroll', $course);

        if (auth()->user()->enrollments()->where('course_id', $course->id)->exists()) {
            return redirect()
                ->route('courses.show', $course)
                ->with('status', 'already-enrolled');
        }

        $course->load('category');

        $prefill = request('code') ?? old('discount_code');
        $code = $this->resolveDiscountCode($course, $prefill);
        $invalidCodeInLink = is_string(request('code')) && request('code') !== '' && ! $code;

        $catalogPrice = $this->pricing->priceAfterCatalogDiscount($course);
        $finalPrice = $this->pricing->finalEnrollmentPrice($course, $code);

        $allowed = ProcessCheckoutPaymentRequest::availablePaymentMethods();
        $oldMethod = old('payment_method');
        $defaultPayment = is_string($oldMethod) && in_array($oldMethod, $allowed, true)
            ? $oldMethod
            : ($allowed[0] ?? null);

        return view('courses.checkout', [
            'course' => $course,
            'discountCode' => $code,
            'catalogPrice' => $catalogPrice,
            'finalPrice' => $finalPrice,
            'prefillCode' => $prefill,
            'invalidCodeInLink' => $invalidCodeInLink,
            'stripeConfigured' => $this->stripe->isConfigured(),
            'paypalConfigured' => $this->paypal->isConfigured(),
            'allowedPaymentMethods' => $allowed,
            'defaultPaymentMethod' => $defaultPayment,
        ]);
    }

    public function store(ProcessCheckoutPaymentRequest $request, Course $course): RedirectResponse
    {
        $this->authorize('enroll', $course);

        if ($request->user()->enrollments()->where('course_id', $course->id)->exists()) {
            return redirect()->route('courses.show', $course)->with('status', 'already-enrolled');
        }

        $discountRaw = $this->discountCodeInput($request);
        $code = $this->resolveDiscountCode($course, $discountRaw);

        if (is_string($discountRaw) && trim($discountRaw) !== '' && ! $code) {
            return back()->withErrors(['discount_code' => __('Invalid or expired discount code.')]);
        }

        $final = $this->pricing->finalEnrollmentPrice($course, $code);

        if ($final <= 0) {
            return $this->storeFreeEnrollment($request, $course, $code, $final);
        }

        $method = (string) $request->validated('payment_method');

        return match ($method) {
            'stripe' => $this->storeStripeRedirect($request, $course, $code, $final),
            'paypal' => $this->storePayPalRedirect($request, $course, $code, $final),
            'demo' => $this->storeDemoPayment($request, $course, $code, $final),
            default => back()->withErrors(['payment_method' => __('Select a valid payment method.')]),
        };
    }

    public function stripeReturn(Request $request, Course $course): RedirectResponse
    {
        $this->authorize('enroll', $course);

        $sessionId = $request->query('session_id');
        if (! is_string($sessionId) || $sessionId === '') {
            return redirect()->route('courses.checkout.show', $course)
                ->withErrors(['gateway' => __('Missing payment session. Please try again.')]);
        }

        try {
            $session = $this->stripe->retrievePaidSession($sessionId);
        } catch (\Throwable $e) {
            Log::error('Stripe session retrieve failed', ['exception' => $e]);

            return redirect()->route('courses.checkout.show', $course)
                ->withErrors(['gateway' => __('Could not verify payment with Stripe.')]);
        }

        if ($session === null) {
            return redirect()->route('courses.checkout.show', $course)
                ->withErrors(['gateway' => __('Payment was not completed.')]);
        }

        $paymentId = (int) ($session->metadata->payment_id ?? 0);
        $payment = Payment::query()
            ->whereKey($paymentId)
            ->where('user_id', $request->user()->id)
            ->where('course_id', $course->id)
            ->where('gateway', 'stripe')
            ->where('status', 'pending')
            ->first();

        if (! $payment || $payment->gateway_checkout_id !== $sessionId) {
            return redirect()->route('courses.checkout.show', $course)
                ->withErrors(['gateway' => __('Payment does not match this checkout.')]);
        }

        $this->finalizePaidPayment($payment);

        return redirect()->route('my-courses')->with('status', 'enrolled-after-payment');
    }

    public function paypalReturn(Request $request, Course $course): RedirectResponse
    {
        $this->authorize('enroll', $course);

        $orderId = $request->query('token');
        if (! is_string($orderId) || $orderId === '') {
            return redirect()->route('courses.checkout.show', $course)
                ->withErrors(['gateway' => __('Missing PayPal order. Please try again.')]);
        }

        $payment = Payment::query()
            ->where('gateway_checkout_id', $orderId)
            ->where('user_id', $request->user()->id)
            ->where('course_id', $course->id)
            ->where('gateway', 'paypal')
            ->where('status', 'pending')
            ->first();

        if (! $payment) {
            return redirect()->route('courses.checkout.show', $course)
                ->withErrors(['gateway' => __('Payment does not match this checkout.')]);
        }

        $capture = $this->paypal->captureOrder($orderId);
        if (! $capture['ok']) {
            if (($capture['issue'] ?? '') === 'COMPLIANCE_VIOLATION') {
                $msg = __('PayPal rejected this transaction due to account compliance checks. Use sandbox buyer/business test accounts from PayPal Developer, or try Stripe for local testing.');
                if (($capture['debug_id'] ?? '') !== '') {
                    $msg .= ' (debug_id: '.$capture['debug_id'].')';
                }

                return redirect()->route('courses.checkout.show', $course)
                    ->withErrors(['gateway' => $msg]);
            }

            return redirect()->route('courses.checkout.show', $course)
                ->withErrors(['gateway' => __('PayPal did not complete the payment.')]);
        }

        $this->finalizePaidPayment($payment);

        return redirect()->route('my-courses')->with('status', 'enrolled-after-payment');
    }

    public function cancel(Course $course): RedirectResponse
    {
        $this->authorize('enroll', $course);

        return redirect()->route('courses.checkout.show', $course)->with('status', 'payment-cancelled');
    }

    private function storeFreeEnrollment(ProcessCheckoutPaymentRequest $request, Course $course, ?DiscountCode $code, float $final): RedirectResponse
    {
        $reference = 'FREE-'.Str::upper(Str::random(10));

        DB::transaction(function () use ($request, $course, $code, $final, $reference): void {
            $payment = Payment::create([
                'user_id' => $request->user()->id,
                'course_id' => $course->id,
                'discount_code_id' => $code?->id,
                'amount' => $final,
                'currency' => 'USD',
                'status' => 'completed',
                'gateway' => 'free',
                'reference' => $reference,
                'paid_at' => now(),
            ]);

            Enrollment::create([
                'payment_id' => $payment->id,
                'user_id' => $request->user()->id,
                'course_id' => $course->id,
                'enrolled_at' => now(),
                'discount_code_id' => $code?->id,
                'final_price' => $final,
            ]);
        });

        return redirect()->route('my-courses')->with('status', 'enrolled-after-payment');
    }

    private function storeDemoPayment(ProcessCheckoutPaymentRequest $request, Course $course, ?DiscountCode $code, float $final): RedirectResponse
    {
        $reference = 'PAY-'.Str::upper(Str::random(10));

        DB::transaction(function () use ($request, $course, $code, $final, $reference): void {
            $payment = Payment::create([
                'user_id' => $request->user()->id,
                'course_id' => $course->id,
                'discount_code_id' => $code?->id,
                'amount' => $final,
                'currency' => 'USD',
                'status' => 'completed',
                'gateway' => 'demo',
                'reference' => $reference,
                'paid_at' => now(),
            ]);

            Enrollment::create([
                'payment_id' => $payment->id,
                'user_id' => $request->user()->id,
                'course_id' => $course->id,
                'enrolled_at' => now(),
                'discount_code_id' => $code?->id,
                'final_price' => $final,
            ]);
        });

        return redirect()->route('my-courses')->with('status', 'enrolled-after-payment');
    }

    private function storeStripeRedirect(ProcessCheckoutPaymentRequest $request, Course $course, ?DiscountCode $code, float $final): RedirectResponse
    {
        if (! $this->stripe->isConfigured()) {
            return back()->withErrors(['gateway' => __('Stripe is not configured. Add STRIPE_SECRET to your environment.')]);
        }

        if ($final > 0 && $final < 0.5) {
            return back()->withErrors(['gateway' => __('This amount is below the card minimum. Choose demo gateway for testing, or adjust pricing.')]);
        }

        $this->abandonPendingCheckouts($request->user()->id, $course->id);

        $reference = 'PAY-'.Str::upper(Str::random(10));

        $payment = Payment::create([
            'user_id' => $request->user()->id,
            'course_id' => $course->id,
            'discount_code_id' => $code?->id,
            'amount' => $final,
            'currency' => 'USD',
            'status' => 'pending',
            'gateway' => 'stripe',
            'reference' => $reference,
            'paid_at' => null,
        ]);

        try {
            $url = $this->stripe->createCheckoutRedirectUrl(
                $payment,
                $course,
                route('courses.checkout.stripe.return', ['course' => $course], true),
                route('courses.checkout.cancel', ['course' => $course], true),
                $request->user()->email,
            );
        } catch (\Throwable $e) {
            Log::error('Stripe checkout session failed', ['exception' => $e]);
            $payment->delete();

            return back()->withErrors(['gateway' => __('Could not start Stripe checkout. Check logs and configuration.')]);
        }

        return redirect()->away($url);
    }

    private function storePayPalRedirect(ProcessCheckoutPaymentRequest $request, Course $course, ?DiscountCode $code, float $final): RedirectResponse
    {
        if (! $this->paypal->isConfigured()) {
            return back()->withErrors(['gateway' => __('PayPal is not configured. Add PAYPAL_CLIENT_ID and PAYPAL_SECRET to your environment.')]);
        }

        $this->abandonPendingCheckouts($request->user()->id, $course->id);

        $reference = 'PAY-'.Str::upper(Str::random(10));

        $payment = Payment::create([
            'user_id' => $request->user()->id,
            'course_id' => $course->id,
            'discount_code_id' => $code?->id,
            'amount' => $final,
            'currency' => 'USD',
            'status' => 'pending',
            'gateway' => 'paypal',
            'reference' => $reference,
            'paid_at' => null,
        ]);

        try {
            $url = $this->paypal->createApprovalUrl(
                $payment,
                $course,
                route('courses.checkout.paypal.return', ['course' => $course], true),
                route('courses.checkout.cancel', ['course' => $course], true),
            );
        } catch (\Throwable $e) {
            Log::error('PayPal checkout failed', ['exception' => $e]);
            $payment->delete();

            $message = __('Could not start PayPal checkout. Check logs and configuration.');
            if (str_contains(strtolower($e->getMessage()), 'invalid_client')) {
                $message = __('PayPal credentials were rejected (invalid_client). Use sandbox credentials with PAYPAL_MODE=sandbox, or live credentials with PAYPAL_MODE=live.');
            }

            return back()->withErrors(['gateway' => $message]);
        }

        return redirect()->away($url);
    }

    private function abandonPendingCheckouts(int $userId, int $courseId): void
    {
        Payment::query()
            ->where('user_id', $userId)
            ->where('course_id', $courseId)
            ->where('status', 'pending')
            ->delete();
    }

    private function finalizePaidPayment(Payment $payment): void
    {
        DB::transaction(function () use ($payment): void {
            $locked = Payment::query()->whereKey($payment->id)->lockForUpdate()->first();
            if (! $locked || $locked->status === 'completed') {
                return;
            }

            $locked->update([
                'status' => 'completed',
                'paid_at' => now(),
            ]);

            if (! Enrollment::query()->where('user_id', $locked->user_id)->where('course_id', $locked->course_id)->exists()) {
                Enrollment::create([
                    'payment_id' => $locked->id,
                    'user_id' => $locked->user_id,
                    'course_id' => $locked->course_id,
                    'enrolled_at' => now(),
                    'discount_code_id' => $locked->discount_code_id,
                    'final_price' => $locked->amount,
                ]);
            }
        });
    }

    private function discountCodeInput(Request $request): mixed
    {
        $fromField = $request->input('discount_code');
        if (is_string($fromField) && trim($fromField) !== '') {
            return $fromField;
        }

        $fromQuery = $request->query('code');

        return is_string($fromQuery) ? $fromQuery : $fromField;
    }

    private function resolveDiscountCode(Course $course, mixed $raw): ?DiscountCode
    {
        if (! is_string($raw)) {
            return null;
        }

        $normalizedInput = $this->normalizeDiscountCode($raw);
        if ($normalizedInput === '') {
            return null;
        }

        // Normalize in PHP to tolerate copied codes that include hidden Unicode separators.
        $codes = DiscountCode::query()
            ->where('course_id', $course->id)
            ->get();

        foreach ($codes as $code) {
            if ($this->normalizeDiscountCode((string) $code->code) !== $normalizedInput) {
                continue;
            }

            return $code->isUsable() ? $code : null;
        }

        return null;
    }

    private function normalizeDiscountCode(string $value): string
    {
        $value = trim($value);

        if ($value === '') {
            return '';
        }

        $value = mb_strtolower($value, 'UTF-8');

        // Remove all separators and invisible formatting chars copied from chat/apps.
        $value = preg_replace('/[\p{Z}\p{Cf}\s]+/u', '', $value) ?? $value;

        return $value;
    }
}
