<?php

namespace Tests\Unit\Models;

use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test project soft delete.
     */
    public function test_project_soft_delete(): void
    {
        $project = Project::factory()->create();
        $projectId = $project->id;

        $project->delete();

        $this->assertSoftDeleted('projects', ['id' => $projectId]);
        $this->assertNotNull($project->fresh()->deleted_at);
    }

    /**
     * Test project can be restored after soft delete.
     */
    public function test_project_can_be_restored(): void
    {
        $project = Project::factory()->create();
        $project->delete();

        $project->restore();

        $this->assertNull($project->fresh()->deleted_at);
        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'deleted_at' => null,
        ]);
    }
}
