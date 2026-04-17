<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Update your name, contact details, and email address.') }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="avatar" :value="__('Profile photo')" />
            <div class="mt-2 flex items-center gap-4">
                @if ($user->avatar_url)
                    <img src="{{ $user->avatar_url }}" alt="{{ $user->fullname }}" class="h-14 w-14 rounded-full object-cover ring-2 ring-slate-200 dark:ring-slate-700">
                @else
                    <div class="flex h-14 w-14 items-center justify-center rounded-full bg-brand-100 text-sm font-bold text-brand-700 dark:bg-brand-900/40 dark:text-brand-300">
                        {{ $user->initials }}
                    </div>
                @endif
                <input id="avatar" name="avatar" type="file" accept="image/*" class="block w-full text-sm text-gray-700 file:me-3 file:rounded-lg file:border-0 file:bg-slate-200 file:px-3 file:py-2 file:font-semibold file:text-slate-800 hover:file:bg-slate-300 dark:text-gray-300 dark:file:bg-slate-700 dark:file:text-slate-100 dark:hover:file:bg-slate-600">
            </div>
            @if ($user->avatar_url)
                <label class="mt-3 inline-flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                    <input type="checkbox" name="remove_avatar" value="1" class="rounded border-slate-300 text-brand-600 focus:ring-brand-500 dark:border-slate-600 dark:bg-slate-900">
                    <span>{{ __('Remove current photo') }}</span>
                </label>
            @endif
            <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">{{ __('PNG/JPG/WEBP up to 2MB.') }}</p>
            <x-input-error class="mt-2" :messages="$errors->get('avatar')" />
        </div>

        <div>
            <x-input-label for="fullname" :value="__('Full name')" />
            <x-text-input id="fullname" name="fullname" type="text" class="mt-1 block w-full" :value="old('fullname', $user->fullname)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('fullname')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />
        </div>

        <div>
            <x-input-label for="phone" :value="__('Phone')" />
            <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone', $user->phone)" autocomplete="tel" />
            <x-input-error class="mt-2" :messages="$errors->get('phone')" />
        </div>

        <div>
            <x-input-label for="country" :value="__('Country')" />
            <x-text-input id="country" name="country" type="text" class="mt-1 block w-full" :value="old('country', $user->country)" autocomplete="country-name" />
            <x-input-error class="mt-2" :messages="$errors->get('country')" />
        </div>

        <div>
            <x-input-label for="language" :value="__('Language')" />
            <x-text-input id="language" name="language" type="text" class="mt-1 block w-full" :value="old('language', $user->language)" />
            <x-input-error class="mt-2" :messages="$errors->get('language')" />
        </div>

        <div>
            <x-input-label for="gender" :value="__('Gender')" />
            <select id="gender" name="gender" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                <option value="">{{ __('Prefer not to say') }}</option>
                <option value="female" @selected(old('gender', $user->gender) === 'female')>{{ __('Female') }}</option>
                <option value="male" @selected(old('gender', $user->gender) === 'male')>{{ __('Male') }}</option>
                <option value="other" @selected(old('gender', $user->gender) === 'other')>{{ __('Other') }}</option>
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('gender')" />
        </div>

        @if (! $user->isVerified)
            <div class="rounded-md border border-amber-200 bg-amber-50 p-3 text-sm text-amber-900 dark:border-amber-900/70 dark:bg-amber-950/40 dark:text-amber-200">
                {{ __('Your email is not verified yet.') }}
                <a href="{{ route('verification.otp.show') }}" class="font-medium underline">{{ __('Enter your OTP') }}</a>
            </div>
        @endif

        @if (session('status') === 'email-changed-verify')
            <p class="text-sm font-medium text-green-700 dark:text-green-400">{{ __('We sent a new code to your updated email address.') }}</p>
        @endif

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 dark:text-gray-400"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
