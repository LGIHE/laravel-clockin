<?php

namespace Tests\Feature\User;

use App\Models\User;
use App\Models\UserLevel;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected UserLevel $adminLevel;
    protected UserLevel $userLevel;

    protected function setUp(): void
    {
        parent::setUp();

        // Create user levels
        $this->adminLevel = UserLevel::factory()->create(['name' => 'admin']);
        $this->userLevel = UserLevel::factory()->create(['name' => 'user']);

        // Create admin user
        $this->adminUser = User::factory()->create([
            'user_level_id' => $this->adminLevel->id,
            'email' => 'admin@test.com',
        ]);
    }

    /** @test */
    public function test_admin_can_create_user_with_valid_data()
    {
        $department = Department::factory()->create();
        $designation = Designation::factory()->create();

        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'user_level_id' => $this->userLevel->id,
            'department_id' => $department->id,
            'designation_id' => $designation->id,
            'status' => 1,
        ];

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->postJson('/api/users', $userData);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'User created successfully',
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'name',
                    'email',
                    'status',
                    'role',
                    'user_level',
                    'department',
                    'designation',
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
            'name' => 'John Doe',
        ]);
    }

    /** @test */
    public function test_user_creation_fails_with_invalid_data()
    {
        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->postJson('/api/users', [
                'name' => '',
                'email' => 'invalid-email',
                'password' => '123', // Too short
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password', 'user_level_id']);
    }

    /** @test */
    public function test_user_creation_fails_with_duplicate_email()
    {
        User::factory()->create(['email' => 'existing@example.com']);

        $userData = [
            'name' => 'John Doe',
            'email' => 'existing@example.com',
            'password' => 'password123',
            'user_level_id' => $this->userLevel->id,
        ];

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->postJson('/api/users', $userData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /** @test */
    public function test_admin_can_update_user()
    {
        $user = User::factory()->create([
            'user_level_id' => $this->userLevel->id,
        ]);

        $updateData = [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ];

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->putJson("/api/users/{$user->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'User updated successfully',
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ]);
    }

    /** @test */
    public function test_admin_can_assign_supervisor()
    {
        $user = User::factory()->create([
            'user_level_id' => $this->userLevel->id,
        ]);

        $supervisor = User::factory()->create([
            'user_level_id' => $this->adminLevel->id,
        ]);

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->putJson("/api/users/{$user->id}/supervisor", [
                'supervisor_id' => $supervisor->id,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Supervisor assigned successfully',
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'supervisor_id' => $supervisor->id,
        ]);
    }

    /** @test */
    public function test_admin_can_assign_projects()
    {
        $user = User::factory()->create([
            'user_level_id' => $this->userLevel->id,
        ]);

        $project1 = Project::factory()->create();
        $project2 = Project::factory()->create();

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->putJson("/api/users/{$user->id}/projects", [
                'project_ids' => [$project1->id, $project2->id],
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Projects assigned successfully',
            ]);

        $user->refresh();
        $projectIds = json_decode($user->project_id, true);
        
        $this->assertContains($project1->id, $projectIds);
        $this->assertContains($project2->id, $projectIds);
    }

    /** @test */
    public function test_admin_can_change_user_status()
    {
        $user = User::factory()->create([
            'user_level_id' => $this->userLevel->id,
            'status' => 1,
        ]);

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->putJson("/api/users/{$user->id}/status", [
                'status' => 0,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'User status updated successfully',
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'status' => 0,
        ]);
    }

    /** @test */
    public function test_admin_can_change_user_password()
    {
        $user = User::factory()->create([
            'user_level_id' => $this->userLevel->id,
            'password' => Hash::make('oldpassword'),
        ]);

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->putJson("/api/users/{$user->id}/password", [
                'old_password' => 'oldpassword',
                'new_password' => 'newpassword123',
                'new_password_confirmation' => 'newpassword123',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Password changed successfully',
            ]);

        $user->refresh();
        $this->assertTrue(Hash::check('newpassword123', $user->password));
    }

    /** @test */
    public function test_password_change_fails_with_incorrect_old_password()
    {
        $user = User::factory()->create([
            'user_level_id' => $this->userLevel->id,
            'password' => Hash::make('oldpassword'),
        ]);

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->putJson("/api/users/{$user->id}/password", [
                'old_password' => 'wrongpassword',
                'new_password' => 'newpassword123',
                'new_password_confirmation' => 'newpassword123',
            ]);

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
            ]);
    }

    /** @test */
    public function test_admin_can_list_users_with_pagination()
    {
        User::factory()->count(15)->create([
            'user_level_id' => $this->userLevel->id,
        ]);

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->getJson('/api/users?per_page=10');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'success',
                'data',
                'meta' => [
                    'current_page',
                    'per_page',
                    'total',
                    'last_page',
                ],
            ]);

        $this->assertEquals(10, count($response->json('data')));
    }

    /** @test */
    public function test_admin_can_search_users()
    {
        User::factory()->create([
            'name' => 'John Smith',
            'email' => 'john@example.com',
            'user_level_id' => $this->userLevel->id,
        ]);

        User::factory()->create([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'user_level_id' => $this->userLevel->id,
        ]);

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->getJson('/api/users?search=John');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $data = $response->json('data');
        $this->assertGreaterThanOrEqual(1, count($data));
    }

    /** @test */
    public function test_admin_can_delete_user()
    {
        $user = User::factory()->create([
            'user_level_id' => $this->userLevel->id,
        ]);

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->deleteJson("/api/users/{$user->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'User deleted successfully',
            ]);

        $this->assertSoftDeleted('users', [
            'id' => $user->id,
        ]);
    }

    /** @test */
    public function test_non_admin_cannot_access_user_management()
    {
        $regularUser = User::factory()->create([
            'user_level_id' => $this->userLevel->id,
        ]);

        $response = $this->actingAs($regularUser, 'sanctum')
            ->getJson('/api/users');

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'Unauthorized action. Insufficient permissions.',
            ]);
    }

    /** @test */
    public function test_unauthenticated_user_cannot_access_user_management()
    {
        $response = $this->getJson('/api/users');

        $response->assertStatus(401);
    }
}

