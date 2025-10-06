<?php

namespace Tests\Feature\Project;

use App\Models\Project;
use App\Models\User;
use App\Models\UserLevel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectTest extends TestCase
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
    public function admin_can_create_project()
    {
        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->postJson('/api/projects', [
                'name' => 'Project Alpha',
                'description' => 'A test project',
                'start_date' => '2025-01-01',
                'end_date' => '2025-12-31',
                'status' => 'ACTIVE',
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Project created successfully',
                'data' => [
                    'name' => 'Project Alpha',
                    'description' => 'A test project',
                    'start_date' => '2025-01-01',
                    'end_date' => '2025-12-31',
                    'status' => 'ACTIVE',
                ],
            ]);

        $this->assertDatabaseHas('projects', [
            'name' => 'Project Alpha',
            'description' => 'A test project',
            'status' => 'ACTIVE',
        ]);
    }

    /** @test */
    public function project_name_must_be_unique()
    {
        Project::factory()->create(['name' => 'Project Alpha']);

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->postJson('/api/projects', [
                'name' => 'Project Alpha',
                'description' => 'Another project',
                'start_date' => '2025-01-01',
                'status' => 'ACTIVE',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    /** @test */
    public function project_name_and_start_date_are_required()
    {
        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->postJson('/api/projects', [
                'description' => 'Some description',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'start_date', 'status']);
    }

    /** @test */
    public function project_status_must_be_valid()
    {
        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->postJson('/api/projects', [
                'name' => 'Project Alpha',
                'start_date' => '2025-01-01',
                'status' => 'INVALID_STATUS',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['status']);
    }

    /** @test */
    public function end_date_must_be_after_or_equal_to_start_date()
    {
        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->postJson('/api/projects', [
                'name' => 'Project Alpha',
                'start_date' => '2025-12-31',
                'end_date' => '2025-01-01',
                'status' => 'ACTIVE',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['end_date']);
    }

    /** @test */
    public function admin_can_list_projects()
    {
        Project::factory()->count(3)->create();

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->getJson('/api/projects');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'description', 'start_date', 'end_date', 'status', 'created_at', 'updated_at'],
                ],
                'meta' => ['current_page', 'per_page', 'total', 'last_page'],
            ]);
    }

    /** @test */
    public function admin_can_filter_projects_by_status()
    {
        Project::factory()->create(['status' => 'ACTIVE']);
        Project::factory()->create(['status' => 'COMPLETED']);

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->getJson('/api/projects?status=ACTIVE');

        $response->assertStatus(200);
        $data = $response->json('data');
        
        foreach ($data as $project) {
            $this->assertEquals('ACTIVE', $project['status']);
        }
    }

    /** @test */
    public function admin_can_view_single_project()
    {
        $project = Project::factory()->create([
            'name' => 'Project Alpha',
            'description' => 'A test project',
            'status' => 'ACTIVE',
        ]);

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->getJson("/api/projects/{$project->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $project->id,
                    'name' => 'Project Alpha',
                    'description' => 'A test project',
                    'status' => 'ACTIVE',
                ],
            ]);
    }

    /** @test */
    public function admin_can_update_project()
    {
        $project = Project::factory()->create([
            'name' => 'Project Alpha',
            'description' => 'Old description',
            'status' => 'ACTIVE',
        ]);

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->putJson("/api/projects/{$project->id}", [
                'name' => 'Project Beta',
                'description' => 'New description',
                'start_date' => '2025-01-01',
                'end_date' => '2025-12-31',
                'status' => 'COMPLETED',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Project updated successfully',
                'data' => [
                    'name' => 'Project Beta',
                    'description' => 'New description',
                    'status' => 'COMPLETED',
                ],
            ]);

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'name' => 'Project Beta',
            'description' => 'New description',
            'status' => 'COMPLETED',
        ]);
    }

    /** @test */
    public function project_name_must_be_unique_when_updating()
    {
        $project1 = Project::factory()->create(['name' => 'Project Alpha']);
        $project2 = Project::factory()->create(['name' => 'Project Beta']);

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->putJson("/api/projects/{$project2->id}", [
                'name' => 'Project Alpha',
                'description' => 'Some description',
                'start_date' => '2025-01-01',
                'status' => 'ACTIVE',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    /** @test */
    public function admin_can_update_project_with_same_name()
    {
        $project = Project::factory()->create([
            'name' => 'Project Alpha',
            'description' => 'Old description',
        ]);

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->putJson("/api/projects/{$project->id}", [
                'name' => 'Project Alpha',
                'description' => 'New description',
                'start_date' => '2025-01-01',
                'status' => 'ACTIVE',
            ]);

        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_delete_project_without_users()
    {
        $project = Project::factory()->create();

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->deleteJson("/api/projects/{$project->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Project deleted successfully',
            ]);

        $this->assertSoftDeleted('projects', [
            'id' => $project->id,
        ]);
    }

    /** @test */
    public function cannot_delete_project_with_assigned_users()
    {
        $project = Project::factory()->create();
        
        // Get existing user level to avoid creating duplicates
        $userLevel = UserLevel::firstOrCreate(['name' => 'user'], ['id' => \Illuminate\Support\Str::uuid()->toString()]);
        
        User::factory()->create([
            'project_id' => $project->id,
            'user_level_id' => $userLevel->id,
        ]);

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->deleteJson("/api/projects/{$project->id}");

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'error' => [
                    'code' => 'PROJECT_HAS_USERS',
                ],
            ]);

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'deleted_at' => null,
        ]);
    }

    /** @test */
    public function admin_can_assign_users_to_project()
    {
        $project = Project::factory()->create();
        $userLevel = UserLevel::firstOrCreate(['name' => 'user'], ['id' => \Illuminate\Support\Str::uuid()->toString()]);
        $user1 = User::factory()->create(['user_level_id' => $userLevel->id]);
        $user2 = User::factory()->create(['user_level_id' => $userLevel->id]);

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->postJson("/api/projects/{$project->id}/users", [
                'user_ids' => [$user1->id, $user2->id],
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Users assigned to project successfully',
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $user1->id,
            'project_id' => $project->id,
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user2->id,
            'project_id' => $project->id,
        ]);
    }

    /** @test */
    public function admin_can_get_project_users()
    {
        $project = Project::factory()->create();
        $userLevel = UserLevel::firstOrCreate(['name' => 'user'], ['id' => \Illuminate\Support\Str::uuid()->toString()]);
        $user1 = User::factory()->create([
            'user_level_id' => $userLevel->id,
            'project_id' => $project->id,
        ]);
        $user2 = User::factory()->create([
            'user_level_id' => $userLevel->id,
            'project_id' => $project->id,
        ]);

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->getJson("/api/projects/{$project->id}/users");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonCount(2, 'data');
    }

    /** @test */
    public function admin_can_remove_user_from_project()
    {
        $project = Project::factory()->create();
        $userLevel = UserLevel::firstOrCreate(['name' => 'user'], ['id' => \Illuminate\Support\Str::uuid()->toString()]);
        $user = User::factory()->create([
            'user_level_id' => $userLevel->id,
            'project_id' => $project->id,
        ]);

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->deleteJson("/api/projects/{$project->id}/users/{$user->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'User removed from project successfully',
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'project_id' => null,
        ]);
    }

    /** @test */
    public function regular_user_cannot_create_project()
    {
        $response = $this->actingAs($this->regularUser, 'sanctum')
            ->postJson('/api/projects', [
                'name' => 'Project Alpha',
                'start_date' => '2025-01-01',
                'status' => 'ACTIVE',
            ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function regular_user_cannot_update_project()
    {
        $project = Project::factory()->create();

        $response = $this->actingAs($this->regularUser, 'sanctum')
            ->putJson("/api/projects/{$project->id}", [
                'name' => 'Updated Name',
                'start_date' => '2025-01-01',
                'status' => 'ACTIVE',
            ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function regular_user_cannot_delete_project()
    {
        $project = Project::factory()->create();

        $response = $this->actingAs($this->regularUser, 'sanctum')
            ->deleteJson("/api/projects/{$project->id}");

        $response->assertStatus(403);
    }

    /** @test */
    public function regular_user_cannot_assign_users_to_project()
    {
        $project = Project::factory()->create();
        $userLevel = UserLevel::firstOrCreate(['name' => 'user'], ['id' => \Illuminate\Support\Str::uuid()->toString()]);
        $user = User::factory()->create(['user_level_id' => $userLevel->id]);

        $response = $this->actingAs($this->regularUser, 'sanctum')
            ->postJson("/api/projects/{$project->id}/users", [
                'user_ids' => [$user->id],
            ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_projects()
    {
        $response = $this->getJson('/api/projects');
        $response->assertStatus(401);

        $response = $this->postJson('/api/projects', ['name' => 'Test']);
        $response->assertStatus(401);
    }
}
