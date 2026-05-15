<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class LogPendingApproval extends Notification
{
    use Queueable;

    public $log;

    public function __construct($log)
    {
        $this->log = $log;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'LogPendingApproval',
            'message' => "Maintenance log requires your approval.",
            'url' => route('maintenance.logs'),
        ];
    }
}
