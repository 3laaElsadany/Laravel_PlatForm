<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class OtpVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_otp_screen_can_be_rendered_for_unverified_user(): void
    {
        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user)->get('/verify-email-otp');

        $response->assertStatus(200);
    }

    public function test_user_can_verify_with_correct_otp(): void
    {
        $user = User::factory()->unverified()->create();

        $otp = '123456';
        Cache::put('email_otp:'.mb_strtolower($user->email), $otp, 600);

        $response = $this->actingAs($user)->post('/verify-email-otp', [
            'otp' => $otp,
        ]);

        $this->assertTrue($user->fresh()->isVerified);
        $response->assertRedirect(route('home', absolute: false));
    }

    public function test_wrong_otp_is_rejected(): void
    {
        $user = User::factory()->unverified()->create();

        Cache::put('email_otp:'.mb_strtolower($user->email), '111111', 600);

        $response = $this->actingAs($user)->from('/verify-email-otp')->post('/verify-email-otp', [
            'otp' => '999999',
        ]);

        $this->assertFalse($user->fresh()->isVerified);
        $response->assertSessionHasErrors('otp');
    }
}
