<?php

namespace App\Services;

use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Str;

class ProjectService
{
    /**
     * Create a new project.
     *
     * @param array $data
     * @return Project
     */
    public function createProject(array $data): Project
    {
        return Project::create([
            'id' => Str::uuid()->toString(),
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'] ?? null,
            'status' => $data['status'],
        ]);
    }

    /**
     * Update an existing project.
     *
     * @param Project $project
     * @param array $data
     * @return Project
     */
    public function updateProject(Project $project, array $data): Project
    {
        $project->update([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'] ?? null,
            'status' => $data['status'],
        ]);

        return $project->fresh();
    }

    /**
     * Assign users to a project.
     *
     * @param Project $project
     * @param array $userIds
     * @return void
     */
    public function assignUsers(Project $project, array $userIds): void
    {
        User::whereIn('id', $userIds)->update(['project_id' => $project->id]);
    }

    /**
     * Remove a user from a project.
     *
     * @param Project $project
     * @param string $userId
     * @return void
     */
    public function removeUser(Project $project, string $userId): void
    {
        $user = User::findOrFail($userId);
        
        if ($user->project_id === $project->id) {
            $user->update(['project_id' => null]);
        }
    }

    /**
     * Get all users assigned to a project.
     *
     * @param Project $project
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getProjectUsers(Project $project)
    {
        return $project->users()->with(['userLevel', 'department', 'designation'])->get();
    }

    /**
     * Get all projects for a user.
     *
     * @param string $userId
     * @return Project|null
     */
    public function getUserProject(string $userId): ?Project
    {
        $user = User::findOrFail($userId);
        return $user->project;
    }

    /**
     * Delete a project (soft delete).
     *
     * @param Project $project
     * @return bool
     */
    public function deleteProject(Project $project): bool
    {
        // Check if project has assigned users
        if ($project->users()->count() > 0) {
            throw new \Exception('Cannot delete project with assigned users');
        }

        return $project->delete();
    }
}
