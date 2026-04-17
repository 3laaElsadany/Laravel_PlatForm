@props(['class' => ''])

<form method="POST" action="{{ route('locale.switch') }}" class="inline-flex">
    @csrf
    <input type="hidden" name="redirect_to" value="{{ url()->full() }}">

    <label class="sr-only" for="locale-switcher">{{ __('Language') }}</label>
    <select
        id="locale-switcher"
        name="locale"
        onchange="this.form.submit()"
        class="inline-flex shrink-0 rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-800 shadow-sm transition hover:bg-slate-50 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/30 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 {{ $class }}"
        aria-label="{{ __('Language') }}"
    >
        <option value="en" @selected(app()->getLocale() === 'en')>{{ __('English') }}</option>
        <option value="ar" @selected(app()->getLocale() === 'ar')>{{ __('Arabic') }}</option>
    </select>
</form>
