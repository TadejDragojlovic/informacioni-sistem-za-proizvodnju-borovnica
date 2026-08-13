<?php

namespace Database\Factories;

use App\Enums\LotRaspodelaStatus;
use App\Models\Lot;
use App\Models\NarudzbinaStavka;
use Illuminate\Database\Eloquent\Factories\Factory;

class LotRaspodelaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'lot_id' => Lot::factory(),
            'narudzbina_stavka_id' => NarudzbinaStavka::factory(),
            'broj_pakovanja' => fake()->numberBetween(1, 20),
            'status' => LotRaspodelaStatus::REZERVISANO,
        ];
    }
}
