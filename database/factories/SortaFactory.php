<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SortaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'naziv' => fake()->unique()->word(),
            'opis' => fake()->sentence(),
        ];
    }
}
