<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Verify your email') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 space-y-6">
                    <p>{{ __('We sent a 6-digit code to :email. Enter it below to activate your account.', ['email' => auth()->user()->email]) }}</p>

                    @if (session('status') === 'verification-code-sent')
                        <p class="text-sm text-emerald-700">{{ __('A fresh code has been sent to your inbox.') }}</p>
                    @endif

                    <form method="POST" action="{{ route('verification.otp.store') }}" class="space-y-6">
                        @csrf
                        <div>
                            <x-input-label for="otp" :value="__('One-time code')" />
                            <x-text-input id="otp" name="otp" type="text" inputmode="numeric" maxlength="6" class="mt-2 tracking-widest" required autofocus autocomplete="one-time-code" />
                            <x-input-error :messages="$errors->get('otp')" class="mt-2" />
                        </div>
                        <x-primary-button>{{ __('Verify and continue') }}</x-primary-button>
                    </form>

                    <form method="POST" action="{{ route('verification.otp.resend') }}">
                        @csrf
                        <x-secondary-button type="submit">{{ __('Resend code') }}</x-secondary-button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
