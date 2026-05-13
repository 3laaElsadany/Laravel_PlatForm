@extends('layouts.public')

@section('title', $category->name . ' — ' . config('app.name'))

@section('content')
    <div class="mb-10">
        <a href="{{ route('home') }}" class="inline-flex items-center gap-1 text-sm font-medium text-brand-600 hover:text-brand-800 dark:text-brand-400 dark:hover:text-brand-300">
            <svg class="h-4 w-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            {{ __('Back to home') }}
        </a>
        <h1 class="mt-4 text-4xl font-extrabold tracking-tight text-slate-900 dark:text-slate-50">{{ $category->name }}</h1>
        @if ($category->description)
            <p class="mt-3 max-w-3xl text-lg text-slate-600 dark:text-slate-300">{{ $category->description }}</p>
        @endif
    </div>

    <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($courses as $course)
            <article class="card-surface flex flex-col overflow-hidden">
                <div class="aspect-[16/10] bg-slate-100 dark:bg-slate-800">
                    @if ($course->img_link)
                        <img src="{{ $course->img_link }}" alt="" class="h-full w-full object-cover">
                    @else
                        <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-slate-100 to-brand-50 text-brand-400 dark:from-slate-800 dark:to-brand-950/50 dark:text-brand-500">
                            <svg class="h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        </div>
                    @endif
                </div>
                <div class="flex flex-1 flex-col p-5">
                    <h2 class="text-lg font-bold text-slate-900 dark:text-slate-50">{{ $course->title }}</h2>
                    <p class="mt-2 flex-1 text-sm text-slate-600 line-clamp-3 dark:text-slate-300">{{ $course->description }}</p>
                    <div class="mt-4 flex items-center justify-between border-t border-slate-100 pt-4 dark:border-slate-700">
                        <div>
                            <p class="font-bold text-slate-900 dark:text-slate-50">{{ number_format($course->priceAfterCatalogDiscount(), 2) }} {{ __('USD') }}</p>
                            <p class="text-xs text-amber-700 dark:text-amber-400">★ {{ number_format((float) ($course->course_ratings_count > 0 ? $course->course_ratings_avg_rating : $course->rate), 1) }}</p>
                        </div>
                        <a href="{{ route('courses.show', $course) }}" class="btn-secondary !py-2 !px-4 text-sm">{{ __('Details') }}</a>
                    </div>
                </div>
            </article>
        @empty
            <p class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-white/60 p-10 text-center text-slate-600 dark:border-slate-600 dark:bg-slate-900/60 dark:text-slate-300">{{ __('No courses in this category yet.') }}</p>
        @endforelse
    </div>

    <div class="mt-10">
        {{ $courses->links() }}
    </div>
@endsection
