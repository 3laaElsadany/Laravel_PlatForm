@extends('layouts.public')

@section('title', __('My courses') . ' — ' . config('app.name'))

@section('content')
    <div class="mb-10">
        <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 dark:text-slate-50">{{ __('My courses') }}</h1>
        <p class="mt-2 text-slate-600 dark:text-slate-300">{{ __('Courses you have purchased and unlocked.') }}</p>
    </div>

    <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($enrollments as $enrollment)
            <article class="card-surface flex flex-col overflow-hidden">
                <div class="aspect-[16/10] bg-slate-100 dark:bg-slate-800">
                    @if ($enrollment->course->img_link)
                        <img src="{{ $enrollment->course->img_link }}" alt="" class="h-full w-full object-cover">
                    @else
                        <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-brand-50 to-slate-100 text-brand-400 dark:from-brand-950/50 dark:to-slate-900 dark:text-brand-500">
                            <svg class="h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        </div>
                    @endif
                </div>
                <div class="flex flex-1 flex-col p-5">
                    <h2 class="text-lg font-bold text-slate-900 dark:text-slate-50">{{ $enrollment->course->title }}</h2>
                    <p class="mt-1 text-xs font-semibold uppercase tracking-wide text-brand-600 dark:text-brand-400">{{ $enrollment->course->category?->name }}</p>
                    <p class="mt-3 text-sm text-slate-600 dark:text-slate-300">{{ __('Enrolled') }}: <span class="font-medium text-slate-800 dark:text-slate-200">{{ $enrollment->enrolled_at?->format('M j, Y') }}</span></p>
                    @if ($enrollment->final_price !== null)
                        <p class="mt-1 text-sm font-semibold text-slate-900 dark:text-slate-50">{{ __('Paid') }}: {{ number_format($enrollment->final_price, 2) }} {{ __('USD') }}</p>
                    @endif
                    @if ($enrollment->payment)
                        <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">{{ __('Receipt') }}: <span class="font-mono text-slate-700 dark:text-slate-300">{{ $enrollment->payment->reference }}</span></p>
                    @endif
                    <a href="{{ route('courses.show', $enrollment->course) }}" class="btn-secondary mt-5 w-full justify-center text-sm">{{ __('Open course') }}</a>
                </div>
            </article>
        @empty
            <div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-white/60 p-12 text-center dark:border-slate-600 dark:bg-slate-900/60">
                <p class="text-slate-600 dark:text-slate-300">{{ __('You have not enrolled in any courses yet.') }}</p>
                <a href="{{ route('home') }}" class="btn-primary mt-6 inline-flex">{{ __('Browse courses') }}</a>
            </div>
        @endforelse
    </div>

    <div class="mt-10">
        {{ $enrollments->links() }}
    </div>
@endsection
