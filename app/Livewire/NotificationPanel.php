<?php

namespace App\Livewire;

use Livewire\Component;

class NotificationPanel extends Component
{
    public $unreadCount = 0;
    public $notifications = [];

    public function mount()
    {
        $this->loadNotifications();
    }

    public function loadNotifications()
    {
        if (auth()->check()) {
            $user = auth()->user();
            $this->unreadCount = $user->unreadNotifications()->count();
            $this->notifications = $user->unreadNotifications()->take(5)->get();
        }
    }

    public function markAsRead($id)
    {
        auth()->user()->unreadNotifications()->where('id', $id)->update(['read_at' => now()]);
        $this->loadNotifications();
    }

    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();
        $this->loadNotifications();
    }

    public function render()
    {
        return view('livewire.notification-panel');
    }
}
