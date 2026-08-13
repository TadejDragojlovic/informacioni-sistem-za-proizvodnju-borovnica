<?php

namespace Database\Seeders;

use App\Enums\LotDogadjajTip;
use App\Enums\LotRaspodelaStatus;
use App\Enums\NarudzbinaStatus;
use App\Models\Lot;
use App\Models\LotDogadjaj;
use App\Models\LotRaspodela;
use App\Models\Narudzbina;
use App\Models\NarudzbinaStavka;
use App\Models\Proizvod;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class NarudzbinaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kupac1 = User::where('email', 'kupac@borovnica.com')->firstOrFail();
        $kupac2 = User::where('email', 'kupac2@borovnica.com')->firstOrFail();
        $kupac3 = User::where('email', 'kupac3@borovnica.com')->firstOrFail();
        $zaposleni = User::where('email', 'zaposleni2@borovnica.com')->firstOrFail();

        $chandler500 = Proizvod::whereHas('sorta', fn ($query) => $query->where('naziv', 'Chandler'))
            ->where('neto_kolicina_g', 500)
            ->firstOrFail();
        $duke250 = Proizvod::whereHas('sorta', fn ($query) => $query->where('naziv', 'Duke'))
            ->where('neto_kolicina_g', 250)
            ->firstOrFail();
        $bluecrop500 = Proizvod::whereHas('sorta', fn ($query) => $query->where('naziv', 'Bluecrop'))
            ->where('neto_kolicina_g', 500)
            ->firstOrFail();

        $lotChandler1 = Lot::where('oznaka', 'BL-2026-001')->firstOrFail();
        $lotChandler2 = Lot::where('oznaka', 'BL-2026-002')->firstOrFail();
        $lotDuke = Lot::where('oznaka', 'BL-2026-003')->firstOrFail();
        $lotBluecrop = Lot::where('oznaka', 'BL-2026-006')->firstOrFail();

        $potvrdjena = Narudzbina::updateOrCreate(
            ['adresa_isporuke' => 'Bulevar oslobođenja 10, Novi Sad'],
            ['user_id' => $kupac1->id, 'status' => NarudzbinaStatus::POTVRDJENA]
        );
        $potvrdjenaStavka = $this->stavka($potvrdjena, $chandler500, 6);

        $this->raspodela($lotChandler1, $potvrdjenaStavka, 4, LotRaspodelaStatus::REZERVISANO);
        $this->dogadjaj($lotChandler1, LotDogadjajTip::KOLICINA_REZERVISANA, '2026-07-01 10:00:00', 2000, null, $zaposleni->id);
        $this->raspodela($lotChandler2, $potvrdjenaStavka, 2, LotRaspodelaStatus::REZERVISANO);
        $this->dogadjaj($lotChandler2, LotDogadjajTip::KOLICINA_REZERVISANA, '2026-07-01 10:05:00', 1000, null, $zaposleni->id);

        $otpremljena = Narudzbina::updateOrCreate(
            ['adresa_isporuke' => 'Kralja Petra 25, Beograd'],
            ['user_id' => $kupac2->id, 'status' => NarudzbinaStatus::OTPREMLJENA]
        );
        $otpremljenaStavka = $this->stavka($otpremljena, $duke250, 4);
        $raspodelaDuke = $this->raspodela($lotDuke, $otpremljenaStavka, 4, LotRaspodelaStatus::IZDATO);
        $this->dogadjaj($lotDuke, LotDogadjajTip::KOLICINA_REZERVISANA, '2026-07-02 09:00:00', 1000, $raspodelaDuke->id, $zaposleni->id);
        $this->dogadjaj($lotDuke, LotDogadjajTip::KOLICINA_IZDATA, '2026-07-02 14:00:00', 1000, $raspodelaDuke->id, $zaposleni->id);

        $otkazana = Narudzbina::updateOrCreate(
            ['adresa_isporuke' => 'Cara Lazara 7, Valjevo'],
            ['user_id' => $kupac3->id, 'status' => NarudzbinaStatus::OTKAZANA]
        );
        $otkazanaStavka = $this->stavka($otkazana, $bluecrop500, 3);
        $raspodelaBluecrop = $this->raspodela($lotBluecrop, $otkazanaStavka, 3, LotRaspodelaStatus::OTKAZANO);
        $this->dogadjaj($lotBluecrop, LotDogadjajTip::KOLICINA_REZERVISANA, '2026-07-03 11:00:00', 1500, $raspodelaBluecrop->id, $zaposleni->id);
        $this->dogadjaj($lotBluecrop, LotDogadjajTip::REZERVACIJA_OSLOBODJENA, '2026-07-03 16:00:00', -1500, $raspodelaBluecrop->id, $zaposleni->id);
    }

    private function stavka(Narudzbina $narudzbina, Proizvod $proizvod, int $kolicina): NarudzbinaStavka
    {
        return NarudzbinaStavka::updateOrCreate(
            [
                'narudzbina_id' => $narudzbina->id,
                'proizvod_id' => $proizvod->id,
            ],
            [
                'kolicina' => $kolicina,
                'neto_kolicina_g' => $proizvod->neto_kolicina_g,
                'cena_po_jedinici' => $proizvod->cena,
            ]
        );
    }

    private function raspodela(
        Lot $lot,
        NarudzbinaStavka $stavka,
        int $brojPakovanja,
        LotRaspodelaStatus $status
    ): LotRaspodela {
        return LotRaspodela::updateOrCreate(
            [
                'lot_id' => $lot->id,
                'narudzbina_stavka_id' => $stavka->id,
            ],
            [
                'broj_pakovanja' => $brojPakovanja,
                'status' => $status,
            ]
        );
    }

    private function dogadjaj(
        Lot $lot,
        LotDogadjajTip $tip,
        string $vreme,
        int $kolicina,
        ?int $lotRaspodelaId,
        ?int $evidentiraoUserId
    ): void {
        $vremeDogadjaja = Carbon::parse($vreme);
        $identifikator = [
            'lot_id' => $lot->id,
            'lot_raspodela_id' => $lotRaspodelaId,
            'tip' => $tip->value,
            'vreme_dogadjaja' => $vremeDogadjaja,
        ];

        LotDogadjaj::updateOrCreate(
            $identifikator,
            array_merge($identifikator, [
                'kolicina_g' => $kolicina,
                'evidentirao_user_id' => $evidentiraoUserId,
            ])
        );
    }
}
