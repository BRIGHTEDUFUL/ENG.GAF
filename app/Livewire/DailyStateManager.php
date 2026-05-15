<?php

namespace App\Livewire;

use App\Models\Aircraft;
use App\Models\DailyAircraftState;
use App\Models\DailyDefect;
use App\Models\DailyServiceRemark;
use App\Models\Wing;
use App\Models\AuditLog;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app', ['heading' => 'Daily Aircraft State'])]
class DailyStateManager extends Component
{
    // ── Filters ─────────────────────────────────────────
    public string  $reportDate  = '';
    public ?int    $wingFilter  = null;

    // ── Modal state ─────────────────────────────────────
    public bool   $showModal         = false;
    public ?int   $editingStateId    = null;
    public bool   $showDeleteConfirm = false;
    public ?int   $deletingId        = null;

    // ── Form ─────────────────────────────────────────────
    public array $form = [
        'aircraft_id'      => '',
        'wing_id'          => '',
        'daily_flight_hrs' => '',
        'total_flight_hrs' => '',
        'daily_landings'   => 0,
        'total_landings'   => '',
        'state'            => 'S',
        'notes'            => '',
    ];

    public array $defects = [];
    public array $remarks = [];

    // ── Lifecycle ────────────────────────────────────────
    public function mount(): void
    {
        $this->reportDate = now()->toDateString();
    }

    // ── Render ───────────────────────────────────────────
    public function render()
    {
        $query = DailyAircraftState::with(['aircraft', 'wing', 'defects', 'serviceRemarks'])
            ->forDate($this->reportDate)
            ->when($this->wingFilter, fn($q) => $q->forWing($this->wingFilter))
            ->orderBy('wing_id')
            ->orderBy('aircraft_id');

        $states   = $query->get();
        $grouped  = $states->groupBy(fn($s) => $s->wing?->name ?? 'Unassigned');
        $wings    = Wing::orderBy('name')->get();
        $aircraft = Aircraft::with('wing')->orderBy('tail_number')->get();

        $todayS   = DailyAircraftState::forDate($this->reportDate)->where('state', 'S')->count();
        $todayUS  = DailyAircraftState::forDate($this->reportDate)->where('state', 'U/S')->count();
        $todayGND = DailyAircraftState::forDate($this->reportDate)->where('state', 'grounded')->count();
        $critical = DailyDefect::whereHas(
            'dailyAircraftState', fn($q) => $q->forDate($this->reportDate)
        )->where('is_critical', true)->count();

        return view('livewire.daily-state-manager', compact(
            'grouped', 'wings', 'aircraft',
            'todayS', 'todayUS', 'todayGND', 'critical'
        ));
    }

    // ── Modal open/close ─────────────────────────────────
    public function openCreate(): void
    {
        $this->reset('form', 'defects', 'remarks', 'editingStateId');
        $this->form['state'] = 'S';
        $this->defects = [['defect_number' => 1, 'description' => '', 'is_critical' => false]];
        $this->remarks = [['remark_number' => 1, 'description' => '', 'due_hours' => '', 'service_location' => '']];
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $state = DailyAircraftState::with(['defects', 'serviceRemarks'])->findOrFail($id);
        $this->editingStateId = $id;

        $this->form = [
            'aircraft_id'      => $state->aircraft_id,
            'wing_id'          => $state->wing_id,
            'daily_flight_hrs' => $state->daily_flight_hrs,
            'total_flight_hrs' => $state->total_flight_hrs,
            'daily_landings'   => $state->daily_landings,
            'total_landings'   => $state->total_landings,
            'state'            => $state->state,
            'notes'            => $state->notes ?? '',
        ];

        $this->defects = $state->defects->map(fn($d) => [
            'defect_number' => $d->defect_number,
            'description'   => $d->description,
            'is_critical'   => $d->is_critical,
        ])->toArray();

        $this->remarks = $state->serviceRemarks->map(fn($r) => [
            'remark_number'    => $r->remark_number,
            'description'      => $r->description,
            'due_hours'        => $r->due_hours,
            'service_location' => $r->service_location,
        ])->toArray();

        if (empty($this->defects)) {
            $this->defects = [['defect_number' => 1, 'description' => '', 'is_critical' => false]];
        }
        if (empty($this->remarks)) {
            $this->remarks = [['remark_number' => 1, 'description' => '', 'due_hours' => '', 'service_location' => '']];
        }

        $this->showModal = true;
    }

    // ── Dynamic defect / remark rows ─────────────────────
    public function addDefect(): void
    {
        $this->defects[] = [
            'defect_number' => count($this->defects) + 1,
            'description'   => '',
            'is_critical'   => false,
        ];
    }

    public function removeDefect(int $index): void
    {
        unset($this->defects[$index]);
        $this->defects = array_values($this->defects);
        // Re-number
        foreach ($this->defects as $i => &$d) {
            $d['defect_number'] = $i + 1;
        }
    }

    public function addRemark(): void
    {
        $this->remarks[] = [
            'remark_number'    => count($this->remarks) + 1,
            'description'      => '',
            'due_hours'        => '',
            'service_location' => '',
        ];
    }

    public function removeRemark(int $index): void
    {
        unset($this->remarks[$index]);
        $this->remarks = array_values($this->remarks);
        foreach ($this->remarks as $i => &$r) {
            $r['remark_number'] = $i + 1;
        }
    }

