#!/usr/bin/env php
<?php

/**
 * Verification Script for Multi-Select Clock-In Implementation
 * 
 * This script verifies that all components of the multi-select
 * clock-in feature are properly installed and configured.
 */

echo "🔍 Multi-Select Clock-In Implementation Verification\n";
echo str_repeat("=", 60) . "\n\n";

$checks = [];
$passed = 0;
$failed = 0;

// Check 1: Migration file exists
echo "1. Checking migration file... ";
$migrationFile = __DIR__ . '/database/migrations/2025_10_21_000001_create_attendance_project_task_tables.php';
if (file_exists($migrationFile)) {
    echo "✅ PASS\n";
    $checks[] = ['Migration file exists', true];
    $passed++;
} else {
    echo "❌ FAIL\n";
    $checks[] = ['Migration file exists', false];
    $failed++;
}

// Check 2: Attendance model updated
echo "2. Checking Attendance model... ";
$attendanceModel = file_get_contents(__DIR__ . '/app/Models/Attendance.php');
if (strpos($attendanceModel, 'public function projects()') !== false && 
    strpos($attendanceModel, 'public function tasks()') !== false) {
    echo "✅ PASS\n";
    $checks[] = ['Attendance model has new relationships', true];
    $passed++;
} else {
    echo "❌ FAIL\n";
    $checks[] = ['Attendance model has new relationships', false];
    $failed++;
}

// Check 3: AttendanceService updated
echo "3. Checking AttendanceService... ";
$attendanceService = file_get_contents(__DIR__ . '/app/Services/AttendanceService.php');
if (strpos($attendanceService, 'is_array($projectIds)') !== false && 
    strpos($attendanceService, 'is_array($taskIds)') !== false) {
    echo "✅ PASS\n";
    $checks[] = ['AttendanceService handles arrays', true];
    $passed++;
} else {
    echo "❌ FAIL\n";
    $checks[] = ['AttendanceService handles arrays', false];
    $failed++;
}

// Check 4: UserDashboard component updated
echo "4. Checking UserDashboard component... ";
$userDashboard = file_get_contents(__DIR__ . '/app/Livewire/Dashboard/UserDashboard.php');
if (strpos($userDashboard, '$selectedProjects = []') !== false && 
    strpos($userDashboard, '$selectedTasks = []') !== false &&
    strpos($userDashboard, '$taskStatuses = []') !== false) {
    echo "✅ PASS\n";
    $checks[] = ['UserDashboard uses array properties', true];
    $passed++;
} else {
    echo "❌ FAIL\n";
    $checks[] = ['UserDashboard uses array properties', false];
    $failed++;
}

// Check 5: View file updated
echo "5. Checking dashboard view... ";
$dashboardView = file_get_contents(__DIR__ . '/resources/views/livewire/dashboard/user-dashboard.blade.php');
if (strpos($dashboardView, 'wire:model="selectedProjects"') !== false && 
    strpos($dashboardView, 'type="checkbox"') !== false) {
    echo "✅ PASS\n";
    $checks[] = ['Dashboard view has checkbox inputs', true];
    $passed++;
} else {
    echo "❌ FAIL\n";
    $checks[] = ['Dashboard view has checkbox inputs', false];
    $failed++;
}

// Check 6: AttendanceController updated
echo "6. Checking AttendanceController... ";
$attendanceController = file_get_contents(__DIR__ . '/app/Http/Controllers/AttendanceController.php');
if (strpos($attendanceController, 'project_ids') !== false && 
    strpos($attendanceController, 'task_ids') !== false) {
    echo "✅ PASS\n";
    $checks[] = ['AttendanceController supports new API format', true];
    $passed++;
} else {
    echo "❌ FAIL\n";
    $checks[] = ['AttendanceController supports new API format', false];
    $failed++;
}

// Check 7: AttendanceResource updated
echo "7. Checking AttendanceResource... ";
$attendanceResource = file_get_contents(__DIR__ . '/app/Http/Resources/AttendanceResource.php');
if (strpos($attendanceResource, "'projects'") !== false && 
    strpos($attendanceResource, "'tasks'") !== false) {
    echo "✅ PASS\n";
    $checks[] = ['AttendanceResource includes new relationships', true];
    $passed++;
} else {
    echo "❌ FAIL\n";
    $checks[] = ['AttendanceResource includes new relationships', false];
    $failed++;
}

// Check 8: Documentation exists
echo "8. Checking documentation... ";
if (file_exists(__DIR__ . '/MULTI_SELECT_CLOCKIN_CHANGES.md') && 
    file_exists(__DIR__ . '/IMPLEMENTATION_SUMMARY.md')) {
    echo "✅ PASS\n";
    $checks[] = ['Documentation files exist', true];
    $passed++;
} else {
    echo "❌ FAIL\n";
    $checks[] = ['Documentation files exist', false];
    $failed++;
}

// Summary
echo "\n" . str_repeat("=", 60) . "\n";
echo "📊 VERIFICATION SUMMARY\n";
echo str_repeat("=", 60) . "\n";
echo "Total Checks: " . ($passed + $failed) . "\n";
echo "✅ Passed: $passed\n";
echo "❌ Failed: $failed\n";
echo "\n";

if ($failed === 0) {
    echo "🎉 All checks passed! The implementation is complete.\n";
    echo "\n";
    echo "Next steps:\n";
    echo "1. Run: php artisan migrate (if not already done)\n";
    echo "2. Clear cache: php artisan optimize:clear\n";
    echo "3. Test the feature in your browser\n";
    echo "4. Review IMPLEMENTATION_SUMMARY.md for details\n";
    exit(0);
} else {
    echo "⚠️  Some checks failed. Please review the implementation.\n";
    echo "\n";
    echo "Failed checks:\n";
    foreach ($checks as $check) {
        if (!$check[1]) {
            echo "  - " . $check[0] . "\n";
        }
    }
    exit(1);
}
