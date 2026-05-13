@extends('layouts.public')

@section('title', __('Checkout') . ' — ' . $course->title)

@section('content')
    <div class="mx-auto max-w-5xl">
        <a href="{{ route('courses.show', $course) }}" class="inline-flex items-center gap-1 text-sm font-medium text-brand-600 hover:text-brand-800 dark:text-brand-400 dark:hover:text-brand-300">
            <svg class="h-4 w-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            {{ __('Back to course') }}
        </a>

        <h1 class="mt-6 text-3xl font-extrabold tracking-tight text-slate-900 dark:text-slate-50">{{ __('Checkout') }}</h1>
        <p class="mt-2 text-slate-600 dark:text-slate-300">{{ __('Complete payment to unlock this course in your account.') }}</p>

        @if (count($allowedPaymentMethods) === 0)
            <div class="mt-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-900 dark:border-red-900/60 dark:bg-red-950/40 dark:text-red-200">
                {{ __('No payment provider is available. Configure Stripe or PayPal, or enable demo payments for testing.') }}
            </div>
        @elseif (count($allowedPaymentMethods) === 1 && ($allowedPaymentMethods[0] ?? null) === 'demo')
            <div class="mt-6 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-950 dark:border-amber-800 dark:bg-amber-950/40 dark:text-amber-100">
                {{ __('Stripe and PayPal are not configured. Only the demo option is available: completing checkout will enroll you immediately with no redirect and no real charge. Add STRIPE_SECRET and/or PayPal credentials in .env to use real payments.') }}
            </div>
        @endif

        <div class="mt-10 grid gap-8 lg:grid-cols-5">
            <div class="card-surface p-6 lg:col-span-2">
                <h2 class="text-sm font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ __('Order summary') }}</h2>
                <div class="mt-4 flex gap-4">
                    @if ($course->img_link)
                        <img src="{{ $course->img_link }}" alt="" class="h-20 w-28 shrink-0 rounded-lg object-cover ring-1 ring-slate-200 dark:ring-slate-600">
                    @endif
                    <div>
                        <p class="font-bold text-slate-900 dark:text-slate-50">{{ $course->title }}</p>
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ $course->category->name }}</p>
                    </div>
                </div>
                <dl class="mt-6 space-y-3 border-t border-slate-100 pt-6 text-sm dark:border-slate-700">
                    <div class="flex justify-between">
                        <dt class="text-slate-600 dark:text-slate-400">{{ __('Course price') }}</dt>
                        <dd class="font-semibold text-slate-900 dark:text-slate-50">{{ number_format($catalogPrice, 2) }} {{ __('USD') }}</dd>
                    </div>
                    <div class="flex justify-between border-t border-slate-100 pt-3 text-base dark:border-slate-700">
                        <dt class="font-bold text-slate-900 dark:text-slate-50">{{ __('Total due') }}</dt>
                        <dd class="font-extrabold text-brand-700 dark:text-brand-400">{{ number_format($finalPrice, 2) }} {{ __('USD') }}</dd>
                    </div>
                </dl>
            </div>

            <div class="card-surface p-6 lg:col-span-3">
                <h2 class="text-sm font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ __('Payment method') }}</h2>
                <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">{{ __('Choose how you want to pay. You will be redirected to complete payment, then returned here to activate your enrollment.') }}</p>
                @if ($stripeConfigured || $paypalConfigured)
                    <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">{{ __('Stripe opens checkout.stripe.com; PayPal opens the PayPal site — same style of hosted payment, then you return to this site.') }}</p>
                @endif

                @php
                    $checkoutActionParams = ['course' => $course];
                @endphp
                <form method="POST" action="{{ route('courses.checkout.store', $checkoutActionParams) }}" class="mt-8 space-y-8">
                    @csrf

                    @php($firstPaymentRadioRequired = count($allowedPaymentMethods) > 0)
                    <fieldset class="space-y-3" @if (count($allowedPaymentMethods) === 0) disabled @endif>
                        <legend class="sr-only">{{ __('Payment method') }}</legend>

                        @if (in_array('trial', $allowedPaymentMethods, true))
                            <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-slate-200 bg-slate-50 p-4 has-[:checked]:border-emerald-400 has-[:checked]:ring-2 has-[:checked]:ring-emerald-400/30 dark:border-slate-600 dark:bg-slate-800/60 dark:has-[:checked]:border-emerald-500">
                                <input type="radio" name="payment_method" value="trial" class="mt-1 text-emerald-600 focus:ring-emerald-500" @checked($defaultPaymentMethod === 'trial') @if ($firstPaymentRadioRequired) required @endif>
                                <span>
                                    <span class="font-semibold text-slate-900 dark:text-slate-50">{{ __('Start a free 30-day trial') }}</span>
                                    <span class="mt-0.5 block text-sm text-slate-600 dark:text-slate-400">{{ __('Begin course access now for one month with no immediate charge.') }}</span>
                                </span>
                            </label>
                            @php($firstPaymentRadioRequired = false)
                        @endif

                        @if ($stripeConfigured)
                            <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-slate-200 bg-white p-4 has-[:checked]:border-violet-400 has-[:checked]:ring-2 has-[:checked]:ring-violet-400/30 dark:border-slate-600 dark:bg-slate-900 dark:has-[:checked]:border-violet-500">
                                <input type="radio" name="payment_method" value="stripe" class="mt-1 text-violet-600 focus:ring-violet-500" @checked($defaultPaymentMethod === 'stripe') @if ($firstPaymentRadioRequired) required @endif>
                                <span>
                                    <span class="font-semibold text-slate-900 dark:text-slate-50">{{ __('Pay with Stripe') }}</span>
                                    <span class="mt-0.5 block text-sm text-slate-600 dark:text-slate-400">{{ __('Secure card payment on Stripe’s checkout page.') }}</span>
                                </span>
                            </label>
                            @php($firstPaymentRadioRequired = false)
                        @endif

                        @if ($paypalConfigured)
                            <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-slate-200 bg-white p-4 has-[:checked]:border-sky-400 has-[:checked]:ring-2 has-[:checked]:ring-sky-400/30 dark:border-slate-600 dark:bg-slate-900 dark:has-[:checked]:border-sky-500">
                                <input type="radio" name="payment_method" value="paypal" class="mt-1 text-sky-600 focus:ring-sky-500" @checked($defaultPaymentMethod === 'paypal') @if ($firstPaymentRadioRequired) required @endif>
                                <span>
                                    <span class="font-semibold text-slate-900 dark:text-slate-50">{{ __('Pay with PayPal') }}</span>
                                    <span class="mt-0.5 block text-sm text-slate-600 dark:text-slate-400">{{ __('Approve and pay on PayPal, then return to activate access.') }}</span>
                                </span>
                            </label>
                            @php($firstPaymentRadioRequired = false)
                        @endif

                        @if (in_array('demo', $allowedPaymentMethods, true))
                            <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-slate-200 bg-slate-50/80 p-4 has-[:checked]:border-indigo-400 has-[:checked]:ring-2 has-[:checked]:ring-indigo-400/30 dark:border-slate-600 dark:bg-slate-800/60 dark:has-[:checked]:border-indigo-500">
                                <input type="radio" name="payment_method" value="demo" class="mt-1 text-indigo-600 focus:ring-indigo-500" @checked($defaultPaymentMethod === 'demo') @if ($firstPaymentRadioRequired) required @endif>
                                <span>
                                    <span class="font-semibold text-slate-900 dark:text-slate-50">{{ __('Demo payment (testing only)') }}</span>
                                    <span class="mt-0.5 block text-sm text-slate-600 dark:text-slate-400">{{ __('Simulated payment for local development — no real charge.') }}</span>
                                </span>
                            </label>
                        @endif
                    </fieldset>
                    <x-input-error :messages="$errors->get('payment_method')" class="mt-2" />

                    <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-slate-200 bg-slate-50/80 p-4 has-[:checked]:border-brand-300 has-[:checked]:bg-brand-50/40 dark:border-slate-600 dark:bg-slate-800/60 dark:has-[:checked]:border-brand-600 dark:has-[:checked]:bg-brand-950/30">
                        <input type="checkbox" name="accept_terms" value="1" class="mt-1 rounded border-slate-300 text-brand-600 focus:ring-brand-500 dark:border-slate-600 dark:bg-slate-900" {{ old('accept_terms') ? 'checked' : '' }} required>
                        <span class="text-sm text-slate-700 dark:text-slate-300">{{ __('I agree to pay the total shown and understand that access is granted after payment is recorded.') }}</span>
                    </label>
                    <x-input-error :messages="$errors->get('accept_terms')" class="-mt-4" />
                    <x-input-error :messages="$errors->get('gateway')" class="mt-2" />

                    <button type="submit" class="btn-primary w-full py-3 text-base" @disabled(count($allowedPaymentMethods) === 0)>
                        {{ __('Continue to payment') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
