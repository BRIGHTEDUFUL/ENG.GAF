<?php

namespace App\Livewire;

use App\Livewire\Forms\AircraftForm;
use App\Models\Aircraft;
use App\Models\Wing;
use Illuminate\Auth\Access\AuthorizationException;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class AircraftManager extends Component
{
    use WithPagination;

    #[Url(as: 'q', except: '')]
    public string $search = '';

    #[Url(as: 'status', except: '')]
    public string $statusFilter = '';

    public bool $showModal = false;
    public bool $showDeleteConfirm = false;
    public ?int $editingId = null;
    public ?int $deletingId = null;
    public AircraftForm $form;

    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedStatusFilter(): void { $this->resetPage(); }

    public function exportCsv()
    {
        $this->authorize('viewAny', Aircraft::class);

        $aircraft = Aircraft::with('wing')
            ->when($this->search, fn($q) => $q->where('tail_number', 'like', "%{$this->search}%")
                ->orWhere('model', 'like', "%{$this->search}%"))
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->get();

        $csvData = "ID,Tail Number,Model,Wing,Total Flight Hrs,Status\n";
        foreach ($aircraft as $ac) {
            $csvData .= "{$ac->id},{$ac->tail_number},{$ac->model},{$ac->wing?->name},{$ac->total_flight_hours},{$ac->status}\n";
        }

        return response()->streamDownload(function () use ($csvData) {
            echo $csvData;
        }, 'aircraft_export_' . date('Y-m-d') . '.csv');
    }

    public function openCreate(): void
    {
        try {
            $this->authorize('create', Aircraft::class);
            $this->form->resetForm();
            $this->editingId = null;
            $this->showModal = true;
        } catch (AuthorizationException) {
            $this->dispatch('notify', type: 'error', message: 'Access denied.');
        }
    }

    public function openEdit(int $id): void
    {
        try {
            $aircraft = Aircraft::findOrFail($id);
            $this->authorize('update', $aircraft);
            $this->form->setAircraft($aircraft);
            $this->editingId = $id;
            $this->showModal = true;
        } catch (AuthorizationException) {
            $this->dispatch('notify', type: 'error', message: 'Access denied.');
        }
    }

    public function save(): void
    {
        $this->form->validate();
        $data = [
            'tail_number'           => strtoupper($this->form->tail_number),
            'model'                 => $this->form->model,
            'manufacturer'          => $this->form->manufacturer,
            'year_manufactured'     => $this->form->year_manufactured,
            'wing_id'               => $this->form->wing_id,
            'status'                => $this->form->status,
            'last_maintenance_date' => $this->form->last_maintenance_date ?: null,
            'notes'                 => $this->form->notes ?: null,
        ];

        if ($this->editingId) {
            Aircraft::findOrFail($this->editingId)->update($data);
            $this->dispatch('notify', type: 'success', message: 'Aircraft updated.');
        } else {
            Aircraft::create($data);
            $this->dispatch('notify', type: 'success', message: 'Aircraft added.');
        }
        $this->showModal = false;
        $this->form->resetForm();
    }

    public function confirmDelete(int $id): void
    {
        try {
            $this->authorize('delete', Aircraft::findOrFail($id));
            $this->deletingId = $id;
            $this->showDeleteConfirm = true;
        } catch (AuthorizationException) {
            $this->dispatch('notify', type: 'error', message: 'Access denied.');
        }
    }

    public function deleteAircraft(): void
    {
        Aircraft::findOrFail($this->deletingId)->delete();
        $this->dispatch('notify', type: 'success', message: 'Aircraft deleted.');
        $this->showDeleteConfirm = false;
        $this->deletingId = null;
    }

    public function render()
    {
        $aircraft = Aircraft::with('wing')
            ->when($this->search, fn($q) => $q->where('tail_number', 'like', "%{$this->search}%")
                ->orWhere('model', 'like', "%{$this->search}%"))
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->latest()
            ->paginate(20);

        $wings = Wing::where('status', 'active')->orderBy('name')->get();

        return view('livewire.aircraft-manager', compact('aircraft', 'wings'))
            ->layout('layouts.app', ['heading' => 'Aircraft Fleet']);
    }
}
