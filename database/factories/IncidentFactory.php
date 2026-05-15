<?php

namespace Database\Factories;

use App\Models\Incident;
use Illuminate\Database\Eloquent\Factories\Factory;

class IncidentFactory extends Factory
{
    protected $model = Incident::class;

    public function definition(): array
    {
        return [
            'title'                    => fake()->sentence(6),
            'description'              => fake()->paragraph(),
            'aircraft_id'              => null,
            'reported_by'              => 1,
            'assigned_investigator_id' => null,
            'severity'                 => fake()->randomElement(['low', 'medium', 'high', 'critical']),
            'status'                   => fake()->randomElement(['open', 'open', 'under-investigation', 'resolved']),
            'incident_date'            => fake()->dateTimeBetween('-6 months', 'now'),
            'resolution_notes'         => null,
            'resolved_at'              => null,
        ];
    }

    public function critical(): static
    {
        return $this->state(['severity' => 'critical', 'status' => 'open']);
    }
}
