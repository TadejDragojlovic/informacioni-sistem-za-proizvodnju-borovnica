<?php

namespace Database\Factories;

use App\Models\Skladiste;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProizvodFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'naziv' => fake()->regexify('[A-Za-z0-9]{50}'),
            'opis' => fake()->text(),
            'kolicina' => fake()->numberBetween(0, 10000),
            'cena' => fake()->randomFloat(2, 0, 99999999.99),
            'skladiste_id' => Skladiste::factory(),
        ];
    }
}
