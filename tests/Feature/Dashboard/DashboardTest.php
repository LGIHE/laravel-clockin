<?php

namespace Tests\Feature\Dashboard;

use App\Models\Attendance;
use App\Models\Department;
use App\Models\Leave;
use App\Models\LeaveCategory;
use App\Models\LeaveStatus;
use App\Models\Notification;
use App\Models\Project;
use App\Models\User;
use App\Models\UserLevel;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected User $supervisor;
    protected User $admin;
    protected UserLevel $userLevel;
    protected UserLevel $supervisorLevel;
    protected UserLevel $adminLevel;
    protected Department $department;

    protected function setUp(): void
    {
        parent::setUp();

        // Create user levels
        $this->userLevel = UserLevel::factory()->create([
            'id' => '550e8400-e29b-41d4-a716-446655440001',
            'name' => 'user',
        ]);

        $this->supervisorLevel = UserLevel::factory()->create([
            'id' => '550e8400-e29b-41d4-a716-446655440002',
            'name' => 'supervisor',
        ]);

        $this->adminLevel = UserLevel::factory()->create([
            'id' => '550e8400-e29b-41d4-a716-446655440003',
            'name' => 'admin',
        ]);

        // Create department
        $this->department = Department::factory()->create();

        // Create users
        $this->user = User::factory()->create([
            'user_level_id' => $this->userLevel->id,
            'department_id' => $this->department->id,
            'status' => 1,
        ]);

        $this->supervisor = User::factory()->create([
            'user_level_id' => $this->supervisorLevel->id,
            'department_id' => $this->department->id,
            'status' => 1,
        ]);

        $this->admin = User::factory()->create([
            'user_level_id' => $this->adminLevel->id,
            'department_id' => $this->department->id,
            'status' => 1,
        ]);
    }

    /**
     * Test user dashboard data retrieval.
     */
    public function test_user_can_retrieve_their_dashboard_data(): void
    {
        // Create attendance records
        Attendance::factory()->create([
            'user_id' => $this->user->id,
            'in_time' => Carbon::now()->subHours(2),
            'out_time' => null,
        ]);

        Attendance::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'in_time' => Carbon::now()->subDays(2),
            'out_time' => Carbon::now()->subDays(2)->addHours(8),
            'worked' => 28800, // 8 hours
        ]);

        // Create leave records
        $leaveCategory = LeaveCategory::factory()->create();
        $leaveStatus = LeaveStatus::factory()->create(['name' => 'approved']);

        Leave::factory()->create([
            'user_id' => $this->user->id,
            'leave_category_id' => $leaveCategory->id,
            'leave_status_id' => $leaveStatus->id,
            'date' => Carbon::now()->addDays(5),
        ]);

        // Create notifications
        Notification::factory()->count(2)->create([
            'notifiable_id' => $this->user->id,
            'read_at' => null,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/dashboard/user');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'success',
                'data' => [
                    'attendance_status' => [
                        'clocked_in',
                        'in_time',
                        'in_message',
                    ],
                    'recent_attendance',
                    'upcoming_leaves',
                    'notifications',
                    'stats' => [
                        'total_hours_this_month',
                        'total_days_this_month',
                        'average_hours_per_day',
                        'total_hours_formatted',
                        'average_hours_formatted',
                    ],
                ],
            ]);

        $this->assertTrue($response->json('data.attendance_status.clocked_in'));
        $this->assertCount(2, $response->json('data.notifications'));
    }

    /**
     * Test supervisor dashboard data retrieval.
     */
    public function test_supervisor_can_retrieve_their_dashboard_data(): void
    {
        // Create team members
        $teamMember1 = User::factory()->create([
            'user_level_id' => $this->userLevel->id,
            'supervisor_id' => $this->supervisor->id,
            'department_id' => $this->department->id,
            'status' => 1,
        ]);

        $teamMember2 = User::factory()->create([
            'user_level_id' => $this->userLevel->id,
            'supervisor_id' => $this->supervisor->id,
            'department_id' => $this->department->id,
            'status' => 1,
        ]);

        // Create attendance for team members
        Attendance::factory()->create([
            'user_id' => $teamMember1->id,
            'in_time' => Carbon::today()->addHours(9),
            'out_time' => null,
        ]);

        Attendance::factory()->create([
            'user_id' => $teamMember2->id,
            'in_time' => Carbon::today()->addHours(8),
            'out_time' => Carbon::today()->addHours(17),
            'worked' => 32400, // 9 hours
        ]);

        // Create pending leave requests
        $leaveCategory = LeaveCategory::factory()->create();
        $pendingStatus = LeaveStatus::factory()->create(['name' => 'pending']);

        Leave::factory()->count(2)->create([
            'user_id' => $teamMember1->id,
            'leave_category_id' => $leaveCategory->id,
            'leave_status_id' => $pendingStatus->id,
            'date' => Carbon::now()->addDays(3),
        ]);

        $response = $this->actingAs($this->supervisor, 'sanctum')
            ->getJson('/api/dashboard/supervisor');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'success',
                'data' => [
                    'team_attendance' => [
                        'total_team_members',
                        'clocked_in',
                        'clocked_out',
                        'not_clocked_in',
                        'attendance_records',
                    ],
                    'pending_leaves',
                    'team_stats' => [
                        'total_team_hours_this_month',
                        'average_hours_per_member',
                        'total_team_hours_formatted',
                        'average_hours_formatted',
                        'pending_leave_requests',
                    ],
                    'team_members',
                ],
            ]);

        $this->assertEquals(2, $response->json('data.team_attendance.total_team_members'));
        $this->assertEquals(1, $response->json('data.team_attendance.clocked_in'));
        $this->assertEquals(1, $response->json('data.team_attendance.clocked_out'));
        $this->assertCount(2, $response->json('data.pending_leaves'));
    }

    /**
     * Test admin dashboard data retrieval.
     */
    public function test_admin_can_retrieve_their_dashboard_data(): void
    {
        // Create additional users
        $additionalUsers = User::factory()->count(5)->create([
            'user_level_id' => $this->userLevel->id,
            'department_id' => $this->department->id,
            'status' => 1,
        ]);

        User::factory()->count(2)->create([
            'user_level_id' => $this->userLevel->id,
            'department_id' => $this->department->id,
            'status' => 0, // Inactive
        ]);

        // Create projects
        Project::factory()->count(3)->create();

        // Create attendance records for the additional users
        foreach ($additionalUsers->take(5) as $user) {
            Attendance::factory()->create([
                'user_id' => $user->id,
                'in_time' => Carbon::today()->addHours(9),
                'out_time' => null,
            ]);
        }

        // Create pending leaves
        $leaveCategory = LeaveCategory::factory()->create();
        $pendingStatus = LeaveStatus::factory()->create(['name' => 'pending']);

        foreach ($additionalUsers->take(3) as $user) {
            Leave::factory()->create([
                'user_id' => $user->id,
                'leave_category_id' => $leaveCategory->id,
                'leave_status_id' => $pendingStatus->id,
                'date' => Carbon::now()->addDays(5),
            ]);
        }

        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/dashboard/admin');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'success',
                'data' => [
                    'system_stats' => [
                        'total_users',
                        'active_users',
                        'inactive_users',
                        'total_departments',
                        'total_projects',
                        'today_attendance',
                        'currently_clocked_in',
                    ],
                    'recent_activities',
                    'pending_approvals',
                    'department_stats',
                ],
            ]);

        $systemStats = $response->json('data.system_stats');
        $this->assertGreaterThan(0, $systemStats['total_users']);
        $this->assertGreaterThan(0, $systemStats['active_users']);
        $this->assertEquals(3, $systemStats['total_projects']);
        $this->assertCount(3, $response->json('data.pending_approvals'));
    }

    /**
     * Test role-based access control for user dashboard.
     */
    public function test_all_authenticated_users_can_access_user_dashboard(): void
    {
        // Test regular user
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/dashboard/user');
        $response->assertStatus(200);

        // Test supervisor
        $response = $this->actingAs($this->supervisor, 'sanctum')
            ->getJson('/api/dashboard/user');
        $response->assertStatus(200);

        // Test admin
        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/dashboard/user');
        $response->assertStatus(200);
    }

    /**
     * Test role-based access control for supervisor dashboard.
     */
    public function test_only_supervisor_and_admin_can_access_supervisor_dashboard(): void
    {
        // Regular user should be denied
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/dashboard/supervisor');
        $response->assertStatus(403);

        // Supervisor should have access
        $response = $this->actingAs($this->supervisor, 'sanctum')
            ->getJson('/api/dashboard/supervisor');
        $response->assertStatus(200);

        // Admin should have access
        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/dashboard/supervisor');
        $response->assertStatus(200);
    }

    /**
     * Test role-based access control for admin dashboard.
     */
    public function test_only_admin_can_access_admin_dashboard(): void
    {
        // Regular user should be denied
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/dashboard/admin');
        $response->assertStatus(403);

        // Supervisor should be denied
        $response = $this->actingAs($this->supervisor, 'sanctum')
            ->getJson('/api/dashboard/admin');
        $response->assertStatus(403);

        // Admin should have access
        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/dashboard/admin');
        $response->assertStatus(200);
    }

    /**
     * Test unauthenticated access is denied.
     */
    public function test_unauthenticated_users_cannot_access_dashboards(): void
    {
        $response = $this->getJson('/api/dashboard/user');
        $response->assertStatus(401);

        $response = $this->getJson('/api/dashboard/supervisor');
        $response->assertStatus(401);

        $response = $this->getJson('/api/dashboard/admin');
        $response->assertStatus(401);
    }

    /**
     * Test user dashboard shows correct attendance status when clocked in.
     */
    public function test_user_dashboard_shows_clocked_in_status(): void
    {
        Attendance::factory()->create([
            'user_id' => $this->user->id,
            'in_time' => Carbon::now()->subHours(3),
            'in_message' => 'Starting work',
            'out_time' => null,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/dashboard/user');

        $response->assertStatus(200);
        
        $attendanceStatus = $response->json('data.attendance_status');
        $this->assertTrue($attendanceStatus['clocked_in']);
        $this->assertNotNull($attendanceStatus['in_time']);
        $this->assertEquals('Starting work', $attendanceStatus['in_message']);
    }

    /**
     * Test user dashboard shows correct attendance status when clocked out.
     */
    public function test_user_dashboard_shows_clocked_out_status(): void
    {
        Attendance::factory()->create([
            'user_id' => $this->user->id,
            'in_time' => Carbon::now()->subHours(8),
            'out_time' => Carbon::now()->subHours(1),
            'worked' => 25200, // 7 hours
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/dashboard/user');

        $response->assertStatus(200);
        
        $attendanceStatus = $response->json('data.attendance_status');
        $this->assertFalse($attendanceStatus['clocked_in']);
        $this->assertNull($attendanceStatus['in_time']);
    }

    /**
     * Test supervisor dashboard shows correct team statistics.
     */
    public function test_supervisor_dashboard_calculates_team_statistics_correctly(): void
    {
        // Create team members
        $teamMember1 = User::factory()->create([
            'user_level_id' => $this->userLevel->id,
            'supervisor_id' => $this->supervisor->id,
            'status' => 1,
        ]);

        $teamMember2 = User::factory()->create([
            'user_level_id' => $this->userLevel->id,
            'supervisor_id' => $this->supervisor->id,
            'status' => 1,
        ]);

        // Create attendance records for this month
        $startOfMonth = Carbon::now()->startOfMonth();
        
        Attendance::factory()->create([
            'user_id' => $teamMember1->id,
            'in_time' => $startOfMonth->copy()->addDays(1)->addHours(9),
            'out_time' => $startOfMonth->copy()->addDays(1)->addHours(17),
            'worked' => 28800, // 8 hours
        ]);

        Attendance::factory()->create([
            'user_id' => $teamMember2->id,
            'in_time' => $startOfMonth->copy()->addDays(2)->addHours(9),
            'out_time' => $startOfMonth->copy()->addDays(2)->addHours(18),
            'worked' => 32400, // 9 hours
        ]);

        $response = $this->actingAs($this->supervisor, 'sanctum')
            ->getJson('/api/dashboard/supervisor');

        $response->assertStatus(200);
        
        $teamStats = $response->json('data.team_stats');
        $this->assertEquals(61200, $teamStats['total_team_hours_this_month']); // 8 + 9 hours
        $this->assertNotNull($teamStats['total_team_hours_formatted']);
    }

    /**
     * Test admin dashboard shows correct department statistics.
     */
    public function test_admin_dashboard_shows_department_statistics(): void
    {
        // Create another department
        $department2 = Department::factory()->create(['name' => 'Engineering']);

        // Create users in different departments
        User::factory()->count(3)->create([
            'user_level_id' => $this->userLevel->id,
            'department_id' => $this->department->id,
            'status' => 1,
        ]);

        User::factory()->count(2)->create([
            'user_level_id' => $this->userLevel->id,
            'department_id' => $department2->id,
            'status' => 1,
        ]);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/dashboard/admin');

        $response->assertStatus(200);
        
        $departmentStats = $response->json('data.department_stats');
        $this->assertIsArray($departmentStats);
        $this->assertGreaterThanOrEqual(2, count($departmentStats));
        
        // Check structure of department stats
        foreach ($departmentStats as $stat) {
            $this->assertArrayHasKey('id', $stat);
            $this->assertArrayHasKey('name', $stat);
            $this->assertArrayHasKey('active_users', $stat);
            $this->assertArrayHasKey('total_hours_this_month', $stat);
            $this->assertArrayHasKey('total_hours_formatted', $stat);
        }
    }
}
