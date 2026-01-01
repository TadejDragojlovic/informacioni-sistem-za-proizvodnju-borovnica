<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use PHPUnit\Framework\Attributes\Test;
use App\Models\User;
use App\Models\Skladiste;
use App\Models\Proizvod;
use App\Models\Resurs;
use App\Models\Narudzbina;

use Carbon\Carbon;

class FinansijskiIzvestajTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function test_izvestaj_tacno_obradjuje_prihode_i_troskove()
    {
        // 1. Setup: Admin koji ima pristup
        $admin = User::factory()->create(['role' => 'admin']);
        $danas = Carbon::create(2026, 1, 1);
        
        // trosak: 3000
        $skladiste = Skladiste::create([
            'lokacija' => 'Valjevo', 
            'kapacitet' => 2000, 
            'temperatura' => -5, 
            'trosak' => 3000
        ]);

        $proizvod = Proizvod::create([
            'naziv' => 'Borovnica Premium', 
            'cena' => 1200, 
            'kolicina' => 100, 
            'skladiste_id' => $skladiste->id
        ]);

        // trosak ukupno => 20 * 10 = 200
        Resurs::create([
            'naziv' => 'Navodnjavanje',
            'trosak' => 20,
            'kolicina' => 10,
            'proizvod_id' => $proizvod->id
        ]);

        $narudzbina = Narudzbina::create([
            'user_id' => User::factory()->create()->id,
            'datum_narudzbine' => $danas,
            'status' => 'isporucena'
        ]);

        // 2 * 1200 = 2400 (prihod)
        $narudzbina->stavke()->create([
            'proizvod_id' => $proizvod->id,
            'kolicina' => 2,
            'cena' => 1200
        ]);

        // generisemo izvestaj samo za danas
        $response = $this->actingAs($admin)->post(route('admin.finansije.generate'), [
            'datum_od' => $danas,
            'datum_do' => $danas,
        ]);

        $response->assertStatus(200);
        $response->assertViewHas('ukupniPrihod', 2400);
        $response->assertViewHas('ukupniRashod', 3200);
        $response->assertViewHas('netoDobit', -800);
    }
}
