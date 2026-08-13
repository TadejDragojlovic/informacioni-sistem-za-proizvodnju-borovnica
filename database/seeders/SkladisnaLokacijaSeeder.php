<?php

namespace Database\Seeders;

use App\Models\SkladisnaLokacija;
use App\Models\Skladiste;
use Illuminate\Database\Seeder;

class SkladisnaLokacijaSeeder extends Seeder
{
    public function run(): void
    {
        $lokacije = [
            'Hladnjača Valjevo' => [
                ['naziv' => 'Prijemna zona', 'opis' => 'Zona za prijem i početnu evidenciju lotova.'],
                ['naziv' => 'Hladna komora 1', 'opis' => 'Komora za čuvanje svežih borovnica.'],
                ['naziv' => 'Zona za otpremu', 'opis' => 'Zona za pripremu robe za otpremu.'],
            ],
            'Skladište za otpremu' => [
                ['naziv' => 'Prijemna zona', 'opis' => 'Zona za prijem robe iz drugih skladišta.'],
                ['naziv' => 'Zona za otpremu', 'opis' => 'Zona za konačnu pripremu narudžbina.'],
            ],
        ];

        foreach ($lokacije as $nazivSkladista => $skladisneLokacije) {
            $skladiste = Skladiste::where('naziv', $nazivSkladista)->firstOrFail();

            foreach ($skladisneLokacije as $lokacija) {
                SkladisnaLokacija::updateOrCreate(
                    [
                        'skladiste_id' => $skladiste->id,
                        'naziv' => $lokacija['naziv'],
                    ],
                    [
                        'opis' => $lokacija['opis'],
                        'aktivna' => true,
                    ]
                );
            }
        }
    }
}
