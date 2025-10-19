<?php

namespace App\Livewire\Notices;

use App\Models\Notice;
use App\Services\NotificationService;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Str;

class NoticeList extends Component
{
    use WithPagination;

    public $search = '';
    public $sortBy = 'created_at';
    public $sortOrder = 'desc';
    public $perPage = 10;
    
    public $showCreateModal = false;
    public $showEditModal = false;
    public $showDeleteModal = false;
    public $showDetailModal = false;
    
    public $noticeId = null;
    public $subject = '';
    public $message = '';
    public $is_active = true;
    
    public $selectedNotice = null;
    public $isAdmin = false;

    protected $rules = [
        'subject' => 'required|string|max:255',
        'message' => 'required|string',
        'is_active' => 'boolean',
    ];

    protected $messages = [
        'subject.required' => 'Subject is required',
        'subject.max' => 'Subject cannot exceed 255 characters',
        'message.required' => 'Message is required',
    ];

    public function mount()
    {
        $this->isAdmin = strtolower(auth()->user()->userLevel->name) === 'admin';
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

    public function openCreateModal()
    {
        if (!$this->isAdmin) {
            $this->dispatch('toast', [
                'message' => 'Unauthorized action',
                'variant' => 'danger'
            ]);
            return;
        }

        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
        $this->resetForm();
    }

    public function createNotice()
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
            $notice = Notice::create([
                'id' => Str::uuid()->toString(),
                'subject' => $this->subject,
                'message' => $this->message,
                'is_active' => $this->is_active ?? true,
            ]);

            // Send notifications to all users
            $notificationService = app(NotificationService::class);
            $notificationService->notifyNewNotice($notice->id, $notice->subject, auth()->id());

            $this->dispatch('toast', [
                'message' => 'Notice created successfully',
                'variant' => 'success'
            ]);

            $this->closeCreateModal();
            
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'message' => 'Error creating notice: ' . $e->getMessage(),
                'variant' => 'danger'
            ]);
        }
    }

    public function openEditModal($noticeId)
    {
        if (!$this->isAdmin) {
            $this->dispatch('toast', [
                'message' => 'Unauthorized action',
                'variant' => 'danger'
            ]);
            return;
        }

        $notice = Notice::find($noticeId);
        
        if ($notice) {
            $this->noticeId = $notice->id;
            $this->subject = $notice->subject;
            $this->message = $notice->message;
            $this->is_active = $notice->is_active ?? true;
            $this->showEditModal = true;
        }
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->resetForm();
    }

    public function updateNotice()
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
            $notice = Notice::findOrFail($this->noticeId);

            $notice->update([
                'subject' => $this->subject,
                'message' => $this->message,
                'is_active' => $this->is_active ?? true,
            ]);

            $this->dispatch('toast', [
                'message' => 'Notice updated successfully',
                'variant' => 'success'
            ]);

            $this->closeEditModal();
            
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'message' => 'Error updating notice: ' . $e->getMessage(),
                'variant' => 'danger'
            ]);
        }
    }

    public function viewNotice($noticeId)
    {
        $this->selectedNotice = Notice::find($noticeId);
        
        if ($this->selectedNotice) {
            $this->showDetailModal = true;
        }
    }

    public function closeDetailModal()
    {
        $this->showDetailModal = false;
        $this->selectedNotice = null;
    }

    public function confirmDelete($noticeId)
    {
        if (!$this->isAdmin) {
            $this->dispatch('toast', [
                'message' => 'Unauthorized action',
                'variant' => 'danger'
            ]);
            return;
        }

        $this->selectedNotice = Notice::find($noticeId);
        
        if ($this->selectedNotice) {
            $this->showDeleteModal = true;
        }
    }

    public function deleteNotice()
    {
        if (!$this->isAdmin) {
            $this->dispatch('toast', [
                'message' => 'Unauthorized action',
                'variant' => 'danger'
            ]);
            return;
        }

        try {
            if (!$this->selectedNotice) {
                throw new \Exception('Notice not found');
            }

            $this->selectedNotice->delete();

            $this->dispatch('toast', [
                'message' => 'Notice deleted successfully',
                'variant' => 'success'
            ]);

            $this->closeDeleteModal();
            
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'message' => 'Error deleting notice: ' . $e->getMessage(),
                'variant' => 'danger'
            ]);
        }
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->selectedNotice = null;
    }

    public function toggleActive($noticeId)
    {
        if (!$this->isAdmin) {
            $this->dispatch('toast', [
                'message' => 'Unauthorized action',
                'variant' => 'danger'
            ]);
            return;
        }

        try {
            $notice = Notice::findOrFail($noticeId);
            $notice->is_active = !$notice->is_active;
            $notice->save();

            $this->dispatch('toast', [
                'message' => 'Notice status updated successfully',
                'variant' => 'success'
            ]);
            
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'message' => 'Error updating notice status: ' . $e->getMessage(),
                'variant' => 'danger'
            ]);
        }
    }

    private function resetForm()
    {
        $this->noticeId = null;
        $this->subject = '';
        $this->message = '';
        $this->is_active = true;
        $this->resetErrorBag();
    }

    public function render()
    {
        $query = Notice::query();

        // Apply search filter
        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('subject', 'like', '%' . $this->search . '%')
                  ->orWhere('message', 'like', '%' . $this->search . '%');
            });
        }

        // Apply sorting
        $query->orderBy($this->sortBy, $this->sortOrder);

        // Paginate results
        $notices = $query->paginate($this->perPage);

        return view('livewire.notices.notice-list', [
            'notices' => $notices,
        ])->layout('components.layouts.app', ['title' => 'Notice Board']);
    }
}
