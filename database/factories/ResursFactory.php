<?php

namespace Database\Factories;

use App\Models\Proizvod;
use Illuminate\Database\Eloquent\Factories\Factory;

class ResursFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'naziv' => fake()->word(),
            'kolicina' => fake()->randomFloat(2, 0, 99),
            'trosak' => fake()->randomFloat(2, 0, 19999.99),
            'proizvod_id' => Proizvod::factory(),
        ];
    }
}
