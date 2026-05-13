<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function __invoke(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));
        $escaped = $this->escapeLikeWildcards($q);

        $categoryFilter = $this->resolveCategoryFilter($request);

        $searchCoursesInCategory = $categoryFilter !== null;

        if ($q === '') {
            return view('search', [
                'q' => $q,
                'categoryFilter' => $categoryFilter,
                'searchCoursesInCategory' => $searchCoursesInCategory,
                'categories' => collect(),
                'courses' => Course::query()->whereRaw('0 = 1')->paginate(12),
            ]);
        }

        if ($searchCoursesInCategory) {
            $courses = Course::query()
                ->with('category')
                ->withAvg('courseRatings', 'rating')
                ->withCount('courseRatings')
                ->where('category_id', $categoryFilter->id)
                ->where(function ($w) use ($escaped): void {
                    $w->where('title', 'like', '%'.$escaped.'%')
                        ->orWhere('description', 'like', '%'.$escaped.'%');
                })
                ->latest('created_at')
                ->paginate(12)
                ->withQueryString();

            return view('search', [
                'q' => $q,
                'categoryFilter' => $categoryFilter,
                'searchCoursesInCategory' => true,
                'categories' => collect(),
                'courses' => $courses,
            ]);
        }

        $categories = Category::query()
            ->where(function ($w) use ($escaped): void {
                $w->where('name', 'like', '%'.$escaped.'%')
                    ->orWhere('description', 'like', '%'.$escaped.'%');
            })
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('search', [
            'q' => $q,
            'categoryFilter' => null,
            'searchCoursesInCategory' => false,
            'categories' => $categories,
            'courses' => Course::query()->whereRaw('0 = 1')->paginate(12),
        ]);
    }

    private function resolveCategoryFilter(Request $request): ?Category
    {
        $raw = $request->query('category');
        if ($raw === null || $raw === '') {
            return null;
        }

        $id = (int) $raw;
        if ($id < 1) {
            return null;
        }

        return Category::query()->find($id);
    }

    private function escapeLikeWildcards(string $value): string
    {
        return str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], $value);
    }
}
