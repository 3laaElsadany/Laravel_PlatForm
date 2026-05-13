<?php

namespace App\Support;

use App\Models\Course;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

final class FilamentInstructor
{
    public static function instructorId(): ?int
    {
        $user = auth()->user();

        if (! $user instanceof User || $user->role !== User::ROLE_TEACHER) {
            return null;
        }

        return $user->id;
    }

    public static function limitCoursesQuery(Builder $query): Builder
    {
        $id = self::instructorId();
        if ($id === null) {
            return $query;
        }

        if ($query->getModel() === null) {
            $query = Course::query();
        }

        return $query->where('courses.instructor_id', $id);
    }

    public static function limitPaymentsForInstructor(Builder $query): Builder
    {
        $id = self::instructorId();
        if ($id === null) {
            return $query;
        }

        $table = $query->getModel()?->getTable() ?? (new Payment())->getTable();

        return $query->whereIn(
            $table . '.course_id',
            Course::query()->where('instructor_id', $id)->select('id')
        );
    }

    public static function scopeToInstructorCourseIds(Builder $query, int $instructorId): Builder
    {
        $table = $query->getModel()?->getTable() ?? (new Course())->getTable();

        return $query->whereIn(
            $table . '.course_id',
            Course::query()->where('instructor_id', $instructorId)->select('id')
        );
    }
}
