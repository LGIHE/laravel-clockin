<?php

namespace Tests\Feature\Holiday;

use App\Models\Holiday;
use App\Models\User;
use App\Models\UserLevel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HolidayTest extends TestCase
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
    public function admin_can_create_holiday()
    {
        $holidayData = [
            'date' => '2025-12-25',
        ];

        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/holidays', $holidayData);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Holiday created successfully',
            ])
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'date',
                    'created_at',
                    'updated_at',
                ],
                'message',
            ]);

        $this->assertDatabaseHas('holidays', [
            'date' => '2025-12-25 00:00:00',
        ]);
    }

    /** @test */
    public function holiday_date_must_be_unique()
    {
        Holiday::factory()->create(['date' => '2025-12-25']);

        $holidayData = [
            'date' => '2025-12-25',
        ];

        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/holidays', $holidayData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['date']);
    }

    /** @test */
    public function holiday_requires_valid_date()
    {
        // Test with missing date
        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/holidays', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['date']);

        // Test with invalid date format
        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/holidays', [
                'date' => 'invalid-date',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['date']);
    }

    /** @test */
    public function admin_can_list_all_holidays()
    {
        Holiday::factory()->count(3)->create();

        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/holidays');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'date',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ])
            ->assertJsonCount(3, 'data');
    }

    /** @test */
    public function holidays_are_sorted_by_date()
    {
        Holiday::factory()->create(['date' => '2025-12-25']);
        Holiday::factory()->create(['date' => '2025-01-01']);
        Holiday::factory()->create(['date' => '2025-07-04']);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/holidays');

        $response->assertStatus(200);

        $dates = collect($response->json('data'))->pluck('date')->toArray();
        
        $this->assertEquals('2025-01-01', $dates[0]);
        $this->assertEquals('2025-07-04', $dates[1]);
        $this->assertEquals('2025-12-25', $dates[2]);
    }

    /** @test */
    public function admin_can_view_single_holiday()
    {
        $holiday = Holiday::factory()->create([
            'date' => '2025-12-25',
        ]);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson("/api/holidays/{$holiday->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $holiday->id,
                    'date' => '2025-12-25',
                ],
            ]);
    }

    /** @test */
    public function admin_can_update_holiday()
    {
        $holiday = Holiday::factory()->create([
            'date' => '2025-12-25',
        ]);

        $updateData = [
            'date' => '2025-12-26',
        ];

        $response = $this->actingAs($this->admin, 'sanctum')
            ->putJson("/api/holidays/{$holiday->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Holiday updated successfully',
                'data' => [
                    'id' => $holiday->id,
                    'date' => '2025-12-26',
                ],
            ]);

        $this->assertDatabaseHas('holidays', [
            'id' => $holiday->id,
            'date' => '2025-12-26 00:00:00',
        ]);
    }

    /** @test */
    public function update_validates_unique_date_except_current_holiday()
    {
        $holiday1 = Holiday::factory()->create(['date' => '2025-12-25']);
        $holiday2 = Holiday::factory()->create(['date' => '2025-12-26']);

        // Try to update holiday2 with holiday1's date
        $response = $this->actingAs($this->admin, 'sanctum')
            ->putJson("/api/holidays/{$holiday2->id}", [
                'date' => '2025-12-25',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['date']);

        // Update with same date should work
        $response = $this->actingAs($this->admin, 'sanctum')
            ->putJson("/api/holidays/{$holiday2->id}", [
                'date' => '2025-12-26',
            ]);

        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_delete_holiday()
    {
        $holiday = Holiday::factory()->create();

        $response = $this->actingAs($this->admin, 'sanctum')
            ->deleteJson("/api/holidays/{$holiday->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Holiday deleted successfully',
            ]);

        $this->assertSoftDeleted('holidays', [
            'id' => $holiday->id,
        ]);
    }

    /** @test */
    public function non_admin_cannot_create_holiday()
    {
        $holidayData = [
            'date' => '2025-12-25',
        ];

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/holidays', $holidayData);

        $response->assertStatus(403);
    }

    /** @test */
    public function non_admin_cannot_update_holiday()
    {
        $holiday = Holiday::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/holidays/{$holiday->id}", [
                'date' => '2025-12-26',
            ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function non_admin_cannot_delete_holiday()
    {
        $holiday = Holiday::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/holidays/{$holiday->id}");

        $response->assertStatus(403);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_holidays()
    {
        $response = $this->getJson('/api/holidays');
        $response->assertStatus(401);

        $response = $this->postJson('/api/holidays', [
            'date' => '2025-12-25',
        ]);
        $response->assertStatus(401);
    }

    /** @test */
    public function returns_404_for_non_existent_holiday()
    {
        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/holidays/non-existent-id');

        $response->assertStatus(404);
    }
}
