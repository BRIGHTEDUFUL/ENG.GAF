<?php

namespace App\Livewire;

use App\Models\AuditLog;
use Illuminate\Auth\Access\AuthorizationException;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class AuditLogViewer extends Component
{
    use WithPagination;

    #[Url(as: 'event', except: '')]
    public string $eventFilter = '';
    #[Url(as: 'date', except: '')]
    public string $dateFilter = '';
    #[Url(as: 'user', except: '')]
    public string $userFilter = '';

    public function mount()
    {
        try {
            $this->authorize('viewAny', AuditLog::class);
        } catch (AuthorizationException) {
            abort(403, 'Access denied.');
        }
    }

    public function updatedEventFilter(): void { $this->resetPage(); }
    public function updatedDateFilter(): void { $this->resetPage(); }
    public function updatedUserFilter(): void { $this->resetPage(); }

    public function render()
    {
        $logs = AuditLog::with('user')
            ->when($this->eventFilter, fn($q) => $q->where('event', $this->eventFilter))
            ->when($this->dateFilter, fn($q) => $q->whereDate('created_at', $this->dateFilter))
            ->when($this->userFilter, fn($q) => $q->whereHas('user', fn($u) => $u->where('name', 'like', "%{$this->userFilter}%")))
            ->latest('created_at')
            ->paginate(50);

        return view('livewire.audit-log-viewer', compact('logs'))
            ->layout('layouts.app', ['heading' => 'Audit Logs']);
    }
}
