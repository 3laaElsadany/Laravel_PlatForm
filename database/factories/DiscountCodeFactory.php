<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\DiscountCode;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<DiscountCode>
 */
class DiscountCodeFactory extends Factory
{
    protected $model = DiscountCode::class;

    public function definition(): array
    {
        return [
            'course_id' => Course::factory(),
            'code' => strtoupper(Str::random(6)),
            'type' => 'percent',
            'value' => fake()->randomElement([5, 10, 15]),
            'is_active' => true,
            'expires_at' => now()->addMonths(3),
        ];
    }
}
