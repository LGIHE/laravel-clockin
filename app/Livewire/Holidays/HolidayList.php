<?php

namespace App\Livewire\Holidays;

use App\Models\Holiday;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Str;
use Carbon\Carbon;

class HolidayList extends Component
{
    use WithPagination;

    public $search = '';
    public $sortBy = 'date';
    public $sortOrder = 'asc';
    public $perPage = 15;
    
    public $showCreateModal = false;
    public $showEditModal = false;
    public $showDeleteModal = false;
    
    public $holidayId = null;
    public $date = '';
    
    public $selectedHoliday = null;
    public $isAdmin = false;
    
    // Calendar view properties
    public $viewMode = 'list'; // 'list' or 'calendar'
    public $currentMonth;
    public $currentYear;

    protected $rules = [
        'date' => 'required|date',
    ];

    protected $messages = [
        'date.required' => 'Holiday date is required',
        'date.date' => 'Please enter a valid date',
    ];

    public function mount()
    {
        $this->isAdmin = auth()->user()->userLevel->name === 'admin';
        $this->currentMonth = now()->month;
        $this->currentYear = now()->year;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function sortByColumn($column)
    {
        if ($this->sortBy === $column) {
            $this->sortOrder = $this->sortOrder === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortOrder = 'asc';
        }
    }

    public function toggleViewMode()
    {
        $this->viewMode = $this->viewMode === 'list' ? 'calendar' : 'list';
    }

    public function previousMonth()
    {
        $date = Carbon::create($this->currentYear, $this->currentMonth, 1)->subMonth();
        $this->currentMonth = $date->month;
        $this->currentYear = $date->year;
    }

    public function nextMonth()
    {
        $date = Carbon::create($this->currentYear, $this->currentMonth, 1)->addMonth();
        $this->currentMonth = $date->month;
        $this->currentYear = $date->year;
    }

    public function goToToday()
    {
        $this->currentMonth = now()->month;
        $this->currentYear = now()->year;
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
        $this->resetForm();
    }

    public function createHoliday()
    {
        if (!$this->isAdmin) {
            $this->dispatch('toast', [
                'message' => 'Unauthorized action',
                'variant' => 'danger'
            ]);
            return;
        }

        $this->validate();

        try {
            // Check for duplicate date
            $exists = Holiday::whereDate('date', $this->date)->exists();
            if ($exists) {
                $this->addError('date', 'A holiday already exists on this date');
                return;
            }

            Holiday::create([
                'id' => Str::uuid()->toString(),
                'date' => $this->date,
            ]);

            $this->dispatch('toast', [
                'message' => 'Holiday created successfully',
                'variant' => 'success'
            ]);

            $this->closeCreateModal();
            
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'message' => 'Error creating holiday: ' . $e->getMessage(),
                'variant' => 'danger'
            ]);
        }
    }

    public function openEditModal($holidayId)
    {
        if (!$this->isAdmin) {
            $this->dispatch('toast', [
                'message' => 'Unauthorized action',
                'variant' => 'danger'
            ]);
            return;
        }

        $holiday = Holiday::find($holidayId);
        
        if ($holiday) {
            $this->holidayId = $holiday->id;
            $this->date = $holiday->date->format('Y-m-d');
            $this->showEditModal = true;
        }
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->resetForm();
    }

    public function updateHoliday()
    {
        if (!$this->isAdmin) {
            $this->dispatch('toast', [
                'message' => 'Unauthorized action',
                'variant' => 'danger'
            ]);
            return;
        }

        $this->validate();

        try {
            $holiday = Holiday::findOrFail($this->holidayId);

            // Check for duplicate date (excluding current holiday)
            $exists = Holiday::whereDate('date', $this->date)
                ->where('id', '!=', $this->holidayId)
                ->exists();
            
            if ($exists) {
                $this->addError('date', 'A holiday already exists on this date');
                return;
            }

            $holiday->update([
                'date' => $this->date,
            ]);

            $this->dispatch('toast', [
                'message' => 'Holiday updated successfully',
                'variant' => 'success'
            ]);

            $this->closeEditModal();
            
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'message' => 'Error updating holiday: ' . $e->getMessage(),
                'variant' => 'danger'
            ]);
        }
    }

    public function confirmDelete($holidayId)
    {
        if (!$this->isAdmin) {
            $this->dispatch('toast', [
                'message' => 'Unauthorized action',
                'variant' => 'danger'
            ]);
            return;
        }

        $this->selectedHoliday = Holiday::find($holidayId);
        
        if ($this->selectedHoliday) {
            $this->showDeleteModal = true;
        }
    }

    public function deleteHoliday()
    {
        if (!$this->isAdmin) {
            $this->dispatch('toast', [
                'message' => 'Unauthorized action',
                'variant' => 'danger'
            ]);
            return;
        }

        try {
            if (!$this->selectedHoliday) {
                throw new \Exception('Holiday not found');
            }

            $this->selectedHoliday->delete();

            $this->dispatch('toast', [
                'message' => 'Holiday deleted successfully',
                'variant' => 'success'
            ]);

            $this->closeDeleteModal();
            
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'message' => 'Error deleting holiday: ' . $e->getMessage(),
                'variant' => 'danger'
            ]);
        }
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->selectedHoliday = null;
    }

    private function resetForm()
    {
        $this->holidayId = null;
        $this->date = '';
        $this->resetErrorBag();
    }

    public function getCalendarData()
    {
        $startOfMonth = Carbon::create($this->currentYear, $this->currentMonth, 1);
        $endOfMonth = $startOfMonth->copy()->endOfMonth();
        
        // Get the first day of the week for the calendar (start from Sunday)
        $startDate = $startOfMonth->copy()->startOfWeek(Carbon::SUNDAY);
        $endDate = $endOfMonth->copy()->endOfWeek(Carbon::SATURDAY);
        
        // Get all holidays for the current month
        $holidays = Holiday::whereBetween('date', [$startOfMonth, $endOfMonth])
            ->orderBy('date')
            ->get()
            ->keyBy(function($holiday) {
                return $holiday->date->format('Y-m-d');
            });
        
        $weeks = [];
        $currentDate = $startDate->copy();
        
        while ($currentDate <= $endDate) {
            $week = [];
            for ($i = 0; $i < 7; $i++) {
                $dateKey = $currentDate->format('Y-m-d');
                $week[] = [
                    'date' => $currentDate->copy(),
                    'isCurrentMonth' => $currentDate->month === $this->currentMonth,
                    'isToday' => $currentDate->isToday(),
                    'isHoliday' => isset($holidays[$dateKey]),
                    'holiday' => $holidays[$dateKey] ?? null,
                ];
                $currentDate->addDay();
            }
            $weeks[] = $week;
        }
        
        return $weeks;
    }

    public function render()
    {
        $query = Holiday::query();

        // Apply search filter (search by date)
        if (!empty($this->search)) {
            $query->whereDate('date', 'like', '%' . $this->search . '%');
        }

        // Apply sorting
        $query->orderBy($this->sortBy, $this->sortOrder);

        // Paginate results for list view
        $holidays = $query->paginate($this->perPage);
        
        // Get calendar data for calendar view
        $calendarWeeks = $this->viewMode === 'calendar' ? $this->getCalendarData() : [];

        return view('livewire.holidays.holiday-list', [
            'holidays' => $holidays,
            'calendarWeeks' => $calendarWeeks,
        ]);
    }
}
