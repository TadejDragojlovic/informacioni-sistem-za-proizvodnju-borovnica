<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Skladiste;
use App\Models\Proizvod;
use App\Models\Resurs;
use App\Models\Narudzbina;
use App\Models\NarudzbinaStavka;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // kreacija admin naloga
        User::factory()->create([
            'name' => 'Admin 1',
            'email' => 'admin@borovnica.com',
            'password' => bcrypt('admin'),
            'role' => 'admin',
        ]);

        // kreiranje laznih podataka za testiranje
        Skladiste::factory(3)->create()->each(function ($skladiste) {
            Proizvod::factory(5)->create([
                'skladiste_id' => $skladiste->id
            ])->each(function ($proizvod) {
                Resurs::factory(2)->create([
                    'proizvod_id' => $proizvod->id
                ]);
            });
        });

        // kreiranje laznih narudzbina
        Narudzbina::factory(10)->create([
            'user_id' => 1,
        ])->each(function ($narudzbina) {
            // stavke narudzbina
            NarudzbinaStavka::factory(rand(1,3))->create([
                'narudzbina_id' => $narudzbina->id,
                'proizvod_id' => Proizvod::inRandomOrder()->first()->id,
            ]);
        });
    }
}
