<?php

namespace App\Livewire;

use App\Models\Aircraft;
use App\Models\Incident;
use App\Models\User;
use App\Livewire\Forms\IncidentForm;
use Illuminate\Auth\Access\AuthorizationException;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class IncidentManager extends Component
{
    use WithPagination;

    public IncidentForm $form;

    #[Url(as: 'q', except: '')]
    public string $search = '';
    #[Url(as: 'severity', except: '')]
    public string $severityFilter = '';
    #[Url(as: 'status', except: '')]
    public string $statusFilter = '';

    public bool $showModal = false;
    public bool $showDeleteConfirm = false;
    public ?int $editingId = null;
    public ?int $deletingId = null;

    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedSeverityFilter(): void { $this->resetPage(); }
    public function updatedStatusFilter(): void { $this->resetPage(); }

    public function exportCsv()
    {
        $this->authorize('viewAny', Incident::class);

        $incidents = Incident::with(['aircraft', 'reporter'])
            ->when($this->search, fn($q) => $q->where('title', 'like', "%{$this->search}%"))
            ->when($this->severityFilter, fn($q) => $q->where('severity', $this->severityFilter))
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->get();

        $csvData = "ID,Title,Aircraft,Severity,Status,Date\n";
        foreach ($incidents as $inc) {
            $csvData .= "{$inc->id},\"" . str_replace('"', '""', $inc->title) . "\",{$inc->aircraft?->tail_number},{$inc->severity},{$inc->status},{$inc->incident_date?->format('Y-m-d')}\n";
        }

        return response()->streamDownload(function () use ($csvData) {
            echo $csvData;
        }, 'incidents_export_' . date('Y-m-d') . '.csv');
    }

    public function openCreate(): void
    {
        try {
            $this->authorize('create', Incident::class);
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
            $incident = Incident::findOrFail($id);
            $this->authorize('update', $incident);
            $this->form->setIncident($incident);
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
            $this->dispatch('notify', type: 'success', message: 'Incident updated.');
        } else {
            $this->form->store();
            $this->dispatch('notify', type: 'success', message: 'Incident created.');
        }
        $this->showModal = false;
    }

    public function confirmDelete(int $id): void
    {
        try {
            $this->authorize('delete', Incident::findOrFail($id));
            $this->deletingId = $id;
            $this->showDeleteConfirm = true;
        } catch (AuthorizationException) {
            $this->dispatch('notify', type: 'error', message: 'Access denied.');
        }
    }

    public function deleteIncident(): void
    {
        Incident::findOrFail($this->deletingId)->delete();
        $this->dispatch('notify', type: 'success', message: 'Incident deleted.');
        $this->showDeleteConfirm = false;
        $this->deletingId = null;
    }

    public function render()
    {
        $incidents = Incident::with(['aircraft', 'reporter', 'investigator'])
            ->when($this->search, fn($q) => $q->where('title', 'like', "%{$this->search}%")
                                              ->orWhere('description', 'like', "%{$this->search}%"))
            ->when($this->severityFilter, fn($q) => $q->where('severity', $this->severityFilter))
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->latest('incident_date')
            ->paginate(20);

        $aircraft = Aircraft::orderBy('tail_number')->get();
        $investigators = User::whereIn('role', ['supervisor', 'commander', 'admin'])->orderBy('name')->get(); 

        return view('livewire.incident-manager', compact('incidents', 'aircraft', 'investigators'))
            ->layout('layouts.app', ['heading' => 'Incident Management']);
    }
}
