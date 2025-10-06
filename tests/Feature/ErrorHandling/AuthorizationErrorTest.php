<?php

namespace Tests\Feature\ErrorHandling;

use App\Models\User;
use App\Models\UserLevel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class AuthorizationErrorTest extends TestCase
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

    public function test_unauthorized_access_returns_403(): void
    {
        $user = User::factory()->create(['user_level_id' => '3']); // Regular user

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/users');

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'Unauthorized action. Insufficient permissions.',
            ]);
    }

    public function test_user_cannot_access_admin_routes(): void
    {
        $user = User::factory()->create(['user_level_id' => '3']); // Regular user

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/users', [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => 'password123',
            ]);

        $response->assertStatus(403);
    }

    public function test_supervisor_cannot_access_admin_only_routes(): void
    {
        $supervisor = User::factory()->create(['user_level_id' => '2']); // Supervisor

        $response = $this->actingAs($supervisor, 'sanctum')
            ->getJson('/api/users');

        $response->assertStatus(403);
    }

    public function test_authorization_failure_is_logged(): void
    {
        $user = User::factory()->create(['user_level_id' => '3']);

        Log::shouldReceive('channel')
            ->with('security')
            ->andReturnSelf();
        
        Log::shouldReceive('warning')
            ->once()
            ->with('Unauthorized access attempt', \Mockery::type('array'));

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/users');

        $response->assertStatus(403);
    }

    public function test_authorization_error_response_structure(): void
    {
        $user = User::factory()->create(['user_level_id' => '3']);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/users');

        $response->assertStatus(403);
        
        $data = $response->json();
        
        $this->assertFalse($data['success']);
        $this->assertIsString($data['message']);
        $this->assertStringContainsString('Unauthorized', $data['message']);
    }

    public function test_admin_can_access_admin_routes(): void
    {
        $admin = User::factory()->create(['user_level_id' => '1']);

        $response = $this->actingAs($admin, 'sanctum')
            ->getJson('/api/users');

        $response->assertStatus(200);
    }

    public function test_authorization_error_includes_proper_message(): void
    {
        $user = User::factory()->create(['user_level_id' => '3']);

        $response = $this->actingAs($user, 'sanctum')
            ->deleteJson('/api/departments/some-id');

        $response->assertStatus(403)
            ->assertJsonFragment([
                'success' => false,
            ]);
    }
}
