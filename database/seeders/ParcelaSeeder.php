<?php

namespace Database\Seeders;

use App\Models\Parcela;
use Illuminate\Database\Seeder;

class ParcelaSeeder extends Seeder
{
    public function run(): void
    {
        $parcele = [
            ['oznaka' => 'P-01', 'povrsina_m2' => 12000, 'zemlja_porekla' => 'Srbija'],
            ['oznaka' => 'P-02', 'povrsina_m2' => 8500, 'zemlja_porekla' => 'Srbija'],
            ['oznaka' => 'P-03', 'povrsina_m2' => 15600, 'zemlja_porekla' => 'Srbija'],
        ];

        foreach ($parcele as $parcela) {
            Parcela::updateOrCreate(
                ['oznaka' => $parcela['oznaka']],
                $parcela
            );
        }
    }
}
