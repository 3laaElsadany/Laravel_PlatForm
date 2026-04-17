<!DOCTYPE html>
@php
    $rtl = str_starts_with(strtolower(app()->getLocale()), 'ar');
@endphp
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ $rtl ? 'rtl' : 'ltr' }}" class="h-full scroll-smooth antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    @include('partials.site-icons')
    <style>[x-cloak]{display:none !important;}</style>
    @include('partials.theme-init')

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="relative min-h-screen font-sans text-gray-900 antialiased dark:bg-gray-950 dark:text-gray-100">
    <div class="absolute end-5 top-5 z-10 flex items-center gap-2 sm:end-10 sm:top-8">
        <x-locale-switcher />
        <x-theme-toggle />
    </div>
    <div class="flex min-h-screen flex-col items-center justify-center gap-10 bg-gray-100 px-4 py-12 sm:px-8 sm:py-16 dark:bg-gray-950">
        <div class="text-center">
            <a href="{{ route('home') }}" class="inline-block" title="{{ __('Home') }}">
                <x-application-logo class="mx-auto h-20 w-20" />
            </a>
            <p class="mt-4 text-xs text-gray-500 dark:text-gray-400">
                <a href="{{ route('home') }}" class="underline hover:text-gray-700 dark:hover:text-gray-200">{{ __('Back to site') }}</a>
            </p>
        </div>

        <div class="w-full max-w-md overflow-hidden rounded-2xl border border-gray-200/80 bg-white px-8 py-8 shadow-md dark:border-gray-700 dark:bg-gray-900 sm:px-10 sm:py-10">
            {{ $slot }}
        </div>
    </div>
</body>
</html>
