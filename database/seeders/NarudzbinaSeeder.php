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

        $potvrdjena = $this->narudzbina(
            $kupac1,
            NarudzbinaStatus::POTVRDJENA,
            'Bulevar oslobođenja 10, Novi Sad',
            '2026-07-01 09:30:00'
        );
        $potvrdjenaStavka = $this->stavka($potvrdjena, $chandler500, 6);

        $this->raspodela($lotChandler1, $potvrdjenaStavka, 4, LotRaspodelaStatus::REZERVISANO);
        $this->dogadjaj($lotChandler1, LotDogadjajTip::KOLICINA_REZERVISANA, '2026-07-01 10:00:00', 2000, null, $zaposleni->id);
        $this->raspodela($lotChandler2, $potvrdjenaStavka, 2, LotRaspodelaStatus::REZERVISANO);
        $this->dogadjaj($lotChandler2, LotDogadjajTip::KOLICINA_REZERVISANA, '2026-07-01 10:05:00', 1000, null, $zaposleni->id);

        $otpremljena = $this->narudzbina(
            $kupac2,
            NarudzbinaStatus::OTPREMLJENA,
            'Kralja Petra 25, Beograd',
            '2026-07-02 08:30:00'
        );
        $otpremljenaStavka = $this->stavka($otpremljena, $duke250, 4);
        $this->izdataRaspodela(
            $lotDuke,
            $otpremljenaStavka,
            4,
            '2026-07-02 09:00:00',
            '2026-07-02 14:00:00',
            $zaposleni
        );

        $otpremljenaChandler = $this->narudzbina(
            $kupac1,
            NarudzbinaStatus::OTPREMLJENA,
            'Bulevar Nemanjića 21, Niš',
            '2026-07-08 08:30:00'
        );
        $otpremljenaChandlerStavka = $this->stavka($otpremljenaChandler, $chandler500, 4);
        $this->izdataRaspodela(
            $lotChandler2,
            $otpremljenaChandlerStavka,
            4,
            '2026-07-08 09:00:00',
            '2026-07-08 15:00:00',
            $zaposleni
        );

        $otpremljenaMesovita = $this->narudzbina(
            $kupac3,
            NarudzbinaStatus::OTPREMLJENA,
            'Kralja Aleksandra I 44, Kragujevac',
            '2026-07-12 08:30:00'
        );
        $bluecropStavka = $this->stavka($otpremljenaMesovita, $bluecrop500, 4);
        $this->izdataRaspodela(
            $lotBluecrop,
            $bluecropStavka,
            4,
            '2026-07-12 09:00:00',
            '2026-07-12 14:00:00',
            $zaposleni
        );
        $dukeStavka = $this->stavka($otpremljenaMesovita, $duke250, 4);
        $this->izdataRaspodela(
            $lotDuke,
            $dukeStavka,
            4,
            '2026-07-12 09:05:00',
            '2026-07-12 14:05:00',
            $zaposleni
        );

        $otkazana = $this->narudzbina(
            $kupac3,
            NarudzbinaStatus::OTKAZANA,
            'Cara Lazara 7, Valjevo',
            '2026-07-03 10:30:00'
        );
        $otkazanaStavka = $this->stavka($otkazana, $bluecrop500, 3);
        $raspodelaBluecrop = $this->raspodela($lotBluecrop, $otkazanaStavka, 3, LotRaspodelaStatus::OTKAZANO);
        $this->dogadjaj($lotBluecrop, LotDogadjajTip::KOLICINA_REZERVISANA, '2026-07-03 11:00:00', 1500, $raspodelaBluecrop->id, $zaposleni->id);
        $this->dogadjaj($lotBluecrop, LotDogadjajTip::REZERVACIJA_OSLOBODJENA, '2026-07-03 16:00:00', -1500, $raspodelaBluecrop->id, $zaposleni->id);
    }

    private function narudzbina(
        User $kupac,
        NarudzbinaStatus $status,
        string $adresaIsporuke,
        string $datum
    ): Narudzbina {
        $narudzbina = Narudzbina::updateOrCreate(
            ['adresa_isporuke' => $adresaIsporuke],
            ['user_id' => $kupac->id, 'status' => $status]
        );
        $vreme = Carbon::parse($datum);
        $narudzbina->forceFill([
            'created_at' => $vreme,
            'updated_at' => $vreme,
        ])->save();

        return $narudzbina;
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

    private function izdataRaspodela(
        Lot $lot,
        NarudzbinaStavka $stavka,
        int $brojPakovanja,
        string $vremeRezervacije,
        string $vremeIzdavanja,
        User $evidentirao
    ): void {
        $raspodela = $this->raspodela($lot, $stavka, $brojPakovanja, LotRaspodelaStatus::IZDATO);
        $kolicinaG = $brojPakovanja * $stavka->neto_kolicina_g;

        $this->dogadjaj(
            $lot,
            LotDogadjajTip::KOLICINA_REZERVISANA,
            $vremeRezervacije,
            $kolicinaG,
            $raspodela->id,
            $evidentirao->id
        );
        $this->dogadjaj(
            $lot,
            LotDogadjajTip::KOLICINA_IZDATA,
            $vremeIzdavanja,
            $kolicinaG,
            $raspodela->id,
            $evidentirao->id,
            $lot->trenutna_skladisna_lokacija_id
        );
    }

    private function dogadjaj(
        Lot $lot,
        LotDogadjajTip $tip,
        string $vreme,
        int $kolicina,
        ?int $lotRaspodelaId,
        ?int $evidentiraoUserId,
        ?int $prethodnaSkladisnaLokacijaId = null
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
                'prethodna_skladisna_lokacija_id' => $prethodnaSkladisnaLokacijaId,
            ])
        );
    }
}
