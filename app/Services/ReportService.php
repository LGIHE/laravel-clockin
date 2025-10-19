<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportService
{
    /**
     * Generate individual attendance report for a specific user.
     *
     * @param array $filters
     * @return array
     */
    public function generateIndividualReport(array $filters): array
    {
        $userId = $filters['user_id'];
        $startDate = Carbon::parse($filters['start_date']);
        $endDate = Carbon::parse($filters['end_date']);

        $user = User::with(['userLevel', 'department', 'designation'])
            ->find($userId);

        if (!$user) {
            throw new \Exception('User not found');
        }

        // Get attendance records for the date range
        $attendances = Attendance::where('user_id', $userId)
            ->whereDate('in_time', '>=', $startDate)
            ->whereDate('in_time', '<=', $endDate)
            ->orderBy('in_time', 'asc')
            ->get();

        // Calculate statistics
        $stats = $this->calculateStatistics($attendances, $startDate, $endDate);

        return [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'department' => $user->department?->name,
                'designation' => $user->designation?->name,
            ],
            'period' => [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
            ],
            'attendances' => $attendances,
            'statistics' => $stats,
        ];
    }

    /**
     * Generate summary report for multiple users.
     *
     * @param array $filters
     * @return array
     */
    public function generateSummaryReport(array $filters): array
    {
        $startDate = Carbon::parse($filters['start_date']);
        $endDate = Carbon::parse($filters['end_date']);

        $query = User::with(['userLevel', 'department', 'designation'])
            ->where('status', 1); // Only active users

        // Apply filters
        if (!empty($filters['user_id'])) {
            $query->where('id', $filters['user_id']);
        }

        if (!empty($filters['department_id'])) {
            $query->where('department_id', $filters['department_id']);
        }

        if (!empty($filters['project_id'])) {
            // Check both direct project_id and many-to-many relationship
            $query->where(function($q) use ($filters) {
                $q->where('project_id', $filters['project_id'])
                  ->orWhereHas('projects', function($projectQuery) use ($filters) {
                      $projectQuery->where('projects.id', $filters['project_id']);
                  });
            });
        }

        $users = $query->get();

        $summaryData = [];

        foreach ($users as $user) {
            $attendances = Attendance::where('user_id', $user->id)
                ->whereDate('in_time', '>=', $startDate)
                ->whereDate('in_time', '<=', $endDate)
                ->get();

            $stats = $this->calculateStatistics($attendances, $startDate, $endDate);

            $summaryData[] = [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'department' => $user->department?->name,
                    'designation' => $user->designation?->name,
                ],
                'statistics' => $stats,
            ];
        }

        return [
            'period' => [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
            ],
            'summary' => $summaryData,
            'overall_statistics' => $this->calculateOverallStatistics($summaryData),
        ];
    }

    /**
     * Generate timesheet for a specific user and month.
     *
     * @param array $filters
     * @return array
     */
    public function generateTimesheet(array $filters): array
    {
        $userId = $filters['user_id'];
        $month = $filters['month'] ?? Carbon::now()->month;
        $year = $filters['year'] ?? Carbon::now()->year;

        $user = User::with(['userLevel', 'department', 'designation'])
            ->find($userId);

        if (!$user) {
            throw new \Exception('User not found');
        }

        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth();

        // Get all attendance records for the month
        $attendances = Attendance::where('user_id', $userId)
            ->whereDate('in_time', '>=', $startDate)
            ->whereDate('in_time', '<=', $endDate)
            ->orderBy('in_time', 'asc')
            ->get();

        // Group by date
        $dailyRecords = [];
        $daysInMonth = $startDate->daysInMonth;

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = Carbon::create($year, $month, $day);
            $dateStr = $date->format('Y-m-d');

            $dayAttendances = $attendances->filter(function ($attendance) use ($dateStr) {
                return $attendance->in_time->format('Y-m-d') === $dateStr;
            });

            $totalWorked = $dayAttendances->sum('worked');

            $dailyRecords[] = [
                'date' => $dateStr,
                'day_name' => $date->format('l'),
                'attendances' => $dayAttendances->values(),
                'total_worked' => $totalWorked,
                'total_worked_formatted' => $this->formatSeconds($totalWorked),
                'status' => $dayAttendances->isEmpty() ? 'absent' : 'present',
            ];
        }

        $stats = $this->calculateStatistics($attendances, $startDate, $endDate);

        return [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'department' => $user->department?->name,
                'designation' => $user->designation?->name,
            ],
            'period' => [
                'month' => $month,
                'year' => $year,
                'month_name' => $startDate->format('F'),
            ],
            'daily_records' => $dailyRecords,
            'statistics' => $stats,
        ];
    }

    /**
     * Calculate statistics from attendance records.
     *
     * @param \Illuminate\Support\Collection $attendances
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return array
     */
    private function calculateStatistics($attendances, Carbon $startDate, Carbon $endDate): array
    {
        $totalDays = $startDate->diffInDays($endDate) + 1;
        $totalWorked = $attendances->sum('worked');
        $completedAttendances = $attendances->filter(fn($a) => $a->out_time !== null);
        $daysPresent = $attendances->pluck('in_time')
            ->map(fn($time) => $time->format('Y-m-d'))
            ->unique()
            ->count();

        $averageHours = $daysPresent > 0 ? $totalWorked / $daysPresent : 0;

        // Calculate late arrivals (after 9:00 AM)
        $lateArrivals = $attendances->filter(function ($attendance) {
            $inTime = $attendance->in_time;
            $nineAM = Carbon::parse($inTime->format('Y-m-d') . ' 09:00:00');
            return $inTime->gt($nineAM);
        })->count();

        // Calculate early departures (before 5:00 PM)
        $earlyDepartures = $completedAttendances->filter(function ($attendance) {
            if (!$attendance->out_time) {
                return false;
            }
            $outTime = $attendance->out_time;
            $fivePM = Carbon::parse($outTime->format('Y-m-d') . ' 17:00:00');
            return $outTime->lt($fivePM);
        })->count();

        return [
            'total_days' => $totalDays,
            'days_present' => $daysPresent,
            'days_absent' => $totalDays - $daysPresent,
            'total_hours' => round($totalWorked / 3600, 2),
            'total_hours_formatted' => $this->formatSeconds($totalWorked),
            'average_hours_per_day' => round($averageHours / 3600, 2),
            'average_hours_per_day_formatted' => $this->formatSeconds((int)$averageHours),
            'late_arrivals' => $lateArrivals,
            'early_departures' => $earlyDepartures,
            'attendance_rate' => $totalDays > 0 ? round(($daysPresent / $totalDays) * 100, 2) : 0,
        ];
    }

    /**
     * Calculate overall statistics from summary data.
     *
     * @param array $summaryData
     * @return array
     */
    private function calculateOverallStatistics(array $summaryData): array
    {
        $totalUsers = count($summaryData);
        $totalHours = 0;
        $totalDaysPresent = 0;
        $totalLateArrivals = 0;
        $totalEarlyDepartures = 0;

        foreach ($summaryData as $data) {
            $stats = $data['statistics'];
            $totalHours += $stats['total_hours'];
            $totalDaysPresent += $stats['days_present'];
            $totalLateArrivals += $stats['late_arrivals'];
            $totalEarlyDepartures += $stats['early_departures'];
        }

        $averageHoursPerUser = $totalUsers > 0 ? $totalHours / $totalUsers : 0;

        return [
            'total_users' => $totalUsers,
            'total_hours' => round($totalHours, 2),
            'average_hours_per_user' => round($averageHoursPerUser, 2),
            'total_days_present' => $totalDaysPresent,
            'total_late_arrivals' => $totalLateArrivals,
            'total_early_departures' => $totalEarlyDepartures,
        ];
    }

    /**
     * Format seconds to HH:MM:SS format.
     *
     * @param int $seconds
     * @return string
     */
    private function formatSeconds(int $seconds): string
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
    }
}
