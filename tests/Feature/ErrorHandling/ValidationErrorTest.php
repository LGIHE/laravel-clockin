<?php

namespace Tests\Feature\ErrorHandling;

use App\Models\User;
use App\Models\UserLevel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ValidationErrorTest extends TestCase
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

    public function test_login_validation_error_response(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'invalid-email',
            'password' => '',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'error' => [
                    'code' => 'VALIDATION_ERROR',
                    'message' => 'The given data was invalid.',
                ],
            ])
            ->assertJsonStructure([
                'success',
                'error' => [
                    'code',
                    'message',
                    'errors' => [
                        'email',
                        'password',
                    ],
                ],
            ]);
    }

    public function test_user_creation_validation_error_response(): void
    {
        $admin = User::factory()->create(['user_level_id' => '1']);

        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/users', [
                'name' => '',
                'email' => 'invalid-email',
                'password' => '123', // Too short
            ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'error' => [
                    'code' => 'VALIDATION_ERROR',
                ],
            ])
            ->assertJsonStructure([
                'success',
                'error' => [
                    'code',
                    'message',
                    'errors',
                ],
            ]);
    }

    public function test_validation_errors_are_formatted_correctly(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => '',
            'password' => '',
        ]);

        $response->assertStatus(422);
        
        $data = $response->json();
        
        $this->assertFalse($data['success']);
        $this->assertEquals('VALIDATION_ERROR', $data['error']['code']);
        $this->assertIsArray($data['error']['errors']);
        $this->assertArrayHasKey('email', $data['error']['errors']);
        $this->assertArrayHasKey('password', $data['error']['errors']);
    }

    public function test_validation_error_messages_are_clear(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'not-an-email',
            'password' => '',
        ]);

        $response->assertStatus(422);
        
        $data = $response->json();
        
        $this->assertIsArray($data['error']['errors']['email']);
        $this->assertIsArray($data['error']['errors']['password']);
    }
}
