<?php

namespace App\Livewire;

use App\Models\Aircraft;
use App\Models\MaintenanceLog;
use App\Models\MaintenanceTask;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class MaintenanceLogManager extends Component
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

    public ?int $maintenance_task_id = null;
    public ?int $aircraft_id = null;
    public ?int $engineer_id = null;
    public string $work_performed = '';
    public string $parts_used = '';
    public string $hours_spent = '';
    public string $log_date = '';
    public string $status = 'draft';

    public function exportCsv()
    {
        $this->authorize('viewAny', MaintenanceLog::class);

        $logs = MaintenanceLog::with(['aircraft', 'engineer', 'maintenanceTask'])
            ->when($this->search, fn($q) => $q->whereHas('aircraft', fn($q2) => $q2->where('tail_number', 'like', "%{$this->search}%")))
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->get();

        $csvData = "ID,Aircraft,Engineer,Date,Hours,Status\n";
        foreach ($logs as $log) {
            $csvData .= "{$log->id},{$log->aircraft?->tail_number},{$log->engineer?->name},{$log->log_date?->format('Y-m-d')},{$log->hours_spent},{$log->status}\n";
        }

        return response()->streamDownload(function () use ($csvData) {
            echo $csvData;
        }, 'maintenance_logs_export_' . date('Y-m-d') . '.csv');
    }

    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedStatusFilter(): void { $this->resetPage(); }

    public function openCreate(): void
    {
        try {
            $this->authorize('create', MaintenanceLog::class);
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
            $log = MaintenanceLog::findOrFail($id);
            $this->authorize('update', $log);
            $this->maintenance_task_id = $log->maintenance_task_id;
            $this->aircraft_id         = $log->aircraft_id;
            $this->engineer_id         = $log->engineer_id;
            $this->work_performed      = $log->work_performed;
            $this->parts_used          = $log->parts_used ?? '';
            $this->hours_spent         = (string) $log->hours_spent;
            $this->log_date            = $log->log_date->format('Y-m-d');
            $this->status              = $log->status;
            $this->editingId           = $id;
            $this->showModal           = true;
        } catch (AuthorizationException) {
            $this->dispatch('notify', type: 'error', message: 'Access denied.');
        }
    }

    public function save(): void
    {
        $this->validate([
            'aircraft_id'         => 'required|exists:aircraft,id',
            'maintenance_task_id' => 'nullable|exists:maintenance_tasks,id',
            'work_performed'      => 'required|string',
            'parts_used'          => 'nullable|string',
            'hours_spent'         => 'required|numeric|min:0.01',
            'log_date'            => 'required|date',
            'status'              => 'required|in:draft,submitted,approved',
            'engineer_id'         => 'nullable|exists:users,id',
        ]);

        $data = [
            'maintenance_task_id' => $this->maintenance_task_id ?: null,
            'aircraft_id'         => $this->aircraft_id,
            'work_performed'      => $this->work_performed,
            'parts_used'          => $this->parts_used ?: null,
            'hours_spent'         => $this->hours_spent,
            'log_date'            => $this->log_date,
            'status'              => $this->status,
            'engineer_id'         => $this->engineer_id ?: auth()->id(),
        ];

        if ($this->editingId) {
            MaintenanceLog::findOrFail($this->editingId)->update($data);
            $this->dispatch('notify', type: 'success', message: 'Log updated.');
        } else {
            MaintenanceLog::create($data);
            $this->dispatch('notify', type: 'success', message: 'Log created.');
        }
        $this->showModal = false;
        $this->resetForm();
    }

    public function approve(int $id): void
    {
        try {
            $log = MaintenanceLog::findOrFail($id);
            $this->authorize('approve', $log);
            $log->update(['status' => 'approved', 'approved_by' => auth()->id(), 'approved_at' => now()]);
            $this->dispatch('notify', type: 'success', message: 'Log approved.');
        } catch (AuthorizationException) {
            $this->dispatch('notify', type: 'error', message: 'Access denied.');
        }
    }

    public function confirmDelete(int $id): void
    {
        try {
            $this->authorize('delete', MaintenanceLog::findOrFail($id));
            $this->deletingId = $id;
            $this->showDeleteConfirm = true;
        } catch (AuthorizationException) {
            $this->dispatch('notify', type: 'error', message: 'Access denied.');
        }
    }

    public function deleteLog(): void
    {
        MaintenanceLog::findOrFail($this->deletingId)->delete();
        $this->dispatch('notify', type: 'success', message: 'Log deleted.');
        $this->showDeleteConfirm = false;
        $this->deletingId = null;
    }

    private function resetForm(): void
    {
        $this->maintenance_task_id = $this->aircraft_id = $this->engineer_id = null;
        $this->work_performed = $this->parts_used = $this->hours_spent = $this->log_date = '';
        $this->status = 'draft';
        $this->resetErrorBag();
    }

    public function render()
    {
        $logs = MaintenanceLog::with(['aircraft', 'engineer', 'maintenanceTask'])
            ->when($this->search, fn($q) => $q->whereHas('aircraft', fn($q2) => $q2->where('tail_number', 'like', "%{$this->search}%")))
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->latest()
            ->paginate(20);

        $aircraft = Aircraft::orderBy('tail_number')->get();
        $tasks    = MaintenanceTask::where('status', '!=', 'completed')->orderBy('title')->get();
        $engineers = User::whereIn('role', ['admin', 'engineer', 'supervisor'])->orderBy('name')->get();

        return view('livewire.maintenance-log-manager', compact('logs', 'aircraft', 'tasks', 'engineers'))
            ->layout('layouts.app', ['heading' => 'Maintenance Logs']);
    }
}
