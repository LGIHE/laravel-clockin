<?php

namespace Tests\Unit\Models;

use App\Models\User;
use App\Models\UserLevel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserLevelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test user level has many users relationship.
     */
    public function test_user_level_has_many_users(): void
    {
        $userLevel = UserLevel::factory()->create();
        $user1 = User::factory()->create(['user_level_id' => $userLevel->id]);
        $user2 = User::factory()->create(['user_level_id' => $userLevel->id]);

        $this->assertCount(2, $userLevel->users);
        $this->assertTrue($userLevel->users->contains($user1));
        $this->assertTrue($userLevel->users->contains($user2));
    }
}
