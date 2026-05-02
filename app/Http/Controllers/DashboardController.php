<?php

namespace App\Http\Controllers;

use App\Models\DiscountCode;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View|RedirectResponse
    {
        $user = auth()->user();
        abort_unless($user instanceof User, 403);

        if ($user->role === User::ROLE_STUDENT) {
            return redirect()->route('my-courses');
        }

        if ($user->isTeacher()) {
            $courseIds = $user->courses()->pluck('id');

            $teacherStats = [
                'courses' => $user->courses()->count(),
                'discount_codes' => DiscountCode::query()->whereIn('course_id', $courseIds)->count(),
                'reviews' => Review::query()->whereIn('course_id', $courseIds)->count(),
                'enrollments' => Enrollment::query()->whereIn('course_id', $courseIds)->count(),
                'purchases' => Payment::query()
                    ->whereIn('course_id', $courseIds)
                    ->where('status', 'completed')
                    ->count(),
                'revenue' => (float) Payment::query()
                    ->whereIn('course_id', $courseIds)
                    ->where('status', 'completed')
                    ->sum('amount'),
            ];

            return view('dashboard', compact('teacherStats'));
        }

        return view('dashboard', ['teacherStats' => null]);
    }
}
