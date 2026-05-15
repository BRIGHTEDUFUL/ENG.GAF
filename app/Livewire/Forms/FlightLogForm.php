<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;
use App\Models\FlightLog;
use Carbon\Carbon;

class FlightLogForm extends Form
{
    public ?FlightLog $log = null;

    #[Validate('required|exists:aircraft,id')]
    public ?int $aircraft_id = null;

    #[Validate('required|exists:users,id')]
    public ?int $pilot_id = null;

    #[Validate('nullable|exists:users,id')]
    public ?int $co_pilot_id = null;

    #[Validate('required|string|max:255')]
    public string $departure_location = '';

    #[Validate('required|string|max:255')]
    public string $arrival_location = '';

    #[Validate('required|date')]
    public string $departure_time = '';

    #[Validate('required|date|after:departure_time')]
    public string $arrival_time = '';

    #[Validate('required|in:training,operational,test,ferry')]
    public string $mission_type = 'training';

    #[Validate('nullable|integer|min:0')]
    public ?int $max_altitude_ft = null;

    #[Validate('nullable|integer|min:0')]
    public ?int $max_speed_knots = null;

    #[Validate('nullable|string')]
    public ?string $notes = '';

    public function setLog(FlightLog $log)
    {
        $this->log = $log;
        $this->aircraft_id = $log->aircraft_id;
        $this->pilot_id = $log->pilot_id;
        $this->co_pilot_id = $log->co_pilot_id;
        $this->departure_location = $log->departure_location;
        $this->arrival_location = $log->arrival_location;
        $this->departure_time = $log->departure_time->format('Y-m-d\TH:i');
        $this->arrival_time = $log->arrival_time->format('Y-m-d\TH:i');
        $this->mission_type = $log->mission_type;
        $this->max_altitude_ft = $log->max_altitude_ft;
        $this->max_speed_knots = $log->max_speed_knots;
        $this->notes = $log->notes;
    }

    public function store()
    {
        $this->validate();
        
        $duration = Carbon::parse($this->departure_time)->diffInMinutes(Carbon::parse($this->arrival_time));
        
        $log = FlightLog::create($this->all() + ['flight_duration_minutes' => $duration]);
        
        // Auto-update Aircraft Hours
        if ($log->aircraft) {
            $hours = $duration / 60;
            $log->aircraft->increment('total_flight_hours', $hours);
            $this->checkMaintenanceThreshold($log->aircraft);
        }

        $this->reset();
    }

    public function update()
    {
        $this->validate();
        
        $oldDuration = $this->log->flight_duration_minutes;
        $newDuration = Carbon::parse($this->departure_time)->diffInMinutes(Carbon::parse($this->arrival_time));
        
        $this->log->update($this->all() + ['flight_duration_minutes' => $newDuration]);

        // Auto-update Aircraft Hours (apply the difference)
        if ($this->log->aircraft) {
            $diffHours = ($newDuration - $oldDuration) / 60;
            if ($diffHours != 0) {
                $this->log->aircraft->increment('total_flight_hours', $diffHours);
                if ($diffHours > 0) {
                    $this->checkMaintenanceThreshold($this->log->aircraft);
                }
            }
        }

        $this->reset();
    }

    private function checkMaintenanceThreshold(\App\Models\Aircraft $aircraft)
    {
        // Example: Auto-create a maintenance task if approaching a 100hr interval
        $hours = $aircraft->total_flight_hours;
        $nextService = ceil($hours / 100) * 100;
        
        // If within 5 hours of the next 100hr phase, and we haven't already created a task for it
        if (($nextService - $hours) <= 5) {
            $taskTitle = "Scheduled 100HR Phase Inspection (Due at {$nextService}hrs)";
            
            $exists = \App\Models\MaintenanceTask::where('aircraft_id', $aircraft->id)
                ->where('title', $taskTitle)
                ->exists();
                
            if (!$exists) {
                \App\Models\MaintenanceTask::create([
                    'aircraft_id' => $aircraft->id,
                    'title' => $taskTitle,
                    'description' => "Automated system alert: Aircraft has reached " . number_format($hours, 1) . " hours and is due for its {$nextService}-hour phase inspection.",
                    'priority' => 'high',
                    'status' => 'pending',
                    'due_date' => now()->addDays(7),
                ]);
            }
        }
    }
}
