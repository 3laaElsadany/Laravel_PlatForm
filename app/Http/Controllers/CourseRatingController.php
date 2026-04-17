<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCourseRatingRequest;
use App\Models\Course;
use App\Models\CourseRating;
use App\Services\CourseLearnerRatingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CourseRatingController extends Controller
{
    public function __construct(
        private readonly CourseLearnerRatingService $learnerRatingService,
    ) {}

    public function store(StoreCourseRatingRequest $request, Course $course): RedirectResponse
    {
        $this->authorize('rate', $course);

        CourseRating::query()->updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'course_id' => $course->id,
            ],
            [
                'rating' => (int) $request->validated('rating'),
            ],
        );

        $this->learnerRatingService->syncAggregateRating($course);

        return back()->with('status', 'rating-saved');
    }

    public function destroy(Request $request, Course $course): RedirectResponse
    {
        $this->authorize('rate', $course);

        $rating = CourseRating::query()
            ->where('course_id', $course->id)
            ->where('user_id', $request->user()->id)
            ->first();

        if ($rating !== null) {
            $this->authorize('delete', $rating);
            $rating->delete();
            $this->learnerRatingService->syncAggregateRating($course);
        }

        return back()->with('status', 'rating-removed');
    }
}
