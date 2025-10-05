<?php

namespace Tests\Unit\Models;

use App\Models\Attendance;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Leave;
use App\Models\User;
use App\Models\UserLevel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test user belongs to user level relationship.
     */
    public function test_user_belongs_to_user_level(): void
    {
        $userLevel = UserLevel::factory()->create(['name' => 'admin']);
        $user = User::factory()->create(['user_level_id' => $userLevel->id]);

        $this->assertInstanceOf(UserLevel::class, $user->userLevel);
        $this->assertEquals($userLevel->id, $user->userLevel->id);
    }

    /**
     * Test user belongs to department relationship.
     */
    public function test_user_belongs_to_department(): void
    {
        $department = Department::factory()->create();
        $user = User::factory()->create(['department_id' => $department->id]);

        $this->assertInstanceOf(Department::class, $user->department);
        $this->assertEquals($department->id, $user->department->id);
    }

    /**
     * Test user belongs to designation relationship.
     */
    public function test_user_belongs_to_designation(): void
    {
        $designation = Designation::factory()->create();
        $user = User::factory()->create(['designation_id' => $designation->id]);

        $this->assertInstanceOf(Designation::class, $user->designation);
        $this->assertEquals($designation->id, $user->designation->id);
    }

    /**
     * Test user has many attendances relationship.
     */
    public function test_user_has_many_attendances(): void
    {
        $user = User::factory()->create();
        $attendance1 = Attendance::factory()->create(['user_id' => $user->id]);
        $attendance2 = Attendance::factory()->create(['user_id' => $user->id]);

        $this->assertCount(2, $user->attendances);
        $this->assertTrue($user->attendances->contains($attendance1));
        $this->assertTrue($user->attendances->contains($attendance2));
    }

    /**
     * Test user has many leaves relationship.
     */
    public function test_user_has_many_leaves(): void
    {
        $user = User::factory()->create();
        $leave1 = Leave::factory()->create(['user_id' => $user->id]);
        $leave2 = Leave::factory()->create(['user_id' => $user->id]);

        $this->assertCount(2, $user->leaves);
        $this->assertTrue($user->leaves->contains($leave1));
        $this->assertTrue($user->leaves->contains($leave2));
    }

    /**
     * Test user role accessor.
     */
    public function test_user_role_accessor(): void
    {
        $userLevel = UserLevel::factory()->create(['name' => 'admin']);
        $user = User::factory()->create(['user_level_id' => $userLevel->id]);

        $this->assertEquals('ADMIN', $user->role);
    }

    /**
     * Test user soft delete.
     */
    public function test_user_soft_delete(): void
    {
        $user = User::factory()->create();
        $userId = $user->id;

        $user->delete();

        $this->assertSoftDeleted('users', ['id' => $userId]);
        $this->assertNotNull($user->fresh()->deleted_at);
    }

    /**
     * Test user can be restored after soft delete.
     */
    public function test_user_can_be_restored(): void
    {
        $user = User::factory()->create();
        $user->delete();

        $user->restore();

        $this->assertNull($user->fresh()->deleted_at);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'deleted_at' => null,
        ]);
    }
}
