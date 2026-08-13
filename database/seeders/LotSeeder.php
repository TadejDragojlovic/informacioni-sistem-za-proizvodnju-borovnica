<?php

namespace Database\Seeders;

use App\Enums\KlasaKvaliteta;
use App\Enums\LotDogadjajTip;
use App\Enums\LotStatus;
use App\Models\Lot;
use App\Models\LotDogadjaj;
use App\Models\Parcela;
use App\Models\SkladisnaLokacija;
use App\Models\Sorta;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class LotSeeder extends Seeder
{
    public function run(): void
    {
        $zaposleni = User::where('email', 'zaposleni@borovnica.com')->firstOrFail();
        $hladnaKomora = SkladisnaLokacija::where('naziv', 'Hladna komora 1')->firstOrFail();

        $lotovi = [
            [
                'oznaka' => 'BL-2026-001',
                'sorta' => 'Chandler',
                'parcela' => 'P-01',
                'datum_berbe' => '2026-06-20',
                'pocetna_kolicina_g' => 10000,
                'raspoloziva_kolicina_g' => 8000,
                'status' => LotStatus::RASPOLOZIV,
                'klasa_kvaliteta' => KlasaKvaliteta::KLASA_I,
                'broj_dokumenta_kvaliteta' => 'KD-2026-001',
                'lokacija_id' => $hladnaKomora->id,
                'napomena' => 'Demo lot sa rezervisanim delom količine.',
            ],
            [
                'oznaka' => 'BL-2026-002',
                'sorta' => 'Chandler',
                'parcela' => 'P-02',
                'datum_berbe' => '2026-06-22',
                'pocetna_kolicina_g' => 8000,
                'raspoloziva_kolicina_g' => 7000,
                'status' => LotStatus::RASPOLOZIV,
                'klasa_kvaliteta' => KlasaKvaliteta::KLASA_I,
                'broj_dokumenta_kvaliteta' => 'KD-2026-002',
                'lokacija_id' => $hladnaKomora->id,
                'napomena' => 'Drugi Chandler lot za FIFO raspodelu.',
            ],
            [
                'oznaka' => 'BL-2026-003',
                'sorta' => 'Duke',
                'parcela' => 'P-02',
                'datum_berbe' => '2026-06-18',
                'pocetna_kolicina_g' => 6000,
                'raspoloziva_kolicina_g' => 5000,
                'status' => LotStatus::RASPOLOZIV,
                'klasa_kvaliteta' => KlasaKvaliteta::KLASA_II,
                'broj_dokumenta_kvaliteta' => 'KD-2026-003',
                'lokacija_id' => $hladnaKomora->id,
                'napomena' => 'Deo lota je izdat u otpremljenoj narudžbini.',
            ],
            [
                'oznaka' => 'BL-2026-004',
                'sorta' => 'Bluecrop',
                'parcela' => 'P-03',
                'datum_berbe' => '2026-06-24',
                'pocetna_kolicina_g' => 5000,
                'raspoloziva_kolicina_g' => 5000,
                'status' => LotStatus::BLOKIRAN,
                'klasa_kvaliteta' => KlasaKvaliteta::KLASA_II,
                'broj_dokumenta_kvaliteta' => 'KD-2026-004',
                'lokacija_id' => $hladnaKomora->id,
                'napomena' => 'Blokiran do dodatne kontrole kvaliteta.',
            ],
            [
                'oznaka' => 'BL-2026-005',
                'sorta' => 'Bluecrop',
                'parcela' => 'P-03',
                'datum_berbe' => '2026-06-15',
                'pocetna_kolicina_g' => 4000,
                'raspoloziva_kolicina_g' => 0,
                'status' => LotStatus::POVUCEN,
                'klasa_kvaliteta' => KlasaKvaliteta::KLASA_II,
                'broj_dokumenta_kvaliteta' => 'KD-2026-005',
                'lokacija_id' => null,
                'napomena' => 'Povučen zbog isteka roka za prodaju.',
            ],
            [
                'oznaka' => 'BL-2026-006',
                'sorta' => 'Bluecrop',
                'parcela' => 'P-01',
                'datum_berbe' => '2026-06-26',
                'pocetna_kolicina_g' => 7000,
                'raspoloziva_kolicina_g' => 7000,
                'status' => LotStatus::RASPOLOZIV,
                'klasa_kvaliteta' => KlasaKvaliteta::KLASA_I,
                'broj_dokumenta_kvaliteta' => 'KD-2026-006',
                'lokacija_id' => $hladnaKomora->id,
                'napomena' => 'Lot sa otkazanom rezervacijom.',
            ],
            [
                'oznaka' => 'BL-2026-007',
                'sorta' => 'Duke',
                'parcela' => 'P-01',
                'datum_berbe' => '2026-06-28',
                'pocetna_kolicina_g' => 2000,
                'raspoloziva_kolicina_g' => 2000,
                'status' => LotStatus::KREIRAN,
                'klasa_kvaliteta' => null,
                'broj_dokumenta_kvaliteta' => null,
                'lokacija_id' => null,
                'napomena' => 'Novoformirani lot koji čeka prijem u skladište.',
            ],
            [
                'oznaka' => 'BL-2026-008',
                'sorta' => 'Duke',
                'parcela' => 'P-03',
                'datum_berbe' => '2026-06-27',
                'pocetna_kolicina_g' => 4500,
                'raspoloziva_kolicina_g' => 4500,
                'status' => LotStatus::USKLADISTEN,
                'klasa_kvaliteta' => KlasaKvaliteta::KLASA_I,
                'broj_dokumenta_kvaliteta' => 'KD-2026-008',
                'lokacija_id' => $hladnaKomora->id,
                'napomena' => 'Primljen u skladište i čeka odobrenje za prodaju.',
            ],
        ];

        foreach ($lotovi as $index => $podaci) {
            $sorta = Sorta::where('naziv', $podaci['sorta'])->firstOrFail();
            $parcela = Parcela::where('oznaka', $podaci['parcela'])->firstOrFail();

            $lot = Lot::updateOrCreate(
                ['oznaka' => $podaci['oznaka']],
                [
                    'sorta_id' => $sorta->id,
                    'parcela_id' => $parcela->id,
                    'trenutna_skladisna_lokacija_id' => $podaci['lokacija_id'],
                    'datum_berbe' => $podaci['datum_berbe'],
                    'pocetna_kolicina_g' => $podaci['pocetna_kolicina_g'],
                    'raspoloziva_kolicina_g' => $podaci['raspoloziva_kolicina_g'],
                    'status' => $podaci['status'],
                    'klasa_kvaliteta' => $podaci['klasa_kvaliteta'],
                    'broj_dokumenta_kvaliteta' => $podaci['broj_dokumenta_kvaliteta'],
                    'napomena' => $podaci['napomena'],
                ]
            );

            $vreme = Carbon::parse($podaci['datum_berbe'])->setTime(8, 0)->addDays($index);

            $this->dogadjaj($lot, LotDogadjajTip::LOT_KREIRAN, $vreme, $zaposleni->id, [
                'kolicina_g' => $lot->pocetna_kolicina_g,
                'novi_status' => LotStatus::KREIRAN,
            ]);

            if ($podaci['lokacija_id'] !== null) {
                $this->dogadjaj($lot, LotDogadjajTip::PRIJEM_U_SKLADISTE, $vreme->copy()->addHours(2), $zaposleni->id, [
                    'prethodni_status' => LotStatus::KREIRAN,
                    'novi_status' => LotStatus::USKLADISTEN,
                    'nova_skladisna_lokacija_id' => $podaci['lokacija_id'],
                ]);
            }

            if ($podaci['klasa_kvaliteta'] !== null) {
                $this->dogadjaj($lot, LotDogadjajTip::KLASA_KVALITETA_DODELJENA, $vreme->copy()->addHours(4), $zaposleni->id, [
                    'razlog' => 'Rezultat kontrole kvaliteta: '.$podaci['broj_dokumenta_kvaliteta'],
                ]);
            }

            if ($podaci['status'] === LotStatus::RASPOLOZIV) {
                $this->dogadjaj($lot, LotDogadjajTip::ODOBREN_ZA_PRODAJU, $vreme->copy()->addHours(6), $zaposleni->id, [
                    'prethodni_status' => LotStatus::USKLADISTEN,
                    'novi_status' => LotStatus::RASPOLOZIV,
                ]);
            }

            if ($podaci['status'] === LotStatus::BLOKIRAN) {
                $this->dogadjaj($lot, LotDogadjajTip::LOT_BLOKIRAN, $vreme->copy()->addHours(6), $zaposleni->id, [
                    'prethodni_status' => LotStatus::USKLADISTEN,
                    'novi_status' => LotStatus::BLOKIRAN,
                    'razlog' => 'Potrebna dodatna kontrola kvaliteta.',
                ]);
            }

            if ($podaci['status'] === LotStatus::POVUCEN) {
                $this->dogadjaj($lot, LotDogadjajTip::LOT_BLOKIRAN, $vreme->copy()->addHours(6), $zaposleni->id, [
                    'prethodni_status' => LotStatus::USKLADISTEN,
                    'novi_status' => LotStatus::BLOKIRAN,
                    'razlog' => 'Privremeno blokiran pre povlačenja.',
                ]);
                $this->dogadjaj($lot, LotDogadjajTip::LOT_POVUCEN, $vreme->copy()->addHours(8), $zaposleni->id, [
                    'prethodni_status' => LotStatus::BLOKIRAN,
                    'novi_status' => LotStatus::POVUCEN,
                    'prethodna_skladisna_lokacija_id' => $hladnaKomora->id,
                    'razlog' => 'Istek roka za prodaju.',
                ]);
            }
        }
    }

    private function dogadjaj(
        Lot $lot,
        LotDogadjajTip $tip,
        Carbon $vreme,
        int $evidentiraoUserId,
        array $podaci = []
    ): void {
        $identifikator = [
            'lot_id' => $lot->id,
            'lot_raspodela_id' => $podaci['lot_raspodela_id'] ?? null,
            'tip' => $tip->value,
            'vreme_dogadjaja' => $vreme,
        ];

        LotDogadjaj::updateOrCreate(
            $identifikator,
            array_merge(
                $identifikator,
                ['evidentirao_user_id' => $evidentiraoUserId],
                $podaci
            )
        );
    }
}
