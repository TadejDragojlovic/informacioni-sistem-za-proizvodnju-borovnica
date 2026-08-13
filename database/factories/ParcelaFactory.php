<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ParcelaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'oznaka' => 'P-'.fake()->unique()->numerify('##'),
            'povrsina_m2' => fake()->numberBetween(1000, 50000),
            'zemlja_porekla' => 'Srbija',
        ];
    }
}
