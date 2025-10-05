<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test successful logout.
     */
    public function test_user_can_logout(): void
    {
        $user = User::factory()->create(['status' => 1]);
        
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/auth/logout');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Logout successful',
            ]);

        // Verify all tokens are deleted
        $this->assertCount(0, $user->tokens);
    }

    /**
     * Test logout requires authentication.
     */
    public function test_logout_requires_authentication(): void
    {
        $response = $this->postJson('/api/auth/logout');

        $response->assertStatus(401);
    }

    /**
     * Test logout revokes all user tokens.
     */
    public function test_logout_revokes_all_tokens(): void
    {
        $user = User::factory()->create(['status' => 1]);
        
        // Create multiple tokens
        $token1 = $user->createToken('token1')->plainTextToken;
        $token2 = $user->createToken('token2')->plainTextToken;

        $user->refresh();
        $this->assertCount(2, $user->tokens);

        // Logout using first token
        $response = $this->withHeader('Authorization', 'Bearer ' . $token1)
            ->postJson('/api/auth/logout');

        $response->assertStatus(200);

        // Verify all tokens are deleted
        $user->refresh();
        $this->assertCount(0, $user->tokens);
    }
}
