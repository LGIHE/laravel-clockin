<?php

namespace App\Policies;

use App\Models\Attendance;
use App\Models\User;

class AttendancePolicy
{
    /**
     * Determine if the user can view any attendance records.
     */
    public function viewAny(User $user): bool
    {
        // All authenticated users can view attendance records
        return true;
    }

    /**
     * Determine if the user can view the attendance record.
     */
    public function view(User $user, Attendance $attendance): bool
    {
        // Users can view their own attendance
        // Supervisors can view their team's attendance
        // Admins can view all attendance
        return $user->role === 'ADMIN' 
            || $attendance->user_id === $user->id
            || ($user->role === 'SUPERVISOR' && $this->isTeamMember($user, $attendance->user_id));
    }

    /**
     * Determine if the user can create attendance records.
     */
    public function create(User $user): bool
    {
        // All authenticated users can clock in/out
        return true;
    }

    /**
     * Determine if the user can update the attendance record.
     */
    public function update(User $user, Attendance $attendance): bool
    {
        // Only admins can update attendance records
        return $user->role === 'ADMIN';
    }

    /**
     * Determine if the user can delete the attendance record.
     */
    public function delete(User $user, Attendance $attendance): bool
    {
        // Only admins can delete attendance records
        return $user->role === 'ADMIN';
    }

    /**
     * Determine if the user can force punch.
     */
    public function forcePunch(User $user): bool
    {
        // Only admins can force punch
        return $user->role === 'ADMIN';
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
