<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SkladisteFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'lokacija' => fake()->word(),
            'kapacitet' => fake()->numberBetween(-10000, 10000),
            'temperatura' => fake()->randomFloat(2, 0, 999.99),
            'trosak' => fake()->randomFloat(2, 0, 9999999999.99),
        ];
    }
}
