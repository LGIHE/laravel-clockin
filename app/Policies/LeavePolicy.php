<?php

namespace App\Policies;

use App\Models\Leave;
use App\Models\User;

class LeavePolicy
{
    /**
     * Determine if the user can view any leaves.
     */
    public function viewAny(User $user): bool
    {
        // All authenticated users can view leaves
        return true;
    }

    /**
     * Determine if the user can view the leave.
     */
    public function view(User $user, Leave $leave): bool
    {
        // Users can view their own leaves
        // Supervisors can view their team's leaves
        // Admins can view all leaves
        return $user->role === 'ADMIN' 
            || $leave->user_id === $user->id
            || ($user->role === 'SUPERVISOR' && $this->isTeamMember($user, $leave->user_id));
    }

    /**
     * Determine if the user can create leaves.
     */
    public function create(User $user): bool
    {
        // All authenticated users can create leaves
        return true;
    }

    /**
     * Determine if the user can update the leave.
     */
    public function update(User $user, Leave $leave): bool
    {
        // Users can only update their own pending leaves
        return $leave->user_id === $user->id 
            && $leave->status->name === 'PENDING';
    }

    /**
     * Determine if the user can delete the leave.
     */
    public function delete(User $user, Leave $leave): bool
    {
        // Users can only delete their own pending leaves
        // Admins can delete any leave
        return $user->role === 'ADMIN'
            || ($leave->user_id === $user->id && $leave->status->name === 'PENDING');
    }

    /**
     * Determine if the user can approve the leave.
     */
    public function approve(User $user, Leave $leave): bool
    {
        // Supervisors can approve their team's leaves
        // Admins can approve any leave
        // Users cannot approve their own leaves
        return $leave->user_id !== $user->id
            && ($user->role === 'ADMIN' 
                || ($user->role === 'SUPERVISOR' && $this->isTeamMember($user, $leave->user_id)));
    }

    /**
     * Determine if the user can reject the leave.
     */
    public function reject(User $user, Leave $leave): bool
    {
        // Same as approve
        return $this->approve($user, $leave);
    }

    /**
     * Check if a user is a team member of the supervisor.
     */
    protected function isTeamMember(User $supervisor, string $userId): bool
    {
        return User::where('id', $userId)
            ->where('supervisor_id', $supervisor->id)
            ->exists();
    }
}
