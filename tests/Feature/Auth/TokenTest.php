<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TokenTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test token refresh functionality.
     */
    public function test_user_can_refresh_token(): void
    {
        $user = User::factory()->create(['status' => 1]);
        
        $oldToken = $user->createToken('auth-token')->plainTextToken;

        // Count tokens before refresh
        $this->assertCount(1, $user->tokens);

        $response = $this->withHeader('Authorization', 'Bearer ' . $oldToken)
            ->postJson('/api/auth/refresh');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Token refreshed successfully',
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'token',
                ],
            ]);

        $newToken = $response->json('data.token');
        $this->assertNotNull($newToken);
        $this->assertNotEquals($oldToken, $newToken);

        // Verify old token was deleted and new token was created
        $user->refresh();
        $this->assertCount(1, $user->tokens);

        // New token should work
        $meResponse = $this->withHeader('Authorization', 'Bearer ' . $newToken)
            ->getJson('/api/auth/me');
        
        $meResponse->assertStatus(200);
    }

    /**
     * Test token refresh requires authentication.
     */
    public function test_token_refresh_requires_authentication(): void
    {
        $response = $this->postJson('/api/auth/refresh');

        $response->assertStatus(401);
    }

    /**
     * Test authenticated user can access protected routes.
     */
    public function test_authenticated_user_can_access_protected_routes(): void
    {
        $user = User::factory()->create(['status' => 1]);
        
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/auth/me');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'success',
                'data' => [
                    'user' => [
                        'id',
                        'name',
                        'email',
                    ],
                ],
            ]);
    }

    /**
     * Test unauthenticated user cannot access protected routes.
     */
    public function test_unauthenticated_user_cannot_access_protected_routes(): void
    {
        $response = $this->getJson('/api/auth/me');

        $response->assertStatus(401);
    }

    /**
     * Test invalid token is rejected.
     */
    public function test_invalid_token_is_rejected(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer invalid-token')
            ->getJson('/api/auth/me');

        $response->assertStatus(401);
    }

    /**
     * Test me endpoint returns user with relationships.
     */
    public function test_me_endpoint_returns_user_with_relationships(): void
    {
        $user = User::factory()->create(['status' => 1]);
        
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/auth/me');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'user' => [
                        'id',
                        'name',
                        'email',
                        'user_level',
                        'department',
                        'designation',
                    ],
                ],
            ]);
    }
}
