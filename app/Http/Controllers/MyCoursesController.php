<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class MyCoursesController extends Controller
{
    public function __invoke(): View
    {
        $enrollments = auth()->user()
            ->enrollments()
            ->with(['course.category', 'payment'])
            ->latest('enrolled_at')
            ->paginate(12);

        return view('my-courses', compact('enrollments'));
    }
}
