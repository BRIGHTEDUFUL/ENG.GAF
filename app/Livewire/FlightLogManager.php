<?php

namespace App\Livewire;

use App\Models\Aircraft;
use App\Models\FlightLog;
use App\Models\User;
use App\Livewire\Forms\FlightLogForm;
use Illuminate\Auth\Access\AuthorizationException;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class FlightLogManager extends Component
{
    use WithPagination;

    public FlightLogForm $form;

    #[Url(as: 'q', except: '')]
    public string $search = '';
    #[Url(as: 'mission', except: '')]
    public string $missionFilter = '';

    public bool $showModal = false;
    public bool $showDeleteConfirm = false;
    public ?int $editingId = null;
    public ?int $deletingId = null;

    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedMissionFilter(): void { $this->resetPage(); }

    public function openCreate(): void
    {
        try {
            $this->authorize('create', FlightLog::class);
            $this->form->reset();
            $this->editingId = null;
            $this->showModal = true;
        } catch (AuthorizationException) {
            $this->dispatch('notify', type: 'error', message: 'Access denied.');
        }
    }

    public function openEdit(int $id): void
    {
        try {
            $log = FlightLog::findOrFail($id);
            $this->authorize('update', $log);
            $this->form->setLog($log);
            $this->editingId = $id;
            $this->showModal = true;
        } catch (AuthorizationException) {
            $this->dispatch('notify', type: 'error', message: 'Access denied.');
        }
    }

    public function save(): void
    {
        if ($this->editingId) {
            $this->form->update();
            $this->dispatch('notify', type: 'success', message: 'Flight log updated.');
        } else {
            $this->form->store();
            $this->dispatch('notify', type: 'success', message: 'Flight log created.');
        }
        $this->showModal = false;
    }

    public function confirmDelete(int $id): void
    {
        try {
            $this->authorize('delete', FlightLog::findOrFail($id));
            $this->deletingId = $id;
            $this->showDeleteConfirm = true;
        } catch (AuthorizationException) {
            $this->dispatch('notify', type: 'error', message: 'Access denied.');
        }
    }

    public function deleteLog(): void
    {
        FlightLog::findOrFail($this->deletingId)->delete();
        $this->dispatch('notify', type: 'success', message: 'Flight log deleted.');
        $this->showDeleteConfirm = false;
        $this->deletingId = null;
    }

    public function render()
    {
        $logs = FlightLog::with(['aircraft', 'pilot', 'coPilot'])
            ->when($this->search, fn($q) => $q->where('departure_location', 'like', "%{$this->search}%")
                                              ->orWhere('arrival_location', 'like', "%{$this->search}%")
                                              ->orWhereHas('aircraft', fn($q) => $q->where('tail_number', 'like', "%{$this->search}%")))
            ->when($this->missionFilter, fn($q) => $q->where('mission_type', $this->missionFilter))
            ->latest('departure_time')
            ->paginate(20);

        $aircraft = Aircraft::orderBy('tail_number')->get();
        $pilots = User::orderBy('name')->get(); 

        return view('livewire.flight-log-manager', compact('logs', 'aircraft', 'pilots'))
            ->layout('layouts.app', ['heading' => 'Flight Logs']);
    }
}
