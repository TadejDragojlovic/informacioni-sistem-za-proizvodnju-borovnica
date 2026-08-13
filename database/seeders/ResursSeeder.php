<?php

namespace Database\Seeders;

use App\Models\Lot;
use App\Models\Resurs;
use App\Models\User;
use Illuminate\Database\Seeder;

class ResursSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $zaposleni = User::where('email', 'zaposleni@borovnica.com')->firstOrFail();

        $resursi = [
            ['lot' => 'BL-2026-001', 'naziv' => 'Organsko djubrivo', 'kolicina' => 12.50, 'jedinica_mere' => 'kg', 'cena_po_jedinici' => 180.00, 'datum_upotrebe' => '2026-05-15'],
            ['lot' => 'BL-2026-001', 'naziv' => 'Ambalazne kutije 500 g', 'kolicina' => 30.00, 'jedinica_mere' => 'kom', 'cena_po_jedinici' => 35.00, 'datum_upotrebe' => '2026-06-20'],
            ['lot' => 'BL-2026-002', 'naziv' => 'Kartonske pregrade', 'kolicina' => 18.00, 'jedinica_mere' => 'kom', 'cena_po_jedinici' => 12.50, 'datum_upotrebe' => '2026-06-22'],
            ['lot' => 'BL-2026-003', 'naziv' => 'Rashladni gel', 'kolicina' => 8.00, 'jedinica_mere' => 'kg', 'cena_po_jedinici' => 240.00, 'datum_upotrebe' => '2026-06-18'],
            ['lot' => 'BL-2026-006', 'naziv' => 'Etikete za sledljivost', 'kolicina' => 25.00, 'jedinica_mere' => 'kom', 'cena_po_jedinici' => 8.00, 'datum_upotrebe' => '2026-06-26'],
        ];

        foreach ($resursi as $resurs) {
            $lot = Lot::where('oznaka', $resurs['lot'])->firstOrFail();

            Resurs::updateOrCreate(
                [
                    'lot_id' => $lot->id,
                    'naziv' => $resurs['naziv'],
                    'datum_upotrebe' => $resurs['datum_upotrebe'],
                ],
                [
                    'kolicina' => $resurs['kolicina'],
                    'jedinica_mere' => $resurs['jedinica_mere'],
                    'cena_po_jedinici' => $resurs['cena_po_jedinici'],
                    'evidentirao_user_id' => $zaposleni->id,
                ]
            );
        }
    }
}
