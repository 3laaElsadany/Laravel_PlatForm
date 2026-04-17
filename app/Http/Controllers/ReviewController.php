<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReviewRequest;
use App\Models\Course;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;

class ReviewController extends Controller
{
    public function store(StoreReviewRequest $request, Course $course): RedirectResponse
    {
        $this->authorize('create', Review::class);

        Review::create([
            'description' => $request->string('description')->toString(),
            'user_id' => $request->user()->id,
            'course_id' => $course->id,
        ]);

        return back()->with('status', 'review-posted');
    }
}
