<?php

namespace Tests\Feature;

use App\Enums\LotDogadjajTip;
use App\Enums\LotRaspodelaStatus;
use App\Enums\LotStatus;
use App\Enums\NarudzbinaStatus;
use App\Models\Lot;
use App\Models\LotDogadjaj;
use App\Models\LotRaspodela;
use App\Models\Narudzbina;
use App\Models\NarudzbinaStavka;
use App\Models\Resurs;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SeederTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function factory_ji_kreiraju_validne_zapise_za_lot_i_sledljivost(): void
    {
        $lot = Lot::factory()->create();
        $stavka = NarudzbinaStavka::factory()->create();
        $raspodela = LotRaspodela::factory()->create([
            'lot_id' => $lot->id,
            'narudzbina_stavka_id' => $stavka->id,
        ]);
        $dogadjaj = LotDogadjaj::factory()->create([
            'lot_id' => $lot->id,
            'lot_raspodela_id' => $raspodela->id,
            'tip' => LotDogadjajTip::KOLICINA_REZERVISANA,
        ]);
        $resurs = Resurs::factory()->create(['lot_id' => $lot->id]);

        $this->assertSame($lot->pocetna_kolicina_g, $lot->raspoloziva_kolicina_g);
        $this->assertSame(LotStatus::KREIRAN, $lot->status);
        $this->assertSame(LotRaspodelaStatus::REZERVISANO, $raspodela->status);
        $this->assertSame(LotDogadjajTip::KOLICINA_REZERVISANA, $dogadjaj->tip);
        $this->assertSame($lot->id, $resurs->lot_id);
    }

    #[Test]
    public function database_seeder_kreira_ocekivani_demo_scenario(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseCount('users', 6);
        $this->assertDatabaseCount('sortas', 3);
        $this->assertDatabaseCount('parcelas', 3);
        $this->assertDatabaseCount('skladistes', 2);
        $this->assertDatabaseCount('skladisna_lokacija', 5);
        $this->assertDatabaseCount('proizvods', 4);
        $this->assertDatabaseCount('lots', 8);
        $this->assertDatabaseCount('narudzbinas', 3);
        $this->assertDatabaseCount('narudzbina_stavkas', 3);
        $this->assertDatabaseCount('lot_raspodela', 4);
        $this->assertDatabaseCount('resurs', 5);

        $this->assertDatabaseHas('narudzbinas', [
            'status' => NarudzbinaStatus::POTVRDJENA->value,
        ]);
        $this->assertDatabaseHas('narudzbinas', [
            'status' => NarudzbinaStatus::OTPREMLJENA->value,
        ]);
        $this->assertDatabaseHas('narudzbinas', [
            'status' => NarudzbinaStatus::OTKAZANA->value,
        ]);
        $this->assertDatabaseHas('lots', [
            'oznaka' => 'BL-2026-004',
            'status' => LotStatus::BLOKIRAN->value,
        ]);
        $this->assertDatabaseHas('lots', [
            'oznaka' => 'BL-2026-005',
            'status' => LotStatus::POVUCEN->value,
        ]);
    }

    #[Test]
    public function database_seeder_je_ponovljiv(): void
    {
        $this->seed(DatabaseSeeder::class);
        $brojZapisa = $this->brojDemoZapisa();

        $this->seed(DatabaseSeeder::class);

        $this->assertSame($brojZapisa, $this->brojDemoZapisa());
    }

    /** @return array<string, int> */
    private function brojDemoZapisa(): array
    {
        return [
            'users' => User::count(),
            'lots' => Lot::count(),
            'narudzbine' => Narudzbina::count(),
            'stavke' => NarudzbinaStavka::count(),
            'raspodele' => LotRaspodela::count(),
            'dogadjaji' => LotDogadjaj::count(),
            'resursi' => Resurs::count(),
        ];
    }
}
