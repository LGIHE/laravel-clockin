<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;

class AttendanceService
{
    /**
     * Clock in a user.
     *
     * @param string $userId
     * @param string|null $message
     * @return Attendance
     * @throws \Exception
     */
    public function clockIn(string $userId, ?string $message = null): Attendance
    {
        // Check if user is already clocked in
        $existingAttendance = Attendance::where('user_id', $userId)
            ->whereNull('out_time')
            ->first();

        if ($existingAttendance) {
            throw new \Exception('User is already clocked in. Please clock out first.');
        }

        // Create new attendance record
        $attendance = Attendance::create([
            'id' => (string) Str::uuid(),
            'user_id' => $userId,
            'in_time' => Carbon::now(),
            'in_message' => $message,
        ]);

        // Update user's last_in_time
        User::where('id', $userId)->update([
            'last_in_time' => Carbon::now(),
        ]);

        return $attendance->load('user');
    }

    /**
     * Clock out a user.
     *
     * @param string $userId
     * @param string|null $message
     * @return Attendance
     * @throws \Exception
     */
    public function clockOut(string $userId, ?string $message = null): Attendance
    {
        // Find the active attendance record
        $attendance = Attendance::where('user_id', $userId)
            ->whereNull('out_time')
            ->first();

        if (!$attendance) {
            throw new \Exception('User is not clocked in. Please clock in first.');
        }

        // Calculate worked hours in seconds
        $workedSeconds = $this->calculateWorkedHours($attendance->in_time, Carbon::now());

        // Update attendance record
        $attendance->update([
            'out_time' => Carbon::now(),
            'out_message' => $message,
            'worked' => $workedSeconds,
        ]);

        return $attendance->load('user');
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
