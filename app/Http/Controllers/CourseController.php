<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function show(Course $course): View
    {
        $course->load([
            'category',
            'reviews.user',
            'courseRatings' => fn ($q) => $q->with('user')->latest('updated_at'),
        ]);

        $user = auth()->user();
        $enrolled = $user !== null && $user->enrollments()->where('course_id', $course->id)->exists();

        $learnerRatings = $course->courseRatings;
        $learnerRatingsCount = $learnerRatings->count();
        $displayRating = $learnerRatingsCount > 0
            ? round((float) $learnerRatings->avg('rating'), 1)
            : (float) $course->rate;
        $myCourseRating = $user !== null
            ? $learnerRatings->firstWhere('user_id', $user->id)
            : null;

        return view('courses.show', [
            'course' => $course,
            'enrolled' => $enrolled,
            'displayRating' => $displayRating,
            'learnerRatingsCount' => $learnerRatingsCount,
            'myCourseRating' => $myCourseRating,
        ]);
    }
}
