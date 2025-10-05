<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Models\UserLevel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create user level for testing
        UserLevel::factory()->create([
            'id' => '550e8400-e29b-41d4-a716-446655440000',
            'name' => 'user',
        ]);
    }

    /**
     * Test successful login with valid credentials.
     */
    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
            'status' => 1,
            'user_level_id' => '550e8400-e29b-41d4-a716-446655440000',
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Login successful',
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'user' => [
                        'id',
                        'name',
                        'email',
                        'status',
                    ],
                    'token',
                ],
            ]);

        $this->assertNotNull($response->json('data.token'));
    }

    /**
     * Test login failure with invalid credentials.
     */
    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
            'status' => 1,
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Login failed',
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'errors',
            ]);
    }

    /**
     * Test login failure with non-existent email.
     */
    public function test_user_cannot_login_with_nonexistent_email(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Login failed',
            ]);
    }

    /**
     * Test inactive account rejection.
     */
    public function test_inactive_user_cannot_login(): void
    {
        $user = User::factory()->create([
            'email' => 'inactive@example.com',
            'password' => bcrypt('password123'),
            'status' => 0, // Inactive
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'inactive@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Login failed',
            ])
            ->assertJsonPath('errors.email.0', 'Your account is inactive. Please contact the administrator.');
    }

    /**
     * Test validation errors for missing fields.
     */
    public function test_login_requires_email_and_password(): void
    {
        $response = $this->postJson('/api/auth/login', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);
    }

    /**
     * Test validation error for invalid email format.
     */
    public function test_login_requires_valid_email_format(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'invalid-email',
            'password' => 'password123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test validation error for short password.
     */
    public function test_login_requires_minimum_password_length(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => '12345', // Less than 6 characters
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    /**
     * Test token generation on successful login.
     */
    public function test_login_generates_sanctum_token(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
            'status' => 1,
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200);
        
        $token = $response->json('data.token');
        $this->assertNotNull($token);
        
        // Verify token works for authenticated requests
        $meResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/auth/me');
        
        $meResponse->assertStatus(200);
    }

    /**
     * Test that old tokens are revoked on new login.
     */
    public function test_login_revokes_existing_tokens(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
            'status' => 1,
            'user_level_id' => '550e8400-e29b-41d4-a716-446655440000',
        ]);

        // First login
        $firstResponse = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $firstToken = $firstResponse->json('data.token');
        $this->assertNotNull($firstToken);

        // Verify user has one token
        $user->refresh();
        $this->assertCount(1, $user->tokens);

        // Second login
        $secondResponse = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $secondToken = $secondResponse->json('data.token');
        $this->assertNotNull($secondToken);
        $this->assertNotEquals($firstToken, $secondToken);

        // Verify old tokens were revoked and only new token exists
        $user->refresh();
        $this->assertCount(1, $user->tokens);

        // Second token should work
        $response = $this->withHeader('Authorization', 'Bearer ' . $secondToken)
            ->getJson('/api/auth/me');
        
        $response->assertStatus(200);
    }
}
