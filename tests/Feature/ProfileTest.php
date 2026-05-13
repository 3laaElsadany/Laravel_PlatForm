<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertOk();
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'fullname' => 'Updated Name',
                'email' => 'updated@example.com',
                'phone' => '+10000000000',
                'country' => 'US',
                'language' => 'en',
                'gender' => 'other',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('verification.otp.show', absolute: false));

        $user->refresh();

        $this->assertSame('Updated Name', $user->fullname);
        $this->assertSame('updated@example.com', $user->email);
        $this->assertFalse($user->isVerified);
    }

    public function test_is_verified_unchanged_when_email_is_unchanged(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'fullname' => 'Updated Name',
                'email' => $user->email,
                'phone' => $user->phone,
                'country' => $user->country,
                'language' => $user->language,
                'gender' => $user->gender,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $this->assertTrue($user->refresh()->isVerified);
    }

    public function test_user_can_delete_their_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete('/profile', [
                'password' => 'password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/');

        $this->assertGuest();
        $this->assertNull($user->fresh());
    }

    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->delete('/profile', [
                'password' => 'wrong-password',
            ]);

        $response
            ->assertSessionHasErrorsIn('userDeletion', 'password')
            ->assertRedirect('/profile');

        $this->assertNotNull($user->fresh());
    }
}
