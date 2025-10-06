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
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class QueryOptimizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test data
        $this->createTestData();
    }

    protected function createTestData(): void
    {
        // Create user levels
        $adminLevel = UserLevel::factory()->create(['name' => 'admin']);
        $userLevel = UserLevel::factory()->create(['name' => 'user']);
        
        // Create departments and designations
        $department = Department::factory()->create();
        $designation = Designation::factory()->create();
        
        // Create admin user
        $this->admin = User::factory()->create([
            'user_level_id' => $adminLevel->id,
            'department_id' => $department->id,
            'designation_id' => $designation->id,
        ]);
        
        // Create regular users with relationships
        $this->users = User::factory()->count(10)->create([
            'user_level_id' => $userLevel->id,
            'department_id' => $department->id,
            'designation_id' => $designation->id,
        ]);
        
        // Create attendance records
        foreach ($this->users as $user) {
            Attendance::factory()->count(5)->create(['user_id' => $user->id]);
        }
        
        // Create leave categories and statuses
        $leaveCategory = LeaveCategory::factory()->create();
        $leaveStatus = LeaveStatus::factory()->create(['name' => 'pending']);
        
        // Create leaves
        foreach ($this->users as $user) {
            Leave::factory()->count(3)->create([
                'user_id' => $user->id,
                'leave_category_id' => $leaveCategory->id,
                'leave_status_id' => $leaveStatus->id,
            ]);
        }
        
        // Create projects
        Project::factory()->count(5)->create();
    }

    /**
     * Test that user list endpoint uses eager loading to prevent N+1 queries.
     */
    public function test_user_list_prevents_n_plus_one_queries(): void
    {
        $this->actingAs($this->admin, 'sanctum');
        
        // Enable query log
        DB::enableQueryLog();
        
        $response = $this->getJson('/api/users?per_page=10');
        
        $queries = DB::getQueryLog();
        $queryCount = count($queries);
        
        // Should have:
        // 1. Main users query with pagination
        // 2. Eager load userLevel
        // 3. Eager load department
        // 4. Eager load designation
        // Total should be around 4-5 queries, not 10+ (N+1 problem)
        
        $this->assertLessThan(10, $queryCount, "Too many queries executed: {$queryCount}. Possible N+1 problem.");
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'email',
                    'user_level',
                    'department',
                    'designation',
                ]
            ],
            'meta',
        ]);
        
        DB::disableQueryLog();
    }

    /**
     * Test that attendance list endpoint uses eager loading.
     */
    public function test_attendance_list_prevents_n_plus_one_queries(): void
    {
        $this->actingAs($this->admin, 'sanctum');
        
        DB::enableQueryLog();
        
        $response = $this->getJson('/api/attendance?per_page=15');
        
        $queries = DB::getQueryLog();
        $queryCount = count($queries);
        
        // Should have minimal queries with eager loading
        $this->assertLessThan(10, $queryCount, "Too many queries executed: {$queryCount}. Possible N+1 problem.");
        
        $response->assertStatus(200);
        
        DB::disableQueryLog();
    }

    /**
     * Test that leave list endpoint uses eager loading.
     */
    public function test_leave_list_prevents_n_plus_one_queries(): void
    {
        $this->actingAs($this->admin, 'sanctum');
        
        DB::enableQueryLog();
        
        $response = $this->getJson('/api/leaves?per_page=10');
        
        $queries = DB::getQueryLog();
        $queryCount = count($queries);
        
        // Should have minimal queries with eager loading
        $this->assertLessThan(10, $queryCount, "Too many queries executed: {$queryCount}. Possible N+1 problem.");
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                '*' => [
                    'id',
                    'user',
                    'category',
                    'status',
                ]
            ],
        ]);
        
        DB::disableQueryLog();
    }

    /**
     * Test that project list endpoint uses eager loading.
     */
    public function test_project_list_prevents_n_plus_one_queries(): void
    {
        $this->actingAs($this->admin, 'sanctum');
        
        DB::enableQueryLog();
        
        $response = $this->getJson('/api/projects?per_page=10');
        
        $queries = DB::getQueryLog();
        $queryCount = count($queries);
        
        // Should have minimal queries
        $this->assertLessThan(8, $queryCount, "Too many queries executed: {$queryCount}. Possible N+1 problem.");
        
        $response->assertStatus(200);
        
        DB::disableQueryLog();
    }

    /**
     * Test that dashboard endpoints use eager loading.
     */
    public function test_user_dashboard_prevents_n_plus_one_queries(): void
    {
        $user = $this->users->first();
        $this->actingAs($user, 'sanctum');
        
        DB::enableQueryLog();
        
        $response = $this->getJson('/api/dashboard/user');
        
        $queries = DB::getQueryLog();
        $queryCount = count($queries);
        
        // Dashboard should be optimized with eager loading
        $this->assertLessThan(15, $queryCount, "Too many queries executed: {$queryCount}. Dashboard needs optimization.");
        
        $response->assertStatus(200);
        
        DB::disableQueryLog();
    }

    /**
     * Test that supervisor dashboard uses eager loading.
     */
    public function test_supervisor_dashboard_prevents_n_plus_one_queries(): void
    {
        // Create supervisor with team
        $supervisorLevel = UserLevel::where('name', 'supervisor')->first() 
            ?? UserLevel::factory()->create(['name' => 'supervisor']);
        
        $supervisor = User::factory()->create([
            'user_level_id' => $supervisorLevel->id,
        ]);
        
        // Assign some users to this supervisor
        $this->users->take(3)->each(function ($user) use ($supervisor) {
            $user->update(['supervisor_id' => $supervisor->id]);
        });
        
        $this->actingAs($supervisor, 'sanctum');
        
        DB::enableQueryLog();
        
        $response = $this->getJson('/api/dashboard/supervisor');
        
        $queries = DB::getQueryLog();
        $queryCount = count($queries);
        
        // Supervisor dashboard should be optimized
        $this->assertLessThan(20, $queryCount, "Too many queries executed: {$queryCount}. Supervisor dashboard needs optimization.");
        
        $response->assertStatus(200);
        
        DB::disableQueryLog();
    }
}
