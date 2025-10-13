<?php

namespace App\Livewire\Notifications;

use Illuminate\Support\Facades\DB;
use Livewire\Component;

class NotificationDropdown extends Component
{
    public $notifications = [];
    public $unreadCount = 0;
    public $isOpen = false;

    protected $listeners = ['notificationRead' => 'loadNotifications'];

    public function mount()
    {
        $this->loadNotifications();
    }

    public function loadNotifications()
    {
        $user = auth()->user();

        // Get unread notifications
        $this->notifications = DB::table('notifications')
            ->where('user_id', $user->id)
            ->where('read', false)
            ->orderBy('created_at', 'desc')
            ->limit(5)
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

        DB::table('notifications')
            ->where('id', $notificationId)
            ->where('user_id', $user->id)
            ->update([
                'read' => true,
                'read_at' => now(),
                'updated_at' => now(),
            ]);

        $this->loadNotifications();

        $this->dispatch('toast', [
            'message' => 'Notification marked as read',
            'variant' => 'success',
        ]);
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

        $this->dispatch('toast', [
            'message' => 'All notifications marked as read',
            'variant' => 'success',
        ]);
    }

    public function toggleDropdown()
    {
        $this->isOpen = !$this->isOpen;
    }

    public function render()
    {
        return view('livewire.notifications.notification-dropdown');
    }
}
