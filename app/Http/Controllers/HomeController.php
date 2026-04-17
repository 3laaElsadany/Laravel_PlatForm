<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $categories = Category::query()
            ->with(['courses' => fn ($q) => $q->latest('created_at')->limit(4)])
            ->orderBy('name')
            ->get();

        return view('home', compact('categories'));
    }
}
