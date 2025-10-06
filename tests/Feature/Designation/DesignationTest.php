<?php

namespace Tests\Feature\Designation;

use App\Models\Designation;
use App\Models\User;
use App\Models\UserLevel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DesignationTest extends TestCase
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
    public function admin_can_create_designation()
    {
        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->postJson('/api/designations', [
                'name' => 'Senior Developer',
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Designation created successfully',
                'data' => [
                    'name' => 'Senior Developer',
                ],
            ]);

        $this->assertDatabaseHas('designations', [
            'name' => 'Senior Developer',
        ]);
    }

    /** @test */
    public function designation_name_must_be_unique()
    {
        Designation::factory()->create(['name' => 'Senior Developer']);

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->postJson('/api/designations', [
                'name' => 'Senior Developer',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    /** @test */
    public function designation_name_is_required()
    {
        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->postJson('/api/designations', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    /** @test */
    public function admin_can_list_designations()
    {
        Designation::factory()->count(3)->create();

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->getJson('/api/designations');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'created_at', 'updated_at'],
                ],
                'meta' => ['current_page', 'per_page', 'total', 'last_page'],
            ]);
    }

    /** @test */
    public function admin_can_view_single_designation()
    {
        $designation = Designation::factory()->create([
            'name' => 'Senior Developer',
        ]);

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->getJson("/api/designations/{$designation->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $designation->id,
                    'name' => 'Senior Developer',
                ],
            ]);
    }

    /** @test */
    public function admin_can_update_designation()
    {
        $designation = Designation::factory()->create([
            'name' => 'Junior Developer',
        ]);

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->putJson("/api/designations/{$designation->id}", [
                'name' => 'Senior Developer',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Designation updated successfully',
                'data' => [
                    'name' => 'Senior Developer',
                ],
            ]);

        $this->assertDatabaseHas('designations', [
            'id' => $designation->id,
            'name' => 'Senior Developer',
        ]);
    }

    /** @test */
    public function designation_name_must_be_unique_when_updating()
    {
        $designation1 = Designation::factory()->create(['name' => 'Senior Developer']);
        $designation2 = Designation::factory()->create(['name' => 'Junior Developer']);

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->putJson("/api/designations/{$designation2->id}", [
                'name' => 'Senior Developer',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    /** @test */
    public function admin_can_update_designation_with_same_name()
    {
        $designation = Designation::factory()->create([
            'name' => 'Senior Developer',
        ]);

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->putJson("/api/designations/{$designation->id}", [
                'name' => 'Senior Developer',
            ]);

        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_delete_designation_without_users()
    {
        $designation = Designation::factory()->create();

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->deleteJson("/api/designations/{$designation->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Designation deleted successfully',
            ]);

        $this->assertSoftDeleted('designations', [
            'id' => $designation->id,
        ]);
    }

    /** @test */
    public function cannot_delete_designation_with_active_users()
    {
        $designation = Designation::factory()->create();
        
        // Get existing user level to avoid creating duplicates
        $userLevel = UserLevel::firstOrCreate(['name' => 'user'], ['id' => \Illuminate\Support\Str::uuid()->toString()]);
        
        User::factory()->create([
            'designation_id' => $designation->id,
            'user_level_id' => $userLevel->id,
        ]);

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->deleteJson("/api/designations/{$designation->id}");

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'error' => [
                    'code' => 'DESIGNATION_HAS_USERS',
                    'message' => 'Cannot delete designation with active users',
                ],
            ]);

        $this->assertDatabaseHas('designations', [
            'id' => $designation->id,
            'deleted_at' => null,
        ]);
    }

    /** @test */
    public function regular_user_cannot_create_designation()
    {
        $response = $this->actingAs($this->regularUser, 'sanctum')
            ->postJson('/api/designations', [
                'name' => 'Senior Developer',
            ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function regular_user_cannot_update_designation()
    {
        $designation = Designation::factory()->create();

        $response = $this->actingAs($this->regularUser, 'sanctum')
            ->putJson("/api/designations/{$designation->id}", [
                'name' => 'Updated Name',
            ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function regular_user_cannot_delete_designation()
    {
        $designation = Designation::factory()->create();

        $response = $this->actingAs($this->regularUser, 'sanctum')
            ->deleteJson("/api/designations/{$designation->id}");

        $response->assertStatus(403);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_designations()
    {
        $response = $this->getJson('/api/designations');
        $response->assertStatus(401);

        $response = $this->postJson('/api/designations', ['name' => 'Test']);
        $response->assertStatus(401);
    }
}
