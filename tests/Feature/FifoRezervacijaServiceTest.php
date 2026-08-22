<?php

namespace Tests\Feature;

use App\Enums\KlasaKvaliteta;
use App\Enums\LotDogadjajTip;
use App\Enums\LotRaspodelaStatus;
use App\Enums\LotStatus;
use App\Enums\NarudzbinaStatus;
use App\Enums\UserRole;
use App\Models\Lot;
use App\Models\LotRaspodela;
use App\Models\Narudzbina;
use App\Models\NarudzbinaStavka;
use App\Models\Proizvod;
use App\Models\SkladisnaLokacija;
use App\Models\Skladiste;
use App\Models\Sorta;
use App\Models\User;
use App\Services\NarudzbinaService;
use Carbon\Carbon;
use DomainException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class FifoRezervacijaServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    #[Test]
    public function fifo_deli_stavku_na_najstarije_raspolozive_lotove(): void
    {
        Carbon::setTestNow('2026-07-06 10:00:00');
        $sorta = Sorta::factory()->create();
        $stavka = $this->stavka($sorta, 6, 500);
        $zaposleni = User::factory()->create(['role' => UserRole::ZAPOSLENI]);
        $najstarijiLot = $this->raspolozivLot($sorta, '2026-06-01', 2000);
        $sledeciLot = $this->raspolozivLot($sorta, '2026-06-05', 5000);
        $najnovijiLot = $this->raspolozivLot($sorta, '2026-06-10', 5000);

        $raspodele = app(NarudzbinaService::class)->rezervisiFifo($stavka, $zaposleni);

        $this->assertCount(2, $raspodele);
        $this->assertSame($najstarijiLot->id, $raspodele[0]->lot_id);
        $this->assertSame(4, $raspodele[0]->broj_pakovanja);
        $this->assertSame($sledeciLot->id, $raspodele[1]->lot_id);
        $this->assertSame(2, $raspodele[1]->broj_pakovanja);
        $this->assertSame(LotStatus::ISCRPLJEN, $najstarijiLot->fresh()->status);
        $this->assertSame(0, $najstarijiLot->fresh()->raspoloziva_kolicina_g);
        $this->assertSame(4000, $sledeciLot->fresh()->raspoloziva_kolicina_g);
        $this->assertSame(5000, $najnovijiLot->fresh()->raspoloziva_kolicina_g);

        foreach ($raspodele as $raspodela) {
            $this->assertSame(LotRaspodelaStatus::REZERVISANO, $raspodela->status);
            $this->assertDatabaseHas('lot_dogadjajs', [
                'lot_id' => $raspodela->lot_id,
                'lot_raspodela_id' => $raspodela->id,
                'tip' => LotDogadjajTip::KOLICINA_REZERVISANA->value,
                'evidentirao_user_id' => $zaposleni->id,
            ]);
        }
    }

    #[Test]
    public function rezervise_samo_cela_pakovanja_i_ostavlja_neiskoristiv_ostatak(): void
    {
        $sorta = Sorta::factory()->create();
        $stavka = $this->stavka($sorta, 2, 500);
        $lot = $this->raspolozivLot($sorta, '2026-06-01', 1250);

        $raspodela = app(NarudzbinaService::class)->rezervisiFifo($stavka)->sole();

        $this->assertSame(2, $raspodela->broj_pakovanja);
        $this->assertSame(250, $lot->fresh()->raspoloziva_kolicina_g);
        $this->assertSame(LotStatus::RASPOLOZIV, $lot->fresh()->status);
    }

    #[Test]
    public function nedovoljna_kolicina_vraca_sve_delimicne_promene(): void
    {
        $sorta = Sorta::factory()->create();
        $stavka = $this->stavka($sorta, 5, 500);
        $prviLot = $this->raspolozivLot($sorta, '2026-06-01', 1000);
        $drugiLot = $this->raspolozivLot($sorta, '2026-06-02', 1000);

        $this->ocekujDomainException(
            fn () => app(NarudzbinaService::class)->rezervisiFifo($stavka),
            'Nema dovoljno raspoložive količine za celu stavku narudžbine.'
        );

        $this->assertDatabaseCount('lot_raspodela', 0);
        $this->assertSame(1000, $prviLot->fresh()->raspoloziva_kolicina_g);
        $this->assertSame(1000, $drugiLot->fresh()->raspoloziva_kolicina_g);
        $this->assertDatabaseMissing('lot_dogadjajs', [
            'tip' => LotDogadjajTip::KOLICINA_REZERVISANA->value,
        ]);
    }

    #[Test]
    public function fifo_iskljucuje_pogresnu_sortu_status_i_neaktivno_skladiste(): void
    {
        $sorta = Sorta::factory()->create();
        $drugaSorta = Sorta::factory()->create();
        $stavka = $this->stavka($sorta, 1, 500);
        $this->raspolozivLot($drugaSorta, '2026-05-01', 5000);
        $blokiranLot = $this->raspolozivLot($sorta, '2026-05-02', 5000);
        $blokiranLot->update(['status' => LotStatus::BLOKIRAN]);
        $neaktivnoSkladiste = Skladiste::factory()->create(['aktivan' => false]);
        $neaktivnaLokacija = SkladisnaLokacija::factory()->create([
            'skladiste_id' => $neaktivnoSkladiste->id,
        ]);
        $this->raspolozivLot($sorta, '2026-05-03', 5000, $neaktivnaLokacija);
        $ispravanLot = $this->raspolozivLot($sorta, '2026-06-01', 5000);

        $raspodela = app(NarudzbinaService::class)->rezervisiFifo($stavka)->sole();

        $this->assertSame($ispravanLot->id, $raspodela->lot_id);
    }

    #[Test]
    public function odbija_rezervaciju_za_narudzbinu_koja_nije_potvrdjena(): void
    {
        $sorta = Sorta::factory()->create();
        $stavka = $this->stavka($sorta, 1, 500, NarudzbinaStatus::OTKAZANA);
        $this->raspolozivLot($sorta, '2026-06-01', 5000);

        $this->ocekujDomainException(
            fn () => app(NarudzbinaService::class)->rezervisiFifo($stavka),
            'Lotovi se mogu rezervisati samo za potvrđenu narudžbinu.'
        );
    }

    #[Test]
    public function odbija_ponovnu_aktivnu_rezervaciju_iste_stavke(): void
    {
        $sorta = Sorta::factory()->create();
        $stavka = $this->stavka($sorta, 1, 500);
        $lot = $this->raspolozivLot($sorta, '2026-06-01', 5000);
        LotRaspodela::factory()->create([
            'lot_id' => $lot->id,
            'narudzbina_stavka_id' => $stavka->id,
            'status' => LotRaspodelaStatus::REZERVISANO,
        ]);

        $this->ocekujDomainException(
            fn () => app(NarudzbinaService::class)->rezervisiFifo($stavka),
            'Stavka narudžbine već ima aktivnu raspodelu lotova.'
        );
    }

    private function stavka(
        Sorta $sorta,
        int $kolicina,
        int $netoKolicinaG,
        NarudzbinaStatus $status = NarudzbinaStatus::POTVRDJENA
    ): NarudzbinaStavka {
        $proizvod = Proizvod::factory()->create([
            'sorta_id' => $sorta->id,
            'neto_kolicina_g' => $netoKolicinaG,
        ]);
        $narudzbina = Narudzbina::factory()->create(['status' => $status]);

        return NarudzbinaStavka::factory()->create([
            'narudzbina_id' => $narudzbina->id,
            'proizvod_id' => $proizvod->id,
            'kolicina' => $kolicina,
            'neto_kolicina_g' => $netoKolicinaG,
        ]);
    }

    private function raspolozivLot(
        Sorta $sorta,
        string $datumBerbe,
        int $raspolozivaKolicinaG,
        ?SkladisnaLokacija $lokacija = null
    ): Lot {
        return Lot::factory()->create([
            'sorta_id' => $sorta->id,
            'trenutna_skladisna_lokacija_id' => ($lokacija ?? SkladisnaLokacija::factory()->create())->id,
            'datum_berbe' => $datumBerbe,
            'pocetna_kolicina_g' => $raspolozivaKolicinaG,
            'raspoloziva_kolicina_g' => $raspolozivaKolicinaG,
            'status' => LotStatus::RASPOLOZIV,
            'klasa_kvaliteta' => KlasaKvaliteta::KLASA_I,
            'broj_dokumenta_kvaliteta' => 'KD-'.$datumBerbe,
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
