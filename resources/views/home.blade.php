@extends('layouts.public')

@section('title', __('Learn online') . ' — ' . config('app.name'))

@section('content')
    <section class="mb-14 text-center sm:text-start">
        <p class="text-sm font-semibold uppercase tracking-widest text-brand-600 dark:text-brand-400">{{ __('Your learning hub') }}</p>
        <h1 class="mt-3 text-4xl font-extrabold tracking-tight text-slate-900 dark:text-slate-50 sm:text-5xl">{{ __('Online courses for every goal') }}</h1>
        <p class="mx-auto mt-4 max-w-2xl text-lg text-slate-600 dark:text-slate-300 sm:mx-0">{{ __('Browse categories, then open a track to see courses. Use the search bar to find a category by name.') }}</p>
    </section>

    <section class="mb-16">
        <div class="mb-8 flex items-end justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-slate-900 dark:text-slate-50">{{ __('Categories') }}</h2>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ __('Pick a track and explore curated courses.') }}</p>
            </div>
        </div>
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($categories as $category)
                <a href="{{ route('categories.show', $category) }}" class="card-surface group flex flex-col p-6 transition hover:-translate-y-0.5 hover:border-brand-200 hover:shadow-card-hover dark:hover:border-brand-700">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-brand-50 text-brand-700 ring-1 ring-brand-100 dark:bg-brand-950/40 dark:text-brand-300 dark:ring-brand-900">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>
                    </div>
                    <h3 class="mt-4 text-xl font-bold text-slate-900 group-hover:text-brand-700 dark:text-slate-50 dark:group-hover:text-brand-400">{{ $category->name }}</h3>
                    <p class="mt-2 flex-1 text-sm leading-relaxed text-slate-600 line-clamp-3 dark:text-slate-300">{{ $category->description }}</p>
                    <p class="mt-4 text-xs font-semibold uppercase tracking-wide text-slate-400 dark:text-slate-500">{{ trans_choice(':count course|:count courses', $category->courses_count, ['count' => $category->courses_count]) }}</p>
                </a>
            @endforeach
        </div>
    </section>
@endsection
