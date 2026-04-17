<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Review>
 */
class ReviewFactory extends Factory
{
    protected $model = Review::class;

    public function definition(): array
    {
        return [
            'description' => fake()->paragraph(),
            'user_id' => User::factory(),
            'course_id' => Course::factory(),
            'created_at' => now(),
        ];
    }
}
