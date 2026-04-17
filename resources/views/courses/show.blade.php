@extends('layouts.public')

@section('title', $course->title . ' — ' . config('app.name'))

@php($discounted = $course->priceAfterCatalogDiscount())

@section('content')
    <div class="mb-8">
        <a href="{{ route('categories.show', $course->category) }}" class="inline-flex items-center gap-1 text-sm font-medium text-brand-600 hover:text-brand-800 dark:text-brand-400 dark:hover:text-brand-300">
            <svg class="h-4 w-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            {{ $course->category->name }}
        </a>
    </div>

    <div class="grid gap-10 lg:grid-cols-3">
        <div class="space-y-10 lg:col-span-2">
            <div class="card-surface overflow-hidden">
                @if ($course->img_link)
                    <div class="aspect-[21/9] max-h-80 overflow-hidden bg-slate-100 dark:bg-slate-800">
                        <img src="{{ $course->img_link }}" alt="" class="h-full w-full object-cover">
                    </div>
                @endif
                <div class="p-6 sm:p-8">
                    <div class="flex flex-wrap items-center gap-3">
                        <span class="rounded-full bg-brand-50 px-3 py-1 text-xs font-bold uppercase tracking-wide text-brand-700 dark:bg-brand-950/50 dark:text-brand-300">{{ $course->category->name }}</span>
                        <span class="flex flex-wrap items-baseline gap-x-2 gap-y-1 text-sm font-semibold text-amber-600 dark:text-amber-400">
                            <span class="inline-flex items-center gap-1">
                                <span aria-hidden="true">★</span>
                                {{ number_format($displayRating, 1) }}
                            </span>
                            <span class="text-xs font-normal text-slate-500 dark:text-slate-400">{{ __('rating') }}</span>
                            @if ($learnerRatingsCount > 0)
                                <span class="w-full text-xs font-normal text-slate-500 dark:text-slate-400 sm:w-auto">{{ __('Based on :count subscriber ratings.', ['count' => $learnerRatingsCount]) }}</span>
                            @else
                                <span class="w-full text-xs font-normal text-slate-500 dark:text-slate-400 sm:w-auto">{{ __('Catalog rating shown until subscribers submit ratings.') }}</span>
                            @endif
                        </span>
                    </div>
                    <h1 class="mt-4 text-3xl font-extrabold tracking-tight text-slate-900 dark:text-slate-50 sm:text-4xl">{{ $course->title }}</h1>
                    <div class="prose prose-slate prose-lg mt-6 max-w-none dark:prose-invert">
                        <p class="whitespace-pre-line leading-relaxed text-slate-700 dark:text-slate-300">{{ $course->description }}</p>
                    </div>
                </div>
            </div>

            @if ($enrolled && $course->video_link)
                <section class="card-surface p-6 sm:p-8">
                    <h2 class="text-lg font-bold text-slate-900 dark:text-slate-50">{{ __('Course video') }}</h2>
                    <div class="mt-4 aspect-video overflow-hidden rounded-xl border border-slate-200 bg-black shadow-inner dark:border-slate-700">
                        <iframe class="h-full w-full" src="{{ $course->video_link }}" title="{{ $course->title }}" allowfullscreen loading="lazy"></iframe>
                    </div>
                </section>
            @endif

            @if (is_array($course->course_includes) && count($course->course_includes))
                <section class="card-surface p-6 sm:p-8">
                    <h2 class="text-lg font-bold text-slate-900 dark:text-slate-50">{{ __('This course includes') }}</h2>
                    <ul class="mt-4 grid gap-3 sm:grid-cols-2">
                        @foreach ($course->course_includes as $item)
                            <li class="flex items-start gap-3 rounded-xl bg-slate-50/80 px-4 py-3 text-sm text-slate-700 ring-1 ring-slate-100 dark:bg-slate-800/60 dark:text-slate-200 dark:ring-slate-700">
                                <span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-300">
                                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </span>
                                <span>{{ $item }}</span>
                            </li>
                        @endforeach
                    </ul>
                </section>
            @endif

            <section class="card-surface p-6 sm:p-8">
                <h2 class="text-lg font-bold text-slate-900 dark:text-slate-50">{{ __('Subscriber ratings') }}</h2>
                @if ($learnerRatingsCount === 0)
                    <p class="mt-4 text-slate-600 dark:text-slate-400">{{ __('No subscriber ratings yet.') }}</p>
                @else
                    <ul class="mt-6 space-y-4">
                        @foreach ($course->courseRatings as $courseRating)
                            <li class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-slate-100 bg-slate-50/50 px-4 py-3 dark:border-slate-700 dark:bg-slate-800/50">
                                <div class="flex items-center gap-3">
                                    @if ($courseRating->user->avatar_url)
                                        <img src="{{ $courseRating->user->avatar_url }}" alt="{{ $courseRating->user->fullname }}" class="h-9 w-9 rounded-full object-cover ring-1 ring-slate-200 dark:ring-slate-600">
                                    @else
                                        <div class="flex h-9 w-9 items-center justify-center rounded-full bg-brand-100 text-xs font-bold text-brand-700 dark:bg-brand-900/40 dark:text-brand-300">
                                            {{ $courseRating->user->initials }}
                                        </div>
                                    @endif
                                    <p class="font-semibold text-slate-900 dark:text-slate-50">{{ $courseRating->user->fullname }}</p>
                                </div>
                                <div class="flex items-center gap-2" aria-label="{{ __(':stars out of 5 stars', ['stars' => $courseRating->rating]) }}">
                                    <span class="text-sm font-bold text-amber-600 dark:text-amber-400">{{ $courseRating->rating }}/5</span>
                                    <span aria-hidden="true">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <span class="{{ $i <= $courseRating->rating ? 'text-amber-500 dark:text-amber-400' : 'text-slate-300 dark:text-slate-600' }}">★</span>
                                        @endfor
                                    </span>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>

            <section class="card-surface p-6 sm:p-8">
                <h2 class="text-lg font-bold text-slate-900 dark:text-slate-50">{{ __('Reviews') }} <span class="font-normal text-slate-500 dark:text-slate-400">({{ $course->reviews->count() }})</span></h2>
                <div class="mt-6 space-y-4">
                    @forelse ($course->reviews as $review)
                        <div class="rounded-xl border border-slate-100 bg-slate-50/50 p-5 dark:border-slate-700 dark:bg-slate-800/50">
                            <div class="flex items-center gap-3">
                                @if ($review->user->avatar_url)
                                    <img src="{{ $review->user->avatar_url }}" alt="{{ $review->user->fullname }}" class="h-10 w-10 rounded-full object-cover ring-1 ring-slate-200 dark:ring-slate-600">
                                @else
                                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-brand-100 text-xs font-bold text-brand-700 dark:bg-brand-900/40 dark:text-brand-300">
                                        {{ $review->user->initials }}
                                    </div>
                                @endif
                                <p class="font-semibold text-slate-900 dark:text-slate-50">{{ $review->user->fullname }}</p>
                            </div>
                            <p class="mt-2 text-sm leading-relaxed text-slate-700 whitespace-pre-line dark:text-slate-300">{{ $review->description }}</p>
                            <p class="mt-3 text-xs font-medium text-slate-400 dark:text-slate-500">{{ $review->created_at?->diffForHumans() }}</p>
                        </div>
                    @empty
                        <p class="text-slate-600 dark:text-slate-400">{{ __('No reviews yet. Be the first to share feedback.') }}</p>
                    @endforelse
                </div>

                @auth
                    @can('create', \App\Models\Review::class)
                        <form method="POST" action="{{ route('courses.reviews.store', $course) }}" class="mt-8 space-y-4 border-t border-slate-100 pt-8 dark:border-slate-700">
                            @csrf
                            <label for="description" class="block text-sm font-semibold text-slate-800 dark:text-slate-200">{{ __('Write a review') }}</label>
                            <textarea id="description" name="description" rows="4" class="block w-full rounded-xl border-slate-300 bg-white shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100" required>{{ old('description') }}</textarea>
                            <x-input-error :messages="$errors->get('description')" />
                            <button type="submit" class="btn-primary">{{ __('Submit review') }}</button>
                        </form>
                    @else
                        <p class="mt-6 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900 dark:border-amber-800 dark:bg-amber-950/40 dark:text-amber-200">{{ __('Verify your email to post a review.') }}</p>
                    @endcan
                @else
                    <p class="mt-6 text-sm text-slate-600 dark:text-slate-400"><a href="{{ route('login') }}" class="font-semibold text-brand-600 hover:text-brand-800 dark:text-brand-400 dark:hover:text-brand-300">{{ __('Log in') }}</a> {{ __('to leave a review.') }}</p>
                @endauth
            </section>
        </div>

        <aside class="lg:col-span-1">
            <div class="card-surface sticky top-24 space-y-6 p-6 shadow-card">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ __('Price') }}</p>
                    <p class="mt-1 text-3xl font-extrabold text-slate-900 dark:text-slate-50">{{ number_format($discounted, 2) }} <span class="text-base font-semibold text-slate-500 dark:text-slate-400">{{ __('USD') }}</span></p>
                    @if ($course->discount > 0)
                        <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">{{ __('Original') }}: <span class="line-through">{{ number_format($course->price, 2) }}</span>
                            <span class="ms-2 font-semibold text-emerald-600 dark:text-emerald-400">-{{ number_format($course->discount, 0) }}%</span>
                        </p>
                    @endif
                </div>

                <div class="rounded-xl bg-slate-50 p-4 text-xs leading-relaxed text-slate-600 dark:bg-slate-800/80 dark:text-slate-300">
                    {{ __('Enrollment happens after a successful payment. You can apply an instructor discount code on the checkout page.') }}
                </div>

                @auth
                    @if ($enrolled)
                        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-900 dark:border-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-200">{{ __('You are enrolled in this course.') }}</div>
                        @can('rate', $course)
                            <div class="space-y-3 border-t border-slate-100 pt-5 dark:border-slate-700">
                                <p class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{ __('Rate this course (optional)') }}</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">{{ __('1 = lowest, 5 = highest.') }}</p>
                                <form method="POST" action="{{ route('courses.ratings.store', $course) }}" class="space-y-3">
                                    @csrf
                                    <label for="course-rating" class="sr-only">{{ __('Your rating') }}</label>
                                    <select id="course-rating" name="rating" class="block w-full rounded-xl border-slate-300 bg-white text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100" required>
                                        @for ($stars = 1; $stars <= 5; $stars++)
                                            <option value="{{ $stars }}" @selected((int) old('rating', $myCourseRating?->rating) === $stars)>{{ __(':count stars', ['count' => $stars]) }}</option>
                                        @endfor
                                    </select>
                                    <x-input-error :messages="$errors->get('rating')" />
                                    <button type="submit" class="btn-primary w-full justify-center text-sm">{{ __('Save rating') }}</button>
                                </form>
                                @if ($myCourseRating)
                                    <form method="POST" action="{{ route('courses.ratings.destroy', $course) }}" class="pt-1">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-secondary w-full justify-center text-sm">{{ __('Remove my rating') }}</button>
                                    </form>
                                @endif
                            </div>
                        @endcan
                    @else
                        @can('enroll', $course)
                            <a href="{{ route('courses.checkout.show', $course) }}" class="btn-primary w-full text-center">{{ __('Pay & enroll') }}</a>
                        @else
                            <p class="text-sm text-slate-600 dark:text-slate-400">{{ __('Enrollment is available to verified student accounts only.') }}</p>
                        @endcan
                    @endif
                @else
                    <a href="{{ route('login') }}" class="btn-primary w-full text-center">{{ __('Log in to enroll') }}</a>
                @endauth
            </div>
        </aside>
    </div>
@endsection
