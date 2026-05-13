<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-100">
            {{ __('Verify your email') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-xl sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm sm:rounded-lg dark:border-gray-700 dark:bg-gray-900 dark:shadow-gray-900/40">
                <div class="space-y-6 p-6 text-gray-900 dark:text-gray-100">
                    <p class="text-gray-700 dark:text-gray-300">{{ __('We sent a 6-digit code to :email. Enter it below to activate your account.', ['email' => auth()->user()->email]) }}</p>

                    @if (session('status') === 'verification-code-sent')
                        <p class="text-sm text-emerald-700 dark:text-emerald-400">{{ __('A fresh code has been sent to your inbox.') }}</p>
                    @endif

                    <form method="POST" action="{{ route('verification.otp.store') }}" class="space-y-6">
                        @csrf
                        <div>
                            <x-input-label for="otp" :value="__('One-time code')" />
                            <x-text-input id="otp" name="otp" type="text" inputmode="numeric" maxlength="6" class="mt-2 tracking-widest dark:border-gray-600 dark:bg-gray-800 dark:text-white" required autofocus autocomplete="one-time-code" />
                            <x-input-error :messages="$errors->get('otp')" class="mt-2" />
                        </div>
                        <x-primary-button>{{ __('Verify and continue') }}</x-primary-button>
                    </form>

                    <form method="POST" action="{{ route('verification.otp.resend') }}">
                        @csrf
                        <x-secondary-button type="submit" class="dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">{{ __('Resend code') }}</x-secondary-button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
