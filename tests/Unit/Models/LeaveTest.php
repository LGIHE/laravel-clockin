<?php

namespace Tests\Unit\Models;

use App\Models\Leave;
use App\Models\LeaveCategory;
use App\Models\LeaveStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeaveTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test leave belongs to user relationship.
     */
    public function test_leave_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $leave = Leave::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $leave->user);
        $this->assertEquals($user->id, $leave->user->id);
    }

    /**
     * Test leave belongs to category relationship.
     */
    public function test_leave_belongs_to_category(): void
    {
        $category = LeaveCategory::factory()->create();
        $leave = Leave::factory()->create(['leave_category_id' => $category->id]);

        $this->assertInstanceOf(LeaveCategory::class, $leave->category);
        $this->assertEquals($category->id, $leave->category->id);
    }

    /**
     * Test leave belongs to status relationship.
     */
    public function test_leave_belongs_to_status(): void
    {
        $status = LeaveStatus::factory()->create();
        $leave = Leave::factory()->create(['leave_status_id' => $status->id]);

        $this->assertInstanceOf(LeaveStatus::class, $leave->status);
        $this->assertEquals($status->id, $leave->status->id);
    }

    /**
     * Test leave soft delete.
     */
    public function test_leave_soft_delete(): void
    {
        $leave = Leave::factory()->create();
        $leaveId = $leave->id;

        $leave->delete();

        $this->assertSoftDeleted('leaves', ['id' => $leaveId]);
        $this->assertNotNull($leave->fresh()->deleted_at);
    }

    /**
     * Test leave can be restored after soft delete.
     */
    public function test_leave_can_be_restored(): void
    {
        $leave = Leave::factory()->create();
        $leave->delete();

        $leave->restore();

        $this->assertNull($leave->fresh()->deleted_at);
        $this->assertDatabaseHas('leaves', [
            'id' => $leave->id,
            'deleted_at' => null,
        ]);
    }
}
