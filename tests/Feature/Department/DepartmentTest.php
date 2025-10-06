<?php

namespace Tests\Feature\Department;

use App\Models\Department;
use App\Models\User;
use App\Models\UserLevel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DepartmentTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;
    private User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Create user levels
        $adminLevel = UserLevel::firstOrCreate(['name' => 'admin'], ['id' => \Illuminate\Support\Str::uuid()->toString()]);
        $userLevel = UserLevel::firstOrCreate(['name' => 'user'], ['id' => \Illuminate\Support\Str::uuid()->toString()]);

        // Create users
        $this->adminUser = User::factory()->create(['user_level_id' => $adminLevel->id]);
        $this->regularUser = User::factory()->create(['user_level_id' => $userLevel->id]);
    }

    /** @test */
    public function admin_can_create_department()
    {
        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->postJson('/api/departments', [
                'name' => 'Engineering',
                'description' => 'Engineering department',
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Department created successfully',
                'data' => [
                    'name' => 'Engineering',
                    'description' => 'Engineering department',
                ],
            ]);

        $this->assertDatabaseHas('departments', [
            'name' => 'Engineering',
            'description' => 'Engineering department',
        ]);
    }

    /** @test */
    public function department_name_must_be_unique()
    {
        Department::factory()->create(['name' => 'Engineering']);

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->postJson('/api/departments', [
                'name' => 'Engineering',
                'description' => 'Another engineering department',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    /** @test */
    public function department_name_is_required()
    {
        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->postJson('/api/departments', [
                'description' => 'Some description',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    /** @test */
    public function admin_can_list_departments()
    {
        Department::factory()->count(3)->create();

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->getJson('/api/departments');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'description', 'created_at', 'updated_at'],
                ],
                'meta' => ['current_page', 'per_page', 'total', 'last_page'],
            ]);
    }

    /** @test */
    public function admin_can_view_single_department()
    {
        $department = Department::factory()->create([
            'name' => 'Engineering',
            'description' => 'Engineering department',
        ]);

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->getJson("/api/departments/{$department->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $department->id,
                    'name' => 'Engineering',
                    'description' => 'Engineering department',
                ],
            ]);
    }

    /** @test */
    public function admin_can_update_department()
    {
        $department = Department::factory()->create([
            'name' => 'Engineering',
            'description' => 'Old description',
        ]);

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->putJson("/api/departments/{$department->id}", [
                'name' => 'Software Engineering',
                'description' => 'New description',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Department updated successfully',
                'data' => [
                    'name' => 'Software Engineering',
                    'description' => 'New description',
                ],
            ]);

        $this->assertDatabaseHas('departments', [
            'id' => $department->id,
            'name' => 'Software Engineering',
            'description' => 'New description',
        ]);
    }

    /** @test */
    public function department_name_must_be_unique_when_updating()
    {
        $department1 = Department::factory()->create(['name' => 'Engineering']);
        $department2 = Department::factory()->create(['name' => 'Marketing']);

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->putJson("/api/departments/{$department2->id}", [
                'name' => 'Engineering',
                'description' => 'Some description',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    /** @test */
    public function admin_can_update_department_with_same_name()
    {
        $department = Department::factory()->create([
            'name' => 'Engineering',
            'description' => 'Old description',
        ]);

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->putJson("/api/departments/{$department->id}", [
                'name' => 'Engineering',
                'description' => 'New description',
            ]);

        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_delete_department_without_users()
    {
        $department = Department::factory()->create();

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->deleteJson("/api/departments/{$department->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Department deleted successfully',
            ]);

        $this->assertSoftDeleted('departments', [
            'id' => $department->id,
        ]);
    }

    /** @test */
    public function cannot_delete_department_with_active_users()
    {
        $department = Department::factory()->create();
        
        // Get existing user level to avoid creating duplicates
        $userLevel = UserLevel::firstOrCreate(['name' => 'user'], ['id' => \Illuminate\Support\Str::uuid()->toString()]);
        
        User::factory()->create([
            'department_id' => $department->id,
            'user_level_id' => $userLevel->id,
        ]);

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->deleteJson("/api/departments/{$department->id}");

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'error' => [
                    'code' => 'DEPARTMENT_HAS_USERS',
                    'message' => 'Cannot delete department with active users',
                ],
            ]);

        $this->assertDatabaseHas('departments', [
            'id' => $department->id,
            'deleted_at' => null,
        ]);
    }

    /** @test */
    public function regular_user_cannot_create_department()
    {
        $response = $this->actingAs($this->regularUser, 'sanctum')
            ->postJson('/api/departments', [
                'name' => 'Engineering',
                'description' => 'Engineering department',
            ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function regular_user_cannot_update_department()
    {
        $department = Department::factory()->create();

        $response = $this->actingAs($this->regularUser, 'sanctum')
            ->putJson("/api/departments/{$department->id}", [
                'name' => 'Updated Name',
            ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function regular_user_cannot_delete_department()
    {
        $department = Department::factory()->create();

        $response = $this->actingAs($this->regularUser, 'sanctum')
            ->deleteJson("/api/departments/{$department->id}");

        $response->assertStatus(403);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_departments()
    {
        $response = $this->getJson('/api/departments');
        $response->assertStatus(401);

        $response = $this->postJson('/api/departments', ['name' => 'Test']);
        $response->assertStatus(401);
    }
}
