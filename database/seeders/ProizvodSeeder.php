<?php

namespace Database\Seeders;

use App\Models\Proizvod;
use App\Models\Sorta;
use Illuminate\Database\Seeder;

class ProizvodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $proizvodi = [
            ['naziv' => 'Sveže borovnice Chandler 250 g', 'sorta' => 'Chandler', 'neto_kolicina_g' => 250, 'cena' => 450.00],
            ['naziv' => 'Sveže borovnice Chandler 500 g', 'sorta' => 'Chandler', 'neto_kolicina_g' => 500, 'cena' => 800.00],
            ['naziv' => 'Sveže borovnice Duke 250 g', 'sorta' => 'Duke', 'neto_kolicina_g' => 250, 'cena' => 420.00],
            ['naziv' => 'Sveže borovnice Bluecrop 500 g', 'sorta' => 'Bluecrop', 'neto_kolicina_g' => 500, 'cena' => 760.00],
        ];

        foreach ($proizvodi as $proizvod) {
            $sorta = Sorta::where('naziv', $proizvod['sorta'])->firstOrFail();

            Proizvod::updateOrCreate(
                ['naziv' => $proizvod['naziv']],
                [
                    'opis' => 'Prodajno pakovanje svežih borovnica sorte '.$proizvod['sorta'].'.',
                    'sorta_id' => $sorta->id,
                    'neto_kolicina_g' => $proizvod['neto_kolicina_g'],
                    'cena' => $proizvod['cena'],
                    'aktivan' => true,
                ]
            );
        }
    }
}
