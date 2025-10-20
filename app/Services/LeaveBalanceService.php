<?php

namespace App\Services;

use App\Models\LeaveBalance;
use App\Models\LeaveCategory;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;

class LeaveBalanceService
{
    /**
     * Calculate annual leave accrual based on months worked
     * 2 days per month = 24 days per year
     */
    public function calculateAnnualLeaveAccrual(User $user, int $year): float
    {
        $hireDate = $user->hire_date ? Carbon::parse($user->hire_date) : $user->created_at;
        $startOfYear = Carbon::create($year, 1, 1);
        $endOfYear = Carbon::create($year, 12, 31);
        
        // If hired after the year, no accrual
        if ($hireDate->isAfter($endOfYear)) {
            return 0;
        }
        
        // Calculate from hire date or start of year, whichever is later
        $accrualStart = $hireDate->isAfter($startOfYear) ? $hireDate : $startOfYear;
        
        // Calculate up to end of year or current date, whichever is earlier
        $accrualEnd = now()->isBefore($endOfYear) ? now() : $endOfYear;
        
        // If accrual hasn't started yet
        if ($accrualStart->isAfter($accrualEnd)) {
            return 0;
        }
        
        // Calculate months worked (including partial months)
        $monthsWorked = $accrualStart->floatDiffInMonths($accrualEnd);
        
        // 2 days per month
        return round($monthsWorked * 2, 1);
    }

    /**
     * Get or create leave balance for a user
     */
    public function getOrCreateBalance(string $userId, string $categoryId, int $year): LeaveBalance
    {
        $balance = LeaveBalance::where('user_id', $userId)
            ->where('leave_category_id', $categoryId)
            ->where('year', $year)
            ->first();
        
        if (!$balance) {
            $user = User::findOrFail($userId);
            $category = LeaveCategory::findOrFail($categoryId);
            
            // Calculate total days based on category
            $totalDays = $category->max_in_year;
            
            // For Annual Leave, calculate based on accrual
            if (stripos($category->name, 'Annual') !== false) {
                $totalDays = $this->calculateAnnualLeaveAccrual($user, $year);
            }
            
            $balance = LeaveBalance::create([
                'id' => Str::uuid()->toString(),
                'user_id' => $userId,
                'leave_category_id' => $categoryId,
                'year' => $year,
                'total_days' => $totalDays,
                'used_days' => 0,
                'carried_forward' => 0,
            ]);
        }
        
        return $balance;
    }

    /**
     * Update balance when leave is approved
     */
    public function deductLeave(string $userId, string $categoryId, int $year, float $days): void
    {
        $balance = $this->getOrCreateBalance($userId, $categoryId, $year);
        $balance->used_days += $days;
        $balance->save();
    }

    /**
     * Add compensation days to balance
     */
    public function addCompensationDays(string $userId, float $days): void
    {
        $year = now()->year;
        
        // Find or create Compensation Leave category
        $category = LeaveCategory::where('name', 'LIKE', '%Compensation%')->first();
        
        if (!$category) {
            throw new \Exception('Compensation Leave category not found');
        }
        
        $balance = $this->getOrCreateBalance($userId, $category->id, $year);
        $balance->total_days += $days;
        $balance->save();
    }

    /**
     * Carry forward Annual and Compensation leave to new year
     * Called on January 1st
     */
    public function carryForwardLeaves(string $userId, int $fromYear): void
    {
        $toYear = $fromYear + 1;
        $expiryDate = Carbon::create($toYear, 3, 31); // March 31 of new year
        
        // Get Annual Leave category
        $annualCategory = LeaveCategory::where('name', 'LIKE', '%Annual%')->first();
        if ($annualCategory) {
            $oldBalance = LeaveBalance::where('user_id', $userId)
                ->where('leave_category_id', $annualCategory->id)
                ->where('year', $fromYear)
                ->first();
            
            if ($oldBalance) {
                $remaining = $oldBalance->total_days - $oldBalance->used_days;
                if ($remaining > 0) {
                    $newBalance = $this->getOrCreateBalance($userId, $annualCategory->id, $toYear);
                    $newBalance->carried_forward = $remaining;
                    $newBalance->carryforward_expires_at = $expiryDate;
                    $newBalance->save();
                }
            }
        }
        
        // Get Compensation Leave category
        $compCategory = LeaveCategory::where('name', 'LIKE', '%Compensation%')->first();
        if ($compCategory) {
            $oldBalance = LeaveBalance::where('user_id', $userId)
                ->where('leave_category_id', $compCategory->id)
                ->where('year', $fromYear)
                ->first();
            
            if ($oldBalance) {
                $remaining = $oldBalance->total_days - $oldBalance->used_days;
                if ($remaining > 0) {
                    $newBalance = $this->getOrCreateBalance($userId, $compCategory->id, $toYear);
                    $newBalance->carried_forward = $remaining;
                    $newBalance->carryforward_expires_at = $expiryDate;
                    $newBalance->save();
                }
            }
        }
    }

    /**
     * Reset all leave balances for new year
     * Should be run on January 1st
     */
    public function resetAllBalancesForNewYear(): void
    {
        $users = User::all();
        $currentYear = now()->year;
        $previousYear = $currentYear - 1;
        
        foreach ($users as $user) {
            // Carry forward Annual and Compensation leaves
            $this->carryForwardLeaves($user->id, $previousYear);
        }
    }

    /**
     * Get available balance including carryforward
     */
    public function getAvailableBalance(string $userId, string $categoryId, int $year): array
    {
        $balance = $this->getOrCreateBalance($userId, $categoryId, $year);
        $category = LeaveCategory::findOrFail($categoryId);
        
        $totalAvailable = $balance->total_days;
        $carriedForward = 0;
        
        // Check if carryforward is still valid
        if ($balance->carried_forward > 0 && $balance->carryforward_expires_at) {
            if (now()->lte($balance->carryforward_expires_at)) {
                $carriedForward = $balance->carried_forward;
                $totalAvailable += $carriedForward;
            }
        }
        
        $remaining = $totalAvailable - $balance->used_days;
        
        return [
            'category' => $category->name,
            'total' => $balance->total_days,
            'carried_forward' => $carriedForward,
            'total_available' => $totalAvailable,
            'used' => $balance->used_days,
            'remaining' => max(0, $remaining),
            'carryforward_expires_at' => $balance->carryforward_expires_at?->format('M d, Y'),
        ];
    }
}
