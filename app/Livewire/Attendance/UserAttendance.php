<?php

namespace App\Livewire\Attendance;

use App\Models\Attendance;
use App\Services\AttendanceService;
use Carbon\Carbon;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class UserAttendance extends Component
{
    public $startDate = '';
    public $endDate = '';
    public $pageSize = 10;
    public $currentPage = 1;
    
    public $attendances = [];
    public $statistics = [];
    public $totalRecords = 0;
    public $totalPages = 1;
    
    public $user;
    public $isLoading = true;
    
    // Edit modal properties
    public $showEditModal = false;           // Controls modal visibility
    public $selectedRecordId = null;         // ID of attendance being edited
    public $editTime = '';                   // Editable time field
    public $editTimeType = 'in';             // Type: 'in' or 'out'
    public $editMessage = '';                // Message text being edited

    // Timesheet modal properties
    public $showTimesheetModal = false;      // Controls timesheet modal visibility

    protected AttendanceService $attendanceService;

    public function boot(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    public function mount()
    {
        $this->user = Auth::user();
        
        // Set default date range to current month
        $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->endDate = Carbon::now()->format('Y-m-d');
        
        $this->loadAttendanceData();
    }

    public function updatedStartDate()
    {
        $this->currentPage = 1;
        $this->loadAttendanceData();
    }

    public function updatedEndDate()
    {
        $this->currentPage = 1;
        $this->loadAttendanceData();
    }

    public function updatedPageSize()
    {
        $this->currentPage = 1;
        $this->loadAttendanceData();
    }

    public function setPage($page)
    {
        $this->currentPage = $page;
        $this->loadAttendanceData();
    }

    public function nextPage()
    {
        if ($this->currentPage < $this->totalPages) {
            $this->currentPage++;
            $this->loadAttendanceData();
        }
    }

    public function previousPage()
    {
        if ($this->currentPage > 1) {
            $this->currentPage--;
            $this->loadAttendanceData();
        }
    }
    
    public function openEditModal($attendanceId, $timeType)
    {
        $attendance = Attendance::find($attendanceId);
        
        if (!$attendance) {
            $this->dispatch('toast', [
                'message' => 'Attendance record not found',
                'variant' => 'danger'
            ]);
            return;
        }
        
        // Check if user is authorized to edit this attendance
        if ($attendance->user_id !== $this->user->id) {
            $this->dispatch('toast', [
                'message' => 'You are not authorized to edit this attendance record',
                'variant' => 'danger'
            ]);
            return;
        }
        
        $this->selectedRecordId = $attendanceId;
        $this->editTimeType = $timeType;
        
        if ($timeType === 'in') {
            // Format for datetime-local input (Y-m-d\TH:i)
            $this->editTime = Carbon::parse($attendance->in_time)->format('Y-m-d\TH:i');
            $this->editMessage = $attendance->in_message ?? '';
        } else {
            if (!$attendance->out_time) {
                $this->dispatch('toast', [
                    'message' => 'No out time recorded yet',
                    'variant' => 'warning'
                ]);
                return;
            }
            $this->editTime = Carbon::parse($attendance->out_time)->format('Y-m-d\TH:i');
            $this->editMessage = $attendance->out_message ?? '';
        }
        
        $this->showEditModal = true;
    }
    
    public function saveTimeMessage()
    {
        if (!$this->selectedRecordId) {
            $this->dispatch('toast', [
                'message' => 'Invalid attendance record',
                'variant' => 'danger'
            ]);
            return;
        }
        
        // Validate time input
        if (!$this->editTime) {
            $this->dispatch('toast', [
                'message' => 'Time is required',
                'variant' => 'danger'
            ]);
            return;
        }
        
        try {
            $attendance = Attendance::find($this->selectedRecordId);
            
            if (!$attendance) {
                throw new \Exception('Attendance record not found');
            }
            
            // Check authorization
            if ($attendance->user_id !== $this->user->id) {
                throw new \Exception('You are not authorized to edit this attendance record');
            }
            
            // Parse the datetime-local input to Carbon instance
            $newTime = Carbon::parse($this->editTime);
            
            // Validate that time is not in the future
            if ($newTime->isFuture()) {
                throw new \Exception('Time cannot be in the future');
            }
            
            // Update the appropriate time and message
            if ($this->editTimeType === 'in') {
                // Validate that in_time is before out_time if out_time exists
                if ($attendance->out_time && $newTime->isAfter(Carbon::parse($attendance->out_time))) {
                    throw new \Exception('Clock in time cannot be after clock out time');
                }
                
                $attendance->in_time = $newTime;
                $attendance->in_message = $this->editMessage;
            } else {
                // Validate that out_time is after in_time
                if ($newTime->isBefore(Carbon::parse($attendance->in_time))) {
                    throw new \Exception('Clock out time cannot be before clock in time');
                }
                
                $attendance->out_time = $newTime;
                $attendance->out_message = $this->editMessage;
            }
            
            $attendance->save();
            
            $this->dispatch('toast', [
                'message' => 'Attendance updated successfully',
                'variant' => 'success'
            ]);
            
            // Reload data to show updated message
            $this->loadAttendanceData();
            
            // Close modal
            $this->closeEditModal();
            
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'message' => $e->getMessage(),
                'variant' => 'danger'
            ]);
        }
    }
    
    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->selectedRecordId = null;
        $this->selectedRecordTime = '';
        $this->editTimeType = 'in';
        $this->editMessage = '';
    }

    public function loadAttendanceData()
    {
        $this->isLoading = true;
        
        try {
            // Get attendance records for the user
            $query = Attendance::where('user_id', $this->user->id)
                ->whereBetween('in_time', [
                    Carbon::parse($this->startDate)->startOfDay(),
                    Carbon::parse($this->endDate)->endOfDay()
                ])
                ->orderBy('in_time', 'desc');

            $this->totalRecords = $query->count();
            $this->totalPages = ceil($this->totalRecords / $this->pageSize);

            $allAttendances = $query->get();
            
            // Get paginated records
            $this->attendances = $query
                ->skip(($this->currentPage - 1) * $this->pageSize)
                ->take($this->pageSize)
                ->get()
                ->map(function ($attendance) {
                    return [
                        'id' => $attendance->id,
                        'date' => Carbon::parse($attendance->in_time)->format('M d, Y'),
                        'inTime' => Carbon::parse($attendance->in_time)->format('m/d/Y, g:i:s A'),
                        'outTime' => $attendance->out_time ? Carbon::parse($attendance->out_time)->format('m/d/Y, g:i:s A') : null,
                        'worked' => $this->calculateWorkedTime($attendance),
                        'status' => $attendance->out_time ? 'Complete' : 'In Progress',
                        'inMessage' => $attendance->in_message,
                        'outMessage' => $attendance->out_message,
                    ];
                })->toArray();

            // Calculate statistics
            $totalSeconds = 0;
            $daysWorked = 0;
            
            foreach ($allAttendances as $attendance) {
                if ($attendance->out_time) {
                    $inTime = Carbon::parse($attendance->in_time);
                    $outTime = Carbon::parse($attendance->out_time);
                    // Calculate seconds worked: outTime - inTime
                    $secondsWorked = $outTime->timestamp - $inTime->timestamp;
                    // Only add if positive (valid time range)
                    if ($secondsWorked > 0) {
                        $totalSeconds += $secondsWorked;
                        $daysWorked++;
                    }
                }
            }
            
            $totalHours = $totalSeconds / 3600;
            $totalDays = Carbon::parse($this->startDate)->diffInDays(Carbon::parse($this->endDate)) + 1;
            $attendancePercentage = $totalDays > 0 ? ($daysWorked / $totalDays) * 100 : 0;

            $this->statistics = [
                'totalHours' => round($totalHours, 2),
                'daysWorked' => $daysWorked,
                'attendancePercentage' => round($attendancePercentage, 1),
            ];
            
        } catch (\Exception $e) {
            \Log::error('Error loading attendance data: ' . $e->getMessage());
            $this->attendances = [];
            $this->statistics = [
                'totalHours' => 0,
                'daysWorked' => 0,
                'attendancePercentage' => 0,
            ];
        }
        
        $this->isLoading = false;
    }

    private function calculateWorkedTime($attendance)
    {
        if (!$attendance->out_time) {
            return '-';
        }

        $inTime = Carbon::parse($attendance->in_time);
        $outTime = Carbon::parse($attendance->out_time);
        
        // Calculate total seconds
        $totalSeconds = $outTime->timestamp - $inTime->timestamp;
        
        // Convert to hours, minutes, seconds
        $hours = floor($totalSeconds / 3600);
        $minutes = floor(($totalSeconds % 3600) / 60);
        $seconds = $totalSeconds % 60;

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }

    public function exportCsv()
    {
        $filename = 'attendance-' . $this->startDate . '-' . $this->endDate . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['#', 'Date', 'In Time', 'Out Time', 'Worked', 'Status']);

            $records = Attendance::where('user_id', $this->user->id)
                ->whereBetween('in_time', [
                    Carbon::parse($this->startDate)->startOfDay(),
                    Carbon::parse($this->endDate)->endOfDay()
                ])
                ->orderBy('in_time', 'desc')
                ->get();

            $index = 1;
            foreach ($records as $record) {
                fputcsv($file, [
                    $index++,
                    Carbon::parse($record->in_time)->format('M d, Y'),
                    Carbon::parse($record->in_time)->format('m/d/Y, g:i:s A'),
                    $record->out_time ? Carbon::parse($record->out_time)->format('m/d/Y, g:i:s A') : '',
                    $this->calculateWorkedTime($record),
                    $record->out_time ? 'Complete' : 'In Progress',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportJson()
    {
        $records = Attendance::where('user_id', $this->user->id)
            ->whereBetween('in_time', [
                Carbon::parse($this->startDate)->startOfDay(),
                Carbon::parse($this->endDate)->endOfDay()
            ])
            ->orderBy('in_time', 'desc')
            ->get()
            ->map(function ($record, $index) {
                return [
                    'id' => $index + 1,
                    'date' => Carbon::parse($record->in_time)->format('M d, Y'),
                    'inTime' => Carbon::parse($record->in_time)->format('m/d/Y, g:i:s A'),
                    'outTime' => $record->out_time ? Carbon::parse($record->out_time)->format('m/d/Y, g:i:s A') : null,
                    'worked' => $this->calculateWorkedTime($record),
                    'status' => $record->out_time ? 'Complete' : 'In Progress',
                ];
            });

        $filename = 'attendance-' . $this->startDate . '-' . $this->endDate . '.json';
        
        return response()->json($records)
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    public function exportPdf()
    {
        $records = Attendance::where('user_id', $this->user->id)
            ->whereBetween('in_time', [
                Carbon::parse($this->startDate)->startOfDay(),
                Carbon::parse($this->endDate)->endOfDay()
            ])
            ->orderBy('in_time', 'desc')
            ->get()
            ->map(function ($record, $index) {
                return [
                    'id' => $index + 1,
                    'date' => Carbon::parse($record->in_time)->format('M d, Y'),
                    'inTime' => Carbon::parse($record->in_time)->format('m/d/Y, g:i:s A'),
                    'outTime' => $record->out_time ? Carbon::parse($record->out_time)->format('m/d/Y, g:i:s A') : null,
                    'worked' => $this->calculateWorkedTime($record),
                    'status' => $record->out_time ? 'Complete' : 'In Progress',
                ];
            });

        $data = [
            'user' => $this->user,
            'records' => $records,
            'statistics' => $this->statistics,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
        ];

        $pdf = Pdf::loadView('reports.user-attendance-pdf', $data);
        return $pdf->download('attendance-' . $this->startDate . '-' . $this->endDate . '.pdf');
    }

    public function openTimesheetModal()
    {
        $this->showTimesheetModal = true;
    }

    public function closeTimesheetModal()
    {
        $this->showTimesheetModal = false;
    }

    public function exportTimesheetExcel()
    {
        $this->closeTimesheetModal();
        
        try {
            $timesheetData = $this->generateTimesheetData();
            
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Set default font
            $spreadsheet->getDefaultStyle()->getFont()->setName('Arial')->setSize(11);
            
            $row = 1;
            
            // Header information
            $sheet->setCellValue('A' . $row, 'NAME OF ORGANISATION OR ENTITY:');
            $sheet->setCellValue('B' . $row, 'LUIGI GIUSSANI FOUNDATION');
            $sheet->getStyle('A' . $row)->getFont()->setBold(true);
            $row++;
            
            $sheet->setCellValue('A' . $row, 'PROJECT NAME:');
            $sheet->setCellValue('B' . $row, $this->getFormattedProjects());
            $sheet->getStyle('A' . $row)->getFont()->setBold(true);
            $row += 2;
            
            $sheet->setCellValue('A' . $row, 'TIME SHEET');
            $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(14);
            $row += 2;
            
            $sheet->setCellValue('A' . $row, 'NAME OF PERSON:');
            $sheet->setCellValue('B' . $row, $timesheetData['user']->name);
            $sheet->getStyle('A' . $row)->getFont()->setBold(true);
            $row++;
            
            $sheet->setCellValue('A' . $row, 'POSITION:');
            $sheet->setCellValue('B' . $row, $timesheetData['user']->designation->name ?? $timesheetData['user']->userLevel->name);
            $sheet->getStyle('A' . $row)->getFont()->setBold(true);
            $row++;
            
            $sheet->setCellValue('A' . $row, 'PERIOD COVERED:');
            $sheet->setCellValue('B' . $row, $timesheetData['period']);
            $sheet->getStyle('A' . $row)->getFont()->setBold(true);
            $row += 2;
            
            // Prepare table data
            $startDate = Carbon::parse($timesheetData['startDate']);
            $endDate = Carbon::parse($timesheetData['endDate']);
            $daysInMonth = $startDate->daysInMonth;
            
            // Build header row
            $headerRow = $row;
            $col = 'A';
            $sheet->setCellValue($col++ . $row, 'Description');
            $sheet->setCellValue($col++ . $row, 'Hours');
            
            for ($day = 1; $day <= $daysInMonth; $day++) {
                $sheet->setCellValue($col++ . $row, (string)$day);
            }
            $sheet->setCellValue($col . $row, 'LOE');
            
            // Style header row
            $lastCol = $col;
            $sheet->getStyle('A' . $row . ':' . $lastCol . $row)->applyFromArray([
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E0E0E0']
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000']
                    ]
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER
                ]
            ]);
            
            $row++;
            
            // Calculate daily hours and totals
            $projects = $this->getUserProjects();
            $projectDailyHours = [];
            $projectTotalHours = [];
            $sickLeaveDays = [];
            $annualLeaveDays = [];
            $holidayDays = [];
            
            // Initialize project hours
            foreach ($projects as $project) {
                $projectDailyHours[$project] = array_fill(1, $daysInMonth, 0);
                $projectTotalHours[$project] = 0;
            }
            
            // Process each day in the month
            for ($day = 1; $day <= $daysInMonth; $day++) {
                $currentDate = Carbon::create($startDate->year, $startDate->month, $day);
                $dateStr = $currentDate->format('Y-m-d');
                
                // Find entry for this day
                $dayEntry = collect($timesheetData['entries'])->firstWhere('date', $currentDate->format('M d, Y'));
                
                if ($dayEntry) {
                    if ($dayEntry['status'] === 'Sick Leave') {
                        $sickLeaveDays[$day] = true;
                    } elseif ($dayEntry['status'] === 'Annual Leave') {
                        $annualLeaveDays[$day] = true;
                    } elseif ($dayEntry['status'] === 'Public Holiday') {
                        $holidayDays[$day] = true;
                    } elseif ($dayEntry['status'] === 'Present' && !empty($dayEntry['hoursWorked'])) {
                        // Cap hours at 8 per day and convert to whole number
                        $hours = min(8, floor(floatval($dayEntry['hoursWorked'])));
                        
                        // Distribute hours across projects randomly (whole numbers only)
                        if (count($projects) === 1) {
                            $projectDailyHours[$projects[0]][$day] = $hours;
                            $projectTotalHours[$projects[0]] += $hours;
                        } else {
                            // Random distribution in whole numbers that adds up to total hours
                            $remaining = $hours;
                            $projectCount = count($projects);
                            
                            foreach ($projects as $index => $project) {
                                if ($index === $projectCount - 1) {
                                    // Last project gets remaining hours
                                    $projectHours = $remaining;
                                } else {
                                    // Random allocation between 1 and (remaining - remaining projects)
                                    // Ensure each remaining project gets at least 1 hour
                                    $remainingProjects = $projectCount - $index - 1;
                                    $maxForThisProject = max(1, $remaining - $remainingProjects);
                                    $projectHours = mt_rand(1, $maxForThisProject);
                                    $remaining -= $projectHours;
                                }
                                
                                $projectDailyHours[$project][$day] = $projectHours;
                                $projectTotalHours[$project] += $projectHours;
                            }
                        }
                    }
                }
            }
            
            // Calculate grand total
            $grandTotal = array_sum($projectTotalHours) + 
                         (count($sickLeaveDays) * 8) + 
                         (count($annualLeaveDays) * 8) + 
                         (count($holidayDays) * 8);
            
            // Add project rows
            foreach ($projects as $project) {
                $col = 'A';
                $sheet->setCellValue($col++ . $row, $project);
                $sheet->setCellValue($col++ . $row, number_format($projectTotalHours[$project], 0));
                
                $currentCol = $col;
                for ($day = 1; $day <= $daysInMonth; $day++) {
                    $currentDate = Carbon::create($startDate->year, $startDate->month, $day);
                    $cellRef = $currentCol . $row;
                    
                    if ($currentDate->isWeekend() || isset($sickLeaveDays[$day]) || 
                        isset($annualLeaveDays[$day]) || isset($holidayDays[$day])) {
                        $sheet->setCellValue($cellRef, '');
                    } else {
                        $hours = $projectDailyHours[$project][$day];
                        $sheet->setCellValue($cellRef, $hours > 0 ? number_format($hours, 0) : '');
                    }
                    
                    // Color weekends with light grey
                    if ($currentDate->isWeekend()) {
                        $sheet->getStyle($cellRef)->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'F0F0F0']
                            ]
                        ]);
                    }
                    
                    $currentCol++;
                }
                
                $loe = $grandTotal > 0 ? round(($projectTotalHours[$project] / $grandTotal) * 100) : 0;
                $sheet->setCellValue($currentCol . $row, $loe . '%');
                
                // Style data row
                $sheet->getStyle('A' . $row . ':' . $lastCol . $row)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'CCCCCC']
                        ]
                    ]
                ]);
                
                $row++;
            }
            
            // Add SICK LEAVE row
            $col = 'A';
            $sickLeaveHours = count($sickLeaveDays) * 8;
            $sheet->setCellValue($col++ . $row, 'SICK LEAVE');
            $sheet->setCellValue($col++ . $row, number_format($sickLeaveHours, 0));
            
            $currentCol = $col;
            for ($day = 1; $day <= $daysInMonth; $day++) {
                $currentDate = Carbon::create($startDate->year, $startDate->month, $day);
                $cellRef = $currentCol . $row;
                $sheet->setCellValue($cellRef, isset($sickLeaveDays[$day]) ? '8' : '');
                
                // Color weekends with light grey
                if ($currentDate->isWeekend()) {
                    $sheet->getStyle($cellRef)->applyFromArray([
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'F0F0F0']
                        ]
                    ]);
                }
                
                $currentCol++;
            }
            
            $loe = $grandTotal > 0 ? round(($sickLeaveHours / $grandTotal) * 100) : 0;
            $sheet->setCellValue($currentCol . $row, $loe . '%');
            $sheet->getStyle('A' . $row . ':' . $lastCol . $row)->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']]]
            ]);
            $row++;
            
            // Add ANNUAL LEAVE row
            $col = 'A';
            $annualLeaveHours = count($annualLeaveDays) * 8;
            $sheet->setCellValue($col++ . $row, 'ANNUAL LEAVE');
            $sheet->setCellValue($col++ . $row, number_format($annualLeaveHours, 0));
            
            $currentCol = $col;
            for ($day = 1; $day <= $daysInMonth; $day++) {
                $currentDate = Carbon::create($startDate->year, $startDate->month, $day);
                $cellRef = $currentCol . $row;
                $sheet->setCellValue($cellRef, isset($annualLeaveDays[$day]) ? '8' : '');
                
                // Color weekends with light grey
                if ($currentDate->isWeekend()) {
                    $sheet->getStyle($cellRef)->applyFromArray([
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'F0F0F0']
                        ]
                    ]);
                }
                
                $currentCol++;
            }
            
            $loe = $grandTotal > 0 ? round(($annualLeaveHours / $grandTotal) * 100) : 0;
            $sheet->setCellValue($currentCol . $row, $loe . '%');
            $sheet->getStyle('A' . $row . ':' . $lastCol . $row)->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']]]
            ]);
            $row++;
            
            // Add PUBLIC HOLIDAY row
            $col = 'A';
            $holidayHours = count($holidayDays) * 8;
            $sheet->setCellValue($col++ . $row, 'PUBLIC HOLIDAY');
            $sheet->setCellValue($col++ . $row, number_format($holidayHours, 0));
            
            $currentCol = $col;
            for ($day = 1; $day <= $daysInMonth; $day++) {
                $currentDate = Carbon::create($startDate->year, $startDate->month, $day);
                $cellRef = $currentCol . $row;
                $sheet->setCellValue($cellRef, isset($holidayDays[$day]) ? '8' : '');
                
                // Color weekends with light grey
                if ($currentDate->isWeekend()) {
                    $sheet->getStyle($cellRef)->applyFromArray([
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'F0F0F0']
                        ]
                    ]);
                }
                
                $currentCol++;
            }
            
            $loe = $grandTotal > 0 ? round(($holidayHours / $grandTotal) * 100) : 0;
            $sheet->setCellValue($currentCol . $row, $loe . '%');
            $sheet->getStyle('A' . $row . ':' . $lastCol . $row)->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']]]
            ]);
            $row++;
            
            // Add Daily total row
            $col = 'A';
            $sheet->setCellValue($col++ . $row, 'Daily total');
            $sheet->setCellValue($col++ . $row, number_format($grandTotal, 0));
            
            $currentCol = $col;
            for ($day = 1; $day <= $daysInMonth; $day++) {
                $currentDate = Carbon::create($startDate->year, $startDate->month, $day);
                $cellRef = $currentCol . $row;
                
                if ($currentDate->isWeekend()) {
                    $sheet->setCellValue($cellRef, '0');
                } elseif (isset($sickLeaveDays[$day]) || isset($annualLeaveDays[$day]) || isset($holidayDays[$day])) {
                    $sheet->setCellValue($cellRef, '8');
                } else {
                    $dayTotal = 0;
                    foreach ($projects as $project) {
                        $dayTotal += $projectDailyHours[$project][$day];
                    }
                    $sheet->setCellValue($cellRef, $dayTotal > 0 ? (string)$dayTotal : '0');
                }
                
                // Color weekends with light grey
                if ($currentDate->isWeekend()) {
                    $sheet->getStyle($cellRef)->applyFromArray([
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'F0F0F0']
                        ]
                    ]);
                }
                
                $currentCol++;
            }
            
            $sheet->setCellValue($currentCol . $row, '100%');
            $sheet->getStyle('A' . $row . ':' . $lastCol . $row)->applyFromArray([
                'font' => ['bold' => true],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]
            ]);
            
            $row += 2;
            
            // Calculate working days
            $workingDays = 0;
            for ($day = 1; $day <= $daysInMonth; $day++) {
                $currentDate = Carbon::create($startDate->year, $startDate->month, $day);
                if (!$currentDate->isWeekend()) {
                    $workingDays++;
                }
            }
            
            // Add summary
            $sheet->setCellValue('A' . $row, 'NUMBER OF HOURS WORKED:');
            $sheet->setCellValue('B' . $row, number_format($grandTotal, 0));
            $sheet->getStyle('A' . $row)->getFont()->setBold(true);
            $row++;
            
            $sheet->setCellValue('A' . $row, 'NUMBER OF DAYS WORKED:');
            $sheet->setCellValue('B' . $row, $workingDays);
            $sheet->getStyle('A' . $row)->getFont()->setBold(true);
            $row += 2;
            
            $sheet->setCellValue('A' . $row, 'SIGNED:');
            $sheet->setCellValue('B' . $row, '____________________________');
            $row++;
            
            $sheet->setCellValue('B' . $row, '(' . $timesheetData['user']->name . ')');
            $row++;
            
            $sheet->setCellValue('A' . $row, 'DATE:');
            $sheet->setCellValue('B' . $row, Carbon::now()->format('d/m/Y'));
            $row += 2;
            
            $sheet->setCellValue('A' . $row, 'APPROVED BY:');
            $sheet->setCellValue('B' . $row, '____________________________');
            $row++;
            
            $supervisorName = $timesheetData['user']->supervisor ? $timesheetData['user']->supervisor->name : 'N/A';
            $sheet->setCellValue('B' . $row, '(' . strtoupper($supervisorName) . ')');
            $row++;
            
            $sheet->setCellValue('A' . $row, 'POSITION:');
            $supervisorPosition = $timesheetData['user']->supervisor && $timesheetData['user']->supervisor->designation 
                ? $timesheetData['user']->supervisor->designation->name 
                : ($timesheetData['user']->supervisor && $timesheetData['user']->supervisor->userLevel 
                    ? $timesheetData['user']->supervisor->userLevel->name 
                    : 'N/A');
            $sheet->setCellValue('B' . $row, $supervisorPosition);
            $row++;
            
            $sheet->setCellValue('A' . $row, 'DATE:');
            $sheet->setCellValue('B' . $row, '____________________________');
            $row += 2;
            
            $sheet->setCellValue('A' . $row, 'Explanatory notes');
            $sheet->getStyle('A' . $row)->getFont()->setBold(true);
            $row++;
            
            $sheet->setCellValue('A' . $row, '- This template is adapted for use in cases where a person is working for several projects or tasks in a same period');
            $row++;
            $sheet->setCellValue('A' . $row, '- To avoid errors, it may be useful to highlight week-ends or public holidays');
            $row++;
            $sheet->setCellValue('A' . $row, '- If you prefer to use timesheets with a periodicity of less than a month (for example weekly or bi-weekly), please adjust the template accordingly');
            
            // Auto-size columns - iterate through all columns used
            $columnIterator = $sheet->getColumnIterator();
            foreach ($columnIterator as $column) {
                $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
            }
            
            // Generate filename
            $filename = 'timesheet-' . $this->sanitizeFilename($timesheetData['user']->name) . '-' . 
                       Carbon::parse($this->startDate)->format('Y-m') . '.xlsx';
            
            // Create Excel file
            $writer = new Xlsx($spreadsheet);
            $tempFile = tempnam(sys_get_temp_dir(), 'timesheet_');
            $writer->save($tempFile);
            
            return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
            
        } catch (\Exception $e) {
            \Log::error('Timesheet Excel export error: ' . $e->getMessage());
            $this->dispatch('toast', [
                'message' => 'Failed to generate timesheet: ' . $e->getMessage(),
                'variant' => 'danger'
            ]);
        }
    }

    public function exportTimesheetPdf()
    {
        $this->closeTimesheetModal();
        
        try {
            $timesheetData = $this->generateTimesheetData();
            $timesheetData['projectNames'] = $this->getFormattedProjects();
            
            // Load supervisor relationship
            $this->user->load('supervisor.designation', 'supervisor.userLevel');
            $timesheetData['user'] = $this->user;
            
            $pdf = Pdf::loadView('reports.timesheet_pdf', $timesheetData)
                ->setPaper('a4', 'landscape')
                ->setOptions([
                    'defaultFont' => 'DejaVu Sans',
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled' => true,
                ]);
            
            return $pdf->download('timesheet-' . $this->sanitizeFilename($timesheetData['user']->name) . '-' . 
                                 Carbon::parse($this->startDate)->format('Y-m') . '.pdf');
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'message' => 'Failed to generate timesheet: ' . $e->getMessage(),
                'variant' => 'danger'
            ]);
        }
    }

    private function generateTimesheetData()
    {
        $startDate = Carbon::parse($this->startDate);
        $endDate = Carbon::parse($this->endDate);
        
        // Get all attendances for the period
        $attendances = Attendance::where('user_id', $this->user->id)
            ->whereBetween('in_time', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->orderBy('in_time', 'asc')
            ->get();
        
        // Get approved leaves for the period
        $leaves = \App\Models\Leave::where('user_id', $this->user->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->with(['category', 'status'])
            ->get();
        
        // Get holidays for the period
        $holidays = \App\Models\Holiday::whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'asc')
            ->get();
        
        // Build timesheet entries
        $entries = [];
        $currentDate = $startDate->copy();
        
        $totalHours = 0;
        $daysWorked = 0;
        $sickLeaveDays = 0;
        $annualLeaveDays = 0;
        $publicHolidays = 0;
        
        while ($currentDate <= $endDate) {
            $dateStr = $currentDate->format('Y-m-d');
            
            // Check if it's a holiday
            $holiday = $holidays->firstWhere('date', $dateStr);
            
            // Check if it's a leave day
            $leave = $leaves->firstWhere(function($l) use ($dateStr) {
                return $l->date->format('Y-m-d') === $dateStr && 
                       strtolower($l->status->name) === 'approved';
            });
            
            // Check if there's attendance
            $attendance = $attendances->firstWhere(function($a) use ($dateStr) {
                return Carbon::parse($a->in_time)->format('Y-m-d') === $dateStr;
            });
            
            $entry = [
                'date' => $currentDate->format('M d, Y'),
                'day' => $currentDate->format('l'),
                'clockIn' => '',
                'clockOut' => '',
                'hoursWorked' => '',
                'status' => '',
                'notes' => '',
            ];
            
            if ($holiday) {
                $entry['status'] = 'Public Holiday';
                $entry['notes'] = $this->sanitizeUtf8($holiday->name ?? '');
                $entry['hoursWorked'] = '8.0';
                $publicHolidays++;
            } elseif ($leave) {
                $leaveType = strtolower($leave->category->name ?? '');
                if (str_contains($leaveType, 'sick')) {
                    $entry['status'] = 'Sick Leave';
                    $sickLeaveDays++;
                } else {
                    $entry['status'] = 'Annual Leave';
                    $annualLeaveDays++;
                }
                $entry['notes'] = $this->sanitizeUtf8($leave->description ?? '');
                $entry['hoursWorked'] = '8.0';
            } elseif ($attendance) {
                $entry['clockIn'] = Carbon::parse($attendance->in_time)->format('H:i:s');
                $entry['clockOut'] = $attendance->out_time ? 
                    Carbon::parse($attendance->out_time)->format('H:i:s') : 'Not clocked out';
                
                if ($attendance->out_time) {
                    $inTime = Carbon::parse($attendance->in_time);
                    $outTime = Carbon::parse($attendance->out_time);
                    $secondsWorked = $outTime->timestamp - $inTime->timestamp;
                    
                    if ($secondsWorked > 0) {
                        $hoursWorked = $secondsWorked / 3600;
                        // Cap at 8 hours per day
                        $hoursWorked = min(8.0, $hoursWorked);
                        $entry['hoursWorked'] = number_format($hoursWorked, 1);
                        $totalHours += $hoursWorked;
                        $entry['status'] = 'Present';
                        $daysWorked++;
                    }
                } else {
                    $entry['status'] = 'In Progress';
                }
                
                $notes = [];
                if ($attendance->in_message) $notes[] = 'In: ' . $this->sanitizeUtf8($attendance->in_message);
                if ($attendance->out_message) $notes[] = 'Out: ' . $this->sanitizeUtf8($attendance->out_message);
                $entry['notes'] = implode(' | ', $notes);
            } elseif ($currentDate->isWeekend()) {
                $entry['status'] = 'Weekend';
            } else {
                $entry['status'] = 'Absent';
            }
            
            $entries[] = $entry;
            $currentDate->addDay();
        }
        
        return [
            'user' => $this->user,
            'period' => $startDate->format('F Y'),
            'startDate' => $startDate->format('Y-m-d'),
            'endDate' => $endDate->format('Y-m-d'),
            'entries' => $entries,
            'summary' => [
                'daysWorked' => $daysWorked,
                'totalHours' => number_format($totalHours, 1),
                'sickLeaveDays' => $sickLeaveDays,
                'annualLeaveDays' => $annualLeaveDays,
                'publicHolidays' => $publicHolidays,
            ],
        ];
    }

    /**
     * Get formatted project names for the user
     */
    private function getFormattedProjects()
    {
        $projects = $this->user->projects()->pluck('name')->toArray();
        
        if (empty($projects)) {
            return 'N/A';
        }
        
        $count = count($projects);
        
        if ($count === 1) {
            return $projects[0];
        } elseif ($count === 2) {
            return $projects[0] . ' & ' . $projects[1];
        } else {
            // More than 2 projects: "Project1, Project2, Project3 & Project4"
            $lastProject = array_pop($projects);
            return implode(', ', $projects) . ' & ' . $lastProject;
        }
    }

    /**
     * Get array of project names for the user
     */
    private function getUserProjects()
    {
        $projects = $this->user->projects()->pluck('name')->toArray();
        
        // If no projects assigned, return default projects
        if (empty($projects)) {
            return ['WELLS', 'ALIVE'];
        }
        
        return $projects;
    }

    /**
     * Sanitize string for UTF-8 encoding to prevent PDF generation errors
     */
    private function sanitizeUtf8($string)
    {
        if (empty($string)) {
            return '';
        }
        
        // Convert to UTF-8 if not already
        $string = mb_convert_encoding($string, 'UTF-8', 'UTF-8');
        
        // Remove any invalid UTF-8 characters
        $string = mb_scrub($string, 'UTF-8');
        
        // Remove control characters except newlines and tabs
        $string = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $string);
        
        return $string;
    }

    /**
     * Sanitize filename for download
     */
    private function sanitizeFilename($filename)
    {
        // Remove special characters and spaces
        $filename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $filename);
        return $filename;
    }

    public function render()
    {
        return view('livewire.attendance.user-attendance')->layout('components.layouts.app', ['title' => 'Attendance']);
    }
}
