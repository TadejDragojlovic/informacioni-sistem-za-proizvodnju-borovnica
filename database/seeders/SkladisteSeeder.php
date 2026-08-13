<?php

namespace Database\Seeders;

use App\Models\Skladiste;
use Illuminate\Database\Seeder;

class SkladisteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $skladista = [
            [
                'naziv' => 'Hladnjača Valjevo',
                'lokacija' => 'Valjevo',
                'mesecni_trosak' => 120000.00,
                'aktivan' => true,
            ],
            [
                'naziv' => 'Skladište za otpremu',
                'lokacija' => 'Beograd',
                'mesecni_trosak' => 65000.00,
                'aktivan' => true,
            ],
        ];

        foreach ($skladista as $skladiste) {
            Skladiste::updateOrCreate(
                ['naziv' => $skladiste['naziv']],
                $skladiste
            );
        }
    }
}
