<?php

namespace Database\Factories;

use App\Models\Sorta;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProizvodFactory extends Factory
{
    public function definition(): array
    {
        return [
            'naziv' => fake()->unique()->words(3, true),
            'opis' => fake()->sentence(),
            'sorta_id' => Sorta::factory(),
            'neto_kolicina_g' => fake()->randomElement([125, 250, 500, 1000]),
            'cena' => fake()->randomFloat(2, 100, 5000),
            'aktivan' => true,
        ];
    }
}
