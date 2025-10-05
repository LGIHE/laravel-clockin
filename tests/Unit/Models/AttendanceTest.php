<?php

namespace Tests\Unit\Models;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test attendance belongs to user relationship.
     */
    public function test_attendance_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $attendance->user);
        $this->assertEquals($user->id, $attendance->user->id);
    }

    /**
     * Test worked hours accessor returns correct format.
     */
    public function test_worked_hours_accessor_returns_correct_format(): void
    {
        $attendance = Attendance::factory()->create(['worked' => 3665]); // 1 hour, 1 minute, 5 seconds

        $this->assertEquals('01:01', $attendance->worked_hours);
    }

    /**
     * Test worked hours accessor with zero worked time.
     */
    public function test_worked_hours_accessor_with_zero(): void
    {
        $attendance = Attendance::factory()->create(['worked' => 0]);

        $this->assertEquals('00:00', $attendance->worked_hours);
    }

    /**
     * Test worked hours accessor with full day.
     */
    public function test_worked_hours_accessor_with_full_day(): void
    {
        $attendance = Attendance::factory()->create(['worked' => 28800]); // 8 hours

        $this->assertEquals('08:00', $attendance->worked_hours);
    }

    /**
     * Test attendance soft delete.
     */
    public function test_attendance_soft_delete(): void
    {
        $attendance = Attendance::factory()->create();
        $attendanceId = $attendance->id;

        $attendance->delete();

        $this->assertSoftDeleted('attendances', ['id' => $attendanceId]);
        $this->assertNotNull($attendance->fresh()->deleted_at);
    }

    /**
     * Test attendance can be restored after soft delete.
     */
    public function test_attendance_can_be_restored(): void
    {
        $attendance = Attendance::factory()->create();
        $attendance->delete();

        $attendance->restore();

        $this->assertNull($attendance->fresh()->deleted_at);
        $this->assertDatabaseHas('attendances', [
            'id' => $attendance->id,
            'deleted_at' => null,
        ]);
    }
}
