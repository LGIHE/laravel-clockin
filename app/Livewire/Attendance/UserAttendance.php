<?php

namespace App\Livewire\Attendance;

use App\Models\Attendance;
use App\Services\AttendanceService;
use Carbon\Carbon;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

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
                    $totalSeconds += $outTime->diffInSeconds($inTime);
                    $daysWorked++;
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
        $diff = $inTime->diff($outTime);

        return sprintf('%02d:%02d:%02d', $diff->h, $diff->i, $diff->s);
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

    public function render()
    {
        return view('livewire.attendance.user-attendance')->layout('components.layouts.app', ['title' => 'Attendance']);
    }
}
