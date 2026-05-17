<?php

namespace App\Livewire\Forms;

use App\Models\Aircraft;
use Livewire\Attributes\Rule;
use Livewire\Form;

class AircraftForm extends Form
{
    public ?Aircraft $aircraft = null;

    public string $tail_number = '';

    public function rules(): array
    {
        return [
            'tail_number' => 'required|string|max:20|unique:aircraft,tail_number,' . ($this->aircraft?->id ?? 'NULL'),
        ];
    }

    #[Rule('required|string|max:100')]
    public string $model = '';

    #[Rule('required|string|max:100')]
    public string $manufacturer = '';

    #[Rule('nullable|integer|min:1900|max:2030')]
    public ?int $year_manufactured = null;

    #[Rule('nullable|exists:wings,id')]
    public ?int $wing_id = null;

    #[Rule('required|in:active,maintenance,grounded,retired')]
    public string $status = 'active';

    #[Rule('nullable|numeric|min:0')]
    public ?float $total_flight_hours = 0.0;

    #[Rule('nullable|date')]
    public string $last_maintenance_date = '';

    #[Rule('nullable|string')]
    public string $notes = '';

    public function setAircraft(Aircraft $aircraft): void
    {
        $this->aircraft              = $aircraft;
        $this->tail_number           = $aircraft->tail_number;
        $this->model                 = $aircraft->model;
        $this->manufacturer          = $aircraft->manufacturer;
        $this->year_manufactured     = $aircraft->year_manufactured;
        $this->wing_id               = $aircraft->wing_id;
        $this->status                = $aircraft->status;
        $this->total_flight_hours    = $aircraft->total_flight_hours;
        $this->last_maintenance_date = $aircraft->last_maintenance_date?->format('Y-m-d') ?? '';
        $this->notes                 = $aircraft->notes ?? '';
    }

    public function resetForm(): void
    {
        $this->aircraft = null;
        $this->tail_number = $this->model = $this->manufacturer = $this->last_maintenance_date = $this->notes = '';
        $this->year_manufactured = $this->wing_id = null;
        $this->total_flight_hours = 0.0;
        $this->status = 'active';
        $this->resetErrorBag();
    }
}
