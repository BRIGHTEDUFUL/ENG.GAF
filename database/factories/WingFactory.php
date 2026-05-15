<?php

namespace Database\Factories;

use App\Models\Wing;
use Illuminate\Database\Eloquent\Factories\Factory;

class WingFactory extends Factory
{
    protected $model = Wing::class;

    public function definition(): array
    {
        return [
            'name'             => fake()->unique()->words(3, true) . ' Wing',
            'code'             => strtoupper(fake()->unique()->lexify('??-###')),
            'base_location'    => fake()->city() . ' Air Base',
            'commander_id'     => null,
            'status'           => fake()->randomElement(['active', 'active', 'active', 'inactive']),
            'established_date' => fake()->dateTimeBetween('-30 years', '-1 year')->format('Y-m-d'),
            'description'      => fake()->optional(0.6)->sentence(),
        ];
    }

    public function active(): static
    {
        return $this->state(['status' => 'active']);
    }
}
