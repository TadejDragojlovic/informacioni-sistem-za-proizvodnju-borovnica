<?php

namespace Tests\Feature;

use App\Models\Proizvod;
use App\Models\Skladiste;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class NarudzbinaTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function test_korisnik_moze_kreirati_narudzbinu()
    {
        $user = User::factory()->create(['role' => 'kupac']);
        $skladiste = Skladiste::create(['lokacija' => 'Valjevo', 'kapacitet' => 1500, 'temperatura' => -10, 'trosak' => 12000]);
        $proizvod = Proizvod::create([
            'naziv' => 'Proizvod1',
            'cena' => 100,
            'kolicina' => 10,
            'skladiste_id' => $skladiste->id,
        ]);

        // simulacija korpe
        $korpa = [
            $proizvod->id => ['naziv' => 'Proizvod1', 'kolicina' => 1, 'cena' => 100],
        ];

        // potvrdjivanje narudzbine
        $response = $this->actingAs($user)
            ->withSession(['korpa' => $korpa])
            ->post(route('narudzbine.potvrdi'));

        // narudzbina postoji u bazi
        $this->assertDatabaseHas('narudzbinas', [
            'user_id' => $user->id,
            'status' => 'kreirana',
        ]);

        // stavka postoji u bazi
        $this->assertDatabaseHas('narudzbina_stavkas', [
            'proizvod_id' => $proizvod->id,
            'kolicina' => 1,
        ]);

        // zaliha se uspesno izmenila
        $this->assertEquals(9, $proizvod->fresh()->kolicina);
    }
}
