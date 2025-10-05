<?php

namespace Tests\Unit\Models;

use App\Models\Leave;
use App\Models\LeaveCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeaveCategoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test leave category has many leaves relationship.
     */
    public function test_leave_category_has_many_leaves(): void
    {
        $category = LeaveCategory::factory()->create();
        $leave1 = Leave::factory()->create(['leave_category_id' => $category->id]);
        $leave2 = Leave::factory()->create(['leave_category_id' => $category->id]);

        $this->assertCount(2, $category->leaves);
        $this->assertTrue($category->leaves->contains($leave1));
        $this->assertTrue($category->leaves->contains($leave2));
    }

    /**
     * Test leave category soft delete.
     */
    public function test_leave_category_soft_delete(): void
    {
        $category = LeaveCategory::factory()->create();
        $categoryId = $category->id;

        $category->delete();

        $this->assertSoftDeleted('leave_categories', ['id' => $categoryId]);
        $this->assertNotNull($category->fresh()->deleted_at);
    }

    /**
     * Test leave category can be restored after soft delete.
     */
    public function test_leave_category_can_be_restored(): void
    {
        $category = LeaveCategory::factory()->create();
        $category->delete();

        $category->restore();

        $this->assertNull($category->fresh()->deleted_at);
        $this->assertDatabaseHas('leave_categories', [
            'id' => $category->id,
            'deleted_at' => null,
        ]);
    }
}
