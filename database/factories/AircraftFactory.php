<?php

namespace Database\Factories;

use App\Models\Aircraft;
use Illuminate\Database\Eloquent\Factories\Factory;

class AircraftFactory extends Factory
{
    protected $model = Aircraft::class;

    private static array $models = [
        'F-16 Fighting Falcon', 'F-22 Raptor', 'F-35 Lightning II',
        'C-130 Hercules', 'B-52 Stratofortress', 'A-10 Thunderbolt II',
        'KC-135 Stratotanker', 'E-3 Sentry', 'MQ-9 Reaper', 'UH-60 Black Hawk',
    ];

    private static array $manufacturers = [
        'Lockheed Martin', 'Boeing', 'Northrop Grumman',
        'General Dynamics', 'Raytheon', 'McDonnell Douglas',
    ];

    public function definition(): array
    {
        return [
            'tail_number'          => strtoupper(fake()->unique()->bothify('AF-####-??')),
            'model'                => fake()->randomElement(self::$models),
            'manufacturer'         => fake()->randomElement(self::$manufacturers),
            'year_manufactured'    => fake()->numberBetween(1980, 2023),
            'wing_id'              => null,
            'status'               => fake()->randomElement(['active', 'active', 'active', 'maintenance', 'grounded', 'retired']),
            'last_maintenance_date'=> fake()->optional(0.8)->dateTimeBetween('-2 years', 'now')?->format('Y-m-d'),
            'total_flight_hours'   => fake()->randomFloat(2, 0, 15000),
            'notes'                => fake()->optional(0.3)->sentence(),
        ];
    }

    public function active(): static
    {
        return $this->state(['status' => 'active']);
    }

    public function maintenance(): static
    {
        return $this->state(['status' => 'maintenance']);
    }
}
