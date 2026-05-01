<x-guest-layout>
    <p class="mb-4 text-center text-sm font-medium text-gray-700">{{ __('Create an account') }}</p>
    <p class="mb-6 text-center text-xs text-gray-500">{{ __('You will receive an email code to verify your address.') }}</p>

    <form method="POST" action="{{ route('register') }}" class="space-y-6">
        @csrf

        <div>
            <x-input-label :value="__('Account type')" />
            <div class="mt-2 space-y-2 rounded-lg border border-gray-200 bg-gray-50/80 p-4 dark:border-gray-700 dark:bg-gray-800/50">
                <label class="flex cursor-pointer items-center gap-3 text-sm text-gray-800 dark:text-gray-200">
                    <input type="radio" name="role" value="student" class="text-indigo-600 focus:ring-indigo-500" {{ old('role', 'student') === 'student' ? 'checked' : '' }} required />
                    <span>{{ __('I am a student') }}</span>
                </label>
                <label class="flex cursor-pointer items-center gap-3 text-sm text-gray-800 dark:text-gray-200">
                    <input type="radio" name="role" value="teacher" class="text-indigo-600 focus:ring-indigo-500" {{ old('role') === 'teacher' ? 'checked' : '' }} required />
                    <span>{{ __('I am an instructor') }}</span>
                </label>
            </div>
            <x-input-error :messages="$errors->get('role')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="fullname" :value="__('Full name')" />
            <x-text-input id="fullname" class="mt-2" type="text" name="fullname" :value="old('fullname')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('fullname')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="mt-2" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="mt-2"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="mt-2"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end gap-4 pt-2">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button>
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
