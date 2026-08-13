<?php

namespace Database\Factories;

use App\Enums\NarudzbinaStatus;
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
            'user_id' => User::factory(),
            'status' => NarudzbinaStatus::POTVRDJENA,
            'adresa_isporuke' => fake()->address(),
        ];
    }
}
