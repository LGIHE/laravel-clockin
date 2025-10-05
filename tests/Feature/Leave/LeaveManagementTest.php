<?php

namespace Tests\Feature\Leave;

use App\Models\Leave;
use App\Models\LeaveCategory;
use App\Models\LeaveStatus;
use App\Models\User;
use App\Models\UserLevel;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class LeaveManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected User $supervisor;
    protected User $admin;
    protected UserLevel $userLevel;
    protected UserLevel $supervisorLevel;
    protected UserLevel $adminLevel;
    protected LeaveCategory $leaveCategory;
    protected LeaveStatus $pendingStatus;
    protected LeaveStatus $approvedStatus;
    protected LeaveStatus $rejectedStatus;

    protected function setUp(): void
    {
        parent::setUp();

        // Create user levels
        $this->userLevel = UserLevel::create([
            'id' => (string) Str::uuid(),
            'name' => 'user',
        ]);

        $this->supervisorLevel = UserLevel::create([
            'id' => (string) Str::uuid(),
            'name' => 'supervisor',
        ]);

        $this->adminLevel = UserLevel::create([
            'id' => (string) Str::uuid(),
            'name' => 'admin',
        ]);

        // Create leave statuses
        $this->pendingStatus = LeaveStatus::create([
            'id' => (string) Str::uuid(),
            'name' => 'pending',
        ]);

        $this->approvedStatus = LeaveStatus::create([
            'id' => (string) Str::uuid(),
            'name' => 'approved',
        ]);

        $this->rejectedStatus = LeaveStatus::create([
            'id' => (string) Str::uuid(),
            'name' => 'rejected',
        ]);

        // Create leave category
        $this->leaveCategory = LeaveCategory::create([
            'id' => (string) Str::uuid(),
            'name' => 'Annual Leave',
            'max_in_year' => 10,
        ]);

        // Create supervisor
        $this->supervisor = User::factory()->create([
            'user_level_id' => $this->supervisorLevel->id,
            'status' => 1,
        ]);

        // Create regular user
        $this->user = User::factory()->create([
            'user_level_id' => $this->userLevel->id,
            'supervisor_id' => $this->supervisor->id,
            'status' => 1,
        ]);

        // Create admin user
        $this->admin = User::factory()->create([
            'user_level_id' => $this->adminLevel->id,
            'status' => 1,
        ]);
    }

    /**
     * Test leave application.
     */
    public function test_user_can_apply_for_leave(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/leaves', [
                'leave_category_id' => $this->leaveCategory->id,
                'date' => Carbon::tomorrow()->format('Y-m-d'),
                'description' => 'Personal reasons',
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Leave application submitted successfully',
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'user_id',
                    'leave_category_id',
                    'leave_status_id',
                    'date',
                    'description',
                ],
            ]);

        $this->assertDatabaseHas('leaves', [
            'user_id' => $this->user->id,
            'leave_category_id' => $this->leaveCategory->id,
            'leave_status_id' => $this->pendingStatus->id,
            'description' => 'Personal reasons',
        ]);
    }

    /**
     * Test leave application validation.
     */
    public function test_leave_application_requires_valid_data(): void
    {
        // Missing required fields
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/leaves', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['leave_category_id', 'date']);

        // Invalid category
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/leaves', [
                'leave_category_id' => 'invalid-id',
                'date' => Carbon::tomorrow()->format('Y-m-d'),
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['leave_category_id']);

        // Past date
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/leaves', [
                'leave_category_id' => $this->leaveCategory->id,
                'date' => Carbon::yesterday()->format('Y-m-d'),
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['date']);
    }

    /**
     * Test leave approval by supervisor.
     */
    public function test_supervisor_can_approve_leave(): void
    {
        $leave = Leave::create([
            'id' => (string) Str::uuid(),
            'user_id' => $this->user->id,
            'leave_category_id' => $this->leaveCategory->id,
            'leave_status_id' => $this->pendingStatus->id,
            'date' => Carbon::tomorrow(),
            'description' => 'Personal reasons',
        ]);

        $response = $this->actingAs($this->supervisor, 'sanctum')
            ->putJson("/api/leaves/{$leave->id}/approve", [
                'comments' => 'Approved by supervisor',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Leave approved successfully',
            ]);

        $leave->refresh();
        $this->assertEquals($this->approvedStatus->id, $leave->leave_status_id);

        // Check notification was created
        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $this->user->id,
            'type' => 'leave_approved',
        ]);
    }

    /**
     * Test leave approval by admin.
     */
    public function test_admin_can_approve_leave(): void
    {
        $leave = Leave::create([
            'id' => (string) Str::uuid(),
            'user_id' => $this->user->id,
            'leave_category_id' => $this->leaveCategory->id,
            'leave_status_id' => $this->pendingStatus->id,
            'date' => Carbon::tomorrow(),
            'description' => 'Personal reasons',
        ]);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->putJson("/api/leaves/{$leave->id}/approve", [
                'comments' => 'Approved by admin',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Leave approved successfully',
            ]);

        $leave->refresh();
        $this->assertEquals($this->approvedStatus->id, $leave->leave_status_id);
    }

    /**
     * Test leave rejection with comments.
     */
    public function test_supervisor_can_reject_leave_with_comments(): void
    {
        $leave = Leave::create([
            'id' => (string) Str::uuid(),
            'user_id' => $this->user->id,
            'leave_category_id' => $this->leaveCategory->id,
            'leave_status_id' => $this->pendingStatus->id,
            'date' => Carbon::tomorrow(),
            'description' => 'Personal reasons',
        ]);

        $response = $this->actingAs($this->supervisor, 'sanctum')
            ->putJson("/api/leaves/{$leave->id}/reject", [
                'comments' => 'Not enough staff available',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Leave rejected successfully',
            ]);

        $leave->refresh();
        $this->assertEquals($this->rejectedStatus->id, $leave->leave_status_id);

        // Check notification was created with comments
        $notification = \DB::table('notifications')
            ->where('notifiable_id', $this->user->id)
            ->where('type', 'leave_rejected')
            ->first();

        $this->assertNotNull($notification);
        $data = json_decode($notification->data, true);
        $this->assertEquals('Not enough staff available', $data['comments']);
    }

    /**
     * Test leave limit validation.
     */
    public function test_leave_limit_validation(): void
    {
        // Create max allowed leaves (10) for the year
        for ($i = 0; $i < 10; $i++) {
            Leave::create([
                'id' => (string) Str::uuid(),
                'user_id' => $this->user->id,
                'leave_category_id' => $this->leaveCategory->id,
                'leave_status_id' => $this->approvedStatus->id,
                'date' => Carbon::now()->addDays($i + 1),
            ]);
        }

        // Try to apply for one more leave
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/leaves', [
                'leave_category_id' => $this->leaveCategory->id,
                'date' => Carbon::now()->addDays(15)->format('Y-m-d'),
                'description' => 'Exceeding limit',
            ]);

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'error' => [
                    'code' => 'LEAVE_APPLICATION_FAILED',
                ],
            ]);

        $this->assertStringContainsString('Leave limit exceeded', $response->json('error.message'));
    }

    /**
     * Test leave cancellation (deletion).
     */
    public function test_user_can_cancel_pending_leave(): void
    {
        $leave = Leave::create([
            'id' => (string) Str::uuid(),
            'user_id' => $this->user->id,
            'leave_category_id' => $this->leaveCategory->id,
            'leave_status_id' => $this->pendingStatus->id,
            'date' => Carbon::tomorrow(),
            'description' => 'Personal reasons',
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/leaves/{$leave->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Leave deleted successfully',
            ]);

        $this->assertSoftDeleted('leaves', [
            'id' => $leave->id,
        ]);
    }

    /**
     * Test user cannot cancel approved leave.
     */
    public function test_user_cannot_cancel_approved_leave(): void
    {
        $leave = Leave::create([
            'id' => (string) Str::uuid(),
            'user_id' => $this->user->id,
            'leave_category_id' => $this->leaveCategory->id,
            'leave_status_id' => $this->approvedStatus->id,
            'date' => Carbon::tomorrow(),
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/leaves/{$leave->id}");

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'error' => [
                    'code' => 'LEAVE_NOT_PENDING',
                    'message' => 'Only pending leaves can be deleted',
                ],
            ]);
    }

    /**
     * Test unauthorized approval attempt.
     */
    public function test_regular_user_cannot_approve_leave(): void
    {
        $otherUser = User::factory()->create([
            'user_level_id' => $this->userLevel->id,
            'status' => 1,
        ]);

        $leave = Leave::create([
            'id' => (string) Str::uuid(),
            'user_id' => $otherUser->id,
            'leave_category_id' => $this->leaveCategory->id,
            'leave_status_id' => $this->pendingStatus->id,
            'date' => Carbon::tomorrow(),
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/leaves/{$leave->id}/approve");

        $response->assertStatus(403);
    }

    /**
     * Test user can update pending leave.
     */
    public function test_user_can_update_pending_leave(): void
    {
        $leave = Leave::create([
            'id' => (string) Str::uuid(),
            'user_id' => $this->user->id,
            'leave_category_id' => $this->leaveCategory->id,
            'leave_status_id' => $this->pendingStatus->id,
            'date' => Carbon::tomorrow(),
            'description' => 'Original description',
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/leaves/{$leave->id}", [
                'description' => 'Updated description',
                'date' => Carbon::now()->addDays(2)->format('Y-m-d'),
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Leave updated successfully',
            ]);

        $leave->refresh();
        $this->assertEquals('Updated description', $leave->description);
    }

    /**
     * Test user cannot update approved leave.
     */
    public function test_user_cannot_update_approved_leave(): void
    {
        $leave = Leave::create([
            'id' => (string) Str::uuid(),
            'user_id' => $this->user->id,
            'leave_category_id' => $this->leaveCategory->id,
            'leave_status_id' => $this->approvedStatus->id,
            'date' => Carbon::tomorrow(),
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/leaves/{$leave->id}", [
                'description' => 'Trying to update',
            ]);

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'error' => [
                    'code' => 'LEAVE_NOT_PENDING',
                ],
            ]);
    }

    /**
     * Test user cannot update another user's leave.
     */
    public function test_user_cannot_update_another_users_leave(): void
    {
        $otherUser = User::factory()->create([
            'user_level_id' => $this->userLevel->id,
            'status' => 1,
        ]);

        $leave = Leave::create([
            'id' => (string) Str::uuid(),
            'user_id' => $otherUser->id,
            'leave_category_id' => $this->leaveCategory->id,
            'leave_status_id' => $this->pendingStatus->id,
            'date' => Carbon::tomorrow(),
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/leaves/{$leave->id}", [
                'description' => 'Trying to update',
            ]);

        $response->assertStatus(403);
    }

    /**
     * Test user can view their own leaves.
     */
    public function test_user_can_view_their_own_leaves(): void
    {
        // Create leaves for the user
        Leave::create([
            'id' => (string) Str::uuid(),
            'user_id' => $this->user->id,
            'leave_category_id' => $this->leaveCategory->id,
            'leave_status_id' => $this->pendingStatus->id,
            'date' => Carbon::tomorrow(),
        ]);

        Leave::create([
            'id' => (string) Str::uuid(),
            'user_id' => $this->user->id,
            'leave_category_id' => $this->leaveCategory->id,
            'leave_status_id' => $this->approvedStatus->id,
            'date' => Carbon::now()->addDays(2),
        ]);

        // Create leave for another user
        $otherUser = User::factory()->create([
            'user_level_id' => $this->userLevel->id,
            'status' => 1,
        ]);

        Leave::create([
            'id' => (string) Str::uuid(),
            'user_id' => $otherUser->id,
            'leave_category_id' => $this->leaveCategory->id,
            'leave_status_id' => $this->pendingStatus->id,
            'date' => Carbon::tomorrow(),
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/leaves');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data',
                'meta',
            ]);

        $data = $response->json('data');
        $this->assertCount(2, $data);
        
        // Verify all leaves belong to the user
        foreach ($data as $leave) {
            $this->assertEquals($this->user->id, $leave['user_id']);
        }
    }

    /**
     * Test supervisor can view team leaves.
     */
    public function test_supervisor_can_view_team_leaves(): void
    {
        // Create leave for supervised user
        Leave::create([
            'id' => (string) Str::uuid(),
            'user_id' => $this->user->id,
            'leave_category_id' => $this->leaveCategory->id,
            'leave_status_id' => $this->pendingStatus->id,
            'date' => Carbon::tomorrow(),
        ]);

        // Create leave for another user not supervised
        $otherUser = User::factory()->create([
            'user_level_id' => $this->userLevel->id,
            'supervisor_id' => null,
            'status' => 1,
        ]);

        Leave::create([
            'id' => (string) Str::uuid(),
            'user_id' => $otherUser->id,
            'leave_category_id' => $this->leaveCategory->id,
            'leave_status_id' => $this->pendingStatus->id,
            'date' => Carbon::tomorrow(),
        ]);

        $response = $this->actingAs($this->supervisor, 'sanctum')
            ->getJson('/api/leaves');

        $response->assertStatus(200);

        $data = $response->json('data');
        // Supervisor should only see their team's leaves
        $this->assertCount(1, $data);
        $this->assertEquals($this->user->id, $data[0]['user_id']);
    }

    /**
     * Test admin can view all leaves.
     */
    public function test_admin_can_view_all_leaves(): void
    {
        // Create leaves for different users
        Leave::create([
            'id' => (string) Str::uuid(),
            'user_id' => $this->user->id,
            'leave_category_id' => $this->leaveCategory->id,
            'leave_status_id' => $this->pendingStatus->id,
            'date' => Carbon::tomorrow(),
        ]);

        $otherUser = User::factory()->create([
            'user_level_id' => $this->userLevel->id,
            'status' => 1,
        ]);

        Leave::create([
            'id' => (string) Str::uuid(),
            'user_id' => $otherUser->id,
            'leave_category_id' => $this->leaveCategory->id,
            'leave_status_id' => $this->pendingStatus->id,
            'date' => Carbon::tomorrow(),
        ]);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/leaves');

        $response->assertStatus(200);

        $data = $response->json('data');
        // Admin should see all leaves
        $this->assertCount(2, $data);
    }

    /**
     * Test leave filtering by status.
     */
    public function test_leave_filtering_by_status(): void
    {
        // Create leaves with different statuses
        Leave::create([
            'id' => (string) Str::uuid(),
            'user_id' => $this->user->id,
            'leave_category_id' => $this->leaveCategory->id,
            'leave_status_id' => $this->pendingStatus->id,
            'date' => Carbon::tomorrow(),
        ]);

        Leave::create([
            'id' => (string) Str::uuid(),
            'user_id' => $this->user->id,
            'leave_category_id' => $this->leaveCategory->id,
            'leave_status_id' => $this->approvedStatus->id,
            'date' => Carbon::now()->addDays(2),
        ]);

        Leave::create([
            'id' => (string) Str::uuid(),
            'user_id' => $this->user->id,
            'leave_category_id' => $this->leaveCategory->id,
            'leave_status_id' => $this->rejectedStatus->id,
            'date' => Carbon::now()->addDays(3),
        ]);

        // Filter by pending status
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/leaves?status=pending');

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals('pending', $data[0]['status']['name']);

        // Filter by approved status
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/leaves?status=approved');

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals('approved', $data[0]['status']['name']);
    }

    /**
     * Test cannot approve already approved leave.
     */
    public function test_cannot_approve_already_approved_leave(): void
    {
        $leave = Leave::create([
            'id' => (string) Str::uuid(),
            'user_id' => $this->user->id,
            'leave_category_id' => $this->leaveCategory->id,
            'leave_status_id' => $this->approvedStatus->id,
            'date' => Carbon::tomorrow(),
        ]);

        $response = $this->actingAs($this->supervisor, 'sanctum')
            ->putJson("/api/leaves/{$leave->id}/approve");

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'error' => [
                    'code' => 'LEAVE_APPROVAL_FAILED',
                    'message' => 'Only pending leaves can be approved',
                ],
            ]);
    }

    /**
     * Test cannot reject already rejected leave.
     */
    public function test_cannot_reject_already_rejected_leave(): void
    {
        $leave = Leave::create([
            'id' => (string) Str::uuid(),
            'user_id' => $this->user->id,
            'leave_category_id' => $this->leaveCategory->id,
            'leave_status_id' => $this->rejectedStatus->id,
            'date' => Carbon::tomorrow(),
        ]);

        $response = $this->actingAs($this->supervisor, 'sanctum')
            ->putJson("/api/leaves/{$leave->id}/reject");

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'error' => [
                    'code' => 'LEAVE_REJECTION_FAILED',
                    'message' => 'Only pending leaves can be rejected',
                ],
            ]);
    }
}

