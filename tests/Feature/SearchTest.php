<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_page_loads_without_query(): void
    {
        $this->get(route('search'))
            ->assertOk()
            ->assertSee(__('Type a keyword above to see results.'));
    }

    public function test_search_without_category_finds_categories_only(): void
    {
        $category = Category::factory()->create([
            'name' => 'UniqueCategorySearchXYZ',
        ]);
        $otherCategory = Category::factory()->create();
        $course = Course::factory()->for($otherCategory)->create([
            'title' => 'UniqueCourseTitleOnlyInCourse',
        ]);

        $this->get(route('search', ['q' => 'UniqueCategorySearch']))
            ->assertOk()
            ->assertSee($category->name, false)
            ->assertDontSee($course->title, false);
    }

    public function test_search_with_category_param_finds_courses_in_that_category(): void
    {
        $categoryA = Category::factory()->create();
        $categoryB = Category::factory()->create();
        $courseInA = Course::factory()->for($categoryA)->create([
            'title' => 'AlphaSQLCourseUnique',
        ]);
        $courseInB = Course::factory()->for($categoryB)->create([
            'title' => 'AlphaSQLCourseOtherCat',
        ]);

        $this->get(route('search', [
            'q' => 'AlphaSQLCourse',
            'category' => $categoryA->id,
        ]))
            ->assertOk()
            ->assertSee($courseInA->title, false)
            ->assertDontSee($courseInB->title, false);
    }
}
