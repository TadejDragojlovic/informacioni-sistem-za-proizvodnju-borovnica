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
use App\Services\LotService;
use Carbon\Carbon;
use DomainException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LotBlokiranjeServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    #[Test]
    public function blokira_raspoloziv_lot_i_belezi_razlog(): void
    {
        Carbon::setTestNow('2026-07-03 09:00:00');
        $zaposleni = User::factory()->create(['role' => UserRole::ZAPOSLENI]);
        $lot = $this->raspolozivLot();

        $blokiranLot = app(LotService::class)->blokiraj(
            $lot,
            'Sumnja na odstupanje temperature.',
            $zaposleni
        );

        $this->assertSame(LotStatus::BLOKIRAN, $blokiranLot->status);
        $dogadjaj = $blokiranLot->dogadjaji
            ->firstWhere('tip', LotDogadjajTip::LOT_BLOKIRAN);

        $this->assertNotNull($dogadjaj);
        $this->assertSame(LotStatus::RASPOLOZIV, $dogadjaj->prethodni_status);
        $this->assertSame(LotStatus::BLOKIRAN, $dogadjaj->novi_status);
        $this->assertSame('Sumnja na odstupanje temperature.', $dogadjaj->razlog);
        $this->assertSame($zaposleni->id, $dogadjaj->evidentirao_user_id);
        $this->assertTrue($dogadjaj->vreme_dogadjaja->equalTo(now()));
    }

    #[Test]
    public function blokira_uskladisten_lot_pre_odobrenja_za_prodaju(): void
    {
        $lot = $this->uskladistenLot();

        $blokiranLot = app(LotService::class)->blokiraj($lot, 'Čeka se dodatna analiza.');

        $this->assertSame(LotStatus::BLOKIRAN, $blokiranLot->status);
        $this->assertSame(
            LotStatus::USKLADISTEN,
            $blokiranLot->dogadjaji->firstWhere('tip', LotDogadjajTip::LOT_BLOKIRAN)->prethodni_status
        );
    }

    #[Test]
    public function odbija_blokiranje_nedozvoljenog_statusa_i_prazan_razlog(): void
    {
        $lot = $this->kreiranLot();

        $this->ocekujDomainException(
            fn () => app(LotService::class)->blokiraj($lot, 'Razlog postoji.'),
            'Samo uskladišten ili raspoloživ lot može biti blokiran.'
        );

        try {
            app(LotService::class)->blokiraj($this->uskladistenLot(), '   ');
            $this->fail('Očekivan je izuzetak za prazan razlog.');
        } catch (InvalidArgumentException $exception) {
            $this->assertSame('Razlog je obavezan.', $exception->getMessage());
        }
    }

    #[Test]
    public function odblokiranjem_vraca_prethodni_status_raspoloziv(): void
    {
        Carbon::setTestNow('2026-07-03 13:00:00');
        $zaposleni = User::factory()->create(['role' => UserRole::ZAPOSLENI]);
        $servis = app(LotService::class);
        $lot = $servis->blokiraj($this->raspolozivLot(), 'Kontrolno blokiranje.');

        $odblokiranLot = $servis->odblokiraj($lot, 'Kontrola je uspešno završena.', $zaposleni);

        $this->assertSame(LotStatus::RASPOLOZIV, $odblokiranLot->status);
        $dogadjaj = $odblokiranLot->dogadjaji
            ->firstWhere('tip', LotDogadjajTip::LOT_ODBLOKIRAN);

        $this->assertNotNull($dogadjaj);
        $this->assertSame(LotStatus::BLOKIRAN, $dogadjaj->prethodni_status);
        $this->assertSame(LotStatus::RASPOLOZIV, $dogadjaj->novi_status);
        $this->assertSame('Kontrola je uspešno završena.', $dogadjaj->razlog);
        $this->assertSame($zaposleni->id, $dogadjaj->evidentirao_user_id);
    }

    #[Test]
    public function odblokiranjem_vraca_prethodni_status_uskladisten(): void
    {
        $servis = app(LotService::class);
        $lot = $servis->blokiraj($this->uskladistenLot(), 'Privremena provera.');

        $odblokiranLot = $servis->odblokiraj($lot, 'Provera završena.');

        $this->assertSame(LotStatus::USKLADISTEN, $odblokiranLot->status);
    }

    #[Test]
    public function odbija_odblokiranje_lota_koji_nije_blokiran(): void
    {
        $lot = $this->uskladistenLot();

        $this->ocekujDomainException(
            fn () => app(LotService::class)->odblokiraj($lot, 'Pokušaj odblokiranja.'),
            'Samo blokiran lot može biti odblokiran.'
        );
    }

    #[Test]
    public function odbija_odblokiranje_bez_pouzdanog_prethodnog_statusa(): void
    {
        $lot = $this->uskladistenLot();
        $lot->update(['status' => LotStatus::BLOKIRAN]);

        $this->ocekujDomainException(
            fn () => app(LotService::class)->odblokiraj($lot, 'Nedostaje istorija.'),
            'Prethodni status lota nije moguće pouzdano utvrditi.'
        );
    }

    #[Test]
    public function odbija_odblokiranje_na_neaktivnoj_lokaciji(): void
    {
        $servis = app(LotService::class);
        $lot = $servis->blokiraj($this->raspolozivLot(), 'Kontrolno blokiranje.');
        $lot->trenutnaSkladisnaLokacija()->update(['aktivna' => false]);

        $this->ocekujDomainException(
            fn () => $servis->odblokiraj($lot, 'Kontrola završena.'),
            'Lot mora biti na aktivnoj lokaciji u aktivnom skladištu.'
        );

        $this->assertSame(LotStatus::BLOKIRAN, $lot->fresh()->status);
        $this->assertDatabaseMissing('lot_dogadjajs', [
            'lot_id' => $lot->id,
            'tip' => LotDogadjajTip::LOT_ODBLOKIRAN->value,
        ]);
    }

    private function kreiranLot(): Lot
    {
        return app(LotService::class)->kreiraj([
            'sorta_id' => Sorta::factory()->create()->id,
            'parcela_id' => Parcela::factory()->create()->id,
            'datum_berbe' => '2026-07-01',
            'pocetna_kolicina_g' => 5000,
        ]);
    }

    private function uskladistenLot(): Lot
    {
        return app(LotService::class)->primiUSkladiste(
            $this->kreiranLot(),
            SkladisnaLokacija::factory()->create()
        );
    }

    private function raspolozivLot(): Lot
    {
        $servis = app(LotService::class);
        $lot = $this->uskladistenLot();
        $servis->dodeliKlasuKvaliteta($lot, KlasaKvaliteta::KLASA_I, 'KD-2026-301');

        return $servis->odobriZaProdaju($lot);
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
