<?php

namespace Tests\Feature\Security;

use App\Models\Attendance;
use App\Models\Leave;
use App\Models\LeaveStatus;
use App\Models\Project;
use App\Models\User;
use App\Models\UserLevel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorizationPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create user levels
        UserLevel::factory()->create(['id' => '1', 'name' => 'USER']);
        UserLevel::factory()->create(['id' => '2', 'name' => 'SUPERVISOR']);
        UserLevel::factory()->create(['id' => '3', 'name' => 'ADMIN']);

        // Create leave statuses
        LeaveStatus::factory()->create(['id' => '1', 'name' => 'PENDING']);
        LeaveStatus::factory()->create(['id' => '2', 'name' => 'APPROVED']);
        LeaveStatus::factory()->create(['id' => '3', 'name' => 'REJECTED']);
    }

    /**
     * Helper to create a leave with all required relationships.
     */
    protected function createLeave(array $attributes = []): Leave
    {
        return Leave::factory()->create($attributes);
    }

    /**
     * Test user can view their own leave.
     */
    public function test_user_can_view_own_leave(): void
    {
        $user = User::factory()->create(['user_level_id' => '1']);
        $leave = $this->createLeave(['user_id' => $user->id]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson("/api/leaves/{$leave->id}");

        $response->assertSuccessful();
    }

    /**
     * Test user cannot view another user's leave.
     */
    public function test_user_cannot_view_another_users_leave(): void
    {
        $user = User::factory()->create(['user_level_id' => '1']);
        $otherUser = User::factory()->create(['user_level_id' => '1']);
        $leave = $this->createLeave(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson("/api/leaves/{$leave->id}");

        $response->assertStatus(403);
    }

    /**
     * Test supervisor can view team member's leave.
     */
    public function test_supervisor_can_view_team_members_leave(): void
    {
        $supervisor = User::factory()->create(['user_level_id' => '2']);
        $teamMember = User::factory()->create([
            'user_level_id' => '1',
            'supervisor_id' => $supervisor->id,
        ]);
        $leave = $this->createLeave(['user_id' => $teamMember->id]);

        $response = $this->actingAs($supervisor, 'sanctum')
            ->getJson("/api/leaves/{$leave->id}");

        $response->assertSuccessful();
    }

    /**
     * Test admin can view any leave.
     */
    public function test_admin_can_view_any_leave(): void
    {
        $admin = User::factory()->create(['user_level_id' => '3']);
        $user = User::factory()->create(['user_level_id' => '1']);
        $leave = $this->createLeave(['user_id' => $user->id]);

        $response = $this->actingAs($admin, 'sanctum')
            ->getJson("/api/leaves/{$leave->id}");

        $response->assertSuccessful();
    }

    /**
     * Test user cannot approve their own leave.
     */
    public function test_user_cannot_approve_own_leave(): void
    {
        $user = User::factory()->create(['user_level_id' => '1']);
        $leave = $this->createLeave([
            'user_id' => $user->id,
            'leave_status_id' => '1', // PENDING
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->putJson("/api/leaves/{$leave->id}/approve", [
                'comments' => 'Approved',
            ]);

        $response->assertStatus(403);
    }

    /**
     * Test supervisor can approve team member's leave.
     */
    public function test_supervisor_can_approve_team_members_leave(): void
    {
        $supervisor = User::factory()->create(['user_level_id' => '2']);
        $teamMember = User::factory()->create([
            'user_level_id' => '1',
            'supervisor_id' => $supervisor->id,
        ]);
        $leave = $this->createLeave([
            'user_id' => $teamMember->id,
            'leave_status_id' => '1', // PENDING
        ]);

        $response = $this->actingAs($supervisor, 'sanctum')
            ->putJson("/api/leaves/{$leave->id}/approve", [
                'comments' => 'Approved',
            ]);

        // Should not be forbidden (403) - may fail for other reasons (400) but not authorization
        $this->assertNotEquals(403, $response->status(), 'Supervisor should be authorized to approve team member leave');
    }

    /**
     * Test only admin can update attendance records.
     */
    public function test_only_admin_can_update_attendance(): void
    {
        $user = User::factory()->create(['user_level_id' => '1']);
        $attendance = Attendance::factory()->create(['user_id' => $user->id]);

        // User tries to update their own attendance
        $response = $this->actingAs($user, 'sanctum')
            ->putJson("/api/attendance/{$attendance->id}", [
                'in_time' => now()->toDateTimeString(),
            ]);

        $response->assertStatus(403);

        // Admin can update attendance
        $admin = User::factory()->create(['user_level_id' => '3']);
        $response = $this->actingAs($admin, 'sanctum')
            ->putJson("/api/attendance/{$attendance->id}", [
                'in_time' => now()->toDateTimeString(),
            ]);

        $response->assertSuccessful();
    }

    /**
     * Test only admin can create projects.
     */
    public function test_only_admin_can_create_projects(): void
    {
        $user = User::factory()->create(['user_level_id' => '1']);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/projects', [
                'name' => 'Test Project',
                'description' => 'Test Description',
                'start_date' => now()->toDateString(),
                'status' => 'ACTIVE',
            ]);

        $response->assertStatus(403);

        // Admin can create projects
        $admin = User::factory()->create(['user_level_id' => '3']);
        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/projects', [
                'name' => 'Test Project',
                'description' => 'Test Description',
                'start_date' => now()->toDateString(),
                'status' => 'ACTIVE',
            ]);

        $response->assertSuccessful();
    }

    /**
     * Test only admin can delete users.
     */
    public function test_only_admin_can_delete_users(): void
    {
        $user = User::factory()->create(['user_level_id' => '1']);
        $targetUser = User::factory()->create(['user_level_id' => '1']);

        // User tries to delete another user
        $response = $this->actingAs($user, 'sanctum')
            ->deleteJson("/api/users/{$targetUser->id}");

        $response->assertStatus(403);

        // Admin can delete users
        $admin = User::factory()->create(['user_level_id' => '3']);
        $response = $this->actingAs($admin, 'sanctum')
            ->deleteJson("/api/users/{$targetUser->id}");

        $response->assertSuccessful();
    }

    /**
     * Test admin cannot delete themselves (policy check).
     */
    public function test_admin_cannot_delete_themselves(): void
    {
        $admin = User::factory()->create(['user_level_id' => '3']);

        // Test the policy directly
        $this->assertFalse($admin->can('delete', $admin), 'Admin should not be able to delete themselves according to policy');
    }
}
