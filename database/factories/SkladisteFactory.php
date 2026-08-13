<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SkladisteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'naziv' => fake()->unique()->company().' hladnjača',
            'lokacija' => fake()->city(),
            'mesecni_trosak' => fake()->randomFloat(2, 10000, 150000),
            'aktivan' => true,
        ];
    }
}
