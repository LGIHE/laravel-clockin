<?php

namespace Tests\Feature\Attendance;

use App\Models\Attendance;
use App\Models\User;
use App\Models\UserLevel;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class AttendanceTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected User $admin;
    protected UserLevel $userLevel;
    protected UserLevel $adminLevel;

    protected function setUp(): void
    {
        parent::setUp();

        // Create user levels
        $this->userLevel = UserLevel::create([
            'id' => (string) Str::uuid(),
            'name' => 'user',
        ]);

        $this->adminLevel = UserLevel::create([
            'id' => (string) Str::uuid(),
            'name' => 'admin',
        ]);

        // Create regular user
        $this->user = User::factory()->create([
            'user_level_id' => $this->userLevel->id,
            'status' => 1,
        ]);

        // Create admin user
        $this->admin = User::factory()->create([
            'user_level_id' => $this->adminLevel->id,
            'status' => 1,
        ]);
    }

    /**
     * Test successful clock-in.
     */
    public function test_user_can_clock_in(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/attendance/clock-in', [
                'message' => 'Starting work',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Clocked in successfully',
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'user_id',
                    'in_time',
                    'in_message',
                ],
            ]);

        $this->assertDatabaseHas('attendances', [
            'user_id' => $this->user->id,
            'in_message' => 'Starting work',
        ]);
    }

    /**
     * Test prevention of double clock-in.
     */
    public function test_user_cannot_clock_in_twice(): void
    {
        // First clock-in
        Attendance::create([
            'id' => (string) Str::uuid(),
            'user_id' => $this->user->id,
            'in_time' => Carbon::now(),
            'in_message' => 'First clock in',
        ]);

        // Attempt second clock-in
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/attendance/clock-in', [
                'message' => 'Second clock in',
            ]);

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'error' => [
                    'code' => 'CLOCK_IN_ERROR',
                    'message' => 'User is already clocked in. Please clock out first.',
                ],
            ]);
    }

    /**
     * Test successful clock-out.
     */
    public function test_user_can_clock_out(): void
    {
        // First clock-in
        $attendance = Attendance::create([
            'id' => (string) Str::uuid(),
            'user_id' => $this->user->id,
            'in_time' => Carbon::now()->subHours(2),
            'in_message' => 'Starting work',
        ]);

        // Clock-out
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/attendance/clock-out', [
                'message' => 'Ending work',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Clocked out successfully',
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'user_id',
                    'in_time',
                    'out_time',
                    'out_message',
                    'worked',
                ],
            ]);

        $attendance->refresh();
        $this->assertNotNull($attendance->out_time);
        $this->assertEquals('Ending work', $attendance->out_message);
        $this->assertGreaterThan(0, $attendance->worked);
    }

    /**
     * Test prevention of clock-out without clock-in.
     */
    public function test_user_cannot_clock_out_without_clock_in(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/attendance/clock-out', [
                'message' => 'Ending work',
            ]);

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'error' => [
                    'code' => 'CLOCK_OUT_ERROR',
                    'message' => 'User is not clocked in. Please clock in first.',
                ],
            ]);
    }

    /**
     * Test worked hours calculation.
     */
    public function test_worked_hours_calculation(): void
    {
        // Clock-in
        $inTime = Carbon::now()->subHours(3)->subMinutes(30);
        $attendance = Attendance::create([
            'id' => (string) Str::uuid(),
            'user_id' => $this->user->id,
            'in_time' => $inTime,
            'in_message' => 'Starting work',
        ]);

        // Clock-out
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/attendance/clock-out', [
                'message' => 'Ending work',
            ]);

        $response->assertStatus(200);

        $attendance->refresh();
        
        // Verify worked hours is approximately 3.5 hours (12600 seconds)
        // Allow for small time differences in test execution
        $this->assertGreaterThanOrEqual(12590, $attendance->worked);
        $this->assertLessThanOrEqual(12610, $attendance->worked);
    }

    /**
     * Test force punch by admin (clock in).
     */
    public function test_admin_can_force_punch_clock_in(): void
    {
        $targetUser = User::factory()->create([
            'user_level_id' => $this->userLevel->id,
            'status' => 1,
        ]);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/attendance/force-punch', [
                'user_id' => $targetUser->id,
                'type' => 'in',
                'time' => Carbon::now()->subHours(1)->toISOString(),
                'message' => 'Force punched by admin',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Force punch completed successfully',
            ]);

        $this->assertDatabaseHas('attendances', [
            'user_id' => $targetUser->id,
            'in_message' => 'Force punched by admin',
        ]);
    }

    /**
     * Test force punch by admin (clock out).
     */
    public function test_admin_can_force_punch_clock_out(): void
    {
        $targetUser = User::factory()->create([
            'user_level_id' => $this->userLevel->id,
            'status' => 1,
        ]);

        // First create a clock-in record
        Attendance::create([
            'id' => (string) Str::uuid(),
            'user_id' => $targetUser->id,
            'in_time' => Carbon::now()->subHours(2),
            'in_message' => 'Starting work',
        ]);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/attendance/force-punch', [
                'user_id' => $targetUser->id,
                'type' => 'out',
                'time' => Carbon::now()->toISOString(),
                'message' => 'Force punched out by admin',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Force punch completed successfully',
            ]);

        $attendance = Attendance::where('user_id', $targetUser->id)->first();
        $this->assertNotNull($attendance->out_time);
        $this->assertEquals('Force punched out by admin', $attendance->out_message);
    }

    /**
     * Test non-admin cannot force punch.
     */
    public function test_non_admin_cannot_force_punch(): void
    {
        $targetUser = User::factory()->create([
            'user_level_id' => $this->userLevel->id,
            'status' => 1,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/attendance/force-punch', [
                'user_id' => $targetUser->id,
                'type' => 'in',
                'time' => Carbon::now()->toISOString(),
                'message' => 'Attempting force punch',
            ]);

        $response->assertStatus(403);
    }

    /**
     * Test attendance record retrieval with filters.
     */
    public function test_attendance_record_retrieval_with_filters(): void
    {
        // Create multiple attendance records
        $otherUser = User::factory()->create([
            'user_level_id' => $this->userLevel->id,
            'status' => 1,
        ]);

        // User's attendance
        Attendance::create([
            'id' => (string) Str::uuid(),
            'user_id' => $this->user->id,
            'in_time' => Carbon::now()->subDays(2),
            'out_time' => Carbon::now()->subDays(2)->addHours(8),
            'worked' => 28800,
        ]);

        Attendance::create([
            'id' => (string) Str::uuid(),
            'user_id' => $this->user->id,
            'in_time' => Carbon::now()->subDay(),
            'out_time' => Carbon::now()->subDay()->addHours(7),
            'worked' => 25200,
        ]);

        // Other user's attendance
        Attendance::create([
            'id' => (string) Str::uuid(),
            'user_id' => $otherUser->id,
            'in_time' => Carbon::now()->subDay(),
            'out_time' => Carbon::now()->subDay()->addHours(6),
            'worked' => 21600,
        ]);

        // Test: Get all attendance records
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/attendance');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data',
                'meta' => [
                    'current_page',
                    'per_page',
                    'total',
                    'last_page',
                ],
            ]);

        // Test: Filter by user_id
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/attendance?user_id=' . $this->user->id);

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertCount(2, $data);

        // Test: Filter by date range
        $startDate = Carbon::now()->subDays(2)->format('Y-m-d');
        $endDate = Carbon::now()->subDays(2)->format('Y-m-d');
        
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/attendance?user_id={$this->user->id}&start_date={$startDate}&end_date={$endDate}");

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertCount(1, $data);
    }

    /**
     * Test get attendance status when clocked in.
     */
    public function test_get_attendance_status_when_clocked_in(): void
    {
        // Clock in
        Attendance::create([
            'id' => (string) Str::uuid(),
            'user_id' => $this->user->id,
            'in_time' => Carbon::now()->subHours(1),
            'in_message' => 'Starting work',
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/attendance/status');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'clocked_in' => true,
                ],
            ])
            ->assertJsonStructure([
                'success',
                'data' => [
                    'clocked_in',
                    'in_time',
                    'duration',
                    'attendance',
                ],
            ]);
    }

    /**
     * Test get attendance status when not clocked in.
     */
    public function test_get_attendance_status_when_not_clocked_in(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/attendance/status');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'clocked_in' => false,
                    'attendance' => null,
                ],
            ]);
    }

    /**
     * Test admin can update attendance record.
     */
    public function test_admin_can_update_attendance_record(): void
    {
        $attendance = Attendance::create([
            'id' => (string) Str::uuid(),
            'user_id' => $this->user->id,
            'in_time' => Carbon::now()->subHours(8),
            'in_message' => 'Original message',
            'out_time' => Carbon::now(),
            'worked' => 28800,
        ]);

        $newInTime = Carbon::now()->subHours(9)->toISOString();
        $newOutTime = Carbon::now()->subMinutes(30)->toISOString();

        $response = $this->actingAs($this->admin, 'sanctum')
            ->putJson("/api/attendance/{$attendance->id}", [
                'in_time' => $newInTime,
                'in_message' => 'Updated message',
                'out_time' => $newOutTime,
                'out_message' => 'Updated out message',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Attendance record updated successfully',
            ]);

        $attendance->refresh();
        $this->assertEquals('Updated message', $attendance->in_message);
        $this->assertEquals('Updated out message', $attendance->out_message);
    }

    /**
     * Test admin can delete attendance record.
     */
    public function test_admin_can_delete_attendance_record(): void
    {
        $attendance = Attendance::create([
            'id' => (string) Str::uuid(),
            'user_id' => $this->user->id,
            'in_time' => Carbon::now()->subHours(8),
            'out_time' => Carbon::now(),
            'worked' => 28800,
        ]);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->deleteJson("/api/attendance/{$attendance->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Attendance record deleted successfully',
            ]);

        $this->assertSoftDeleted('attendances', [
            'id' => $attendance->id,
        ]);
    }

    /**
     * Test non-admin cannot update attendance record.
     */
    public function test_non_admin_cannot_update_attendance_record(): void
    {
        $attendance = Attendance::create([
            'id' => (string) Str::uuid(),
            'user_id' => $this->user->id,
            'in_time' => Carbon::now()->subHours(8),
            'out_time' => Carbon::now(),
            'worked' => 28800,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/attendance/{$attendance->id}", [
                'in_message' => 'Trying to update',
            ]);

        $response->assertStatus(403);
    }

    /**
     * Test non-admin cannot delete attendance record.
     */
    public function test_non_admin_cannot_delete_attendance_record(): void
    {
        $attendance = Attendance::create([
            'id' => (string) Str::uuid(),
            'user_id' => $this->user->id,
            'in_time' => Carbon::now()->subHours(8),
            'out_time' => Carbon::now(),
            'worked' => 28800,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/attendance/{$attendance->id}");

        $response->assertStatus(403);
    }
}
