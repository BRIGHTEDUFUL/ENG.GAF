<?php

namespace App\Livewire;

use App\Models\Aircraft;
use App\Models\MaintenanceTask;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class MaintenanceTaskManager extends Component
{
    use WithPagination;

    #[Url(as: 'q', except: '')]
    public string $search = '';
    #[Url(as: 'priority', except: '')]
    public string $priorityFilter = '';
    #[Url(as: 'status', except: '')]
    public string $statusFilter = '';

    public bool $showModal = false;
    public bool $showDeleteConfirm = false;
    public ?int $editingId = null;
    public ?int $deletingId = null;

    // Form fields
    public string $title = '';
    public string $description = '';
    public ?int $aircraft_id = null;
    public ?int $assigned_to = null;
    public string $priority = 'medium';
    public string $status = 'pending';
    public string $due_date = '';

    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedPriorityFilter(): void { $this->resetPage(); }
    public function updatedStatusFilter(): void { $this->resetPage(); }

    public function openCreate(): void
    {
        try {
            $this->authorize('create', MaintenanceTask::class);
            $this->resetForm();
            $this->editingId = null;
            $this->showModal = true;
        } catch (AuthorizationException) {
            $this->dispatch('notify', type: 'error', message: 'Access denied.');
        }
    }

    public function openEdit(int $id): void
    {
        try {
            $task = MaintenanceTask::findOrFail($id);
            $this->authorize('update', $task);
            $this->title       = $task->title;
            $this->description = $task->description ?? '';
            $this->aircraft_id = $task->aircraft_id;
            $this->assigned_to = $task->assigned_to;
            $this->priority    = $task->priority;
            $this->status      = $task->status;
            $this->due_date    = $task->due_date?->format('Y-m-d') ?? '';
            $this->editingId   = $id;
            $this->showModal   = true;
        } catch (AuthorizationException) {
            $this->dispatch('notify', type: 'error', message: 'Access denied.');
        }
    }

    public function save(): void
    {
        $this->validate([
            'title'       => 'required|string|max:200',
            'description' => 'nullable|string',
            'aircraft_id' => 'required|exists:aircraft,id',
            'assigned_to' => 'nullable|exists:users,id',
            'priority'    => 'required|in:low,medium,high,critical',
            'status'      => 'required|in:pending,in-progress,completed',
            'due_date'    => 'nullable|date',
        ]);

        $data = [
            'title'       => $this->title,
            'description' => $this->description ?: null,
            'aircraft_id' => $this->aircraft_id,
            'assigned_to' => $this->assigned_to ?: null,
            'priority'    => $this->priority,
            'status'      => $this->status,
            'due_date'    => $this->due_date ?: null,
            'completed_at'=> $this->status === 'completed' ? now() : null,
        ];

        if ($this->editingId) {
            MaintenanceTask::findOrFail($this->editingId)->update($data);
            $this->dispatch('notify', type: 'success', message: 'Task updated.');
        } else {
            $data['created_by'] = auth()->id();
            MaintenanceTask::create($data);
            $this->dispatch('notify', type: 'success', message: 'Task created.');
        }
        $this->showModal = false;
        $this->resetForm();
    }

    public function logAndClose(int $id): void
    {
        try {
            $task = MaintenanceTask::findOrFail($id);
            $this->authorize('update', $task);
            
            // Mark task as completed
            $task->update([
                'status' => 'completed',
                'completed_at' => now()
            ]);

            // Auto-generate a Maintenance Log draft
            \App\Models\MaintenanceLog::create([
                'aircraft_id' => $task->aircraft_id,
                'task_id' => $task->id,
                'technician_id' => auth()->id(),
                'log_date' => now(),
                'maintenance_type' => 'scheduled', // default
                'description' => "Completed task: {$task->title}\n\nNotes:\n",
                'status' => 'draft',
            ]);

            $this->dispatch('notify', type: 'success', message: 'Task closed and Maintenance Log draft created!');
        } catch (AuthorizationException) {
            $this->dispatch('notify', type: 'error', message: 'Access denied.');
        }
    }

    public function confirmDelete(int $id): void
    {
        try {
            $this->authorize('delete', MaintenanceTask::findOrFail($id));
            $this->deletingId = $id;
            $this->showDeleteConfirm = true;
        } catch (AuthorizationException) {
            $this->dispatch('notify', type: 'error', message: 'Access denied.');
        }
    }

    public function deleteTask(): void
    {
        MaintenanceTask::findOrFail($this->deletingId)->delete();
        $this->dispatch('notify', type: 'success', message: 'Task deleted.');
        $this->showDeleteConfirm = false;
        $this->deletingId = null;
    }

    private function resetForm(): void
    {
        $this->title = $this->description = $this->due_date = '';
        $this->aircraft_id = $this->assigned_to = null;
        $this->priority = 'medium';
        $this->status = 'pending';
        $this->resetErrorBag();
    }

    public function render()
    {
        $tasks = MaintenanceTask::with(['aircraft', 'assignedEngineer'])
            ->when($this->search, fn($q) => $q->where('title', 'like', "%{$this->search}%"))
            ->when($this->priorityFilter, fn($q) => $q->where('priority', $this->priorityFilter))
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->latest()
            ->paginate(20);

        $aircraft  = Aircraft::orderBy('tail_number')->get();
        $engineers = User::where('role', 'engineer')->orderBy('name')->get();

        return view('livewire.maintenance-task-manager', compact('tasks', 'aircraft', 'engineers'))
            ->layout('layouts.app', ['heading' => 'Maintenance Tasks']);
    }
}
