<?php

namespace Tests\Feature\Security;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RateLimitingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test login rate limiting.
     */
    public function test_login_rate_limiting(): void
    {
        // Attempt to login 6 times (limit is 5 per minute)
        for ($i = 0; $i < 6; $i++) {
            $response = $this->postJson('/api/auth/login', [
                'email' => 'test@example.com',
                'password' => 'wrongpassword',
            ]);

            if ($i < 5) {
                // First 5 attempts should get through (even if they fail authentication)
                $this->assertNotEquals(429, $response->status());
            } else {
                // 6th attempt should be rate limited
                $response->assertStatus(429);
            }
        }
    }

    /**
     * Test API rate limiting for authenticated users.
     */
    public function test_api_rate_limiting_for_authenticated_users(): void
    {
        $user = User::factory()->create();

        // Make 61 requests (limit is 60 per minute)
        for ($i = 0; $i < 61; $i++) {
            $response = $this->actingAs($user, 'sanctum')
                ->getJson('/api/auth/me');

            if ($i < 60) {
                // First 60 requests should succeed
                $this->assertNotEquals(429, $response->status());
            } else {
                // 61st request should be rate limited
                $response->assertStatus(429);
            }
        }
    }

    /**
     * Test forgot password rate limiting.
     */
    public function test_forgot_password_rate_limiting(): void
    {
        // Attempt to request password reset 6 times (limit is 5 per minute)
        for ($i = 0; $i < 6; $i++) {
            $response = $this->postJson('/api/auth/forgot-password', [
                'email' => 'test@example.com',
            ]);

            if ($i < 5) {
                // First 5 attempts should get through
                $this->assertNotEquals(429, $response->status());
            } else {
                // 6th attempt should be rate limited
                $response->assertStatus(429);
            }
        }
    }

    /**
     * Test rate limiting is per IP address.
     */
    public function test_rate_limiting_is_per_ip_address(): void
    {
        // Make 5 requests from first IP
        for ($i = 0; $i < 5; $i++) {
            $response = $this->postJson('/api/auth/login', [
                'email' => 'test@example.com',
                'password' => 'wrongpassword',
            ]);
            $this->assertNotEquals(429, $response->status());
        }

        // Make a request from a different IP (simulated by changing the IP)
        $response = $this->withServerVariables(['REMOTE_ADDR' => '192.168.1.2'])
            ->postJson('/api/auth/login', [
                'email' => 'test@example.com',
                'password' => 'wrongpassword',
            ]);

        // Should not be rate limited because it's from a different IP
        $this->assertNotEquals(429, $response->status());
    }
}
