<!DOCTYPE html>
@php
    $rtl = str_starts_with(strtolower(app()->getLocale()), 'ar');
@endphp
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ $rtl ? 'rtl' : 'ltr' }}" class="h-full scroll-smooth antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>
    @include('partials.site-icons')
    <style>[x-cloak]{display:none !important;}</style>
    @include('partials.theme-init')
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700|figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-full flex-col bg-slate-50 font-sans text-slate-900 dark:bg-slate-950 dark:text-slate-100">
    <div class="pointer-events-none fixed inset-0 -z-10 overflow-hidden">
        <div class="absolute -top-32 start-1/2 h-96 w-[48rem] -translate-x-1/2 rounded-full bg-gradient-to-br from-brand-100/90 via-indigo-100/50 to-transparent blur-3xl dark:from-brand-900/20 dark:via-indigo-950/40 dark:to-transparent"></div>
        <div class="absolute bottom-0 end-0 h-72 w-72 rounded-full bg-violet-200/30 blur-3xl dark:bg-violet-900/20"></div>
    </div>

    <header id="public-header" class="sticky top-0 z-50 border-b border-slate-200/80 bg-white/90 backdrop-blur-md dark:border-slate-800/80 dark:bg-slate-900/90">
        <div class="site-container flex flex-wrap items-center gap-x-5 gap-y-4 py-4 md:py-5">
            <a href="{{ route('home') }}" class="group flex shrink-0 items-center gap-3">
                <img src="{{ asset('images/site-logo.png') }}" alt="{{ config('app.name') }}" width="36" height="36" class="h-9 w-9 shrink-0 rounded-xl object-contain shadow-md ring-1 ring-slate-200/80 dark:ring-slate-700/80" />
                <span class="text-lg font-bold tracking-tight text-slate-900 transition group-hover:text-brand-700 dark:text-white dark:group-hover:text-brand-400">{{ config('app.name') }}</span>
            </a>

            @php
                $navCategory = request()->route('category');
                $navCategorySearch = request()->routeIs('categories.show') && $navCategory instanceof \App\Models\Category;
                if (! $navCategorySearch && request()->routeIs('search') && request()->filled('category')) {
                    $navCategory = \App\Models\Category::query()->find((int) request()->query('category'));
                    $navCategorySearch = $navCategory instanceof \App\Models\Category;
                }
            @endphp
            <form method="get" action="{{ route('search') }}" class="order-3 flex min-w-0 w-full basis-full items-center gap-3 sm:order-none sm:basis-64 sm:flex-1 sm:max-w-xl" role="search">
                @if ($navCategorySearch)
                    <input type="hidden" name="category" value="{{ $navCategory->id }}">
                @endif
                <label for="nav-search-q" class="sr-only">{{ __('Search') }}</label>
                <input id="nav-search-q" name="q" type="search" value="{{ request('q') }}" placeholder="{{ $navCategorySearch ? __('Search courses in this category…') : __('Search categories…') }}" class="min-h-11 w-full min-w-0 flex-1 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/30 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 dark:placeholder:text-slate-500">
                <button type="submit" class="btn-secondary min-h-11 shrink-0 whitespace-nowrap !px-5 !py-2.5 text-sm">{{ __('Search') }}</button>
            </form>

            <div class="ms-auto flex shrink-0 flex-wrap items-center justify-end gap-3 sm:ms-0">
                <nav class="flex flex-wrap items-center justify-end gap-2 sm:gap-2.5">
                    <a href="{{ route('home') }}" class="rounded-lg px-3.5 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white">{{ __('Courses') }}</a>
                    @auth
                        @if(auth()->user()->isVerified)
                            <a href="{{ route('dashboard') }}" class="rounded-lg px-3.5 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white">{{ __('Dashboard') }}</a>
                            @if(auth()->user()->role === \App\Models\User::ROLE_STUDENT)
                                <a href="{{ route('my-courses') }}" class="rounded-lg px-3.5 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white">{{ __('My courses') }}</a>
                            @endif
                            <a href="{{ route('profile.edit') }}" class="rounded-lg px-3.5 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white">{{ __('Profile') }}</a>
                        @else
                            <a href="{{ route('verification.otp.show') }}" class="rounded-lg px-3.5 py-2.5 text-sm font-semibold text-amber-800 hover:bg-amber-50 dark:text-amber-300 dark:hover:bg-amber-950/50">{{ __('Verify email') }}</a>
                        @endif
                        @if(auth()->user()->isAdmin())
                            <a href="{{ url('/admin') }}" class="rounded-lg px-3.5 py-2.5 text-sm font-semibold text-violet-700 hover:bg-violet-50 dark:text-violet-300 dark:hover:bg-violet-950/40">{{ __('Admin') }}</a>
                        @elseif(auth()->user()->isTeacher())
                            <a href="{{ url('/admin') }}" class="rounded-lg px-3.5 py-2.5 text-sm font-semibold text-violet-700 hover:bg-violet-50 dark:text-violet-300 dark:hover:bg-violet-950/40">{{ __('Instructor panel') }}</a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="rounded-lg px-3.5 py-2.5 text-sm font-medium text-slate-500 hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200">{{ __('Log out') }}</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="rounded-lg px-3.5 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800">{{ __('Log in') }}</a>
                        <a href="{{ route('register') }}" class="btn-primary !py-2 !px-4 text-sm">{{ __('Register') }}</a>
                    @endauth
                </nav>
                <x-locale-switcher />
                <x-theme-toggle />
            </div>
        </div>
    </header>

    <main class="site-container flex-1 space-y-8 py-12 sm:py-16">
        @if (session('status'))
            @php($status = session('status'))
            <div class="rounded-2xl border border-emerald-200/80 bg-emerald-50/90 px-4 py-3.5 text-sm font-medium text-emerald-900 shadow-sm dark:border-emerald-800/80 dark:bg-emerald-950/50 dark:text-emerald-200">
                @if ($status === 'enrolled-after-payment')
                    {{ __('Payment completed. You are now enrolled in this course.') }}
                @elseif ($status === 'already-enrolled')
                    {{ __('You are already enrolled in this course.') }}
                @elseif ($status === 'email-verified')
                    {{ __('Your email has been verified.') }}
                @elseif ($status === 'review-posted')
                    {{ __('Thanks! Your review was posted.') }}
                @elseif ($status === 'rating-saved')
                    {{ __('Your course rating was saved.') }}
                @elseif ($status === 'rating-removed')
                    {{ __('Your course rating was removed.') }}
                @elseif ($status === 'payment-cancelled')
                    {{ __('Checkout was cancelled. You have not been charged.') }}
                @else
                    {{ __($status) }}
                @endif
            </div>
        @endif

        @if ($errors->any() && ! request()->routeIs('courses.checkout.show'))
            <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3.5 text-sm text-red-900 dark:border-red-900/60 dark:bg-red-950/40 dark:text-red-200">
                <ul class="list-inside list-disc space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="mt-auto border-t border-slate-200/80 bg-white/90 py-12 backdrop-blur-sm dark:border-slate-800/80 dark:bg-slate-900/90">
        <div class="site-container flex flex-col items-center justify-between gap-8 sm:flex-row">
            <div class="text-center sm:text-start">
                <p class="font-semibold text-slate-900 dark:text-white">{{ config('app.name') }}</p>
                <p class="mt-1 max-w-md text-sm text-slate-500 dark:text-slate-400">{{ __('Learn at your pace with structured paths and clear pricing.') }}</p>
            </div>
            <p class="text-sm text-slate-400 dark:text-slate-500">&copy; {{ date('Y') }} {{ config('app.name') }}</p>
        </div>
    </footer>
</body>
</html>
