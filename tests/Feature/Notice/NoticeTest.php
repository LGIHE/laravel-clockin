<?php

namespace Tests\Feature\Notice;

use App\Models\Notice;
use App\Models\User;
use App\Models\UserLevel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NoticeTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Create user levels
        $adminLevel = UserLevel::factory()->create(['name' => 'admin']);
        $userLevel = UserLevel::factory()->create(['name' => 'user']);

        // Create admin and regular user
        $this->admin = User::factory()->create(['user_level_id' => $adminLevel->id]);
        $this->user = User::factory()->create(['user_level_id' => $userLevel->id]);
    }

    /** @test */
    public function admin_can_create_notice()
    {
        $noticeData = [
            'subject' => 'Important Announcement',
            'message' => 'This is an important company-wide announcement.',
        ];

        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/notices', $noticeData);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Notice created successfully',
            ])
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'subject',
                    'message',
                    'created_at',
                    'updated_at',
                ],
                'message',
            ]);

        $this->assertDatabaseHas('notices', [
            'subject' => 'Important Announcement',
            'message' => 'This is an important company-wide announcement.',
        ]);
    }

    /** @test */
    public function notice_requires_subject_and_message()
    {
        // Test with missing subject
        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/notices', [
                'message' => 'Test message',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['subject']);

        // Test with missing message
        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/notices', [
                'subject' => 'Test subject',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['message']);

        // Test with both missing
        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/notices', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['subject', 'message']);
    }

    /** @test */
    public function notice_subject_must_not_exceed_255_characters()
    {
        $noticeData = [
            'subject' => str_repeat('a', 256),
            'message' => 'Test message',
        ];

        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/notices', $noticeData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['subject']);
    }

    /** @test */
    public function all_authenticated_users_can_list_notices()
    {
        Notice::factory()->count(3)->create();

        // Admin can list
        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/notices');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'subject',
                        'message',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ])
            ->assertJsonCount(3, 'data');

        // Regular user can also list
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/notices');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    /** @test */
    public function notices_are_sorted_by_creation_date_descending()
    {
        $notice1 = Notice::factory()->create(['created_at' => now()->subDays(2)]);
        $notice2 = Notice::factory()->create(['created_at' => now()->subDays(1)]);
        $notice3 = Notice::factory()->create(['created_at' => now()]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/notices');

        $response->assertStatus(200);

        $ids = collect($response->json('data'))->pluck('id')->toArray();
        
        // Most recent first
        $this->assertEquals($notice3->id, $ids[0]);
        $this->assertEquals($notice2->id, $ids[1]);
        $this->assertEquals($notice1->id, $ids[2]);
    }

    /** @test */
    public function all_authenticated_users_can_view_single_notice()
    {
        $notice = Notice::factory()->create([
            'subject' => 'Test Notice',
            'message' => 'Test message content',
        ]);

        // Admin can view
        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson("/api/notices/{$notice->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $notice->id,
                    'subject' => 'Test Notice',
                    'message' => 'Test message content',
                ],
            ]);

        // Regular user can also view
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/notices/{$notice->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $notice->id,
                ],
            ]);
    }

    /** @test */
    public function admin_can_update_notice()
    {
        $notice = Notice::factory()->create([
            'subject' => 'Original Subject',
            'message' => 'Original message',
        ]);

        $updateData = [
            'subject' => 'Updated Subject',
            'message' => 'Updated message content',
        ];

        $response = $this->actingAs($this->admin, 'sanctum')
            ->putJson("/api/notices/{$notice->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Notice updated successfully',
                'data' => [
                    'id' => $notice->id,
                    'subject' => 'Updated Subject',
                    'message' => 'Updated message content',
                ],
            ]);

        $this->assertDatabaseHas('notices', [
            'id' => $notice->id,
            'subject' => 'Updated Subject',
            'message' => 'Updated message content',
        ]);
    }

    /** @test */
    public function admin_can_delete_notice()
    {
        $notice = Notice::factory()->create();

        $response = $this->actingAs($this->admin, 'sanctum')
            ->deleteJson("/api/notices/{$notice->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Notice deleted successfully',
            ]);

        $this->assertSoftDeleted('notices', [
            'id' => $notice->id,
        ]);
    }

    /** @test */
    public function non_admin_cannot_create_notice()
    {
        $noticeData = [
            'subject' => 'Test Notice',
            'message' => 'Test message',
        ];

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/notices', $noticeData);

        $response->assertStatus(403);
    }

    /** @test */
    public function non_admin_cannot_update_notice()
    {
        $notice = Notice::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/notices/{$notice->id}", [
                'subject' => 'Updated Subject',
                'message' => 'Updated message',
            ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function non_admin_cannot_delete_notice()
    {
        $notice = Notice::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/notices/{$notice->id}");

        $response->assertStatus(403);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_notices()
    {
        $response = $this->getJson('/api/notices');
        $response->assertStatus(401);

        $response = $this->postJson('/api/notices', [
            'subject' => 'Test',
            'message' => 'Test message',
        ]);
        $response->assertStatus(401);
    }

    /** @test */
    public function returns_404_for_non_existent_notice()
    {
        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/notices/non-existent-id');

        $response->assertStatus(404);
    }
}
