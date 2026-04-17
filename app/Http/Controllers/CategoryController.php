<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function show(Category $category): View
    {
        $courses = $category->courses()
            ->withAvg('courseRatings', 'rating')
            ->withCount('courseRatings')
            ->latest('created_at')
            ->paginate(9);

        return view('categories.show', compact('category', 'courses'));
    }
}
