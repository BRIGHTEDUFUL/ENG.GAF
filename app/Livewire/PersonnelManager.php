<?php

namespace App\Livewire;

use App\Livewire\Forms\PersonnelForm;
use App\Models\User;
use App\Models\Wing;
use App\Services\PersonnelService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class PersonnelManager extends Component
{
    use WithPagination;

    #[Url(as: 'q', except: '')]
    public string $search = '';

    #[Url(as: 'role', except: '')]
    public string $roleFilter = '';

    public bool $showModal = false;
    public bool $showDeleteConfirm = false;
    public ?int $editingId = null;
    public ?int $deletingId = null;

    public PersonnelForm $form;
    protected PersonnelService $personnelService;

    public function boot(PersonnelService $personnelService): void
    {
        $this->personnelService = $personnelService;
    }

    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedRoleFilter(): void { $this->resetPage(); }

    public function exportCsv()
    {
        $this->authorizeAccess();

        $personnel = User::with('wing')
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%")->orWhere('email', 'like', "%{$this->search}%"))
            ->when($this->roleFilter, fn($q) => $q->where('role', $this->roleFilter))
            ->get();

        $csvData = "ID,Name,Email,Role,Rank,Wing\n";
        foreach ($personnel as $p) {
            $csvData .= "{$p->id},\"" . str_replace('"', '""', $p->name) . "\",{$p->email},{$p->role},{$p->rank},{$p->wing?->name}\n";
        }

        return response()->streamDownload(function () use ($csvData) {
            echo $csvData;
        }, 'personnel_export_' . date('Y-m-d') . '.csv');
    }

    public function openCreate(): void
    {
        $this->authorizeAccess();
        $this->form->resetForm();
        $this->editingId = null;
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $this->authorizeAccess();
        $personnel = $this->personnelService->find($id);
        $this->form->setPersonnel($personnel);
        $this->editingId = $id;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->authorizeAccess();
        $this->form->validate();

        $uniqueRule = $this->editingId === null ? 'unique:users,email' : 'unique:users,email,' . $this->editingId;
        $emailValidator = Validator::make(['email' => $this->form->email], ['email' => $uniqueRule]);

        if ($emailValidator->fails()) {
            $this->addError('form.email', $emailValidator->errors()->first('email'));
            return;
        }

        $data = [
            'name' => $this->form->name,
            'email' => $this->form->email,
            'role' => $this->form->role,
            'rank' => $this->form->rank,
            'wing_id' => $this->form->wing_id ?: null,
        ];
        
        if ($this->form->password) {
            $data['password'] = bcrypt($this->form->password);
        } elseif ($this->editingId === null) {
            $data['password'] = bcrypt('password'); // Default password
        }

        if ($this->editingId === null) {
            $this->personnelService->create($data);
        } else {
            $personnel = $this->personnelService->find($this->editingId);
            $this->personnelService->update($personnel, $data);
        }

        $this->dispatch('notify', type: 'success', message: 'Personnel saved.');
        $this->showModal = false;
        $this->form->resetForm();
    }

    public function confirmDelete(int $id): void
    {
        $this->authorizeAccess();
        $this->deletingId = $id;
        $this->showDeleteConfirm = true;
    }

    public function deletePersonnel(): void
    {
        $this->authorizeAccess();
        $personnel = $this->personnelService->find($this->deletingId);
        $this->personnelService->delete($personnel);
        $this->dispatch('notify', type: 'success', message: 'Personnel deleted.');
        $this->showDeleteConfirm = false;
        $this->deletingId = null;
    }

    protected function authorizeAccess()
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Access denied.');
        }
    }

    public function render()
    {
        $personnel = $this->personnelService->list([
            'search' => $this->search,
            'role'   => $this->roleFilter,
        ]);

        $wings = Wing::orderBy('name')->get();

        return view('livewire.personnel-manager', [
            'personnel' => $personnel,
            'wings'     => $wings,
        ])->layout('layouts.app', ['heading' => 'Personnel Directory']);
    }
}
