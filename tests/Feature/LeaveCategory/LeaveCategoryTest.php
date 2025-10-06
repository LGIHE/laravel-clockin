<?php

namespace Tests\Feature\LeaveCategory;

use App\Models\Leave;
use App\Models\LeaveCategory;
use App\Models\User;
use App\Models\UserLevel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeaveCategoryTest extends TestCase
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
    public function admin_can_create_leave_category_with_limits()
    {
        $categoryData = [
            'name' => 'Annual Leave',
            'max_in_year' => 20,
        ];

        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/leave-categories', $categoryData);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Leave category created successfully',
            ])
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'name',
                    'max_in_year',
                    'created_at',
                    'updated_at',
                ],
                'message',
            ]);

        $this->assertDatabaseHas('leave_categories', [
            'name' => 'Annual Leave',
            'max_in_year' => 20,
        ]);
    }

    /** @test */
    public function leave_category_name_must_be_unique()
    {
        LeaveCategory::factory()->create(['name' => 'Sick Leave']);

        $categoryData = [
            'name' => 'Sick Leave',
            'max_in_year' => 10,
        ];

        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/leave-categories', $categoryData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    /** @test */
    public function leave_category_requires_valid_max_in_year()
    {
        // Test with missing max_in_year
        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/leave-categories', [
                'name' => 'Test Leave',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['max_in_year']);

        // Test with max_in_year less than 1
        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/leave-categories', [
                'name' => 'Test Leave',
                'max_in_year' => 0,
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['max_in_year']);

        // Test with max_in_year greater than 365
        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/leave-categories', [
                'name' => 'Test Leave',
                'max_in_year' => 366,
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['max_in_year']);
    }

    /** @test */
    public function admin_can_list_all_leave_categories()
    {
        LeaveCategory::factory()->count(3)->create();

        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/leave-categories');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'max_in_year',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ])
            ->assertJsonCount(3, 'data');
    }

    /** @test */
    public function admin_can_view_single_leave_category()
    {
        $category = LeaveCategory::factory()->create([
            'name' => 'Medical Leave',
            'max_in_year' => 15,
        ]);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson("/api/leave-categories/{$category->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $category->id,
                    'name' => 'Medical Leave',
                    'max_in_year' => 15,
                ],
            ]);
    }

    /** @test */
    public function admin_can_update_leave_category()
    {
        $category = LeaveCategory::factory()->create([
            'name' => 'Old Name',
            'max_in_year' => 10,
        ]);

        $updateData = [
            'name' => 'Updated Name',
            'max_in_year' => 25,
        ];

        $response = $this->actingAs($this->admin, 'sanctum')
            ->putJson("/api/leave-categories/{$category->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Leave category updated successfully',
                'data' => [
                    'id' => $category->id,
                    'name' => 'Updated Name',
                    'max_in_year' => 25,
                ],
            ]);

        $this->assertDatabaseHas('leave_categories', [
            'id' => $category->id,
            'name' => 'Updated Name',
            'max_in_year' => 25,
        ]);
    }

    /** @test */
    public function update_validates_unique_name_except_current_category()
    {
        $category1 = LeaveCategory::factory()->create(['name' => 'Category 1']);
        $category2 = LeaveCategory::factory()->create(['name' => 'Category 2']);

        // Try to update category2 with category1's name
        $response = $this->actingAs($this->admin, 'sanctum')
            ->putJson("/api/leave-categories/{$category2->id}", [
                'name' => 'Category 1',
                'max_in_year' => 10,
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);

        // Update with same name should work
        $response = $this->actingAs($this->admin, 'sanctum')
            ->putJson("/api/leave-categories/{$category2->id}", [
                'name' => 'Category 2',
                'max_in_year' => 15,
            ]);

        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_delete_leave_category_without_active_leaves()
    {
        $category = LeaveCategory::factory()->create();

        $response = $this->actingAs($this->admin, 'sanctum')
            ->deleteJson("/api/leave-categories/{$category->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Leave category deleted successfully',
            ]);

        $this->assertSoftDeleted('leave_categories', [
            'id' => $category->id,
        ]);
    }

    /** @test */
    public function cannot_delete_leave_category_with_active_leaves()
    {
        $category = LeaveCategory::factory()->create();
        
        // Create an active leave with this category
        Leave::factory()->create([
            'leave_category_id' => $category->id,
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->deleteJson("/api/leave-categories/{$category->id}");

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'error' => [
                    'code' => 'CATEGORY_IN_USE',
                    'message' => 'Cannot delete leave category with active leaves',
                ],
            ]);

        $this->assertDatabaseHas('leave_categories', [
            'id' => $category->id,
            'deleted_at' => null,
        ]);
    }

    /** @test */
    public function can_delete_leave_category_with_soft_deleted_leaves()
    {
        $category = LeaveCategory::factory()->create();
        
        // Create a soft-deleted leave with this category
        $leave = Leave::factory()->create([
            'leave_category_id' => $category->id,
            'user_id' => $this->user->id,
        ]);
        $leave->delete(); // Soft delete the leave

        $response = $this->actingAs($this->admin, 'sanctum')
            ->deleteJson("/api/leave-categories/{$category->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Leave category deleted successfully',
            ]);

        $this->assertSoftDeleted('leave_categories', [
            'id' => $category->id,
        ]);
    }

    /** @test */
    public function non_admin_cannot_create_leave_category()
    {
        $categoryData = [
            'name' => 'Test Leave',
            'max_in_year' => 10,
        ];

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/leave-categories', $categoryData);

        $response->assertStatus(403);
    }

    /** @test */
    public function non_admin_cannot_update_leave_category()
    {
        $category = LeaveCategory::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/leave-categories/{$category->id}", [
                'name' => 'Updated Name',
                'max_in_year' => 20,
            ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function non_admin_cannot_delete_leave_category()
    {
        $category = LeaveCategory::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/leave-categories/{$category->id}");

        $response->assertStatus(403);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_leave_categories()
    {
        $response = $this->getJson('/api/leave-categories');
        $response->assertStatus(401);

        $response = $this->postJson('/api/leave-categories', [
            'name' => 'Test',
            'max_in_year' => 10,
        ]);
        $response->assertStatus(401);
    }
}
