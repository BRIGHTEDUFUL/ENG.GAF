<?php

namespace App\Livewire;

use App\Livewire\Forms\WingForm;
use App\Models\User;
use App\Models\Wing;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class WingManager extends Component
{
    use WithPagination;

    #[Url(as: 'q', except: '')]
    public string $search = '';

    public bool $showModal = false;
    public bool $showDeleteConfirm = false;
    public ?int $editingId = null;
    public ?int $deletingId = null;
    public WingForm $form;

    public function updatedSearch(): void { $this->resetPage(); }

    public function exportCsv()
    {
        $this->authorize('viewAny', Wing::class);

        $wings = Wing::with('commander')
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%")
                ->orWhere('code', 'like', "%{$this->search}%")
                ->orWhere('base_location', 'like', "%{$this->search}%"))
            ->withCount(['aircraft', 'personnel'])
            ->get();

        $csvData = "ID,Name,Code,Base Location,Commander,Aircraft Count,Status\n";
        foreach ($wings as $wing) {
            $csvData .= "{$wing->id},\"" . str_replace('"', '""', $wing->name) . "\",{$wing->code},\"" . str_replace('"', '""', $wing->base_location) . "\",{$wing->commander?->name},{$wing->aircraft_count},{$wing->status}\n";
        }

        return response()->streamDownload(function () use ($csvData) {
            echo $csvData;
        }, 'wings_export_' . date('Y-m-d') . '.csv');
    }

    public function openCreate(): void
    {
        try {
            $this->authorize('create', Wing::class);
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
            $wing = Wing::findOrFail($id);
            $this->authorize('update', $wing);
            $this->form->setWing($wing);
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
            'name'             => $this->form->name,
            'code'             => strtoupper($this->form->code),
            'base_location'    => $this->form->base_location,
            'commander_id'     => $this->form->commander_id ?: null,
            'status'           => $this->form->status,
            'established_date' => $this->form->established_date ?: null,
            'description'      => $this->form->description ?: null,
        ];

        if ($this->editingId) {
            Wing::findOrFail($this->editingId)->update($data);
            $this->dispatch('notify', type: 'success', message: 'Wing updated.');
        } else {
            Wing::create($data);
            $this->dispatch('notify', type: 'success', message: 'Wing created.');
        }
        $this->showModal = false;
        $this->form->resetForm();
    }

    public function confirmDelete(int $id): void
    {
        try {
            $this->authorize('delete', Wing::findOrFail($id));
            $this->deletingId = $id;
            $this->showDeleteConfirm = true;
        } catch (AuthorizationException) {
            $this->dispatch('notify', type: 'error', message: 'Access denied.');
        }
    }

    public function deleteWing(): void
    {
        try {
            Wing::findOrFail($this->deletingId)->delete();
            $this->dispatch('notify', type: 'success', message: 'Wing deleted.');
        } catch (ModelNotFoundException) {
            $this->dispatch('notify', type: 'error', message: 'Wing not found.');
        }
        $this->showDeleteConfirm = false;
        $this->deletingId = null;
    }

    public function render()
    {
        $wings = Wing::with('commander')
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%")
                ->orWhere('code', 'like', "%{$this->search}%")
                ->orWhere('base_location', 'like', "%{$this->search}%"))
            ->withCount(['aircraft', 'personnel'])
            ->latest()
            ->paginate(20);

        $commanders = User::where('role', 'commander')->orderBy('name')->get();

        return view('livewire.wing-manager', compact('wings', 'commanders'))
            ->layout('layouts.app', ['heading' => 'Wings Management']);
    }
}
