<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class AttendanceService
{
    /**
     * Clock in a user.
     *
     * @param string $userId
     * @param string|null $message
     * @param array|string|null $projectIds - Array of project IDs or single project ID
     * @param array|string|null $taskIds - Array of task IDs or single task ID
     * @return Attendance
     * @throws \Exception
     */
    public function clockIn(string $userId, ?string $message = null, $projectIds = null, $taskIds = null): Attendance
    {
        // Check if user is already clocked in
        $existingAttendance = Attendance::where('user_id', $userId)
            ->whereNull('out_time')
            ->first();

        if ($existingAttendance) {
            throw new \Exception('User is already clocked in. Please clock out first.');
        }

        // Normalize project IDs to array
        $projectIdsArray = [];
        if (!empty($projectIds)) {
            $projectIdsArray = is_array($projectIds) ? $projectIds : [$projectIds];
            $projectIdsArray = array_filter($projectIdsArray); // Remove empty values
        }

        // Normalize task IDs to array
        $taskIdsArray = [];
        if (!empty($taskIds)) {
            $taskIdsArray = is_array($taskIds) ? $taskIds : [$taskIds];
            $taskIdsArray = array_filter($taskIdsArray); // Remove empty values
        }

        // Validate that at least one project is selected
        if (empty($projectIdsArray)) {
            throw new \Exception('Please select at least one project.');
        }

        // Create new attendance record
        $attendance = Attendance::create([
            'id' => (string) Str::uuid(),
            'user_id' => $userId,
            'in_time' => Carbon::now(),
            'in_message' => $message,
            // Keep legacy fields for backward compatibility (use first project/task)
            'project_id' => $projectIdsArray[0] ?? null,
            'task_id' => $taskIdsArray[0] ?? null,
            'task_status' => !empty($taskIdsArray) ? 'in-progress' : null,
        ]);

        // Attach multiple projects
        if (!empty($projectIdsArray)) {
            $attendance->projects()->attach($projectIdsArray);
        }

        // Attach multiple tasks with default status
        if (!empty($taskIdsArray)) {
            $taskData = [];
            foreach ($taskIdsArray as $taskId) {
                $taskData[$taskId] = ['status' => 'in-progress'];
            }
            $attendance->tasks()->attach($taskData);
        }

        // Update user's last_in_time
        User::where('id', $userId)->update([
            'last_in_time' => Carbon::now(),
        ]);

        // Invalidate user stats cache
        $cacheKey = "user_stats:{$userId}:" . Carbon::now()->format('Y-m');
        Cache::forget($cacheKey);

        return $attendance->load(['user', 'projects', 'tasks']);
    }

    /**
     * Clock out a user.
     *
     * @param string $userId
     * @param string|null $message
     * @param array|string|null $taskStatuses - Array of task statuses keyed by task_id, or single status for all tasks
     * @return Attendance
     * @throws \Exception
     */
    public function clockOut(string $userId, ?string $message = null, $taskStatuses = null): Attendance
    {
        // Find the active attendance record
        $attendance = Attendance::where('user_id', $userId)
            ->whereNull('out_time')
            ->with(['tasks'])
            ->first();

        if (!$attendance) {
            throw new \Exception('User is not clocked in. Please clock in first.');
        }

        // Calculate worked hours in seconds
        $workedSeconds = $this->calculateWorkedHours($attendance->in_time, Carbon::now());

        // Prepare update data
        $updateData = [
            'out_time' => Carbon::now(),
            'out_message' => $message,
            'worked' => $workedSeconds,
        ];

        // Handle task statuses
        if (!empty($taskStatuses) && $attendance->tasks->isNotEmpty()) {
            // If taskStatuses is a string, apply it to all tasks
            if (is_string($taskStatuses)) {
                $updateData['task_status'] = $taskStatuses; // Update legacy field
                
                // Update all tasks in pivot table
                foreach ($attendance->tasks as $task) {
                    $attendance->tasks()->updateExistingPivot($task->id, ['status' => $taskStatuses]);
                    
                    // Also update the Task model status
                    $task->update(['status' => $taskStatuses]);
                    \Log::info('Task status updated', [
                        'task_id' => $task->id,
                        'new_status' => $taskStatuses
                    ]);
                }
            } 
            // If taskStatuses is an array, update each task individually
            elseif (is_array($taskStatuses)) {
                foreach ($taskStatuses as $taskId => $status) {
                    if ($attendance->tasks->contains('id', $taskId)) {
                        $attendance->tasks()->updateExistingPivot($taskId, ['status' => $status]);
                        
                        // Also update the Task model status
                        $task = \App\Models\Task::find($taskId);
                        if ($task) {
                            $task->update(['status' => $status]);
                            \Log::info('Task status updated', [
                                'task_id' => $taskId,
                                'new_status' => $status
                            ]);
                        }
                    }
                }
                
                // Update legacy field with first task status
                if (!empty($taskStatuses)) {
                    $updateData['task_status'] = reset($taskStatuses);
                }
            }
        }

        // Update attendance record
        $attendance->update($updateData);

        // Invalidate user stats cache
        $cacheKey = "user_stats:{$userId}:" . Carbon::now()->format('Y-m');
        Cache::forget($cacheKey);

        return $attendance->load(['user', 'projects', 'tasks']);
    }

    /**
     * Get current attendance status for a user.
     *
     * @param string $userId
     * @return array
     */
    public function getCurrentStatus(string $userId): array
    {
        $attendance = Attendance::where('user_id', $userId)
            ->whereNull('out_time')
            ->first();

        if ($attendance) {
            return [
                'clocked_in' => true,
                'attendance' => $attendance,
                'in_time' => $attendance->in_time,
                'duration' => $this->calculateWorkedHours($attendance->in_time, Carbon::now()),
            ];
        }

        return [
            'clocked_in' => false,
            'attendance' => null,
            'in_time' => null,
            'duration' => 0,
        ];
    }

    /**
     * Calculate worked hours in seconds.
     *
     * @param \Carbon\Carbon $inTime
     * @param \Carbon\Carbon $outTime
     * @return int
     */
    public function calculateWorkedHours(Carbon $inTime, Carbon $outTime): int
    {
        return $inTime->diffInSeconds($outTime);
    }

    /**
     * Force punch for a user (admin only).
     *
     * @param string $userId
     * @param string $type ('in' or 'out')
     * @param string $time
     * @param string|null $message
     * @return Attendance
     * @throws \Exception
     */
    public function forcePunch(string $userId, string $type, string $time, ?string $message = null): Attendance
    {
        $punchTime = Carbon::parse($time);

        if ($type === 'in') {
            // Check if user is already clocked in
            $existingAttendance = Attendance::where('user_id', $userId)
                ->whereNull('out_time')
                ->first();

            if ($existingAttendance) {
                throw new \Exception('User is already clocked in. Please force clock out first.');
            }

            // Create new attendance record
            $attendance = Attendance::create([
                'id' => (string) Str::uuid(),
                'user_id' => $userId,
                'in_time' => $punchTime,
                'in_message' => $message ?? 'Force punched by admin',
            ]);

            // Update user's last_in_time
            User::where('id', $userId)->update([
                'last_in_time' => $punchTime,
            ]);

            return $attendance->load('user');
        } elseif ($type === 'out') {
            // Find the active attendance record
            $attendance = Attendance::where('user_id', $userId)
                ->whereNull('out_time')
                ->first();

            if (!$attendance) {
                throw new \Exception('User is not clocked in. Please force clock in first.');
            }

            // Calculate worked hours in seconds
            $workedSeconds = $this->calculateWorkedHours($attendance->in_time, $punchTime);

            // Update attendance record
            $attendance->update([
                'out_time' => $punchTime,
                'out_message' => $message ?? 'Force punched by admin',
                'worked' => $workedSeconds,
            ]);

            return $attendance->load('user');
        }

        throw new \Exception('Invalid punch type. Must be "in" or "out".');
    }

    /**
     * Get attendance records with filters.
     *
     * @param array $filters
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAttendanceRecords(array $filters = [])
    {
        $query = Attendance::with(['user.userLevel', 'user.department', 'user.designation']);

        // Filter by user
        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        // Filter by date range
        if (!empty($filters['start_date'])) {
            $query->whereDate('in_time', '>=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $query->whereDate('in_time', '<=', $filters['end_date']);
        }

        // Filter by status (clocked in/out)
        if (isset($filters['status'])) {
            if ($filters['status'] === 'clocked_in') {
                $query->whereNull('out_time');
            } elseif ($filters['status'] === 'clocked_out') {
                $query->whereNotNull('out_time');
            }
        }

        // Sort by in_time descending by default
        $sortBy = $filters['sort_by'] ?? 'in_time';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);

        // Paginate results
        $perPage = $filters['per_page'] ?? 15;
        
        return $query->paginate($perPage);
    }
}
