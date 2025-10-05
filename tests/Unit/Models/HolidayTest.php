<?php

namespace Tests\Unit\Models;

use App\Models\Holiday;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HolidayTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test holiday soft delete.
     */
    public function test_holiday_soft_delete(): void
    {
        $holiday = Holiday::factory()->create();
        $holidayId = $holiday->id;

        $holiday->delete();

        $this->assertSoftDeleted('holidays', ['id' => $holidayId]);
        $this->assertNotNull($holiday->fresh()->deleted_at);
    }

    /**
     * Test holiday can be restored after soft delete.
     */
    public function test_holiday_can_be_restored(): void
    {
        $holiday = Holiday::factory()->create();
        $holiday->delete();

        $holiday->restore();

        $this->assertNull($holiday->fresh()->deleted_at);
        $this->assertDatabaseHas('holidays', [
            'id' => $holiday->id,
            'deleted_at' => null,
        ]);
    }
}
