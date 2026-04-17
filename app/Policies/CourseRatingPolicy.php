<?php

namespace App\Policies;

use App\Models\CourseRating;
use App\Models\User;

class CourseRatingPolicy
{
    public function delete(User $user, CourseRating $courseRating): bool
    {
        return $user->id === $courseRating->user_id || $user->isAdmin();
    }
}
