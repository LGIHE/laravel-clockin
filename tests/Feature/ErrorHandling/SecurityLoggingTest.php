<?php

namespace Tests\Feature\ErrorHandling;

use App\Models\User;
use App\Models\UserLevel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class SecurityLoggingTest extends TestCase
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

    public function test_failed_login_is_logged_to_auth_channel(): void
    {
        Log::shouldReceive('channel')
            ->with('auth')
            ->andReturnSelf();
        
        Log::shouldReceive('warning')
            ->once()
            ->with('Failed login attempt', \Mockery::on(function ($context) {
                return isset($context['email']) 
                    && isset($context['ip']) 
                    && isset($context['timestamp']);
            }));

        $this->postJson('/api/auth/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'wrongpassword',
        ]);
    }

    public function test_successful_login_is_logged_to_auth_channel(): void
    {
        $user = User::factory()->create([
            'user_level_id' => '3',
            'email' => 'user@example.com',
            'password' => bcrypt('password'),
            'status' => 1,
        ]);

        Log::shouldReceive('channel')
            ->with('auth')
            ->andReturnSelf();
        
        Log::shouldReceive('info')
            ->once()
            ->with('Successful login', \Mockery::on(function ($context) use ($user) {
                return isset($context['user_id']) 
                    && $context['user_id'] === $user->id
                    && isset($context['email'])
                    && isset($context['ip']);
            }));

        $this->postJson('/api/auth/login', [
            'email' => 'user@example.com',
            'password' => 'password',
        ]);
    }

    public function test_unauthorized_access_is_logged_to_security_channel(): void
    {
        $user = User::factory()->create(['user_level_id' => '3']);

        Log::shouldReceive('channel')
            ->with('security')
            ->andReturnSelf();
        
        Log::shouldReceive('warning')
            ->once()
            ->with('Unauthorized access attempt', \Mockery::on(function ($context) use ($user) {
                return isset($context['user_id']) 
                    && $context['user_id'] === $user->id
                    && isset($context['ip'])
                    && isset($context['url']);
            }));

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/users');
    }

    public function test_logout_is_logged_to_auth_channel(): void
    {
        $user = User::factory()->create([
            'user_level_id' => '3',
            'status' => 1,
        ]);

        Log::shouldReceive('channel')
            ->with('auth')
            ->andReturnSelf();
        
        Log::shouldReceive('info')
            ->once()
            ->with('User logged out', \Mockery::on(function ($context) use ($user) {
                return isset($context['user_id']) 
                    && $context['user_id'] === $user->id
                    && isset($context['ip']);
            }));

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/auth/logout');
    }

    public function test_password_reset_request_is_logged(): void
    {
        $user = User::factory()->create([
            'user_level_id' => '3',
            'email' => 'user@example.com',
        ]);

        Log::shouldReceive('channel')
            ->with('auth')
            ->andReturnSelf();
        
        Log::shouldReceive('info')
            ->once()
            ->with('Password reset requested', \Mockery::on(function ($context) {
                return isset($context['email']) 
                    && isset($context['ip']);
            }));

        // Also mock the error channel in case password reset fails
        Log::shouldReceive('channel')
            ->with('daily')
            ->andReturnSelf();
        
        Log::shouldReceive('error')
            ->zeroOrMoreTimes();

        $this->postJson('/api/auth/forgot-password', [
            'email' => 'user@example.com',
        ]);
    }

    public function test_inactive_account_login_attempt_is_logged(): void
    {
        $user = User::factory()->create([
            'user_level_id' => '3',
            'email' => 'inactive@example.com',
            'password' => bcrypt('password'),
            'status' => 0, // Inactive
        ]);

        Log::shouldReceive('channel')
            ->with('auth')
            ->andReturnSelf();
        
        Log::shouldReceive('warning')
            ->once()
            ->with('Failed login attempt', \Mockery::type('array'));

        $this->postJson('/api/auth/login', [
            'email' => 'inactive@example.com',
            'password' => 'password',
        ]);
    }
}
