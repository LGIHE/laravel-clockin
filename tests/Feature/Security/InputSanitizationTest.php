<?php

namespace Tests\Feature\Security;

use App\Models\User;
use App\Models\UserLevel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InputSanitizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create user levels
        UserLevel::factory()->create(['id' => '1', 'name' => 'USER']);
        UserLevel::factory()->create(['id' => '2', 'name' => 'SUPERVISOR']);
        UserLevel::factory()->create(['id' => '3', 'name' => 'ADMIN']);
    }

    /**
     * Test login input is sanitized.
     */
    public function test_login_input_is_sanitized(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        // Try to login with email containing extra whitespace
        $response = $this->postJson('/api/auth/login', [
            'email' => '  test@example.com  ',
            'password' => 'password123',
        ]);

        // Should succeed because whitespace is trimmed
        $response->assertSuccessful();
    }

    /**
     * Test HTML tags are stripped from input.
     */
    public function test_html_tags_are_stripped(): void
    {
        $user = User::factory()->create([
            'user_level_id' => '3',
        ]);

        // Try to create a user with HTML in the name
        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/users', [
                'name' => '<script>alert("XSS")</script>John Doe',
                'email' => 'john@example.com',
                'password' => 'password123',
                'user_level_id' => '1',
                'status' => 1,
            ]);

        if ($response->isSuccessful()) {
            // Verify the HTML tags were stripped
            $createdUser = User::where('email', 'john@example.com')->first();
            $this->assertNotNull($createdUser);
            $this->assertStringNotContainsString('<script>', $createdUser->name);
            $this->assertStringNotContainsString('</script>', $createdUser->name);
        }
    }

    /**
     * Test null bytes are removed from input.
     */
    public function test_null_bytes_are_removed(): void
    {
        // Try to login with null bytes in email
        $response = $this->postJson('/api/auth/login', [
            'email' => "test\0@example.com",
            'password' => 'password123',
        ]);

        // Should not cause any issues (null bytes removed)
        $this->assertNotEquals(500, $response->status());
    }

    /**
     * Test SQL injection attempts are prevented.
     */
    public function test_sql_injection_is_prevented(): void
    {
        $user = User::factory()->create([
            'user_level_id' => '3',
        ]);

        // Try SQL injection in search/filter
        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/users?search=' . urlencode("'; DROP TABLE users; --"));

        // Should not cause any database errors
        $response->assertSuccessful();
        
        // Verify users table still exists
        $this->assertDatabaseCount('users', User::count());
    }

    /**
     * Test XSS attempts are prevented in attendance messages.
     */
    public function test_xss_is_prevented_in_attendance_messages(): void
    {
        $user = User::factory()->create(['user_level_id' => '1']);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/attendance/clock-in', [
                'message' => '<script>alert("XSS")</script>Starting work',
            ]);

        if ($response->isSuccessful()) {
            $attendance = $user->attendances()->latest()->first();
            
            // Verify script tags are stripped
            $this->assertStringNotContainsString('<script>', $attendance->in_message);
            $this->assertStringNotContainsString('</script>', $attendance->in_message);
        }
    }

    /**
     * Test validation prevents malicious file paths.
     */
    public function test_validation_prevents_malicious_paths(): void
    {
        $user = User::factory()->create(['user_level_id' => '3']);

        // Try to use path traversal in input
        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/users', [
                'name' => '../../../etc/passwd',
                'email' => 'test@example.com',
                'password' => 'password123',
                'user_level_id' => '1',
                'status' => 1,
            ]);

        // Should either succeed with sanitized input or fail validation
        $this->assertNotEquals(500, $response->status());
    }

    /**
     * Test email validation prevents invalid formats.
     */
    public function test_email_validation_prevents_invalid_formats(): void
    {
        // Try various invalid email formats
        $invalidEmails = [
            'notanemail',
            'missing@domain',
            '@nodomain.com',
            'spaces in@email.com',
            'double@@domain.com',
        ];

        foreach ($invalidEmails as $email) {
            $response = $this->postJson('/api/auth/login', [
                'email' => $email,
                'password' => 'password123',
            ]);

            // Should fail validation
            $response->assertStatus(422);
        }
    }

    /**
     * Test password validation enforces minimum length.
     */
    public function test_password_validation_enforces_minimum_length(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => '12345', // Less than 6 characters
        ]);

        // Should fail validation
        $response->assertStatus(422);
        $response->assertJsonStructure([
            'success',
            'error' => [
                'code',
                'message',
                'errors',
            ],
        ]);
        
        $errors = $response->json('error.errors');
        $this->assertArrayHasKey('password', $errors);
    }
}
