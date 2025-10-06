<?php

namespace Tests\Feature\Performance;

use App\Models\Attendance;
use App\Models\Department;
use App\Models\Designation;
use App\Models\User;
use App\Models\UserLevel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class CachePerformanceTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Clear cache before each test
        Cache::flush();
        
        // Create test data
        $adminLevel = UserLevel::factory()->create(['name' => 'admin']);
        $userLevel = UserLevel::factory()->create(['name' => 'user']);
        $department = Department::factory()->create();
        $designation = Designation::factory()->create();
        
        $this->admin = User::factory()->create([
            'user_level_id' => $adminLevel->id,
            'department_id' => $department->id,
            'designation_id' => $designation->id,
        ]);
        
        $this->user = User::factory()->create([
            'user_level_id' => $userLevel->id,
            'department_id' => $department->id,
            'designation_id' => $designation->id,
        ]);
        
        // Create attendance records
        Attendance::factory()->count(10)->create(['user_id' => $this->user->id]);
    }

    /**
     * Test that user dashboard data is cached.
     */
    public function test_user_dashboard_uses_cache(): void
    {
        $this->actingAs($this->user, 'sanctum');
        
        // First request - should cache the data
        $response1 = $this->getJson('/api/dashboard/user');
        $response1->assertStatus(200);
        
        // Check that user cache exists
        $userCacheKey = "user:{$this->user->id}";
        $this->assertTrue(Cache::has($userCacheKey), 'User data should be cached');
        
        // Check that stats cache exists
        $statsCacheKey = "user_stats:{$this->user->id}:" . now()->format('Y-m');
        $this->assertTrue(Cache::has($statsCacheKey), 'User stats should be cached');
        
        // Second request - should use cached data
        $response2 = $this->getJson('/api/dashboard/user');
        $response2->assertStatus(200);
        
        // Responses should be identical
        $this->assertEquals($response1->json('data.stats'), $response2->json('data.stats'));
    }

    /**
     * Test that admin dashboard data is cached.
     */
    public function test_admin_dashboard_uses_cache(): void
    {
        $this->actingAs($this->admin, 'sanctum');
        
        // First request - should cache the data
        $response1 = $this->getJson('/api/dashboard/admin');
        $response1->assertStatus(200);
        
        // Check that admin stats cache exists
        $this->assertTrue(Cache::has('admin_system_stats'), 'Admin system stats should be cached');
        
        // Second request - should use cached data
        $response2 = $this->getJson('/api/dashboard/admin');
        $response2->assertStatus(200);
        
        // System stats should be identical
        $this->assertEquals(
            $response1->json('data.system_stats'),
            $response2->json('data.system_stats')
        );
    }

    /**
     * Test that supervisor team data is cached.
     */
    public function test_supervisor_dashboard_uses_cache(): void
    {
        // Create supervisor
        $supervisorLevel = UserLevel::factory()->create(['name' => 'supervisor']);
        $supervisor = User::factory()->create([
            'user_level_id' => $supervisorLevel->id,
        ]);
        
        // Assign team members
        $teamMember = User::factory()->create([
            'user_level_id' => UserLevel::where('name', 'user')->first()->id,
            'supervisor_id' => $supervisor->id,
        ]);
        
        $this->actingAs($supervisor, 'sanctum');
        
        // First request - should cache the data
        $response1 = $this->getJson('/api/dashboard/supervisor');
        $response1->assertStatus(200);
        
        // Check that supervisor team cache exists
        $cacheKey = "supervisor_team:{$supervisor->id}";
        $this->assertTrue(Cache::has($cacheKey), 'Supervisor team data should be cached');
        
        // Second request - should use cached data
        $response2 = $this->getJson('/api/dashboard/supervisor');
        $response2->assertStatus(200);
    }

    /**
     * Test that cache is invalidated when user is updated.
     */
    public function test_cache_invalidated_on_user_update(): void
    {
        $this->actingAs($this->admin, 'sanctum');
        
        // Cache user data
        $userCacheKey = "user:{$this->user->id}";
        Cache::put($userCacheKey, $this->user, 300);
        
        $this->assertTrue(Cache::has($userCacheKey), 'User cache should exist before update');
        
        // Update user
        $response = $this->putJson("/api/users/{$this->user->id}", [
            'name' => 'Updated Name',
            'email' => $this->user->email,
            'user_level_id' => $this->user->user_level_id,
        ]);
        
        $response->assertStatus(200);
        
        // Cache should be invalidated
        $this->assertFalse(Cache::has($userCacheKey), 'User cache should be invalidated after update');
    }

    /**
     * Test that cache is invalidated when user is deleted.
     */
    public function test_cache_invalidated_on_user_delete(): void
    {
        $this->actingAs($this->admin, 'sanctum');
        
        // Create a user to delete
        $userToDelete = User::factory()->create([
            'user_level_id' => UserLevel::where('name', 'user')->first()->id,
        ]);
        
        // Cache user data
        $userCacheKey = "user:{$userToDelete->id}";
        Cache::put($userCacheKey, $userToDelete, 300);
        
        $this->assertTrue(Cache::has($userCacheKey), 'User cache should exist before delete');
        
        // Delete user
        $response = $this->deleteJson("/api/users/{$userToDelete->id}");
        $response->assertStatus(200);
        
        // Cache should be invalidated
        $this->assertFalse(Cache::has($userCacheKey), 'User cache should be invalidated after delete');
    }

    /**
     * Test that attendance operations invalidate user stats cache.
     */
    public function test_cache_invalidated_on_attendance_operations(): void
    {
        // Create a fresh user without attendance records
        $freshUser = User::factory()->create([
            'user_level_id' => UserLevel::where('name', 'user')->first()->id,
        ]);
        
        $this->actingAs($freshUser, 'sanctum');
        
        // Cache user stats
        $statsCacheKey = "user_stats:{$freshUser->id}:" . now()->format('Y-m');
        Cache::put($statsCacheKey, ['test' => 'data'], 600);
        
        $this->assertTrue(Cache::has($statsCacheKey), 'Stats cache should exist before clock in');
        
        // Clock in
        $response = $this->postJson('/api/attendance/clock-in', [
            'message' => 'Starting work',
        ]);
        
        $response->assertStatus(200);
        
        // Stats cache should be invalidated
        $this->assertFalse(Cache::has($statsCacheKey), 'Stats cache should be invalidated after clock in');
    }

    /**
     * Test cache hit rate improvement.
     */
    public function test_cache_improves_response_time(): void
    {
        $this->actingAs($this->user, 'sanctum');
        
        // First request (cache miss)
        $start1 = microtime(true);
        $response1 = $this->getJson('/api/dashboard/user');
        $time1 = microtime(true) - $start1;
        
        $response1->assertStatus(200);
        
        // Second request (cache hit)
        $start2 = microtime(true);
        $response2 = $this->getJson('/api/dashboard/user');
        $time2 = microtime(true) - $start2;
        
        $response2->assertStatus(200);
        
        // Second request should be faster or similar (cached data)
        // Note: This is a basic test; in production, the difference would be more significant
        $this->assertLessThanOrEqual($time1 * 1.5, $time2, 'Cached request should not be significantly slower');
    }

    /**
     * Test that admin stats cache is invalidated when user is created.
     */
    public function test_admin_cache_invalidated_on_user_creation(): void
    {
        $this->actingAs($this->admin, 'sanctum');
        
        // Cache admin stats
        Cache::put('admin_system_stats', ['test' => 'data'], 300);
        $this->assertTrue(Cache::has('admin_system_stats'), 'Admin stats cache should exist');
        
        // Create new user
        $response = $this->postJson('/api/users', [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'user_level_id' => UserLevel::where('name', 'user')->first()->id,
            'designation_id' => $this->user->designation_id,
            'department_id' => $this->user->department_id,
        ]);
        
        $response->assertStatus(201);
        
        // Admin stats cache should be invalidated
        $this->assertFalse(Cache::has('admin_system_stats'), 'Admin stats cache should be invalidated after user creation');
    }
}
