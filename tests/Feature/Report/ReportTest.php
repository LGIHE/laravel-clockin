<?php

namespace Tests\Feature\Report;

use App\Models\Attendance;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Project;
use App\Models\User;
use App\Models\UserLevel;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected User $admin;
    protected User $supervisor;
    protected UserLevel $userLevel;
    protected UserLevel $adminLevel;
    protected UserLevel $supervisorLevel;
    protected Department $department;
    protected Designation $designation;
    protected Project $project;

    protected function setUp(): void
    {
        parent::setUp();

        // Create user levels
        $this->userLevel = UserLevel::create([
            'id' => (string) Str::uuid(),
            'name' => 'user',
        ]);

        $this->supervisorLevel = UserLevel::create([
            'id' => (string) Str::uuid(),
            'name' => 'supervisor',
        ]);

        $this->adminLevel = UserLevel::create([
            'id' => (string) Str::uuid(),
            'name' => 'admin',
        ]);

        // Create department
        $this->department = Department::create([
            'id' => (string) Str::uuid(),
            'name' => 'Engineering',
        ]);

        // Create designation
        $this->designation = Designation::create([
            'id' => (string) Str::uuid(),
            'name' => 'Software Engineer',
        ]);

        // Create project
        $this->project = Project::create([
            'id' => (string) Str::uuid(),
            'name' => 'Test Project',
            'status' => 'ACTIVE',
        ]);

        // Create regular user
        $this->user = User::factory()->create([
            'user_level_id' => $this->userLevel->id,
            'department_id' => $this->department->id,
            'designation_id' => $this->designation->id,
            'project_id' => $this->project->id,
            'status' => 1,
        ]);

        // Create supervisor
        $this->supervisor = User::factory()->create([
            'user_level_id' => $this->supervisorLevel->id,
            'department_id' => $this->department->id,
            'status' => 1,
        ]);

        // Create admin user
        $this->admin = User::factory()->create([
            'user_level_id' => $this->adminLevel->id,
            'status' => 1,
        ]);
    }

    /**
     * Test individual report generation with valid data.
     */
    public function test_individual_report_generation(): void
    {
        // Create attendance records for the user
        $startDate = Carbon::now()->subDays(5);
        $endDate = Carbon::now();

        for ($i = 0; $i < 5; $i++) {
            $date = $startDate->copy()->addDays($i);
            Attendance::create([
                'id' => (string) Str::uuid(),
                'user_id' => $this->user->id,
                'in_time' => $date->copy()->setTime(9, 0),
                'out_time' => $date->copy()->setTime(17, 0),
                'worked' => 28800, // 8 hours
            ]);
        }

        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/reports/individual?' . http_build_query([
                'user_id' => $this->user->id,
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
            ]));

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
                        'department',
                        'designation',
                    ],
                    'period' => [
                        'start_date',
                        'end_date',
                    ],
                    'attendances',
                    'statistics' => [
                        'total_days',
                        'days_present',
                        'days_absent',
                        'total_hours',
                        'average_hours_per_day',
                        'late_arrivals',
                        'early_departures',
                        'attendance_rate',
                    ],
                ],
            ]);

        $data = $response->json('data');
        $this->assertEquals($this->user->id, $data['user']['id']);
        $this->assertEquals(5, $data['statistics']['days_present']);
        $this->assertEquals(40, $data['statistics']['total_hours']);
    }

    /**
     * Test individual report validation errors.
     */
    public function test_individual_report_validation_errors(): void
    {
        // Missing user_id
        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/reports/individual?' . http_build_query([
                'start_date' => Carbon::now()->subDays(5)->format('Y-m-d'),
                'end_date' => Carbon::now()->format('Y-m-d'),
            ]));

        $response->assertStatus(422);

        // Invalid date range (end before start)
        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/reports/individual?' . http_build_query([
                'user_id' => $this->user->id,
                'start_date' => Carbon::now()->format('Y-m-d'),
                'end_date' => Carbon::now()->subDays(5)->format('Y-m-d'),
            ]));

        $response->assertStatus(422);

        // Non-existent user
        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/reports/individual?' . http_build_query([
                'user_id' => (string) Str::uuid(),
                'start_date' => Carbon::now()->subDays(5)->format('Y-m-d'),
                'end_date' => Carbon::now()->format('Y-m-d'),
            ]));

        $response->assertStatus(422);
    }

    /**
     * Test summary report generation.
     */
    public function test_summary_report_generation(): void
    {
        // Create another user
        $user2 = User::factory()->create([
            'user_level_id' => $this->userLevel->id,
            'department_id' => $this->department->id,
            'status' => 1,
        ]);

        $startDate = Carbon::now()->subDays(3);
        $endDate = Carbon::now();

        // Create attendance for user 1
        for ($i = 0; $i < 3; $i++) {
            $date = $startDate->copy()->addDays($i);
            Attendance::create([
                'id' => (string) Str::uuid(),
                'user_id' => $this->user->id,
                'in_time' => $date->copy()->setTime(9, 0),
                'out_time' => $date->copy()->setTime(17, 0),
                'worked' => 28800,
            ]);
        }

        // Create attendance for user 2
        for ($i = 0; $i < 2; $i++) {
            $date = $startDate->copy()->addDays($i);
            Attendance::create([
                'id' => (string) Str::uuid(),
                'user_id' => $user2->id,
                'in_time' => $date->copy()->setTime(9, 30),
                'out_time' => $date->copy()->setTime(18, 0),
                'worked' => 30600, // 8.5 hours
            ]);
        }

        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/reports/summary?' . http_build_query([
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
            ]));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'success',
                'data' => [
                    'period' => [
                        'start_date',
                        'end_date',
                    ],
                    'summary',
                    'overall_statistics' => [
                        'total_users',
                        'total_hours',
                        'average_hours_per_user',
                        'total_days_present',
                        'total_late_arrivals',
                        'total_early_departures',
                    ],
                ],
            ]);

        $data = $response->json('data');
        $this->assertGreaterThanOrEqual(2, $data['overall_statistics']['total_users']);
        $this->assertGreaterThan(0, $data['overall_statistics']['total_hours']);
    }

    /**
     * Test summary report filtering by department.
     */
    public function test_summary_report_filtering_by_department(): void
    {
        // Create another department
        $department2 = Department::create([
            'id' => (string) Str::uuid(),
            'name' => 'Marketing',
        ]);

        // Create user in different department
        $user2 = User::factory()->create([
            'user_level_id' => $this->userLevel->id,
            'department_id' => $department2->id,
            'status' => 1,
        ]);

        $startDate = Carbon::now()->subDays(2);
        $endDate = Carbon::now();

        // Create attendance for both users
        Attendance::create([
            'id' => (string) Str::uuid(),
            'user_id' => $this->user->id,
            'in_time' => $startDate->copy()->setTime(9, 0),
            'out_time' => $startDate->copy()->setTime(17, 0),
            'worked' => 28800,
        ]);

        Attendance::create([
            'id' => (string) Str::uuid(),
            'user_id' => $user2->id,
            'in_time' => $startDate->copy()->setTime(9, 0),
            'out_time' => $startDate->copy()->setTime(17, 0),
            'worked' => 28800,
        ]);

        // Filter by Engineering department
        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/reports/summary?' . http_build_query([
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'department_id' => $this->department->id,
            ]));

        $response->assertStatus(200);
        $data = $response->json('data');
        
        // Should only include users from Engineering department
        $userIds = collect($data['summary'])->pluck('user.id')->toArray();
        $this->assertContains($this->user->id, $userIds);
        $this->assertNotContains($user2->id, $userIds);
    }

    /**
     * Test summary report filtering by project.
     */
    public function test_summary_report_filtering_by_project(): void
    {
        // Create another project
        $project2 = Project::create([
            'id' => (string) Str::uuid(),
            'name' => 'Another Project',
            'status' => 'ACTIVE',
        ]);

        // Create user in different project
        $user2 = User::factory()->create([
            'user_level_id' => $this->userLevel->id,
            'project_id' => $project2->id,
            'status' => 1,
        ]);

        $startDate = Carbon::now()->subDays(2);
        $endDate = Carbon::now();

        // Create attendance for both users
        Attendance::create([
            'id' => (string) Str::uuid(),
            'user_id' => $this->user->id,
            'in_time' => $startDate->copy()->setTime(9, 0),
            'out_time' => $startDate->copy()->setTime(17, 0),
            'worked' => 28800,
        ]);

        Attendance::create([
            'id' => (string) Str::uuid(),
            'user_id' => $user2->id,
            'in_time' => $startDate->copy()->setTime(9, 0),
            'out_time' => $startDate->copy()->setTime(17, 0),
            'worked' => 28800,
        ]);

        // Filter by Test Project
        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/reports/summary?' . http_build_query([
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'project_id' => $this->project->id,
            ]));

        $response->assertStatus(200);
        $data = $response->json('data');
        
        // Should only include users from Test Project
        $userIds = collect($data['summary'])->pluck('user.id')->toArray();
        $this->assertContains($this->user->id, $userIds);
        $this->assertNotContains($user2->id, $userIds);
    }

    /**
     * Test timesheet generation.
     */
    public function test_timesheet_generation(): void
    {
        $month = Carbon::now()->month;
        $year = Carbon::now()->year;
        $startDate = Carbon::create($year, $month, 1);

        // Create attendance records for first 5 days of the month
        for ($i = 0; $i < 5; $i++) {
            $date = $startDate->copy()->addDays($i);
            Attendance::create([
                'id' => (string) Str::uuid(),
                'user_id' => $this->user->id,
                'in_time' => $date->copy()->setTime(9, 0),
                'out_time' => $date->copy()->setTime(17, 0),
                'worked' => 28800,
            ]);
        }

        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/reports/timesheet?' . http_build_query([
                'user_id' => $this->user->id,
                'month' => $month,
                'year' => $year,
            ]));

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
                        'department',
                        'designation',
                    ],
                    'period' => [
                        'month',
                        'year',
                        'month_name',
                    ],
                    'daily_records',
                    'statistics',
                ],
            ]);

        $data = $response->json('data');
        $this->assertEquals($month, $data['period']['month']);
        $this->assertEquals($year, $data['period']['year']);
        
        // Should have records for all days in the month
        $daysInMonth = Carbon::create($year, $month, 1)->daysInMonth;
        $this->assertCount($daysInMonth, $data['daily_records']);
        
        // Check that first 5 days are marked as present
        for ($i = 0; $i < 5; $i++) {
            $this->assertEquals('present', $data['daily_records'][$i]['status']);
        }
    }

    /**
     * Test timesheet validation errors.
     */
    public function test_timesheet_validation_errors(): void
    {
        // Missing user_id
        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/reports/timesheet?' . http_build_query([
                'month' => 1,
                'year' => 2024,
            ]));

        $response->assertStatus(422);

        // Invalid month
        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/reports/timesheet?' . http_build_query([
                'user_id' => $this->user->id,
                'month' => 13,
                'year' => 2024,
            ]));

        $response->assertStatus(422);

        // Invalid year
        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/reports/timesheet?' . http_build_query([
                'user_id' => $this->user->id,
                'month' => 1,
                'year' => 1999,
            ]));

        $response->assertStatus(422);
    }

    /**
     * Test statistics calculation for late arrivals.
     */
    public function test_statistics_calculation_late_arrivals(): void
    {
        $startDate = Carbon::now()->subDays(5);
        $endDate = Carbon::now();

        // Create attendance with some late arrivals (after 9:00 AM)
        for ($i = 0; $i < 5; $i++) {
            $date = $startDate->copy()->addDays($i);
            $inTime = $i < 2 ? $date->copy()->setTime(9, 30) : $date->copy()->setTime(8, 45);
            
            Attendance::create([
                'id' => (string) Str::uuid(),
                'user_id' => $this->user->id,
                'in_time' => $inTime,
                'out_time' => $date->copy()->setTime(17, 0),
                'worked' => 28800,
            ]);
        }

        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/reports/individual?' . http_build_query([
                'user_id' => $this->user->id,
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
            ]));

        $response->assertStatus(200);
        $data = $response->json('data');
        
        // Should have 2 late arrivals
        $this->assertEquals(2, $data['statistics']['late_arrivals']);
    }

    /**
     * Test statistics calculation for early departures.
     */
    public function test_statistics_calculation_early_departures(): void
    {
        $startDate = Carbon::now()->subDays(5);
        $endDate = Carbon::now();

        // Create attendance with some early departures (before 5:00 PM)
        for ($i = 0; $i < 5; $i++) {
            $date = $startDate->copy()->addDays($i);
            $outTime = $i < 3 ? $date->copy()->setTime(16, 30) : $date->copy()->setTime(17, 30);
            
            Attendance::create([
                'id' => (string) Str::uuid(),
                'user_id' => $this->user->id,
                'in_time' => $date->copy()->setTime(9, 0),
                'out_time' => $outTime,
                'worked' => 28800,
            ]);
        }

        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/reports/individual?' . http_build_query([
                'user_id' => $this->user->id,
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
            ]));

        $response->assertStatus(200);
        $data = $response->json('data');
        
        // Should have 3 early departures
        $this->assertEquals(3, $data['statistics']['early_departures']);
    }

    /**
     * Test statistics calculation for attendance rate.
     */
    public function test_statistics_calculation_attendance_rate(): void
    {
        $startDate = Carbon::now()->subDays(9); // 10 days total
        $endDate = Carbon::now();

        // Create attendance for 7 out of 10 days
        for ($i = 0; $i < 7; $i++) {
            $date = $startDate->copy()->addDays($i);
            Attendance::create([
                'id' => (string) Str::uuid(),
                'user_id' => $this->user->id,
                'in_time' => $date->copy()->setTime(9, 0),
                'out_time' => $date->copy()->setTime(17, 0),
                'worked' => 28800,
            ]);
        }

        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/reports/individual?' . http_build_query([
                'user_id' => $this->user->id,
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
            ]));

        $response->assertStatus(200);
        $data = $response->json('data');
        
        // Attendance rate should be 70% (7 out of 10 days)
        $this->assertEquals(70, $data['statistics']['attendance_rate']);
        $this->assertEquals(7, $data['statistics']['days_present']);
        $this->assertEquals(3, $data['statistics']['days_absent']);
    }

    /**
     * Test statistics calculation for average hours.
     */
    public function test_statistics_calculation_average_hours(): void
    {
        $startDate = Carbon::now()->subDays(4);
        $endDate = Carbon::now();

        // Create attendance with varying hours
        $workedHours = [28800, 32400, 25200, 30600, 27000]; // 8, 9, 7, 8.5, 7.5 hours

        for ($i = 0; $i < 5; $i++) {
            $date = $startDate->copy()->addDays($i);
            Attendance::create([
                'id' => (string) Str::uuid(),
                'user_id' => $this->user->id,
                'in_time' => $date->copy()->setTime(9, 0),
                'out_time' => $date->copy()->setTime(17, 0),
                'worked' => $workedHours[$i],
            ]);
        }

        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/reports/individual?' . http_build_query([
                'user_id' => $this->user->id,
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
            ]));

        $response->assertStatus(200);
        $data = $response->json('data');
        
        // Total hours should be 40
        $this->assertEquals(40, $data['statistics']['total_hours']);
        
        // Average should be 8 hours per day
        $this->assertEquals(8, $data['statistics']['average_hours_per_day']);
    }

    /**
     * Test export validation.
     */
    public function test_export_validation(): void
    {
        // Missing type
        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/reports/export?' . http_build_query([
                'format' => 'pdf',
                'user_id' => $this->user->id,
                'start_date' => Carbon::now()->subDays(5)->format('Y-m-d'),
                'end_date' => Carbon::now()->format('Y-m-d'),
            ]));

        $response->assertStatus(422);

        // Invalid format
        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/reports/export?' . http_build_query([
                'type' => 'individual',
                'format' => 'invalid',
                'user_id' => $this->user->id,
                'start_date' => Carbon::now()->subDays(5)->format('Y-m-d'),
                'end_date' => Carbon::now()->format('Y-m-d'),
            ]));

        $response->assertStatus(422);

        // Missing user_id for individual report
        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/reports/export?' . http_build_query([
                'type' => 'individual',
                'format' => 'pdf',
                'start_date' => Carbon::now()->subDays(5)->format('Y-m-d'),
                'end_date' => Carbon::now()->format('Y-m-d'),
            ]));

        $response->assertStatus(422);
    }

    /**
     * Test user can access their own individual report.
     */
    public function test_user_can_access_own_individual_report(): void
    {
        $startDate = Carbon::now()->subDays(2);
        $endDate = Carbon::now();

        Attendance::create([
            'id' => (string) Str::uuid(),
            'user_id' => $this->user->id,
            'in_time' => $startDate->copy()->setTime(9, 0),
            'out_time' => $startDate->copy()->setTime(17, 0),
            'worked' => 28800,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/reports/individual?' . http_build_query([
                'user_id' => $this->user->id,
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
            ]));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }

    /**
     * Test supervisor can access summary reports.
     */
    public function test_supervisor_can_access_summary_reports(): void
    {
        $startDate = Carbon::now()->subDays(2);
        $endDate = Carbon::now();

        Attendance::create([
            'id' => (string) Str::uuid(),
            'user_id' => $this->user->id,
            'in_time' => $startDate->copy()->setTime(9, 0),
            'out_time' => $startDate->copy()->setTime(17, 0),
            'worked' => 28800,
        ]);

        $response = $this->actingAs($this->supervisor, 'sanctum')
            ->getJson('/api/reports/summary?' . http_build_query([
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
            ]));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }

    /**
     * Test report with no attendance data.
     */
    public function test_report_with_no_attendance_data(): void
    {
        $startDate = Carbon::now()->subDays(5);
        $endDate = Carbon::now();

        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/reports/individual?' . http_build_query([
                'user_id' => $this->user->id,
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
            ]));

        $response->assertStatus(200);
        $data = $response->json('data');
        
        $this->assertEquals(0, $data['statistics']['days_present']);
        $this->assertEquals(0, $data['statistics']['total_hours']);
        $this->assertEmpty($data['attendances']);
    }
}
