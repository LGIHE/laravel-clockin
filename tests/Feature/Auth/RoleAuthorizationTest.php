<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Models\UserLevel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RoleAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create user levels for testing
        UserLevel::factory()->create([
            'id' => '550e8400-e29b-41d4-a716-446655440001',
            'name' => 'admin',
        ]);

        UserLevel::factory()->create([
            'id' => '550e8400-e29b-41d4-a716-446655440002',
            'name' => 'supervisor',
        ]);

        UserLevel::factory()->create([
            'id' => '550e8400-e29b-41d4-a716-446655440003',
            'name' => 'user',
        ]);
    }

    /**
     * Test role middleware allows access for authorized role.
     */
    public function test_role_middleware_allows_authorized_user(): void
    {
        $user = User::factory()->create([
            'status' => 1,
            'user_level_id' => '550e8400-e29b-41d4-a716-446655440001', // admin
        ]);

        Sanctum::actingAs($user);

        // Create a test route with role middleware
        \Route::middleware(['auth:sanctum', 'role:admin'])->get('/test-admin-route', function () {
            return response()->json(['success' => true]);
        });

        $response = $this->getJson('/test-admin-route');

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    /**
     * Test role middleware denies access for unauthorized role.
     */
    public function test_role_middleware_denies_unauthorized_user(): void
    {
        $user = User::factory()->create([
            'status' => 1,
            'user_level_id' => '550e8400-e29b-41d4-a716-446655440003', // user
        ]);

        Sanctum::actingAs($user);

        // Create a test route with role middleware
        \Route::middleware(['auth:sanctum', 'role:admin'])->get('/test-admin-only', function () {
            return response()->json(['success' => true]);
        });

        $response = $this->getJson('/test-admin-only');

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'Unauthorized action. Insufficient permissions.',
            ]);
    }

    /**
     * Test role middleware allows multiple roles.
     */
    public function test_role_middleware_allows_multiple_roles(): void
    {
        $supervisor = User::factory()->create([
            'status' => 1,
            'user_level_id' => '550e8400-e29b-41d4-a716-446655440002', // supervisor
        ]);

        Sanctum::actingAs($supervisor);

        // Create a test route with multiple roles
        \Route::middleware(['auth:sanctum', 'role:admin,supervisor'])->get('/test-multi-role', function () {
            return response()->json(['success' => true]);
        });

        $response = $this->getJson('/test-multi-role');

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    /**
     * Test role middleware requires authentication.
     */
    public function test_role_middleware_requires_authentication(): void
    {
        // Create a test route with role middleware
        \Route::middleware(['auth:sanctum', 'role:admin'])->get('/test-auth-required', function () {
            return response()->json(['success' => true]);
        });

        $response = $this->getJson('/test-auth-required');

        $response->assertStatus(401);
    }

    /**
     * Test role middleware is case insensitive.
     */
    public function test_role_middleware_is_case_insensitive(): void
    {
        $admin = User::factory()->create([
            'status' => 1,
            'user_level_id' => '550e8400-e29b-41d4-a716-446655440001', // admin
        ]);

        Sanctum::actingAs($admin);

        // Create a test route with uppercase role
        \Route::middleware(['auth:sanctum', 'role:ADMIN'])->get('/test-case-insensitive', function () {
            return response()->json(['success' => true]);
        });

        $response = $this->getJson('/test-case-insensitive');

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }
}
