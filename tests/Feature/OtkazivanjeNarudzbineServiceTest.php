<?php

namespace Tests\Feature;

use App\Enums\LotDogadjajTip;
use App\Enums\LotRaspodelaStatus;
use App\Enums\LotStatus;
use App\Enums\NarudzbinaStatus;
use App\Enums\UserRole;
use App\Models\Lot;
use App\Models\LotRaspodela;
use App\Models\Narudzbina;
use App\Models\NarudzbinaStavka;
use App\Models\User;
use App\Services\NarudzbinaService;
use DomainException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class OtkazivanjeNarudzbineServiceTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function otkazuje_narudzbinu_i_oslobadja_sve_rezervacije(): void
    {
        $zaposleni = User::factory()->create(['role' => UserRole::ZAPOSLENI]);
        $narudzbina = Narudzbina::factory()->create(['status' => NarudzbinaStatus::POTVRDJENA]);
        $stavka = NarudzbinaStavka::factory()->create([
            'narudzbina_id' => $narudzbina->id,
            'kolicina' => 3,
            'neto_kolicina_g' => 500,
        ]);
        $iscrpljenLot = Lot::factory()->create([
            'pocetna_kolicina_g' => 1000,
            'raspoloziva_kolicina_g' => 0,
            'status' => LotStatus::ISCRPLJEN,
        ]);
        $blokiranLot = Lot::factory()->create([
            'pocetna_kolicina_g' => 2000,
            'raspoloziva_kolicina_g' => 1500,
            'status' => LotStatus::BLOKIRAN,
        ]);
        $prvaRaspodela = $this->raspodela($iscrpljenLot, $stavka, 2);
        $drugaRaspodela = $this->raspodela($blokiranLot, $stavka, 1);

        $otkazana = app(NarudzbinaService::class)->otkazi(
            $narudzbina,
            'Kupac je odustao od narudžbine.',
            $zaposleni
        );

        $this->assertSame(NarudzbinaStatus::OTKAZANA, $otkazana->status);
        $this->assertSame(LotRaspodelaStatus::OTKAZANO, $prvaRaspodela->fresh()->status);
        $this->assertSame(LotRaspodelaStatus::OTKAZANO, $drugaRaspodela->fresh()->status);
        $this->assertSame(1000, $iscrpljenLot->fresh()->raspoloziva_kolicina_g);
        $this->assertSame(LotStatus::RASPOLOZIV, $iscrpljenLot->fresh()->status);
        $this->assertSame(2000, $blokiranLot->fresh()->raspoloziva_kolicina_g);
        $this->assertSame(LotStatus::BLOKIRAN, $blokiranLot->fresh()->status);
        $this->assertDatabaseCount('lot_dogadjajs', 2);
        $this->assertDatabaseHas('lot_dogadjajs', [
            'lot_raspodela_id' => $prvaRaspodela->id,
            'tip' => LotDogadjajTip::REZERVACIJA_OSLOBODJENA->value,
            'kolicina_g' => -1000,
            'evidentirao_user_id' => $zaposleni->id,
            'razlog' => 'Kupac je odustao od narudžbine.',
        ]);
    }

    #[Test]
    public function dozvoljava_otkazivanje_potvrdjene_narudzbine_bez_rezervacija(): void
    {
        $narudzbina = Narudzbina::factory()->create(['status' => NarudzbinaStatus::POTVRDJENA]);

        $otkazana = app(NarudzbinaService::class)->otkazi(
            $narudzbina,
            'Narudžbina otkazana pre rezervacije.'
        );

        $this->assertSame(NarudzbinaStatus::OTKAZANA, $otkazana->status);
    }

    #[Test]
    public function ne_dozvoljava_otkazivanje_nakon_izdavanja(): void
    {
        $narudzbina = Narudzbina::factory()->create(['status' => NarudzbinaStatus::POTVRDJENA]);
        $stavka = NarudzbinaStavka::factory()->create(['narudzbina_id' => $narudzbina->id]);
        $raspodela = LotRaspodela::factory()->create([
            'narudzbina_stavka_id' => $stavka->id,
            'status' => LotRaspodelaStatus::IZDATO,
        ]);

        $this->ocekujDomainException(
            fn () => app(NarudzbinaService::class)->otkazi(
                $narudzbina,
                'Nedozvoljeno kasno otkazivanje.'
            ),
            'Narudžbina sa izdatom količinom ne može biti otkazana.'
        );

        $this->assertSame(NarudzbinaStatus::POTVRDJENA, $narudzbina->fresh()->status);
        $this->assertSame(LotRaspodelaStatus::IZDATO, $raspodela->fresh()->status);
    }

    private function raspodela(Lot $lot, NarudzbinaStavka $stavka, int $brojPakovanja): LotRaspodela
    {
        return LotRaspodela::factory()->create([
            'lot_id' => $lot->id,
            'narudzbina_stavka_id' => $stavka->id,
            'broj_pakovanja' => $brojPakovanja,
            'status' => LotRaspodelaStatus::REZERVISANO,
        ]);
    }

    private function ocekujDomainException(callable $operacija, string $poruka): void
    {
        try {
            $operacija();
            $this->fail('Očekivan je izuzetak zbog kršenja poslovnog pravila.');
        } catch (DomainException $exception) {
            $this->assertSame($poruka, $exception->getMessage());
        }
    }
}
