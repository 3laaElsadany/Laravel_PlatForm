<?php

namespace App\Services;

use App\Models\Course;

class CourseLearnerRatingService
{
    /**
     * Persist the aggregate learner rating on the course row: average of all
     * `course_ratings`, or the catalog baseline when there are none.
     */
    public function syncAggregateRating(Course $course): void
    {
        $course->refresh();

        $count = $course->courseRatings()->count();

        if ($count === 0) {
            $baseline = $course->catalog_rate ?? (float) $course->getRawOriginal('rate');

            $course->forceFill(['rate' => round($baseline, 1)])->saveQuietly();

            return;
        }

        $avg = (float) $course->courseRatings()->avg('rating');

        $course->forceFill(['rate' => round($avg, 1)])->saveQuietly();
    }
}
