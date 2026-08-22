<?php

namespace Tests\Feature;

use App\Enums\KlasaKvaliteta;
use App\Enums\LotDogadjajTip;
use App\Enums\LotStatus;
use App\Enums\UserRole;
use App\Models\Lot;
use App\Models\Parcela;
use App\Models\SkladisnaLokacija;
use App\Models\Sorta;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LotControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function zaposleni_vodi_lot_od_kreiranja_do_odobrenja_za_prodaju(): void
    {
        $zaposleni = User::factory()->create(['role' => UserRole::ZAPOSLENI]);
        $sorta = Sorta::factory()->create();
        $parcela = Parcela::factory()->create();
        $lokacija = SkladisnaLokacija::factory()->create();

        $this->actingAs($zaposleni)
            ->post(route('lotovi.store'), [
                'sorta_id' => $sorta->id,
                'parcela_id' => $parcela->id,
                'datum_berbe' => '2026-07-15',
                'pocetna_kolicina_g' => 12000,
                'napomena' => 'Kontrolni lot.',
            ])
            ->assertSessionHas('success');

        $lot = Lot::query()->sole();

        $this->actingAs($zaposleni)
            ->patch(route('lotovi.prijem', $lot), [
                'skladisna_lokacija_id' => $lokacija->id,
            ])
            ->assertSessionHas('success');

        $this->actingAs($zaposleni)
            ->patch(route('lotovi.kvalitet', $lot), [
                'klasa_kvaliteta' => KlasaKvaliteta::KLASA_I->value,
                'broj_dokumenta_kvaliteta' => 'KVAL-HTTP-002',
            ])
            ->assertSessionHas('success');

        $this->actingAs($zaposleni)
            ->patch(route('lotovi.odobrenje-prodaje', $lot))
            ->assertSessionHas('success');

        $lot->refresh();

        $this->assertSame(LotStatus::RASPOLOZIV, $lot->status);
        $this->assertSame($lokacija->id, $lot->trenutna_skladisna_lokacija_id);
        $this->assertSame(KlasaKvaliteta::KLASA_I, $lot->klasa_kvaliteta);
        $this->assertEqualsCanonicalizing(
            [
                LotDogadjajTip::LOT_KREIRAN,
                LotDogadjajTip::PRIJEM_U_SKLADISTE,
                LotDogadjajTip::KLASA_KVALITETA_DODELJENA,
                LotDogadjajTip::ODOBREN_ZA_PRODAJU,
            ],
            $lot->dogadjaji()->pluck('tip')->all()
        );
    }
}
