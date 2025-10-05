<?php

namespace Tests\Unit\Models;

use App\Models\Department;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DepartmentTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test department has many users relationship.
     */
    public function test_department_has_many_users(): void
    {
        $department = Department::factory()->create();
        $user1 = User::factory()->create(['department_id' => $department->id]);
        $user2 = User::factory()->create(['department_id' => $department->id]);

        $this->assertCount(2, $department->users);
        $this->assertTrue($department->users->contains($user1));
        $this->assertTrue($department->users->contains($user2));
    }

    /**
     * Test department soft delete.
     */
    public function test_department_soft_delete(): void
    {
        $department = Department::factory()->create();
        $departmentId = $department->id;

        $department->delete();

        $this->assertSoftDeleted('departments', ['id' => $departmentId]);
        $this->assertNotNull($department->fresh()->deleted_at);
    }

    /**
     * Test department can be restored after soft delete.
     */
    public function test_department_can_be_restored(): void
    {
        $department = Department::factory()->create();
        $department->delete();

        $department->restore();

        $this->assertNull($department->fresh()->deleted_at);
        $this->assertDatabaseHas('departments', [
            'id' => $department->id,
            'deleted_at' => null,
        ]);
    }
}
