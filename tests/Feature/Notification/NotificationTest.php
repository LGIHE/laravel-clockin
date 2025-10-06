<?php

namespace Tests\Feature\Notification;

use App\Models\Leave;
use App\Models\LeaveCategory;
use App\Models\LeaveStatus;
use App\Models\User;
use App\Models\UserLevel;
use App\Services\LeaveService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create user levels
        UserLevel::create(['id' => Str::uuid()->toString(), 'name' => 'admin']);
        UserLevel::create(['id' => Str::uuid()->toString(), 'name' => 'supervisor']);
        UserLevel::create(['id' => Str::uuid()->toString(), 'name' => 'user']);

        // Create leave statuses
        LeaveStatus::create(['id' => Str::uuid()->toString(), 'name' => 'pending']);
        LeaveStatus::create(['id' => Str::uuid()->toString(), 'name' => 'approved']);
        LeaveStatus::create(['id' => Str::uuid()->toString(), 'name' => 'rejected']);
    }

    /**
     * Test notification creation on leave approval.
     */
    public function test_notification_created_on_leave_approval(): void
    {
        // Create users
        $userLevel = UserLevel::where('name', 'user')->first();
        $supervisorLevel = UserLevel::where('name', 'supervisor')->first();

        $user = User::factory()->create([
            'user_level_id' => $userLevel->id,
        ]);

        $supervisor = User::factory()->create([
            'user_level_id' => $supervisorLevel->id,
        ]);

        // Create leave category
        $category = LeaveCategory::factory()->create([
            'max_in_year' => 10,
        ]);

        // Create leave
        $pendingStatus = LeaveStatus::where('name', 'pending')->first();
        $leave = Leave::factory()->create([
            'user_id' => $user->id,
            'leave_category_id' => $category->id,
            'leave_status_id' => $pendingStatus->id,
            'date' => now()->addDays(5),
        ]);

        // Approve leave
        $leaveService = new LeaveService();
        $leaveService->approveLeave($leave->id, $supervisor->id, 'Approved for good reason');

        // Check notification was created
        $notification = DB::table('notifications')
            ->where('notifiable_id', $user->id)
            ->where('type', 'leave_approved')
            ->first();

        $this->assertNotNull($notification);
        $this->assertEquals($user->id, $notification->notifiable_id);
        $this->assertEquals('App\Models\User', $notification->notifiable_type);
        $this->assertNull($notification->read_at);

        $data = json_decode($notification->data, true);
        $this->assertEquals('Leave Approved', $data['title']);
        $this->assertStringContainsString('approved', $data['message']);
        $this->assertEquals($leave->id, $data['leave_id']);
        $this->assertEquals($supervisor->name, $data['reviewer']);
        $this->assertEquals('Approved for good reason', $data['comments']);
    }

    /**
     * Test notification creation on leave rejection.
     */
    public function test_notification_created_on_leave_rejection(): void
    {
        // Create users
        $userLevel = UserLevel::where('name', 'user')->first();
        $supervisorLevel = UserLevel::where('name', 'supervisor')->first();

        $user = User::factory()->create([
            'user_level_id' => $userLevel->id,
        ]);

        $supervisor = User::factory()->create([
            'user_level_id' => $supervisorLevel->id,
        ]);

        // Create leave category
        $category = LeaveCategory::factory()->create([
            'max_in_year' => 10,
        ]);

        // Create leave
        $pendingStatus = LeaveStatus::where('name', 'pending')->first();
        $leave = Leave::factory()->create([
            'user_id' => $user->id,
            'leave_category_id' => $category->id,
            'leave_status_id' => $pendingStatus->id,
            'date' => now()->addDays(5),
        ]);

        // Reject leave
        $leaveService = new LeaveService();
        $leaveService->rejectLeave($leave->id, $supervisor->id, 'Not enough coverage');

        // Check notification was created
        $notification = DB::table('notifications')
            ->where('notifiable_id', $user->id)
            ->where('type', 'leave_rejected')
            ->first();

        $this->assertNotNull($notification);
        $this->assertEquals($user->id, $notification->notifiable_id);
        $this->assertEquals('App\Models\User', $notification->notifiable_type);
        $this->assertNull($notification->read_at);

        $data = json_decode($notification->data, true);
        $this->assertEquals('Leave Rejected', $data['title']);
        $this->assertStringContainsString('rejected', $data['message']);
        $this->assertEquals($leave->id, $data['leave_id']);
        $this->assertEquals($supervisor->name, $data['reviewer']);
        $this->assertEquals('Not enough coverage', $data['comments']);
    }

    /**
     * Test notification retrieval.
     */
    public function test_user_can_retrieve_notifications(): void
    {
        // Create user
        $userLevel = UserLevel::where('name', 'user')->first();
        $user = User::factory()->create([
            'user_level_id' => $userLevel->id,
        ]);

        // Create notifications
        DB::table('notifications')->insert([
            [
                'id' => Str::uuid()->toString(),
                'notifiable_id' => $user->id,
                'notifiable_type' => 'App\Models\User',
                'type' => 'leave_approved',
                'data' => json_encode([
                    'title' => 'Leave Approved',
                    'message' => 'Your leave has been approved',
                ]),
                'read_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid()->toString(),
                'notifiable_id' => $user->id,
                'notifiable_type' => 'App\Models\User',
                'type' => 'leave_rejected',
                'data' => json_encode([
                    'title' => 'Leave Rejected',
                    'message' => 'Your leave has been rejected',
                ]),
                'read_at' => null,
                'created_at' => now()->subDay(),
                'updated_at' => now()->subDay(),
            ],
        ]);

        // Retrieve notifications
        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/notifications');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonCount(2, 'data');

        // Check notifications are ordered by created_at desc
        $notifications = $response->json('data');
        $this->assertEquals('leave_approved', $notifications[0]['type']);
        $this->assertEquals('leave_rejected', $notifications[1]['type']);
    }

    /**
     * Test user can only retrieve their own notifications.
     */
    public function test_user_can_only_retrieve_own_notifications(): void
    {
        // Create users
        $userLevel = UserLevel::where('name', 'user')->first();
        $user1 = User::factory()->create([
            'user_level_id' => $userLevel->id,
        ]);
        $user2 = User::factory()->create([
            'user_level_id' => $userLevel->id,
        ]);

        // Create notification for user1
        DB::table('notifications')->insert([
            'id' => Str::uuid()->toString(),
            'notifiable_id' => $user1->id,
            'notifiable_type' => 'App\Models\User',
            'type' => 'leave_approved',
            'data' => json_encode([
                'title' => 'Leave Approved',
                'message' => 'Your leave has been approved',
            ]),
            'read_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create notification for user2
        DB::table('notifications')->insert([
            'id' => Str::uuid()->toString(),
            'notifiable_id' => $user2->id,
            'notifiable_type' => 'App\Models\User',
            'type' => 'leave_rejected',
            'data' => json_encode([
                'title' => 'Leave Rejected',
                'message' => 'Your leave has been rejected',
            ]),
            'read_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // User1 retrieves notifications
        $response = $this->actingAs($user1, 'sanctum')
            ->getJson('/api/notifications');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');

        $notifications = $response->json('data');
        $this->assertEquals('leave_approved', $notifications[0]['type']);
    }

    /**
     * Test marking notification as read.
     */
    public function test_user_can_mark_notification_as_read(): void
    {
        // Create user
        $userLevel = UserLevel::where('name', 'user')->first();
        $user = User::factory()->create([
            'user_level_id' => $userLevel->id,
        ]);

        // Create notification
        $notificationId = Str::uuid()->toString();
        DB::table('notifications')->insert([
            'id' => $notificationId,
            'notifiable_id' => $user->id,
            'notifiable_type' => 'App\Models\User',
            'type' => 'leave_approved',
            'data' => json_encode([
                'title' => 'Leave Approved',
                'message' => 'Your leave has been approved',
            ]),
            'read_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Mark as read
        $response = $this->actingAs($user, 'sanctum')
            ->putJson("/api/notifications/{$notificationId}/read");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Notification marked as read',
            ]);

        // Check notification is marked as read
        $notification = DB::table('notifications')
            ->where('id', $notificationId)
            ->first();

        $this->assertNotNull($notification->read_at);
    }

    /**
     * Test user cannot mark another user's notification as read.
     */
    public function test_user_cannot_mark_another_users_notification_as_read(): void
    {
        // Create users
        $userLevel = UserLevel::where('name', 'user')->first();
        $user1 = User::factory()->create([
            'user_level_id' => $userLevel->id,
        ]);
        $user2 = User::factory()->create([
            'user_level_id' => $userLevel->id,
        ]);

        // Create notification for user1
        $notificationId = Str::uuid()->toString();
        DB::table('notifications')->insert([
            'id' => $notificationId,
            'notifiable_id' => $user1->id,
            'notifiable_type' => 'App\Models\User',
            'type' => 'leave_approved',
            'data' => json_encode([
                'title' => 'Leave Approved',
                'message' => 'Your leave has been approved',
            ]),
            'read_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // User2 tries to mark user1's notification as read
        $response = $this->actingAs($user2, 'sanctum')
            ->putJson("/api/notifications/{$notificationId}/read");

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'error' => [
                    'code' => 'NOT_FOUND',
                    'message' => 'Notification not found',
                ],
            ]);

        // Check notification is still unread
        $notification = DB::table('notifications')
            ->where('id', $notificationId)
            ->first();

        $this->assertNull($notification->read_at);
    }

    /**
     * Test marking non-existent notification as read.
     */
    public function test_marking_non_existent_notification_returns_404(): void
    {
        // Create user
        $userLevel = UserLevel::where('name', 'user')->first();
        $user = User::factory()->create([
            'user_level_id' => $userLevel->id,
        ]);

        $fakeId = Str::uuid()->toString();

        // Try to mark non-existent notification as read
        $response = $this->actingAs($user, 'sanctum')
            ->putJson("/api/notifications/{$fakeId}/read");

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'error' => [
                    'code' => 'NOT_FOUND',
                    'message' => 'Notification not found',
                ],
            ]);
    }

    /**
     * Test unauthenticated user cannot access notifications.
     */
    public function test_unauthenticated_user_cannot_access_notifications(): void
    {
        $response = $this->getJson('/api/notifications');

        $response->assertStatus(401);
    }
}
