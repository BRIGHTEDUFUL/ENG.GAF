<?php

namespace App\Livewire\Forms;

use App\Models\User;
use Livewire\Attributes\Rule;
use Livewire\Form;

class PersonnelForm extends Form
{
    #[Rule('required|string|max:100')]
    public string $name = '';

    #[Rule('required|email|max:255')]
    public string $email = '';

    #[Rule('nullable|string|min:8')]
    public ?string $password = null;

    #[Rule('required|string|max:50')]
    public string $role = 'engineer';

    #[Rule('nullable|string|max:50')]
    public ?string $rank = null;

    #[Rule('nullable|exists:wings,id')]
    public ?int $wing_id = null;

    public function setPersonnel(User $personnel): void
    {
        $this->name = $personnel->name;
        $this->email = $personnel->email;
        $this->role = $personnel->role;
        $this->rank = $personnel->rank;
        $this->wing_id = $personnel->wing_id;
        $this->password = null;
    }

    public function resetForm(): void
    {
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->role = 'engineer';
        $this->rank = null;
        $this->wing_id = null;

        $this->resetErrorBag();
    }
}
