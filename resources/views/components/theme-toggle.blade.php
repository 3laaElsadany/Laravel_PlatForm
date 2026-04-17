@props(['class' => ''])

<button
    type="button"
    data-fin-theme-toggle
    x-data="{
        dark: false,
        sync() {
            this.dark = document.documentElement.classList.contains('dark');
        },
        toggle() {
            if (typeof window.finToggleTheme === 'function') {
                window.finToggleTheme();
            }
            this.sync();
        },
        bind() {
            this.sync();
            var root = document.documentElement;
            var self = this;
            try {
                var obs = new MutationObserver(function () {
                    self.sync();
                });
                obs.observe(root, { attributes: true, attributeFilter: ['class'] });
            } catch (e) {}
            window.addEventListener('fin-theme-changed', function () {
                self.sync();
            });
            window.addEventListener('storage', function (e) {
                if (e.key === 'theme') {
                    if (typeof window.finApplyTheme === 'function') {
                        window.finApplyTheme();
                    }
                    self.sync();
                }
            });
        },
    }"
    x-init="bind()"
    @click.prevent="toggle()"
    class="fin-theme-toggle inline-flex shrink-0 items-center gap-2 rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 text-sm font-semibold text-slate-800 shadow-sm transition hover:bg-white hover:text-slate-950 {{ $class }}"
    aria-label="{{ __('Toggle dark mode') }}"
    title="{{ __('Toggle dark mode') }}"
>
    <span x-show="!dark" class="inline-flex items-center gap-2">
        <svg class="h-4 w-4 shrink-0 text-slate-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
        </svg>
        <span>{{ __('Dark mode') }}</span>
    </span>
    <span x-show="dark" class="inline-flex items-center gap-2" x-cloak>
        <svg class="h-4 w-4 shrink-0 text-slate-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
        </svg>
        <span>{{ __('Light mode') }}</span>
    </span>
</button>
