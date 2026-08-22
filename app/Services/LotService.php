<?php

namespace App\Services;

use App\Enums\LotDogadjajTip;
use App\Enums\LotStatus;
use App\Models\Lot;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class LotService
{
    public function __construct(
        private readonly LotOznakaGenerator $oznakaGenerator
    ) {}

    public function kreiraj(array $podaci, ?User $evidentirao = null): Lot
    {
        $pocetnaKolicina = (int) ($podaci['pocetna_kolicina_g'] ?? 0);

        if ($pocetnaKolicina <= 0) {
            throw new InvalidArgumentException('Početna količina lota mora biti veća od nule.');
        }

        $datumBerbe = CarbonImmutable::parse($podaci['datum_berbe']);

        return DB::transaction(function () use ($podaci, $datumBerbe, $pocetnaKolicina, $evidentirao): Lot {
            $lot = Lot::create([
                'oznaka' => $this->oznakaGenerator->generisi($datumBerbe),
                'sorta_id' => $podaci['sorta_id'],
                'parcela_id' => $podaci['parcela_id'],
                'trenutna_skladisna_lokacija_id' => null,
                'datum_berbe' => $datumBerbe,
                'pocetna_kolicina_g' => $pocetnaKolicina,
                'raspoloziva_kolicina_g' => $pocetnaKolicina,
                'status' => LotStatus::KREIRAN,
                'klasa_kvaliteta' => null,
                'broj_dokumenta_kvaliteta' => null,
                'napomena' => $podaci['napomena'] ?? null,
            ]);

            $lot->dogadjaji()->create([
                'tip' => LotDogadjajTip::LOT_KREIRAN,
                'kolicina_g' => $pocetnaKolicina,
                'vreme_dogadjaja' => now(),
                'evidentirao_user_id' => $evidentirao?->id,
                'prethodni_status' => null,
                'novi_status' => LotStatus::KREIRAN,
                'razlog' => null,
            ]);

            return $lot->load('dogadjaji');
        });
    }
}
