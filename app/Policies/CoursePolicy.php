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

        $enrollment = $user->enrollments()->where('course_id', $course->id)->latest('enrolled_at')->first();

        if ($enrollment === null) {
            return true;
        }

        if ($enrollment->isTrial() && $enrollment->isTrialExpired()) {
            return true;
        }

        return false;
    }

    /**
     * Only admins may set or clear their 1–5 star rating for this course.
     */
    public function rate(User $user, Course $course): bool
    {
        return $user->isAdmin();
    }
}
