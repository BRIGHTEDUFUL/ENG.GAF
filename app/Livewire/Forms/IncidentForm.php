<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;
use App\Models\Incident;

class IncidentForm extends Form
{
    public ?Incident $incident = null;

    #[Validate('required|string|max:200')]
    public string $title = '';

    #[Validate('nullable|string')]
    public ?string $location = '';

    #[Validate('required|string')]
    public string $description = '';

    #[Validate('nullable|exists:aircraft,id')]
    public ?int $aircraft_id = null;

    #[Validate('nullable|exists:users,id')]
    public ?int $assigned_investigator_id = null;

    #[Validate('required|in:low,medium,high,critical')]
    public string $severity = 'medium';

    #[Validate('required|in:open,under-investigation,resolved,closed')]
    public string $status = 'open';

    #[Validate('required|date')]
    public string $incident_date = '';

    #[Validate('nullable|string')]
    public ?string $resolution_notes = '';

    public function setIncident(Incident $incident)
    {
        $this->incident = $incident;
        $this->title = $incident->title;
        $this->description = $incident->description;
        $this->aircraft_id = $incident->aircraft_id;
        $this->assigned_investigator_id = $incident->assigned_investigator_id;
        $this->severity = $incident->severity;
        $this->status = $incident->status;
        $this->incident_date = $incident->incident_date->format('Y-m-d');
        $this->location = $incident->location;
        $this->resolution_notes = $incident->resolution_notes;
    }

    public function store()
    {
        $this->validate();
        
        $data = $this->except('incident');
        $data['reported_by'] = auth()->id();
        
        $incident = Incident::create($data);
        
        $this->handleAircraftStatus($incident);
        
        // Notify commanders and supervisors
        $usersToNotify = \App\Models\User::whereIn('role', ['commander', 'supervisor'])->get();
        \Illuminate\Support\Facades\Notification::send($usersToNotify, new \App\Notifications\SystemAlert(
            'New Incident Reported',
            "{$incident->title} on aircraft " . ($incident->aircraft?->tail_number ?? 'Unknown'),
            $incident->severity === 'critical' || $incident->severity === 'high' ? 'error' : 'warning',
            route('incidents.index')
        ));
        
        $this->reset();
    }

    public function update()
    {
        $this->validate();
        
        $data = $this->except('incident');
        if (in_array($this->status, ['resolved', 'closed']) && !$this->incident->resolved_at) {
            $data['resolved_at'] = now();
        } elseif (in_array($this->status, ['open', 'under-investigation'])) {
            $data['resolved_at'] = null;
        }

        $this->incident->update($data);
        
        $this->handleAircraftStatus($this->incident);

        $this->reset();
    }
    
    private function handleAircraftStatus(Incident $incident)
    {
        if (!$incident->aircraft_id) return;
        
        $aircraft = \App\Models\Aircraft::find($incident->aircraft_id);
        if (!$aircraft) return;

        // Ground aircraft if incident is high or critical
        if (in_array($incident->severity, ['high', 'critical']) && in_array($incident->status, ['open', 'under-investigation'])) {
            $aircraft->update(['status' => 'grounded']);
        }
        // If resolved and it was grounded, maybe move to maintenance or active? 
        // We'll let the user decide, but we definitely enforce the grounding.
    }
}
