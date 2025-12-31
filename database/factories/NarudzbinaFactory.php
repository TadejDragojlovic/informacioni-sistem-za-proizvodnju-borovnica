<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class NarudzbinaFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'datum_narudzbine' => fake()->date(),
            'ukupna_cena' => fake()->randomFloat(2, 0, 9999999999.99),
            'status' => fake()->randomElement(["kreirana","potvrdjena","u_obradi","otpremljena","isporucena","otkazana","vracena"]),
            'user_id' => User::factory(),
        ];
    }
}
