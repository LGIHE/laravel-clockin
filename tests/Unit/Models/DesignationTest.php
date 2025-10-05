<?php

namespace Tests\Unit\Models;

use App\Models\Designation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DesignationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test designation has many users relationship.
     */
    public function test_designation_has_many_users(): void
    {
        $designation = Designation::factory()->create();
        $user1 = User::factory()->create(['designation_id' => $designation->id]);
        $user2 = User::factory()->create(['designation_id' => $designation->id]);

        $this->assertCount(2, $designation->users);
        $this->assertTrue($designation->users->contains($user1));
        $this->assertTrue($designation->users->contains($user2));
    }

    /**
     * Test designation soft delete.
     */
    public function test_designation_soft_delete(): void
    {
        $designation = Designation::factory()->create();
        $designationId = $designation->id;

        $designation->delete();

        $this->assertSoftDeleted('designations', ['id' => $designationId]);
        $this->assertNotNull($designation->fresh()->deleted_at);
    }

    /**
     * Test designation can be restored after soft delete.
     */
    public function test_designation_can_be_restored(): void
    {
        $designation = Designation::factory()->create();
        $designation->delete();

        $designation->restore();

        $this->assertNull($designation->fresh()->deleted_at);
        $this->assertDatabaseHas('designations', [
            'id' => $designation->id,
            'deleted_at' => null,
        ]);
    }
}
