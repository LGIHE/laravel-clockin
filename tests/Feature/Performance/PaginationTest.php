<?php

namespace Tests\Feature\Performance;

use App\Models\Attendance;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Leave;
use App\Models\LeaveCategory;
use App\Models\LeaveStatus;
use App\Models\Project;
use App\Models\User;
use App\Models\UserLevel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaginationTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        
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
        
        // Create multiple users
        User::factory()->count(25)->create([
            'user_level_id' => $userLevel->id,
            'department_id' => $department->id,
            'designation_id' => $designation->id,
        ]);
    }

    /**
     * Test that user list endpoint supports pagination.
     */
    public function test_user_list_supports_pagination(): void
    {
        $this->actingAs($this->admin, 'sanctum');
        
        $response = $this->getJson('/api/users?per_page=10');
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data',
            'meta' => [
                'current_page',
                'per_page',
                'total',
                'last_page',
            ],
        ]);
        
        $meta = $response->json('meta');
        
        $this->assertEquals(10, $meta['per_page']);
        $this->assertEquals(1, $meta['current_page']);
        $this->assertGreaterThan(10, $meta['total']);
        $this->assertGreaterThan(1, $meta['last_page']);
        
        // Test that data count matches per_page
        $this->assertCount(10, $response->json('data'));
    }

    /**
     * Test that user list pagination can navigate to different pages.
     */
    public function test_user_list_pagination_navigation(): void
    {
        $this->actingAs($this->admin, 'sanctum');
        
        // Get first page
        $response1 = $this->getJson('/api/users?per_page=10&page=1');
        $response1->assertStatus(200);
        $page1Data = $response1->json('data');
        
        // Get second page
        $response2 = $this->getJson('/api/users?per_page=10&page=2');
        $response2->assertStatus(200);
        $page2Data = $response2->json('data');
        
        // Pages should have different data
        $this->assertNotEquals($page1Data[0]['id'], $page2Data[0]['id']);
    }

    /**
     * Test that attendance list endpoint supports pagination.
     */
    public function test_attendance_list_supports_pagination(): void
    {
        // Create attendance records
        $users = User::where('user_level_id', '!=', $this->admin->user_level_id)->take(5)->get();
        foreach ($users as $user) {
            Attendance::factory()->count(10)->create(['user_id' => $user->id]);
        }
        
        $this->actingAs($this->admin, 'sanctum');
        
        $response = $this->getJson('/api/attendance?per_page=15');
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data',
            'meta' => [
                'current_page',
                'per_page',
                'total',
                'last_page',
            ],
        ]);
        
        $meta = $response->json('meta');
        
        $this->assertEquals(15, $meta['per_page']);
        $this->assertEquals(1, $meta['current_page']);
        
        // Test that data count matches per_page or is less if last page
        $this->assertLessThanOrEqual(15, count($response->json('data')));
    }

    /**
     * Test that leave list endpoint supports pagination.
     */
    public function test_leave_list_supports_pagination(): void
    {
        // Create leave records
        $leaveCategory = LeaveCategory::factory()->create();
        $leaveStatus = LeaveStatus::factory()->create(['name' => 'pending']);
        
        $users = User::where('user_level_id', '!=', $this->admin->user_level_id)->take(5)->get();
        foreach ($users as $user) {
            Leave::factory()->count(5)->create([
                'user_id' => $user->id,
                'leave_category_id' => $leaveCategory->id,
                'leave_status_id' => $leaveStatus->id,
            ]);
        }
        
        $this->actingAs($this->admin, 'sanctum');
        
        $response = $this->getJson('/api/leaves?per_page=10');
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data',
            'meta' => [
                'current_page',
                'per_page',
                'total',
                'last_page',
            ],
        ]);
        
        $meta = $response->json('meta');
        
        $this->assertEquals(10, $meta['per_page']);
        $this->assertLessThanOrEqual(10, count($response->json('data')));
    }

    /**
     * Test that project list endpoint supports pagination.
     */
    public function test_project_list_supports_pagination(): void
    {
        // Create projects
        Project::factory()->count(15)->create();
        
        $this->actingAs($this->admin, 'sanctum');
        
        $response = $this->getJson('/api/projects?per_page=10');
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data',
            'meta' => [
                'current_page',
                'per_page',
                'total',
                'last_page',
            ],
        ]);
        
        $meta = $response->json('meta');
        
        $this->assertEquals(10, $meta['per_page']);
        $this->assertEquals(1, $meta['current_page']);
        $this->assertGreaterThan(10, $meta['total']);
        
        // Test that data count matches per_page
        $this->assertCount(10, $response->json('data'));
    }

    /**
     * Test that pagination per_page parameter can be customized.
     */
    public function test_pagination_per_page_customization(): void
    {
        $this->actingAs($this->admin, 'sanctum');
        
        // Test with 5 per page
        $response1 = $this->getJson('/api/users?per_page=5');
        $response1->assertStatus(200);
        $this->assertEquals(5, $response1->json('meta.per_page'));
        $this->assertCount(5, $response1->json('data'));
        
        // Test with 20 per page
        $response2 = $this->getJson('/api/users?per_page=20');
        $response2->assertStatus(200);
        $this->assertEquals(20, $response2->json('meta.per_page'));
        $this->assertLessThanOrEqual(20, count($response2->json('data')));
    }

    /**
     * Test that pagination works with filters.
     */
    public function test_pagination_works_with_filters(): void
    {
        $this->actingAs($this->admin, 'sanctum');
        
        // Get users with status filter and pagination
        $response = $this->getJson('/api/users?status=1&per_page=10');
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data',
            'meta',
        ]);
        
        // All returned users should have status 1
        $users = $response->json('data');
        foreach ($users as $user) {
            $this->assertEquals(1, $user['status']);
        }
    }

    /**
     * Test that pagination works with sorting.
     */
    public function test_pagination_works_with_sorting(): void
    {
        $this->actingAs($this->admin, 'sanctum');
        
        // Get users sorted by name ascending
        $response = $this->getJson('/api/users?sort_by=name&sort_direction=asc&per_page=10');
        
        $response->assertStatus(200);
        
        $users = $response->json('data');
        
        // Verify sorting
        for ($i = 0; $i < count($users) - 1; $i++) {
            $this->assertLessThanOrEqual(
                $users[$i + 1]['name'],
                $users[$i]['name'],
                'Users should be sorted by name in ascending order'
            );
        }
    }

    /**
     * Test that empty pages return correct structure.
     */
    public function test_pagination_handles_empty_pages(): void
    {
        $this->actingAs($this->admin, 'sanctum');
        
        // Request a page that doesn't exist
        $response = $this->getJson('/api/users?per_page=10&page=999');
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data',
            'meta',
        ]);
        
        // Data should be empty
        $this->assertEmpty($response->json('data'));
    }
}
