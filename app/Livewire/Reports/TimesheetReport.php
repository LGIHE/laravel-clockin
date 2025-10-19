<?php

namespace App\Livewire\Reports;

use App\Models\User;
use App\Services\ReportService;
use Livewire\Component;
use Carbon\Carbon;

class TimesheetReport extends Component
{
    public $userId;
    public $month;
    public $year;
    public $reportData = null;
    public $users = [];
    public $isAdmin = false;

    protected $rules = [
        'userId' => 'required|string|exists:users,id',
        'month' => 'required|integer|min:1|max:12',
        'year' => 'required|integer|min:2000|max:2100',
    ];

    protected $messages = [
        'userId.required' => 'Please select a user',
        'month.required' => 'Month is required',
        'year.required' => 'Year is required',
    ];

    public function mount()
    {
        $this->isAdmin = strtolower(auth()->user()->userLevel->name) === 'admin';
        
        // Set default to current month and year
        $this->month = Carbon::now()->month;
        $this->year = Carbon::now()->year;
        
        // If admin, load all users; otherwise, set to current user
        if ($this->isAdmin) {
            $this->users = User::where('status', 1)
                ->orderBy('name')
                ->get();
        } else {
            $this->userId = auth()->id();
        }
    }

    public function generateReport()
    {
        $this->validate();

        try {
            $reportService = app(ReportService::class);
            $this->reportData = $reportService->generateTimesheet([
                'user_id' => $this->userId,
                'month' => $this->month,
                'year' => $this->year,
            ]);

            session()->flash('success', 'Timesheet generated successfully');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to generate timesheet: ' . $e->getMessage());
            $this->reportData = null;
        }
    }

    public function exportPdf()
    {
        $this->validate();

        return redirect()->route('reports.export', [
            'type' => 'timesheet',
            'format' => 'pdf',
            'user_id' => $this->userId,
            'month' => $this->month,
            'year' => $this->year,
        ]);
    }

    public function exportExcel()
    {
        $this->validate();

        return redirect()->route('reports.export', [
            'type' => 'timesheet',
            'format' => 'excel',
            'user_id' => $this->userId,
            'month' => $this->month,
            'year' => $this->year,
        ]);
    }

    public function exportCsv()
    {
        $this->validate();

        return redirect()->route('reports.export', [
            'type' => 'timesheet',
            'format' => 'csv',
            'user_id' => $this->userId,
            'month' => $this->month,
            'year' => $this->year,
        ]);
    }

    public function previousMonth()
    {
        $date = Carbon::create($this->year, $this->month, 1)->subMonth();
        $this->month = $date->month;
        $this->year = $date->year;
        $this->reportData = null;
    }

    public function nextMonth()
    {
        $date = Carbon::create($this->year, $this->month, 1)->addMonth();
        $this->month = $date->month;
        $this->year = $date->year;
        $this->reportData = null;
    }

    public function clearReport()
    {
        $this->reportData = null;
    }

    public function render()
    {
        return view('livewire.reports.timesheet-report')
            ->layout('components.layouts.app', ['title' => 'Timesheet Report']);
    }
}
