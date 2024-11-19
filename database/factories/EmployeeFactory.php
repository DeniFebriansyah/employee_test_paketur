<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employee>
 */
class EmployeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [

            'name' => fake()->name(),
            'phone_number' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'role' => fake()->randomElement(['manager', 'employee']),
            'company_id' => \App\Models\Company::factory(),
            'deleted_at' => null,
            'created_at' => now(),
            'updated_at' => now(),

        ];
    }
}