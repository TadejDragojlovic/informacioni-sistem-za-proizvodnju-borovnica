<?php

namespace App\Services;

use App\Enums\LotDogadjajTip;
use App\Enums\LotStatus;
use App\Models\Lot;
use App\Models\SkladisnaLokacija;
use App\Models\Skladiste;
use App\Models\User;
use Carbon\CarbonImmutable;
use DomainException;
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

    public function primiUSkladiste(
        Lot $lot,
        SkladisnaLokacija $skladisnaLokacija,
        ?User $evidentirao = null
    ): Lot {
        return DB::transaction(function () use ($lot, $skladisnaLokacija, $evidentirao): Lot {
            $zakljucanLot = Lot::query()->lockForUpdate()->findOrFail($lot->id);
            $zakljucanaLokacija = SkladisnaLokacija::query()
                ->lockForUpdate()
                ->findOrFail($skladisnaLokacija->id);
            $skladiste = Skladiste::query()
                ->lockForUpdate()
                ->findOrFail($zakljucanaLokacija->skladiste_id);

            if ($zakljucanLot->status !== LotStatus::KREIRAN) {
                throw new DomainException('Samo lot u statusu KREIRAN može biti primljen u skladište.');
            }

            if (! $zakljucanaLokacija->aktivna) {
                throw new DomainException('Lot nije moguće primiti na neaktivnu skladišnu lokaciju.');
            }

            if (! $skladiste->aktivan) {
                throw new DomainException('Lot nije moguće primiti u neaktivno skladište.');
            }

            $zakljucanLot->update([
                'trenutna_skladisna_lokacija_id' => $zakljucanaLokacija->id,
                'status' => LotStatus::USKLADISTEN,
            ]);

            $zakljucanLot->dogadjaji()->create([
                'tip' => LotDogadjajTip::PRIJEM_U_SKLADISTE,
                'kolicina_g' => null,
                'vreme_dogadjaja' => now(),
                'evidentirao_user_id' => $evidentirao?->id,
                'prethodni_status' => LotStatus::KREIRAN,
                'novi_status' => LotStatus::USKLADISTEN,
                'prethodna_skladisna_lokacija_id' => null,
                'nova_skladisna_lokacija_id' => $zakljucanaLokacija->id,
                'razlog' => null,
            ]);

            return $zakljucanLot->load(['trenutnaSkladisnaLokacija', 'dogadjaji']);
        });
    }
}
