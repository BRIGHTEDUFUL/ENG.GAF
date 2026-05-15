<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CriticalIncident extends Notification
{
    use Queueable;

    public $incident;

    public function __construct($incident)
    {
        $this->incident = $incident;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'CriticalIncident',
            'message' => "Critical Incident reported: '{$this->incident->title}'",
            'url' => route('incidents.index'),
        ];
    }
}
