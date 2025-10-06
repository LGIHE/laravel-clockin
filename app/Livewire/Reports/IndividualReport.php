<?php

namespace App\Livewire\Reports;

use App\Models\User;
use App\Services\ReportService;
use Livewire\Component;
use Carbon\Carbon;

class IndividualReport extends Component
{
    public $userId;
    public $startDate;
    public $endDate;
    public $reportData = null;
    public $users = [];
    public $isAdmin = false;

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
        
        // Set default dates (last 30 days)
        $this->endDate = Carbon::now()->format('Y-m-d');
        $this->startDate = Carbon::now()->subDays(30)->format('Y-m-d');
        
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
            $this->reportData = $reportService->generateIndividualReport([
                'user_id' => $this->userId,
                'start_date' => $this->startDate,
                'end_date' => $this->endDate,
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

        return redirect()->route('reports.export', [
            'type' => 'individual',
            'format' => 'pdf',
            'user_id' => $this->userId,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
        ]);
    }

    public function exportExcel()
    {
        $this->validate();

        return redirect()->route('reports.export', [
            'type' => 'individual',
            'format' => 'excel',
            'user_id' => $this->userId,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
        ]);
    }

    public function exportCsv()
    {
        $this->validate();

        return redirect()->route('reports.export', [
            'type' => 'individual',
            'format' => 'csv',
            'user_id' => $this->userId,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
        ]);
    }

    public function clearReport()
    {
        $this->reportData = null;
    }

    public function render()
    {
        return view('livewire.reports.individual-report');
    }
}
