<?php

namespace App\Livewire\Notifications;

use Illuminate\Support\Facades\DB;
use Livewire\Component;

class NotificationDropdown extends Component
{
    public $notifications = [];
    public $allNotifications = [];
    public $unreadCount = 0;
    public $isOpen = false;
    public $showAllModal = false;

    protected $listeners = ['notificationRead' => 'loadNotifications'];

    public function mount()
    {
        $this->loadNotifications();
    }

    public function loadNotifications()
    {
        $user = auth()->user();

        // Get all notifications (both read and unread), limited to recent ones
        $this->notifications = DB::table('notifications')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($notification) {
                $data = is_string($notification->data) ? json_decode($notification->data, true) : $notification->data;
                return (object) [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'title' => $notification->title ?? 'Notification',
                    'message' => $notification->message ?? '',
                    'action_url' => $notification->action_url,
                    'created_at' => $notification->created_at,
                    'read' => $notification->read,
                    'data' => $data,
                ];
            });

        $this->unreadCount = DB::table('notifications')
            ->where('user_id', $user->id)
            ->where('read', false)
            ->count();
    }

    public function markAsRead($notificationId)
    {
        $user = auth()->user();

        $notification = DB::table('notifications')
            ->where('id', $notificationId)
            ->where('user_id', $user->id)
            ->first();

        if ($notification && !$notification->read) {
            DB::table('notifications')
                ->where('id', $notificationId)
                ->where('user_id', $user->id)
                ->update([
                    'read' => true,
                    'read_at' => now(),
                    'updated_at' => now(),
                ]);

            $this->loadNotifications();
            
            // Reload all notifications if modal is open
            if ($this->showAllModal) {
                $this->loadAllNotifications();
            }
        }

        // Return the action URL for navigation
        return $notification ? $notification->action_url : null;
    }

    public function markAllAsRead()
    {
        $user = auth()->user();

        DB::table('notifications')
            ->where('user_id', $user->id)
            ->where('read', false)
            ->update([
                'read' => true,
                'read_at' => now(),
                'updated_at' => now(),
            ]);

        $this->loadNotifications();
        
        // Reload all notifications if modal is open
        if ($this->showAllModal) {
            $this->loadAllNotifications();
        }

        $this->dispatch('toast', [
            'message' => 'All notifications marked as read',
            'variant' => 'success',
        ]);
    }

    public function toggleDropdown()
    {
        $this->isOpen = !$this->isOpen;
    }

    public function openAllNotificationsModal()
    {
        $this->loadAllNotifications();
        $this->showAllModal = true;
    }

    public function closeAllNotificationsModal()
    {
        $this->showAllModal = false;
    }

    public function loadAllNotifications()
    {
        $user = auth()->user();

        // Get all notifications (paginated or limited to a reasonable number)
        $this->allNotifications = DB::table('notifications')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(50) // Show up to 50 notifications
            ->get()
            ->map(function ($notification) {
                $data = is_string($notification->data) ? json_decode($notification->data, true) : $notification->data;
                return (object) [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'title' => $notification->title ?? 'Notification',
                    'message' => $notification->message ?? '',
                    'action_url' => $notification->action_url,
                    'created_at' => $notification->created_at,
                    'read' => $notification->read,
                    'data' => $data,
                ];
            });
    }

    public function render()
    {
        return view('livewire.notifications.notification-dropdown');
    }
}
