<?php

namespace App\Livewire;

use App\Models\Aircraft;
use App\Models\DailyAircraftState;
use App\Models\Incident;
use App\Models\MaintenanceTask;
use App\Models\FlightLog;
use App\Models\MaintenanceLog;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app', ['heading' => 'Aircraft Profile'])]
class AircraftProfile extends Component
{
    public Aircraft $aircraft;

    public function mount(int $id)
    {
        $this->aircraft = Aircraft::with(['wing'])->findOrFail($id);
    }

    public function render()
    {
        // Panel 1: Upcoming Maintenance (Open Tasks)
        $openTasks = MaintenanceTask::where('aircraft_id', $this->aircraft->id)
            ->where('status', '!=', 'completed')
            ->orderByRaw("FIELD(priority, 'critical', 'high', 'medium', 'low')")
            ->get();

        // Panel 2: Open Incidents
        $openIncidents = Incident::where('aircraft_id', $this->aircraft->id)
            ->whereIn('status', ['open', 'under-investigation'])
            ->orderByRaw("FIELD(severity, 'critical', 'high', 'medium', 'low')")
            ->get();

        // Panel 3: Recent Activity (Flights and Maintenance Logs merged)
        $flights = FlightLog::with('pilot')
            ->where('aircraft_id', $this->aircraft->id)
            ->latest('departure_time')
            ->take(5)
            ->get()
            ->map(function ($f) {
                return [
                    'type' => 'flight',
                    'date' => $f->departure_time,
                    'title' => 'Flight: ' . ucfirst($f->mission_type ?? 'Mission'),
                    'desc' => "Pilot: {$f->pilot?->name} | Duration: " . round($f->flight_duration_minutes/60, 1) . "h",
                    'color' => 'bg-purple-100 text-purple-700',
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>'
                ];
            });

        $maintenance = MaintenanceLog::with(['engineer', 'maintenanceTask'])
            ->where('aircraft_id', $this->aircraft->id)
            ->latest('performed_at')
            ->take(5)
            ->get()
            ->map(function ($m) {
                return [
                    'type' => 'maintenance',
                    'date' => $m->performed_at,
                    'title' => 'Maintenance: ' . ucfirst($m->maintenance_type ?? 'General'),
                    'desc' => "Engineer: {$m->engineer?->name} | Task: " . ($m->maintenanceTask?->title ?? 'None'),
                    'color' => 'bg-amber-100 text-amber-700',
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>'
                ];
            });

        $incidents = Incident::where('aircraft_id', $this->aircraft->id)
            ->latest('incident_date')
            ->take(3)
            ->get()
            ->map(function ($i) {
                return [
                    'type' => 'incident',
                    'date' => $i->incident_date,
                    'title' => 'Incident: ' . ucfirst($i->severity) . ' Severity',
                    'desc' => $i->title,
                    'color' => 'bg-red-100 text-red-700',
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>'
                ];
            });

        $timeline = $flights->concat($maintenance)->concat($incidents)
            ->sortByDesc('date')
            ->values()
            ->take(8);

        // Service thresholds logic (mocked up as an example: 100hr inspection)
        $hoursRemaining = 100 - fmod($this->aircraft->total_flight_hours, 100);
        $serviceProgress = 100 - $hoursRemaining; // Percentage towards next 100hr inspection

        return view('livewire.aircraft-profile', compact(
            'openTasks', 'openIncidents', 'timeline', 'hoursRemaining', 'serviceProgress'
        ));
    }
}
