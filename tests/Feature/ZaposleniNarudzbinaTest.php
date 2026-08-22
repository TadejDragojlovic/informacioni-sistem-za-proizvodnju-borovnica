<?php

namespace Tests\Feature;

use App\Enums\KlasaKvaliteta;
use App\Enums\LotDogadjajTip;
use App\Enums\LotRaspodelaStatus;
use App\Enums\LotStatus;
use App\Enums\NarudzbinaStatus;
use App\Enums\UserRole;
use App\Models\Lot;
use App\Models\Narudzbina;
use App\Models\NarudzbinaStavka;
use App\Models\Proizvod;
use App\Models\SkladisnaLokacija;
use App\Models\Sorta;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ZaposleniNarudzbinaTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function zaposleni_rezervise_i_otprema_narudzbinu_preko_kontrolera(): void
    {
        [$zaposleni, $narudzbina, $stavka] = $this->pripremiNarudzbinuZaRezervaciju();

        $this->actingAs($zaposleni)
            ->post(route('narudzbine.stavke.fifo-rezervacija', $stavka))
            ->assertSessionHas('success');

        $this->actingAs($zaposleni)
            ->patch(route('narudzbine.update', $narudzbina), [
                'status' => NarudzbinaStatus::OTPREMLJENA->value,
            ])
            ->assertRedirect(route('narudzbine.index'))
            ->assertSessionHas('success');

        $this->assertSame(NarudzbinaStatus::OTPREMLJENA, $narudzbina->fresh()->status);
        $this->assertSame(
            LotRaspodelaStatus::IZDATO,
            $stavka->raspodele()->sole()->status
        );
        $this->assertDatabaseHas('lot_dogadjajs', [
            'tip' => LotDogadjajTip::KOLICINA_IZDATA->value,
            'evidentirao_user_id' => $zaposleni->id,
        ]);
    }

    #[Test]
    public function zaposleni_otkazuje_narudzbinu_i_oslobadja_rezervaciju_preko_kontrolera(): void
    {
        [$zaposleni, $narudzbina, $stavka, $lot] = $this->pripremiNarudzbinuZaRezervaciju();

        $this->actingAs($zaposleni)
            ->post(route('narudzbine.stavke.fifo-rezervacija', $stavka));

        $this->actingAs($zaposleni)
            ->patch(route('narudzbine.otkazivanje', $narudzbina), [
                'razlog' => 'Kupac je odustao.',
            ])
            ->assertSessionHas('success');

        $this->assertSame(NarudzbinaStatus::OTKAZANA, $narudzbina->fresh()->status);
        $this->assertSame(2000, $lot->fresh()->raspoloziva_kolicina_g);
        $this->assertSame(LotRaspodelaStatus::OTKAZANO, $stavka->raspodele()->sole()->status);
    }

    /** @return array{User, Narudzbina, NarudzbinaStavka, Lot} */
    private function pripremiNarudzbinuZaRezervaciju(): array
    {
        $zaposleni = User::factory()->create(['role' => UserRole::ZAPOSLENI]);
        $kupac = User::factory()->create(['role' => UserRole::KUPAC]);
        $sorta = Sorta::factory()->create();
        $proizvod = Proizvod::factory()->create([
            'sorta_id' => $sorta->id,
            'neto_kolicina_g' => 500,
        ]);
        $lokacija = SkladisnaLokacija::factory()->create();
        $lot = Lot::factory()->create([
            'sorta_id' => $sorta->id,
            'trenutna_skladisna_lokacija_id' => $lokacija->id,
            'pocetna_kolicina_g' => 2000,
            'raspoloziva_kolicina_g' => 2000,
            'status' => LotStatus::RASPOLOZIV,
            'klasa_kvaliteta' => KlasaKvaliteta::KLASA_I,
            'broj_dokumenta_kvaliteta' => 'KVAL-HTTP-001',
        ]);
        $narudzbina = Narudzbina::factory()->create([
            'user_id' => $kupac->id,
            'status' => NarudzbinaStatus::POTVRDJENA,
        ]);
        $stavka = NarudzbinaStavka::factory()->create([
            'narudzbina_id' => $narudzbina->id,
            'proizvod_id' => $proizvod->id,
            'kolicina' => 2,
            'neto_kolicina_g' => 500,
        ]);

        return [$zaposleni, $narudzbina, $stavka, $lot];
    }
}
