<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    /**
     * Determine if the user can view any projects.
     */
    public function viewAny(User $user): bool
    {
        // All authenticated users can view projects
        return true;
    }

    /**
     * Determine if the user can view the project.
     */
    public function view(User $user, Project $project): bool
    {
        // All authenticated users can view projects
        return true;
    }

    /**
     * Determine if the user can create projects.
     */
    public function create(User $user): bool
    {
        // Only admins can create projects
        return $user->role === 'ADMIN';
    }

    /**
     * Determine if the user can update the project.
     */
    public function update(User $user, Project $project): bool
    {
        // Only admins can update projects
        return $user->role === 'ADMIN';
    }

    /**
     * Determine if the user can delete the project.
     */
    public function delete(User $user, Project $project): bool
    {
        // Only admins can delete projects
        return $user->role === 'ADMIN';
    }

    /**
     * Determine if the user can assign users to the project.
     */
    public function assignUsers(User $user, Project $project): bool
    {
        // Only admins can assign users to projects
        return $user->role === 'ADMIN';
    }

    /**
     * Determine if the user can remove users from the project.
     */
    public function removeUsers(User $user, Project $project): bool
    {
        // Only admins can remove users from projects
        return $user->role === 'ADMIN';
    }
}
