<?php

namespace Database\Factories;

use App\Models\Skladiste;
use Illuminate\Database\Eloquent\Factories\Factory;

class SkladisnaLokacijaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'skladiste_id' => Skladiste::factory(),
            'naziv' => fake()->unique()->words(2, true),
            'opis' => fake()->sentence(),
            'aktivna' => true,
        ];
    }
}
