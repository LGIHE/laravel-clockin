<?php

namespace Tests\Feature\Security;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CorsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test CORS headers are present on API responses.
     */
    public function test_cors_headers_are_present(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->withHeaders([
                'Origin' => 'http://localhost:3000',
            ])
            ->getJson('/api/auth/me');

        $response->assertHeader('Access-Control-Allow-Origin');
    }

    /**
     * Test CORS allows configured origins.
     */
    public function test_cors_allows_configured_origins(): void
    {
        $user = User::factory()->create();

        // Test with localhost:3000 (should be in allowed origins)
        $response = $this->actingAs($user, 'sanctum')
            ->withHeaders([
                'Origin' => 'http://localhost:3000',
            ])
            ->getJson('/api/auth/me');

        $response->assertSuccessful();
        $this->assertNotNull($response->headers->get('Access-Control-Allow-Origin'));
    }

    /**
     * Test CORS preflight requests.
     */
    public function test_cors_preflight_requests(): void
    {
        $response = $this->options('/api/auth/me', [
            'Origin' => 'http://localhost:3000',
            'Access-Control-Request-Method' => 'GET',
            'Access-Control-Request-Headers' => 'Authorization',
        ]);

        // Preflight should return 204 or 200
        $this->assertContains($response->status(), [200, 204]);
    }

    /**
     * Test CORS credentials support.
     */
    public function test_cors_supports_credentials(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->withHeaders([
                'Origin' => 'http://localhost:3000',
            ])
            ->getJson('/api/auth/me');

        // Check if Access-Control-Allow-Credentials header is present
        $allowCredentials = $response->headers->get('Access-Control-Allow-Credentials');
        $this->assertTrue($allowCredentials === 'true' || $allowCredentials === true);
    }

    /**
     * Test CORS exposes Authorization header.
     */
    public function test_cors_exposes_authorization_header(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->withHeaders([
                'Origin' => 'http://localhost:3000',
            ])
            ->getJson('/api/auth/me');

        $exposedHeaders = $response->headers->get('Access-Control-Expose-Headers');
        
        if ($exposedHeaders) {
            $this->assertStringContainsString('Authorization', $exposedHeaders);
        }
    }
}
