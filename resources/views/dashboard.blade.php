<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            @isset($teacherStats)
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <div class="overflow-hidden rounded-lg bg-white p-6 shadow sm:rounded-lg dark:bg-gray-900 dark:shadow-gray-900/40">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Your courses') }}</p>
                        <p class="mt-2 text-3xl font-semibold tabular-nums text-gray-900 dark:text-gray-100">{{ number_format($teacherStats['courses']) }}</p>
                    </div>
                    <div class="overflow-hidden rounded-lg bg-white p-6 shadow sm:rounded-lg dark:bg-gray-900 dark:shadow-gray-900/40">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Discount codes') }}</p>
                        <p class="mt-2 text-3xl font-semibold tabular-nums text-gray-900 dark:text-gray-100">{{ number_format($teacherStats['discount_codes']) }}</p>
                    </div>
                    <div class="overflow-hidden rounded-lg bg-white p-6 shadow sm:rounded-lg dark:bg-gray-900 dark:shadow-gray-900/40">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Reviews') }}</p>
                        <p class="mt-2 text-3xl font-semibold tabular-nums text-gray-900 dark:text-gray-100">{{ number_format($teacherStats['reviews']) }}</p>
                    </div>
                    <div class="overflow-hidden rounded-lg bg-white p-6 shadow sm:rounded-lg dark:bg-gray-900 dark:shadow-gray-900/40">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Subscriptions') }}</p>
                        <p class="mt-2 text-3xl font-semibold tabular-nums text-gray-900 dark:text-gray-100">{{ number_format($teacherStats['enrollments']) }}</p>
                    </div>
                    <div class="overflow-hidden rounded-lg bg-white p-6 shadow sm:rounded-lg dark:bg-gray-900 dark:shadow-gray-900/40">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Completed purchases') }}</p>
                        <p class="mt-2 text-3xl font-semibold tabular-nums text-gray-900 dark:text-gray-100">{{ number_format($teacherStats['purchases']) }}</p>
                    </div>
                    <div class="overflow-hidden rounded-lg bg-white p-6 shadow sm:rounded-lg dark:bg-gray-900 dark:shadow-gray-900/40">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Total revenue') }}</p>
                        <p class="mt-2 text-3xl font-semibold tabular-nums text-gray-900 dark:text-gray-100">{{ number_format($teacherStats['revenue'], 2) }} USD</p>
                    </div>
                </div>
                <p class="mt-8 text-center text-sm text-gray-600 dark:text-gray-400">
                    <a href="{{ url('/admin') }}" class="font-semibold text-indigo-600 underline hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300">{{ __('Open instructor panel') }}</a>
                </p>
            @else
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-900 dark:shadow-gray-900/40">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <p class="mb-4">{{ __("You're logged in!") }}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            <a href="{{ route('my-courses') }}" class="font-semibold text-indigo-600 underline hover:text-indigo-800 dark:text-indigo-400">{{ __('Go to my courses') }}</a>
                        </p>
                    </div>
                </div>
            @endisset
        </div>
    </div>
</x-app-layout>
