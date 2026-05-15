<?php

namespace Database\Factories;

use App\Models\Personnel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Personnel>
 */
class PersonnelFactory extends Factory
{
    protected $model = Personnel::class;

    private static array $departments = [
        'Engineering',
        'Human Resources',
        'Finance',
        'Marketing',
        'Operations',
        'Legal',
        'Sales',
        'IT Support',
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name'  => fake()->lastName(),
            'email'      => fake()->unique()->safeEmail(),
            'phone'      => fake()->boolean(80)
                ? substr(fake()->phoneNumber(), 0, 20)
                : null,
            'department' => fake()->randomElement(self::$departments),
            'position'   => fake()->jobTitle(),
            'hire_date'  => fake()->dateTimeBetween('-10 years', 'now')->format('Y-m-d'),
            'status'     => fake()->boolean(80) ? 'active' : 'inactive',
            'avatar'     => null,
            'notes'      => fake()->boolean(30) ? fake()->sentence() : null,
        ];
    }

    /**
     * State: active personnel.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    /**
     * State: inactive personnel.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }
}
