<?php

namespace Database\Factories;

use App\Models\MaintenanceTask;
use Illuminate\Database\Eloquent\Factories\Factory;

class MaintenanceTaskFactory extends Factory
{
    protected $model = MaintenanceTask::class;

    private static array $titles = [
        'Engine inspection and oil change',
        'Landing gear hydraulic check',
        'Avionics system calibration',
        'Fuel system leak inspection',
        'Airframe structural inspection',
        'Navigation system update',
        'Radar system maintenance',
        'Communication system check',
        'Ejection seat inspection',
        'Tire replacement and brake check',
    ];

    public function definition(): array
    {
        return [
            'title'       => fake()->randomElement(self::$titles),
            'description' => fake()->optional(0.7)->paragraph(),
            'aircraft_id' => 1,
            'assigned_to' => null,
            'created_by'  => 1,
            'priority'    => fake()->randomElement(['low', 'medium', 'medium', 'high', 'critical']),
            'status'      => fake()->randomElement(['pending', 'pending', 'in-progress', 'completed']),
            'due_date'    => fake()->optional(0.7)->dateTimeBetween('now', '+30 days')?->format('Y-m-d'),
            'completed_at'=> null,
        ];
    }

    public function critical(): static
    {
        return $this->state(['priority' => 'critical', 'status' => 'pending']);
    }

    public function completed(): static
    {
        return $this->state(['status' => 'completed', 'completed_at' => now()]);
    }
}
