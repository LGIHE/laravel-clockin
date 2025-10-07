<?php

namespace App\Livewire\Reports;

use App\Models\User;
use App\Services\ReportService;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

class IndividualReport extends Component
{
    use WithPagination;

    public $userId;
    public $startDate;
    public $endDate;
    public $reportData = null;
    public $users = [];
    public $isAdmin = false;
    public $perPage = 10;
    public $currentPage = 1;

    protected $rules = [
        'userId' => 'required|string|exists:users,id',
        'startDate' => 'required|date',
        'endDate' => 'required|date|after_or_equal:startDate',
    ];

    protected $messages = [
        'userId.required' => 'Please select a user',
        'startDate.required' => 'Start date is required',
        'endDate.required' => 'End date is required',
        'endDate.after_or_equal' => 'End date must be after or equal to start date',
    ];

    public function mount()
    {
        $this->isAdmin = auth()->user()->userLevel->name === 'admin';
        
        // Set default dates (current month)
        $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->endDate = Carbon::now()->format('Y-m-d');
        
        // Load all users for dropdown
        $this->users = User::where('status', 1)
            ->with('userLevel')
            ->orderBy('name')
            ->get();
        
        // Set default to current user
        $this->userId = auth()->id();
        $this->generateReport();
    }

    public function updatedUserId()
    {
        $this->currentPage = 1;
        if ($this->userId && $this->startDate && $this->endDate) {
            $this->generateReport();
        }
    }

    public function updatedStartDate()
    {
        $this->currentPage = 1;
        if ($this->userId && $this->startDate && $this->endDate) {
            $this->generateReport();
        }
    }

    public function updatedEndDate()
    {
        $this->currentPage = 1;
        if ($this->userId && $this->startDate && $this->endDate) {
            $this->generateReport();
        }
    }

    public function updatedPerPage()
    {
        $this->currentPage = 1;
    }

    public function generateReport()
    {
        if (!$this->userId || !$this->startDate || !$this->endDate) {
            return;
        }

        try {
            $reportService = app(ReportService::class);
            $this->reportData = $reportService->generateIndividualReport([
                'user_id' => $this->userId,
                'start_date' => $this->startDate,
                'end_date' => $this->endDate,
            ]);
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to generate report: ' . $e->getMessage());
            $this->reportData = null;
        }
    }

    public function getPaginatedAttendances()
    {
        if (!$this->reportData || !isset($this->reportData['attendances'])) {
            return collect();
        }

        $attendances = collect($this->reportData['attendances']);
        $total = $attendances->count();
        
        return [
            'data' => $attendances->slice(($this->currentPage - 1) * $this->perPage, $this->perPage)->values(),
            'total' => $total,
            'from' => ($this->currentPage - 1) * $this->perPage + 1,
            'to' => min($this->currentPage * $this->perPage, $total),
            'current_page' => $this->currentPage,
            'last_page' => ceil($total / $this->perPage),
        ];
    }

    public function previousPage()
    {
        if ($this->currentPage > 1) {
            $this->currentPage--;
        }
    }

    public function nextPage()
    {
        $paginated = $this->getPaginatedAttendances();
        if ($this->currentPage < $paginated['last_page']) {
            $this->currentPage++;
        }
    }

    public function gotoPage($page)
    {
        $this->currentPage = $page;
    }

    public function exportPdf()
    {
        if (!$this->userId || !$this->startDate || !$this->endDate) {
            session()->flash('error', 'Please select a user and date range');
            return;
        }

        return redirect()->route('reports.export', [
            'type' => 'individual',
            'format' => 'pdf',
            'user_id' => $this->userId,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
        ]);
    }

    public function exportCsv()
    {
        if (!$this->userId || !$this->startDate || !$this->endDate) {
            session()->flash('error', 'Please select a user and date range');
            return;
        }

        return redirect()->route('reports.export', [
            'type' => 'individual',
            'format' => 'csv',
            'user_id' => $this->userId,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
        ]);
    }

    public function exportJson()
    {
        if (!$this->userId || !$this->startDate || !$this->endDate) {
            session()->flash('error', 'Please select a user and date range');
            return;
        }

        return redirect()->route('reports.export', [
            'type' => 'individual',
            'format' => 'json',
            'user_id' => $this->userId,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
        ]);
    }

    public function generateTimesheet()
    {
        if (!$this->userId || !$this->startDate || !$this->endDate) {
            session()->flash('error', 'Please select a user and date range');
            return;
        }

        return redirect()->route('reports.export', [
            'type' => 'timesheet',
            'format' => 'pdf',
            'user_id' => $this->userId,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
        ]);
    }

    public function render()
    {
        $paginatedData = $this->getPaginatedAttendances();
        
        return view('livewire.reports.individual-report', [
            'paginatedAttendances' => $paginatedData
        ])->layout('components.layouts.app', ['title' => 'Individual Report']);
    }
}
