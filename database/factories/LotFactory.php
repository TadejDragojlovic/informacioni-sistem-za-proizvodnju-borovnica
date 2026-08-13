<?php

namespace Database\Factories;

use App\Enums\LotStatus;
use App\Models\Parcela;
use App\Models\Sorta;
use Illuminate\Database\Eloquent\Factories\Factory;

class LotFactory extends Factory
{
    public function definition(): array
    {
        $kolicina = fake()->numberBetween(5000, 50000);

        return [
            'oznaka' => 'LOT-'.fake()->unique()->numerify('####-###'),
            'sorta_id' => Sorta::factory(),
            'parcela_id' => Parcela::factory(),
            'trenutna_skladisna_lokacija_id' => null,
            'datum_berbe' => fake()->date(),
            'pocetna_kolicina_g' => $kolicina,
            'raspoloziva_kolicina_g' => $kolicina,
            'status' => LotStatus::KREIRAN,
            'klasa_kvaliteta' => null,
            'broj_dokumenta_kvaliteta' => null,
            'napomena' => null,
        ];
    }
}
