<?php

namespace Tests\Feature\Auth;

use App\Mail\OtpVerificationMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register_and_are_sent_to_otp_screen(): void
    {
        Mail::fake();

        $response = $this->post('/register', [
            'fullname' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => User::ROLE_STUDENT,
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('verification.otp.show', absolute: false));

        Mail::assertSent(OtpVerificationMail::class);

        $this->assertSame(User::ROLE_STUDENT, auth()->user()->role);
    }

    public function test_users_can_register_as_teacher(): void
    {
        Mail::fake();

        $response = $this->post('/register', [
            'fullname' => 'Teacher User',
            'email' => 'teacher@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => User::ROLE_TEACHER,
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('verification.otp.show', absolute: false));
        $this->assertSame(User::ROLE_TEACHER, auth()->user()->role);
    }
}
