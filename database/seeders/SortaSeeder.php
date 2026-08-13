<?php

namespace Database\Seeders;

use App\Models\Sorta;
use Illuminate\Database\Seeder;

class SortaSeeder extends Seeder
{
    public function run(): void
    {
        $sorte = [
            'Chandler' => 'Kasna sorta krupnih i čvrstih plodova.',
            'Duke' => 'Rana sorta pogodna za početak berbe.',
            'Bluecrop' => 'Pouzdana sorta sa dobrim prinosom i kvalitetom ploda.',
        ];

        foreach ($sorte as $naziv => $opis) {
            Sorta::updateOrCreate(
                ['naziv' => $naziv],
                ['opis' => $opis]
            );
        }
    }
}
