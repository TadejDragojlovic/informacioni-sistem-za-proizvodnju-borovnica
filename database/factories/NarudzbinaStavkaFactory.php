<?php

namespace Database\Factories;

use App\Models\;
use App\Models\Narudzbina;
use Illuminate\Database\Eloquent\Factories\Factory;

class NarudzbinaStavkaFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'narudzbina_id' => Narudzbina::factory(),
            'proizvod_id' => ::factory(),
            'kolicina' => fake()->numberBetween(-10000, 10000),
        ];
    }
}
