<?php

namespace Tests\Feature\ErrorHandling;

use App\Models\User;
use App\Models\UserLevel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotFoundErrorTest extends TestCase
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

    public function test_not_found_route_returns_404(): void
    {
        $response = $this->getJson('/api/nonexistent-route');

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'error' => [
                    'code' => 'NOT_FOUND',
                ],
            ]);
    }

    public function test_not_found_resource_returns_404(): void
    {
        $admin = User::factory()->create(['user_level_id' => '1']);

        $response = $this->actingAs($admin, 'sanctum')
            ->getJson('/api/users/nonexistent-id');

        $response->assertStatus(404);
    }

    public function test_model_not_found_returns_404(): void
    {
        $admin = User::factory()->create(['user_level_id' => '1']);

        $response = $this->actingAs($admin, 'sanctum')
            ->getJson('/api/departments/99999999-9999-9999-9999-999999999999');

        $response->assertStatus(404);
    }

    public function test_not_found_error_response_structure(): void
    {
        $response = $this->getJson('/api/nonexistent-endpoint');

        $response->assertStatus(404);
        
        $data = $response->json();
        
        $this->assertFalse($data['success']);
        $this->assertArrayHasKey('error', $data);
        $this->assertArrayHasKey('code', $data['error']);
        $this->assertArrayHasKey('message', $data['error']);
    }

    public function test_not_found_error_has_proper_code(): void
    {
        $response = $this->getJson('/api/invalid-route');

        $response->assertStatus(404);
        
        $data = $response->json();
        
        $this->assertEquals('NOT_FOUND', $data['error']['code']);
    }

    public function test_not_found_error_message_is_descriptive(): void
    {
        $response = $this->getJson('/api/missing-endpoint');

        $response->assertStatus(404);
        
        $data = $response->json();
        
        $this->assertIsString($data['error']['message']);
        $this->assertNotEmpty($data['error']['message']);
    }

    public function test_authenticated_user_gets_404_for_missing_resource(): void
    {
        $user = User::factory()->create(['user_level_id' => '3']);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/attendance/nonexistent-id');

        $response->assertStatus(404);
    }
}
