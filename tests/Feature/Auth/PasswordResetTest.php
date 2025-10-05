<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test forgot password sends reset link.
     */
    public function test_forgot_password_sends_reset_link(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'test@example.com',
            'status' => 1,
        ]);

        $response = $this->postJson('/api/auth/forgot-password', [
            'email' => 'test@example.com',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }

    /**
     * Test forgot password with non-existent email.
     */
    public function test_forgot_password_fails_with_nonexistent_email(): void
    {
        $response = $this->postJson('/api/auth/forgot-password', [
            'email' => 'nonexistent@example.com',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test forgot password requires email.
     */
    public function test_forgot_password_requires_email(): void
    {
        $response = $this->postJson('/api/auth/forgot-password', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test forgot password requires valid email format.
     */
    public function test_forgot_password_requires_valid_email_format(): void
    {
        $response = $this->postJson('/api/auth/forgot-password', [
            'email' => 'invalid-email',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test password reset with valid token.
     */
    public function test_password_can_be_reset_with_valid_token(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('oldpassword'),
            'status' => 1,
        ]);

        $token = Password::createToken($user);

        $response = $this->postJson('/api/auth/reset-password', [
            'token' => $token,
            'email' => 'test@example.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        // Verify password was changed
        $user->refresh();
        $this->assertTrue(Hash::check('newpassword123', $user->password));
    }

    /**
     * Test password reset with invalid token.
     */
    public function test_password_reset_fails_with_invalid_token(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'status' => 1,
        ]);

        $response = $this->postJson('/api/auth/reset-password', [
            'token' => 'invalid-token',
            'email' => 'test@example.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);
    }

    /**
     * Test password reset requires all fields.
     */
    public function test_password_reset_requires_all_fields(): void
    {
        $response = $this->postJson('/api/auth/reset-password', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['token', 'email', 'password']);
    }

    /**
     * Test password reset requires password confirmation.
     */
    public function test_password_reset_requires_password_confirmation(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'status' => 1,
        ]);

        $token = Password::createToken($user);

        $response = $this->postJson('/api/auth/reset-password', [
            'token' => $token,
            'email' => 'test@example.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'differentpassword',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    /**
     * Test password reset requires minimum password length.
     */
    public function test_password_reset_requires_minimum_password_length(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'status' => 1,
        ]);

        $token = Password::createToken($user);

        $response = $this->postJson('/api/auth/reset-password', [
            'token' => $token,
            'email' => 'test@example.com',
            'password' => '12345', // Less than 6 characters
            'password_confirmation' => '12345',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    /**
     * Test password reset with non-existent email.
     */
    public function test_password_reset_fails_with_nonexistent_email(): void
    {
        $response = $this->postJson('/api/auth/reset-password', [
            'token' => 'some-token',
            'email' => 'nonexistent@example.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }
}
