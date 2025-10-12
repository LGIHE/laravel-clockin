<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine if the user can view any users.
     */
    public function viewAny(User $user): bool
    {
        // Admins and supervisors can view users
        return in_array($user->role, ['ADMIN', 'SUPERVISOR']);
    }

    /**
     * Determine if the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        // Users can view their own profile
        // Supervisors can view their team members
        // Admins can view all users
        return $user->role === 'ADMIN' 
            || $user->id === $model->id
            || ($user->role === 'SUPERVISOR' && $model->supervisors->contains($user->id));
    }

    /**
     * Determine if the user can create users.
     */
    public function create(User $user): bool
    {
        // Only admins can create users
        return $user->role === 'ADMIN';
    }

    /**
     * Determine if the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        // Users can update their own profile (limited fields)
        // Admins can update any user
        return $user->role === 'ADMIN' || $user->id === $model->id;
    }

    /**
     * Determine if the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        // Only admins can delete users
        // Cannot delete yourself
        return $user->role === 'ADMIN' && $user->id !== $model->id;
    }

    /**
     * Determine if the user can assign supervisors.
     */
    public function assignSupervisor(User $user): bool
    {
        return $user->role === 'ADMIN';
    }

    /**
     * Determine if the user can assign projects.
     */
    public function assignProjects(User $user): bool
    {
        return $user->role === 'ADMIN';
    }

    /**
     * Determine if the user can change status.
     */
    public function changeStatus(User $user): bool
    {
        return $user->role === 'ADMIN';
    }

    /**
     * Determine if the user can change password.
     */
    public function changePassword(User $user, User $model): bool
    {
        // Users can change their own password
        // Admins can change any user's password
        return $user->role === 'ADMIN' || $user->id === $model->id;
    }
}
