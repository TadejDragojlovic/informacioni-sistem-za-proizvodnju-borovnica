<?php

namespace Database\Factories;

use App\Models\Lot;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ResursFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'lot_id' => Lot::factory(),
            'naziv' => fake()->word(),
            'kolicina' => fake()->randomFloat(2, 0.01, 99),
            'jedinica_mere' => fake()->randomElement(['kg', 'l', 'kom']),
            'cena_po_jedinici' => fake()->randomFloat(2, 0.01, 19999.99),
            'datum_upotrebe' => fake()->date(),
            'evidentirao_user_id' => User::factory(),
        ];
    }
}
