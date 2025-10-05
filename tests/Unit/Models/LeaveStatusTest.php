<?php

namespace Tests\Unit\Models;

use App\Models\Leave;
use App\Models\LeaveStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeaveStatusTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test leave status has many leaves relationship.
     */
    public function test_leave_status_has_many_leaves(): void
    {
        $status = LeaveStatus::factory()->create();
        $leave1 = Leave::factory()->create(['leave_status_id' => $status->id]);
        $leave2 = Leave::factory()->create(['leave_status_id' => $status->id]);

        $this->assertCount(2, $status->leaves);
        $this->assertTrue($status->leaves->contains($leave1));
        $this->assertTrue($status->leaves->contains($leave2));
    }
}
