<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;

class CoursePolicy
{
    public function enroll(User $user, Course $course): bool
    {
        if (! $user->isVerified) {
            return false;
        }

        if ($user->role !== User::ROLE_STUDENT) {
            return false;
        }

        return ! $user->enrollments()->where('course_id', $course->id)->exists();
    }

    /**
     * Verified subscribers may set or clear their 1–5 star rating for this course.
     */
    public function rate(User $user, Course $course): bool
    {
        if (! $user->isVerified) {
            return false;
        }

        return $user->enrollments()->where('course_id', $course->id)->exists();
    }
}
