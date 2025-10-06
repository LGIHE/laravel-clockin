<?php

namespace App\Livewire\Reports;

use App\Models\User;
use App\Models\Department;
use App\Models\Project;
use App\Services\ReportService;
use Livewire\Component;
use Carbon\Carbon;

class SummaryReport extends Component
{
    public $startDate;
    public $endDate;
    public $userId = '';
    public $departmentId = '';
    public $projectId = '';
    public $reportData = null;
    
    public $users = [];
    public $departments = [];
    public $projects = [];

    protected $rules = [
        'startDate' => 'required|date',
        'endDate' => 'required|date|after_or_equal:startDate',
        'userId' => 'nullable|string|exists:users,id',
        'departmentId' => 'nullable|string|exists:departments,id',
        'projectId' => 'nullable|string|exists:projects,id',
    ];

    protected $messages = [
        'startDate.required' => 'Start date is required',
        'endDate.required' => 'End date is required',
        'endDate.after_or_equal' => 'End date must be after or equal to start date',
    ];

    public function mount()
    {
        // Set default dates (last 30 days)
        $this->endDate = Carbon::now()->format('Y-m-d');
        $this->startDate = Carbon::now()->subDays(30)->format('Y-m-d');
        
        // Load filter options
        $this->users = User::where('status', 1)->orderBy('name')->get();
        $this->departments = Department::orderBy('name')->get();
        $this->projects = Project::where('status', 'ACTIVE')->orderBy('name')->get();
    }

    public function generateReport()
    {
        $this->validate();

        try {
            $reportService = app(ReportService::class);
            $this->reportData = $reportService->generateSummaryReport([
                'start_date' => $this->startDate,
                'end_date' => $this->endDate,
                'user_id' => $this->userId ?: null,
                'department_id' => $this->departmentId ?: null,
                'project_id' => $this->projectId ?: null,
            ]);

            session()->flash('success', 'Report generated successfully');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to generate report: ' . $e->getMessage());
            $this->reportData = null;
        }
    }

    public function exportPdf()
    {
        $this->validate();

        return redirect()->route('reports.export', array_filter([
            'type' => 'summary',
            'format' => 'pdf',
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'user_id' => $this->userId ?: null,
            'department_id' => $this->departmentId ?: null,
            'project_id' => $this->projectId ?: null,
        ]));
    }

    public function exportExcel()
    {
        $this->validate();

        return redirect()->route('reports.export', array_filter([
            'type' => 'summary',
            'format' => 'excel',
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'user_id' => $this->userId ?: null,
            'department_id' => $this->departmentId ?: null,
            'project_id' => $this->projectId ?: null,
        ]));
    }

    public function exportCsv()
    {
        $this->validate();

        return redirect()->route('reports.export', array_filter([
            'type' => 'summary',
            'format' => 'csv',
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'user_id' => $this->userId ?: null,
            'department_id' => $this->departmentId ?: null,
            'project_id' => $this->projectId ?: null,
        ]));
    }

    public function clearFilters()
    {
        $this->userId = '';
        $this->departmentId = '';
        $this->projectId = '';
        $this->reportData = null;
    }

    public function clearReport()
    {
        $this->reportData = null;
    }

    public function render()
    {
        return view('livewire.reports.summary-report');
    }
}
