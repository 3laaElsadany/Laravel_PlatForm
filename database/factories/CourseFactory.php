<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Course>
 */
class CourseFactory extends Factory
{
    protected $model = Course::class;

    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'description' => fake()->paragraphs(3, true),
            'price' => fake()->randomFloat(2, 19, 299),
            'discount' => fake()->randomElement([0, 10, 20, 25]),
            'category_id' => Category::factory(),
            'rate' => fake()->randomFloat(1, 3, 5),
            'course_includes' => [
                'Lifetime access',
                'Certificate of completion',
                'Downloadable resources',
            ],
            'video_img_link' => null,
            'video_link' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
            'img_link' => 'https://picsum.photos/seed/'.fake()->uuid().'/800/450',
            'created_at' => now(),
        ];
    }
}
