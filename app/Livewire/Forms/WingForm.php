<?php

namespace App\Livewire\Forms;

use App\Models\Wing;
use Livewire\Attributes\Rule;
use Livewire\Form;

class WingForm extends Form
{
    #[Rule('required|string|max:150')]
    public string $name = '';

    #[Rule('required|string|max:20')]
    public string $code = '';

    #[Rule('required|string|max:255')]
    public string $base_location = '';

    #[Rule('nullable|exists:users,id')]
    public ?int $commander_id = null;

    #[Rule('required|in:active,inactive')]
    public string $status = 'active';

    #[Rule('nullable|date')]
    public string $established_date = '';

    #[Rule('nullable|string')]
    public string $description = '';

    public function setWing(Wing $wing): void
    {
        $this->name             = $wing->name;
        $this->code             = $wing->code;
        $this->base_location    = $wing->base_location;
        $this->commander_id     = $wing->commander_id;
        $this->status           = $wing->status;
        $this->established_date = $wing->established_date?->format('Y-m-d') ?? '';
        $this->description      = $wing->description ?? '';
    }

    public function resetForm(): void
    {
        $this->name = $this->code = $this->base_location = $this->established_date = $this->description = '';
        $this->commander_id = null;
        $this->status = 'active';
        $this->resetErrorBag();
    }
}
