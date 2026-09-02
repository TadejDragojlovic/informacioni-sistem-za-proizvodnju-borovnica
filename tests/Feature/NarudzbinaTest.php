<?php

namespace Tests\Feature;

use App\Enums\NarudzbinaStatus;
use App\Enums\UserRole;
use App\Models\Narudzbina;
use App\Models\NarudzbinaStavka;
use App\Models\Proizvod;
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
        $user = User::factory()->create(['role' => UserRole::KUPAC->value]);
        $proizvod = Proizvod::factory()->create([
            'naziv' => 'Borovnice 500 g',
            'neto_kolicina_g' => 500,
            'cena' => 800,
        ]);

        // simulacija korpe
        $korpa = [
            $proizvod->id => ['naziv' => $proizvod->naziv, 'kolicina' => 2, 'cena' => 800],
        ];

        // potvrdjivanje narudzbine
        $response = $this->actingAs($user)
            ->withSession(['korpa' => $korpa])
            ->post(route('narudzbine.potvrdi'), [
                'adresa_isporuke' => 'Kralja Petra 10, Valjevo',
            ]);

        // narudzbina postoji u bazi
        $this->assertDatabaseHas('narudzbinas', [
            'user_id' => $user->id,
            'status' => NarudzbinaStatus::POTVRDJENA->value,
            'adresa_isporuke' => 'Kralja Petra 10, Valjevo',
        ]);

        // stavka postoji u bazi
        $this->assertDatabaseHas('narudzbina_stavkas', [
            'proizvod_id' => $proizvod->id,
            'kolicina' => 2,
            'neto_kolicina_g' => 500,
            'cena_po_jedinici' => 800,
        ]);

        $response->assertRedirect(route('user.orders'));
        $this->assertFalse(session()->has('korpa'));
    }

    #[Test]
    public function kupac_vidi_samo_svoje_narudzbine_sa_cenom_sacuvanom_prilikom_kupovine(): void
    {
        $kupac = User::factory()->create(['role' => UserRole::KUPAC]);
        $drugiKupac = User::factory()->create(['role' => UserRole::KUPAC]);
        $proizvod = Proizvod::factory()->create(['naziv' => 'Borovnice 250 g', 'cena' => 500]);
        $narudzbina = Narudzbina::factory()->create(['user_id' => $kupac->id]);

        NarudzbinaStavka::factory()->create([
            'narudzbina_id' => $narudzbina->id,
            'proizvod_id' => $proizvod->id,
            'kolicina' => 2,
            'cena_po_jedinici' => 500,
        ]);

        $proizvod->update(['cena' => 900]);

        $this->actingAs($kupac)
            ->get(route('user.orders.show', $narudzbina))
            ->assertOk()
            ->assertSee('1.000,00 RSD');

        $this->actingAs($drugiKupac)
            ->get(route('user.orders.show', $narudzbina))
            ->assertNotFound();
    }
}
