<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'course_id' => Course::factory(),
            'discount_code_id' => null,
            'amount' => fake()->randomFloat(2, 10, 150),
            'currency' => 'USD',
            'status' => 'completed',
            'gateway' => 'demo',
            'reference' => 'PAY-'.Str::upper(Str::random(10)),
            'paid_at' => now(),
        ];
    }
}
