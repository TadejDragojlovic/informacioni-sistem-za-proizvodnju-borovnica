<?php

namespace Database\Factories;

use App\Enums\LotDogadjajTip;
use App\Models\Lot;
use Illuminate\Database\Eloquent\Factories\Factory;

class LotDogadjajFactory extends Factory
{
    public function definition(): array
    {
        return [
            'lot_id' => Lot::factory(),
            'lot_raspodela_id' => null,
            'tip' => LotDogadjajTip::LOT_KREIRAN,
            'kolicina_g' => null,
            'vreme_dogadjaja' => fake()->dateTime(),
            'evidentirao_user_id' => null,
            'prethodni_status' => null,
            'novi_status' => null,
            'prethodna_skladisna_lokacija_id' => null,
            'nova_skladisna_lokacija_id' => null,
            'razlog' => null,
        ];
    }
}
