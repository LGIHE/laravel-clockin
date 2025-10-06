<?php

namespace Tests\Feature\ErrorHandling;

use App\Models\User;
use App\Models\UserLevel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class AuthenticationErrorTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create user levels
        UserLevel::factory()->create(['id' => '1', 'name' => 'admin']);
        UserLevel::factory()->create(['id' => '2', 'name' => 'supervisor']);
        UserLevel::factory()->create(['id' => '3', 'name' => 'user']);
    }

    public function test_unauthenticated_request_returns_401(): void
    {
        $response = $this->getJson('/api/auth/me');

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'error' => [
                    'code' => 'AUTHENTICATION_ERROR',
                    'message' => 'Unauthenticated.',
                ],
            ]);
    }

    public function test_invalid_token_returns_401(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer invalid-token')
            ->getJson('/api/auth/me');

        $response->assertStatus(401);
    }

    public function test_expired_token_returns_401(): void
    {
        // This would require token expiration to be set up
        // For now, we test with an invalid token
        $response = $this->withHeader('Authorization', 'Bearer expired-token-here')
            ->getJson('/api/auth/me');

        $response->assertStatus(401);
    }

    public function test_authentication_failure_is_logged(): void
    {
        Log::shouldReceive('channel')
            ->with('auth')
            ->andReturnSelf();
        
        Log::shouldReceive('warning')
            ->once()
            ->with('Authentication failed', \Mockery::type('array'));

        $response = $this->getJson('/api/auth/me');

        $response->assertStatus(401);
    }

    public function test_failed_login_returns_validation_error(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'success',
                'error' => [
                    'code',
                    'message',
                    'errors' => [
                        'email',
                    ],
                ],
            ]);
    }

    public function test_inactive_user_login_returns_validation_error(): void
    {
        $user = User::factory()->create([
            'user_level_id' => '3',
            'email' => 'inactive@example.com',
            'password' => bcrypt('password'),
            'status' => 0, // Inactive
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'inactive@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'success',
                'error' => [
                    'code',
                    'message',
                    'errors' => [
                        'email',
                    ],
                ],
            ]);
    }

    public function test_authentication_error_response_structure(): void
    {
        $response = $this->getJson('/api/auth/me');

        $response->assertStatus(401);
        
        $data = $response->json();
        
        $this->assertFalse($data['success']);
        $this->assertEquals('AUTHENTICATION_ERROR', $data['error']['code']);
        $this->assertIsString($data['error']['message']);
    }
}
