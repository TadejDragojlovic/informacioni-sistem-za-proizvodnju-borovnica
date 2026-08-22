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
use App\Models\SkladisnaLokacija;
use App\Models\User;
use App\Services\NarudzbinaService;
use DomainException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class OtpremaNarudzbineServiceTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function otprema_potpuno_rezervisanu_narudzbinu(): void
    {
        $zaposleni = User::factory()->create(['role' => UserRole::ZAPOSLENI]);
        $narudzbina = Narudzbina::factory()->create(['status' => NarudzbinaStatus::POTVRDJENA]);
        $stavka = NarudzbinaStavka::factory()->create([
            'narudzbina_id' => $narudzbina->id,
            'kolicina' => 4,
            'neto_kolicina_g' => 500,
        ]);
        $prviLot = $this->lot(LotStatus::ISCRPLJEN, 0);
        $drugiLot = $this->lot(LotStatus::RASPOLOZIV, 3000);
        $prvaRaspodela = $this->raspodela($prviLot, $stavka, 2);
        $drugaRaspodela = $this->raspodela($drugiLot, $stavka, 2);

        $otpremljena = app(NarudzbinaService::class)->otpremi($narudzbina, $zaposleni);

        $this->assertSame(NarudzbinaStatus::OTPREMLJENA, $otpremljena->status);
        $this->assertSame(LotRaspodelaStatus::IZDATO, $prvaRaspodela->fresh()->status);
        $this->assertSame(LotRaspodelaStatus::IZDATO, $drugaRaspodela->fresh()->status);
        $this->assertSame(0, $prviLot->fresh()->raspoloziva_kolicina_g);
        $this->assertSame(3000, $drugiLot->fresh()->raspoloziva_kolicina_g);
        $this->assertDatabaseCount('lot_dogadjajs', 2);
        $this->assertDatabaseHas('lot_dogadjajs', [
            'lot_raspodela_id' => $prvaRaspodela->id,
            'tip' => LotDogadjajTip::KOLICINA_IZDATA->value,
            'kolicina_g' => 1000,
            'evidentirao_user_id' => $zaposleni->id,
        ]);
    }

    #[Test]
    public function odbija_otpremu_nepotpuno_rezervisane_narudzbine_bez_delimicnih_izmena(): void
    {
        $narudzbina = Narudzbina::factory()->create(['status' => NarudzbinaStatus::POTVRDJENA]);
        $stavka = NarudzbinaStavka::factory()->create([
            'narudzbina_id' => $narudzbina->id,
            'kolicina' => 3,
        ]);
        $raspodela = $this->raspodela($this->lot(), $stavka, 2);

        $this->ocekujDomainException(
            fn () => app(NarudzbinaService::class)->otpremi($narudzbina),
            'Sve stavke moraju biti potpuno rezervisane pre otpreme.'
        );

        $this->assertSame(NarudzbinaStatus::POTVRDJENA, $narudzbina->fresh()->status);
        $this->assertSame(LotRaspodelaStatus::REZERVISANO, $raspodela->fresh()->status);
        $this->assertDatabaseCount('lot_dogadjajs', 0);
    }

    #[Test]
    public function blokiran_lot_zaustavlja_celu_otpremu(): void
    {
        $narudzbina = Narudzbina::factory()->create(['status' => NarudzbinaStatus::POTVRDJENA]);
        $stavka = NarudzbinaStavka::factory()->create([
            'narudzbina_id' => $narudzbina->id,
            'kolicina' => 2,
        ]);
        $ispravnaRaspodela = $this->raspodela($this->lot(), $stavka, 1);
        $blokiranaRaspodela = $this->raspodela(
            $this->lot(LotStatus::BLOKIRAN),
            $stavka,
            1
        );

        $this->ocekujDomainException(
            fn () => app(NarudzbinaService::class)->otpremi($narudzbina),
            'Blokiran, povučen ili neuskladišten lot ne može biti izdat.'
        );

        $this->assertSame(LotRaspodelaStatus::REZERVISANO, $ispravnaRaspodela->fresh()->status);
        $this->assertSame(LotRaspodelaStatus::REZERVISANO, $blokiranaRaspodela->fresh()->status);
        $this->assertSame(NarudzbinaStatus::POTVRDJENA, $narudzbina->fresh()->status);
    }

    private function lot(
        LotStatus $status = LotStatus::RASPOLOZIV,
        int $raspolozivaKolicinaG = 5000
    ): Lot {
        return Lot::factory()->create([
            'trenutna_skladisna_lokacija_id' => SkladisnaLokacija::factory()->create()->id,
            'pocetna_kolicina_g' => 5000,
            'raspoloziva_kolicina_g' => $raspolozivaKolicinaG,
            'status' => $status,
        ]);
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
