<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseCheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_page_requires_auth_and_verification(): void
    {
        $course = Course::factory()->for(Category::factory())->create();

        $this->get(route('courses.checkout.show', $course))
            ->assertRedirect(route('login'));
    }

    public function test_student_can_pay_and_enroll(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->for(Category::factory())->create(['price' => 100, 'discount' => 0]);

        $this->actingAs($user)
            ->post(route('courses.checkout.store', $course), [
                'payment_method' => 'demo',
                'accept_terms' => '1',
            ])
            ->assertRedirect(route('my-courses'));

        $this->assertDatabaseHas('payments', [
            'user_id' => $user->id,
            'course_id' => $course->id,
            'status' => 'completed',
        ]);

        $this->assertDatabaseHas('enrollments', [
            'user_id' => $user->id,
            'course_id' => $course->id,
        ]);

        $payment = Payment::query()->where('user_id', $user->id)->first();
        $enrollment = Enrollment::query()->where('user_id', $user->id)->first();
        $this->assertNotNull($payment);
        $this->assertSame($payment->id, $enrollment->payment_id);
    }

    public function test_terms_must_be_accepted(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->for(Category::factory())->create();

        $this->actingAs($user)
            ->from(route('courses.checkout.show', $course))
            ->post(route('courses.checkout.store', $course), [
                'payment_method' => 'demo',
            ])
            ->assertSessionHasErrors(['accept_terms']);
    }
}