    // ── Auto-populate from yesterday ─────────────────────
    public function autoPopulate(): void
    {
        $yesterday = now()->subDay()->toDateString();
        $today     = $this->reportDate;

        $yesterday_us = DailyAircraftState::with(['defects', 'serviceRemarks'])
            ->forDate($yesterday)
            ->where('state', 'U/S')
            ->get();

        $created = 0;
        foreach ($yesterday_us as $prev) {
            // Skip if today's entry already exists for this aircraft
            $exists = DailyAircraftState::where('report_date', $today)
                ->where('aircraft_id', $prev->aircraft_id)
                ->exists();
            if ($exists) continue;

            DB::transaction(function () use ($prev, $today, &$created) {
                // Smart Calculation: Get sum of today's flight logs
                $todayMins = \App\Models\FlightLog::where('aircraft_id', $prev->aircraft_id)
                    ->whereDate('departure_time', $today)
                    ->sum('flight_duration_minutes');
                $dailyHrs = $todayMins > 0 ? round($todayMins / 60, 2) : 0;

                $newState = DailyAircraftState::create([
                    'report_date'      => $today,
                    'aircraft_id'      => $prev->aircraft_id,
                    'wing_id'          => $prev->wing_id,
                    'daily_flight_hrs' => $dailyHrs,
                    'total_flight_hrs' => $prev->aircraft?->total_flight_hours ?? $prev->total_flight_hrs,
                    'daily_landings'   => 0,
                    'total_landings'   => $prev->total_landings,
                    'state'            => 'U/S',
                    'notes'            => 'Carried forward from ' . $prev->report_date->format('d M Y'),
                    'created_by'       => auth()->id(),
                ]);

                foreach ($prev->defects as $defect) {
                    DailyDefect::create([
                        'daily_aircraft_state_id' => $newState->id,
                        'defect_number'           => $defect->defect_number,
                        'description'             => $defect->description,
                        'is_critical'             => $defect->is_critical,
                    ]);
                }

                foreach ($prev->serviceRemarks as $remark) {
                    DailyServiceRemark::create([
                        'daily_aircraft_state_id' => $newState->id,
                        'remark_number'           => $remark->remark_number,
                        'description'             => $remark->description,
                        'due_hours'               => $remark->due_hours,
                        'service_location'        => $remark->service_location,
                    ]);
                }

                $created++;
            });
        }

        $this->dispatch('notify', type: 'success', message: "Auto-populated {$created} U/S aircraft from yesterday.");
    }

    // ── Save ─────────────────────────────────────────────
    public function save(): void
    {
        $this->validate([
            'form.aircraft_id'      => 'required|exists:aircraft,id',
            'form.state'            => 'required|in:S,U/S,grounded',
            'form.daily_flight_hrs' => 'nullable|numeric|min:0|max:99',
            'form.total_flight_hrs' => 'nullable|numeric|min:0',
            'form.daily_landings'   => 'nullable|integer|min:0',
            'form.total_landings'   => 'nullable|integer|min:0',
            'defects.*.description' => 'nullable|string|max:1000',
            'remarks.*.description' => 'nullable|string|max:500',
            'remarks.*.due_hours'   => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () {
            $data = array_merge($this->form, [
                'report_date' => $this->reportDate,
                'created_by'  => auth()->id(),
            ]);

            if ($this->editingStateId) {
                $state = DailyAircraftState::findOrFail($this->editingStateId);
                $state->update($data);
                $action = 'updated';
            } else {
                $state  = DailyAircraftState::create($data);
                $action = 'created';
            }

            // Sync defects
            $state->defects()->delete();
            foreach ($this->defects as $d) {
                if (!empty($d['description'])) {
                    $state->defects()->create([
                        'defect_number' => $d['defect_number'],
                        'description'   => $d['description'],
                        'is_critical'   => (bool) ($d['is_critical'] ?? false),
                    ]);
                }
            }

            // Sync remarks
            $state->serviceRemarks()->delete();
            foreach ($this->remarks as $r) {
                if (!empty($r['description'])) {
                    $state->serviceRemarks()->create([
                        'remark_number'    => $r['remark_number'],
                        'description'      => $r['description'],
                        'due_hours'        => $r['due_hours'] ?: null,
                        'service_location' => $r['service_location'] ?: null,
                    ]);
                }
            }

            // Audit log
            AuditLog::create([
                'user_id'        => auth()->id(),
                'event'          => $action,
                'auditable_type' => DailyAircraftState::class,
                'auditable_id'   => $state->id,
                'ip_address'     => request()->ip(),
                'user_agent'     => request()->userAgent(),
            ]);
        });

        $this->showModal = false;
        $this->dispatch('notify', type: 'success', message: 'Daily state entry saved successfully.');
    }

    // ── Delete ───────────────────────────────────────────
    public function confirmDelete(int $id): void
    {
        $this->deletingId       = $id;
        $this->showDeleteConfirm = true;
    }

    public function deleteState(): void
    {
        DailyAircraftState::findOrFail($this->deletingId)->delete();
        $this->showDeleteConfirm = false;
        $this->dispatch('notify', type: 'success', message: 'Entry deleted.');
    }

    // ── Auto-fill wing and calculate today's hours when aircraft selected ─────────────
    public function updatedFormAircraftId($value): void
    {
        if ($value) {
            $ac = Aircraft::find($value);
            if ($ac) {
                $this->form['wing_id']          = $ac->wing_id;
                $this->form['total_flight_hrs'] = $ac->total_flight_hours;
                
                // Smart Calculation: Get sum of today's flight logs
                $todayMins = \App\Models\FlightLog::where('aircraft_id', $ac->id)
                    ->whereDate('departure_time', $this->reportDate)
                    ->sum('flight_duration_minutes');
                    
                $this->form['daily_flight_hrs'] = $todayMins > 0 ? round($todayMins / 60, 2) : 0;
            }
        }
    }
}
