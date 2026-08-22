<?php

namespace Tests\Feature;

use App\Enums\KlasaKvaliteta;
use App\Enums\LotDogadjajTip;
use App\Enums\LotRaspodelaStatus;
use App\Enums\LotStatus;
use App\Enums\UserRole;
use App\Models\Lot;
use App\Models\LotRaspodela;
use App\Models\NarudzbinaStavka;
use App\Models\Parcela;
use App\Models\Proizvod;
use App\Models\SkladisnaLokacija;
use App\Models\Sorta;
use App\Models\User;
use App\Services\LotService;
use Carbon\Carbon;
use DomainException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LotPovlacenjeServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    #[Test]
    public function povlaci_lot_iz_prodaje_i_cuva_prethodno_stanje_u_dogadjaju(): void
    {
        Carbon::setTestNow('2026-07-04 08:00:00');
        $zaposleni = User::factory()->create(['role' => UserRole::ZAPOSLENI]);
        $lot = $this->raspolozivLot();
        $prethodnaLokacijaId = $lot->trenutna_skladisna_lokacija_id;

        $povucenLot = app(LotService::class)->povuci(
            $lot,
            'Neusaglašen rezultat naknadne kontrole.',
            $zaposleni
        );

        $this->assertSame(LotStatus::POVUCEN, $povucenLot->status);
        $this->assertSame(0, $povucenLot->raspoloziva_kolicina_g);
        $this->assertNull($povucenLot->trenutna_skladisna_lokacija_id);

        $dogadjaj = $povucenLot->dogadjaji
            ->firstWhere('tip', LotDogadjajTip::LOT_POVUCEN);

        $this->assertNotNull($dogadjaj);
        $this->assertSame(LotStatus::RASPOLOZIV, $dogadjaj->prethodni_status);
        $this->assertSame(LotStatus::POVUCEN, $dogadjaj->novi_status);
        $this->assertSame($prethodnaLokacijaId, $dogadjaj->prethodna_skladisna_lokacija_id);
        $this->assertSame('Neusaglašen rezultat naknadne kontrole.', $dogadjaj->razlog);
        $this->assertSame($zaposleni->id, $dogadjaj->evidentirao_user_id);
        $this->assertTrue($dogadjaj->vreme_dogadjaja->equalTo(now()));
    }

    #[Test]
    public function otkazuje_aktivne_rezervacije_povucenog_lota(): void
    {
        $lot = $this->raspolozivLot();
        $raspodela = $this->raspodela($lot, 4, 500, LotRaspodelaStatus::REZERVISANO);
        $lot->update(['raspoloziva_kolicina_g' => 3000]);

        $povucenLot = app(LotService::class)->povuci($lot, 'Lot se preventivno povlači.');

        $this->assertSame(LotRaspodelaStatus::OTKAZANO, $raspodela->fresh()->status);
        $this->assertSame(LotStatus::POVUCEN, $povucenLot->status);

        $dogadjaj = $povucenLot->dogadjaji
            ->firstWhere('tip', LotDogadjajTip::REZERVACIJA_OSLOBODJENA);

        $this->assertNotNull($dogadjaj);
        $this->assertSame($raspodela->id, $dogadjaj->lot_raspodela_id);
        $this->assertSame(-2000, $dogadjaj->kolicina_g);
        $this->assertSame('Rezervacija otkazana zbog povlačenja lota.', $dogadjaj->razlog);
    }

    #[Test]
    public function ne_menja_izdate_raspodele_prilikom_povlacenja(): void
    {
        $lot = $this->raspolozivLot();
        $izdataRaspodela = $this->raspodela($lot, 2, 500, LotRaspodelaStatus::IZDATO);

        app(LotService::class)->povuci($lot, 'Opoziv nakon otpreme.');

        $this->assertSame(LotRaspodelaStatus::IZDATO, $izdataRaspodela->fresh()->status);
        $this->assertDatabaseMissing('lot_dogadjajs', [
            'lot_raspodela_id' => $izdataRaspodela->id,
            'tip' => LotDogadjajTip::REZERVACIJA_OSLOBODJENA->value,
        ]);
    }

    #[Test]
    public function dozvoljava_povlacenje_blokiranog_i_iscrpljenog_lota(): void
    {
        $servis = app(LotService::class);
        $blokiranLot = $servis->blokiraj($this->raspolozivLot(), 'Sumnja u kvalitet.');
        $iscrpljenLot = Lot::factory()->create([
            'status' => LotStatus::ISCRPLJEN,
            'raspoloziva_kolicina_g' => 0,
        ]);

        $this->assertSame(
            LotStatus::POVUCEN,
            $servis->povuci($blokiranLot, 'Potvrđena neusaglašenost.')->status
        );
        $this->assertSame(
            LotStatus::POVUCEN,
            $servis->povuci($iscrpljenLot, 'Opoziv već otpremljenog lota.')->status
        );
    }

    #[Test]
    public function odbija_ponovno_povlacenje_i_prazan_razlog(): void
    {
        $servis = app(LotService::class);
        $lot = $servis->povuci($this->raspolozivLot(), 'Prvo povlačenje.');

        try {
            $servis->povuci($lot, 'Ponovljeno povlačenje.');
            $this->fail('Očekivan je izuzetak za već povučen lot.');
        } catch (DomainException $exception) {
            $this->assertSame('Lot je već povučen.', $exception->getMessage());
        }

        try {
            $servis->povuci($this->raspolozivLot(), '   ');
            $this->fail('Očekivan je izuzetak za prazan razlog.');
        } catch (InvalidArgumentException $exception) {
            $this->assertSame('Razlog je obavezan.', $exception->getMessage());
        }
    }

    private function raspolozivLot(): Lot
    {
        $servis = app(LotService::class);
        $lot = $servis->kreiraj([
            'sorta_id' => Sorta::factory()->create()->id,
            'parcela_id' => Parcela::factory()->create()->id,
            'datum_berbe' => '2026-07-01',
            'pocetna_kolicina_g' => 5000,
        ]);
        $lot = $servis->primiUSkladiste($lot, SkladisnaLokacija::factory()->create());
        $servis->dodeliKlasuKvaliteta($lot, KlasaKvaliteta::KLASA_I, 'KD-2026-401');

        return $servis->odobriZaProdaju($lot);
    }

    private function raspodela(
        Lot $lot,
        int $brojPakovanja,
        int $netoKolicina,
        LotRaspodelaStatus $status
    ): LotRaspodela {
        $proizvod = Proizvod::factory()->create([
            'sorta_id' => $lot->sorta_id,
            'neto_kolicina_g' => $netoKolicina,
        ]);
        $stavka = NarudzbinaStavka::factory()->create([
            'proizvod_id' => $proizvod->id,
            'neto_kolicina_g' => $netoKolicina,
            'kolicina' => $brojPakovanja,
        ]);

        return LotRaspodela::factory()->create([
            'lot_id' => $lot->id,
            'narudzbina_stavka_id' => $stavka->id,
            'broj_pakovanja' => $brojPakovanja,
            'status' => $status,
        ]);
    }
}
