@extends('layouts.public')

@section('title', __('Search') . ($q !== '' ? ' — '.$q : '') . ' — ' . config('app.name'))

@section('content')
    <div class="mb-10">
        <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 dark:text-slate-50">{{ __('Search') }}</h1>
        @if ($searchCoursesInCategory && $categoryFilter)
            <p class="mt-2 text-slate-600 dark:text-slate-300">{{ __('Searching courses in :category.', ['category' => $categoryFilter->name]) }}</p>
        @else
            <p class="mt-2 text-slate-600 dark:text-slate-300">{{ __('Search categories by keyword. Open a category page to search courses inside that track.') }}</p>
        @endif

        <form method="get" action="{{ route('search') }}" class="mt-6 flex max-w-2xl flex-col gap-3 sm:flex-row sm:items-stretch">
            @if ($searchCoursesInCategory && $categoryFilter)
                <input type="hidden" name="category" value="{{ $categoryFilter->id }}">
            @endif
            <label for="search-q" class="sr-only">{{ __('Search query') }}</label>
            <input
                id="search-q"
                name="q"
                type="search"
                value="{{ $q }}"
                autocomplete="off"
                placeholder="{{ $searchCoursesInCategory ? __('Search courses in this category…') : __('Search categories…') }}"
                class="block w-full flex-1 rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-brand-500 focus:ring-brand-500 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100 dark:placeholder:text-slate-500"
            >
            <button type="submit" class="btn-primary shrink-0 px-6 py-3">{{ __('Search') }}</button>
        </form>
    </div>

    @if ($q === '')
        <p class="rounded-2xl border border-dashed border-slate-300 bg-white/60 p-10 text-center text-slate-600 dark:border-slate-600 dark:bg-slate-900/60 dark:text-slate-300">{{ __('Type a keyword above to see results.') }}</p>
    @else
        @if (! $searchCoursesInCategory)
            <section>
                <h2 class="mb-4 text-xl font-bold text-slate-900 dark:text-slate-50">{{ __('Categories') }}</h2>
                @if ($categories->isEmpty())
                    <p class="rounded-2xl border border-dashed border-slate-300 bg-white/60 p-10 text-center text-slate-600 dark:border-slate-600 dark:bg-slate-900/60 dark:text-slate-300">{{ __('No categories matched your search.') }}</p>
                @else
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($categories as $category)
                            <a href="{{ route('categories.show', $category) }}" class="card-surface block p-5 transition hover:shadow-card-hover">
                                <h3 class="font-bold text-slate-900 dark:text-slate-50">{{ $category->name }}</h3>
                                <p class="mt-2 line-clamp-2 text-sm text-slate-600 dark:text-slate-300">{{ $category->description }}</p>
                            </a>
                        @endforeach
                    </div>
                    <div class="mt-8">
                        {{ $categories->links() }}
                    </div>
                @endif
            </section>
        @else
            <section>
                <h2 class="mb-4 text-xl font-bold text-slate-900 dark:text-slate-50">{{ __('Courses') }}</h2>
                @if ($courses->isEmpty())
                    <p class="rounded-2xl border border-dashed border-slate-300 bg-white/60 p-10 text-center text-slate-600 dark:border-slate-600 dark:bg-slate-900/60 dark:text-slate-300">{{ __('No courses matched your search.') }}</p>
                @else
                    <div class="grid gap-8 sm:grid-cols-2 xl:grid-cols-3">
                        @foreach ($courses as $course)
                            <article class="card-surface flex flex-col overflow-hidden transition hover:shadow-card-hover">
                                <div class="aspect-[16/10] bg-slate-100 dark:bg-slate-800">
                                    @if ($course->img_link)
                                        <img src="{{ $course->img_link }}" alt="" class="h-full w-full object-cover">
                                    @else
                                        <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-brand-100 to-slate-100 text-brand-600 dark:from-brand-950 dark:to-slate-900 dark:text-brand-400">
                                            <svg class="h-12 w-12 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex flex-1 flex-col p-5">
                                    <p class="text-xs font-bold uppercase tracking-wider text-brand-600 dark:text-brand-400">{{ $course->category?->name }}</p>
                                    <h3 class="mt-1 text-lg font-bold text-slate-900 dark:text-slate-50">{{ $course->title }}</h3>
                                    <p class="mt-2 flex-1 text-sm text-slate-600 line-clamp-2 dark:text-slate-300">{{ \Illuminate\Support\Str::limit($course->description ?? '', 120) }}</p>
                                    <a href="{{ route('courses.show', $course) }}" class="btn-primary mt-4 w-full justify-center text-sm">{{ __('View course') }}</a>
                                </div>
                            </article>
                        @endforeach
                    </div>
                    <div class="mt-8">
                        {{ $courses->links() }}
                    </div>
                @endif
            </section>
        @endif
    @endif
@endsection
