<?php

namespace Tests\Feature;

use App\Enums\LotRaspodelaStatus;
use App\Enums\LotStatus;
use App\Enums\NarudzbinaStatus;
use App\Enums\UserRole;
use App\Models\Lot;
use App\Models\LotRaspodela;
use App\Models\Narudzbina;
use App\Models\NarudzbinaStavka;
use App\Models\Proizvod;
use App\Models\Resurs;
use App\Models\SkladisnaLokacija;
use App\Models\Skladiste;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class FinansijskiIzvestajTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function test_izvestaj_tacno_obradjuje_prihode_i_troskove()
    {
        // 1. Setup: Admin koji ima pristup
        $admin = User::factory()->create(['role' => UserRole::ADMIN->value]);
        $danas = Carbon::create(2026, 1, 1);

        $skladiste = Skladiste::factory()->create([
            'mesecni_trosak' => 3000,
        ]);
        $skladisnaLokacija = SkladisnaLokacija::factory()->create([
            'skladiste_id' => $skladiste->id,
        ]);
        $proizvod = Proizvod::factory()->create([
            'naziv' => 'Borovnica Premium',
            'neto_kolicina_g' => 500,
            'cena' => 1200,
        ]);
        $lot = Lot::factory()->create([
            'sorta_id' => $proizvod->sorta_id,
            'trenutna_skladisna_lokacija_id' => $skladisnaLokacija->id,
            'status' => LotStatus::RASPOLOZIV,
        ]);

        $narudzbina = Narudzbina::factory()->create([
            'user_id' => User::factory()->create()->id,
            'status' => NarudzbinaStatus::OTPREMLJENA,
            'created_at' => $danas,
            'updated_at' => $danas,
        ]);
        $stavka = NarudzbinaStavka::factory()->create([
            'narudzbina_id' => $narudzbina->id,
            'proizvod_id' => $proizvod->id,
            'kolicina' => 2,
            'neto_kolicina_g' => 500,
            'cena_po_jedinici' => 1200,
        ]);
        LotRaspodela::factory()->create([
            'lot_id' => $lot->id,
            'narudzbina_stavka_id' => $stavka->id,
            'broj_pakovanja' => 2,
            'status' => LotRaspodelaStatus::IZDATO,
        ]);
        Resurs::factory()->create([
            'lot_id' => $lot->id,
            'naziv' => 'Navodnjavanje',
            'kolicina' => 10,
            'cena_po_jedinici' => 20,
        ]);

        // generisemo izvestaj samo za danas
        $response = $this->actingAs($admin)->post(route('admin.finansije.generate'), [
            'datum_od' => $danas->toDateString(),
            'datum_do' => $danas->toDateString(),
        ]);

        $response->assertStatus(200);
        $response->assertViewHas('ukupniPrihod', 2400);
        $response->assertViewHas('ukupniRashod', 3200);
        $response->assertViewHas('netoDobit', -800);
    }
}
