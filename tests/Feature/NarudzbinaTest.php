<?php

namespace Tests\Feature;

use App\Enums\NarudzbinaStatus;
use App\Enums\UserRole;
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
}
