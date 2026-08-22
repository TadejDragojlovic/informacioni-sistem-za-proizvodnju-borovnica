<?php

namespace Tests\Feature;

use App\Enums\KlasaKvaliteta;
use App\Enums\LotDogadjajTip;
use App\Enums\LotStatus;
use App\Enums\UserRole;
use App\Models\Lot;
use App\Models\Parcela;
use App\Models\SkladisnaLokacija;
use App\Models\Skladiste;
use App\Models\Sorta;
use App\Models\User;
use App\Services\LotService;
use Carbon\Carbon;
use DomainException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LotServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    #[Test]
    public function kreira_lot_sa_pocetnim_stanjem_i_dogadjajem(): void
    {
        Carbon::setTestNow('2026-07-01 08:30:00');
        $sorta = Sorta::factory()->create();
        $parcela = Parcela::factory()->create();
        $zaposleni = User::factory()->create(['role' => UserRole::ZAPOSLENI]);

        $lot = app(LotService::class)->kreiraj([
            'sorta_id' => $sorta->id,
            'parcela_id' => $parcela->id,
            'datum_berbe' => '2026-06-30',
            'pocetna_kolicina_g' => 12500,
            'napomena' => 'Jutarnja berba.',
        ], $zaposleni);

        $this->assertSame('BL-2026-001', $lot->oznaka);
        $this->assertSame(LotStatus::KREIRAN, $lot->status);
        $this->assertSame(12500, $lot->pocetna_kolicina_g);
        $this->assertSame(12500, $lot->raspoloziva_kolicina_g);
        $this->assertNull($lot->trenutna_skladisna_lokacija_id);
        $this->assertNull($lot->klasa_kvaliteta);

        $dogadjaj = $lot->dogadjaji->sole();
        $this->assertSame(LotDogadjajTip::LOT_KREIRAN, $dogadjaj->tip);
        $this->assertSame(12500, $dogadjaj->kolicina_g);
        $this->assertSame(LotStatus::KREIRAN, $dogadjaj->novi_status);
        $this->assertSame($zaposleni->id, $dogadjaj->evidentirao_user_id);
        $this->assertTrue($dogadjaj->vreme_dogadjaja->equalTo(now()));
    }

    #[Test]
    public function generise_sledeci_redni_broj_posebno_za_svaku_godinu_berbe(): void
    {
        $sorta = Sorta::factory()->create();
        $parcela = Parcela::factory()->create();
        $servis = app(LotService::class);

        Lot::factory()->create([
            'oznaka' => 'BL-2026-007',
            'sorta_id' => $sorta->id,
            'parcela_id' => $parcela->id,
        ]);

        $lot2026 = $servis->kreiraj($this->podaci($sorta, $parcela, '2026-07-01'));
        $lot2027 = $servis->kreiraj($this->podaci($sorta, $parcela, '2027-07-01'));

        $this->assertSame('BL-2026-008', $lot2026->oznaka);
        $this->assertSame('BL-2027-001', $lot2027->oznaka);
    }

    #[Test]
    public function odbija_nevalidnu_pocetnu_kolicinu_bez_upisa_u_bazu(): void
    {
        $sorta = Sorta::factory()->create();
        $parcela = Parcela::factory()->create();

        try {
            app(LotService::class)->kreiraj([
                'sorta_id' => $sorta->id,
                'parcela_id' => $parcela->id,
                'datum_berbe' => '2026-07-01',
                'pocetna_kolicina_g' => 0,
            ]);

            $this->fail('Očekivan je izuzetak za nevalidnu početnu količinu.');
        } catch (InvalidArgumentException $exception) {
            $this->assertSame('Početna količina lota mora biti veća od nule.', $exception->getMessage());
        }

        $this->assertDatabaseCount('lots', 0);
        $this->assertDatabaseCount('lot_dogadjajs', 0);
    }

    #[Test]
    public function prima_kreiran_lot_na_aktivnu_skladisnu_lokaciju(): void
    {
        Carbon::setTestNow('2026-07-01 10:00:00');
        $zaposleni = User::factory()->create(['role' => UserRole::ZAPOSLENI]);
        $lokacija = SkladisnaLokacija::factory()->create();
        $lot = $this->kreiranLot();

        $primljenLot = app(LotService::class)->primiUSkladiste($lot, $lokacija, $zaposleni);

        $this->assertSame(LotStatus::USKLADISTEN, $primljenLot->status);
        $this->assertSame($lokacija->id, $primljenLot->trenutna_skladisna_lokacija_id);

        $dogadjaj = $primljenLot->dogadjaji
            ->firstWhere('tip', LotDogadjajTip::PRIJEM_U_SKLADISTE);

        $this->assertNotNull($dogadjaj);
        $this->assertSame(LotStatus::KREIRAN, $dogadjaj->prethodni_status);
        $this->assertSame(LotStatus::USKLADISTEN, $dogadjaj->novi_status);
        $this->assertNull($dogadjaj->prethodna_skladisna_lokacija_id);
        $this->assertSame($lokacija->id, $dogadjaj->nova_skladisna_lokacija_id);
        $this->assertSame($zaposleni->id, $dogadjaj->evidentirao_user_id);
        $this->assertTrue($dogadjaj->vreme_dogadjaja->equalTo(now()));
    }

    #[Test]
    public function odbija_prijem_lota_koji_nije_u_statusu_kreiran(): void
    {
        $lokacija = SkladisnaLokacija::factory()->create();
        $lot = Lot::factory()->create(['status' => LotStatus::USKLADISTEN]);

        $this->ocekujOdbijenPrijem(
            $lot,
            $lokacija,
            'Samo lot u statusu KREIRAN može biti primljen u skladište.'
        );

        $this->assertNull($lot->fresh()->trenutna_skladisna_lokacija_id);
    }

    #[Test]
    public function odbija_prijem_na_neaktivnu_skladisnu_lokaciju(): void
    {
        $lokacija = SkladisnaLokacija::factory()->create(['aktivna' => false]);
        $lot = $this->kreiranLot();

        $this->ocekujOdbijenPrijem(
            $lot,
            $lokacija,
            'Lot nije moguće primiti na neaktivnu skladišnu lokaciju.'
        );
    }

    #[Test]
    public function odbija_prijem_u_neaktivno_skladiste(): void
    {
        $skladiste = Skladiste::factory()->create(['aktivan' => false]);
        $lokacija = SkladisnaLokacija::factory()->create([
            'skladiste_id' => $skladiste->id,
            'aktivna' => true,
        ]);
        $lot = $this->kreiranLot();

        $this->ocekujOdbijenPrijem(
            $lot,
            $lokacija,
            'Lot nije moguće primiti u neaktivno skladište.'
        );
    }

    #[Test]
    public function dodeljuje_klasu_i_dokument_kvaliteta_uskladistenom_lotu(): void
    {
        Carbon::setTestNow('2026-07-01 12:00:00');
        $zaposleni = User::factory()->create(['role' => UserRole::ZAPOSLENI]);
        $lot = $this->uskladistenLot();

        $ocenjenLot = app(LotService::class)->dodeliKlasuKvaliteta(
            $lot,
            KlasaKvaliteta::KLASA_I,
            'KD-2026-101',
            $zaposleni
        );

        $this->assertSame(KlasaKvaliteta::KLASA_I, $ocenjenLot->klasa_kvaliteta);
        $this->assertSame('KD-2026-101', $ocenjenLot->broj_dokumenta_kvaliteta);
        $this->assertSame(LotStatus::USKLADISTEN, $ocenjenLot->status);

        $dogadjaj = $ocenjenLot->dogadjaji
            ->firstWhere('tip', LotDogadjajTip::KLASA_KVALITETA_DODELJENA);

        $this->assertNotNull($dogadjaj);
        $this->assertSame($zaposleni->id, $dogadjaj->evidentirao_user_id);
        $this->assertSame('Klasa: klasa_i; dokument: KD-2026-101', $dogadjaj->razlog);
        $this->assertTrue($dogadjaj->vreme_dogadjaja->equalTo(now()));
    }

    #[Test]
    public function odbija_dodelu_kvaliteta_lotu_koji_nije_uskladisten(): void
    {
        $lot = $this->kreiranLot();

        $this->ocekujDomainException(
            fn () => app(LotService::class)->dodeliKlasuKvaliteta(
                $lot,
                KlasaKvaliteta::KLASA_I,
                'KD-2026-102'
            ),
            'Klasa kvaliteta može biti dodeljena samo uskladištenom lotu.'
        );

        $this->assertNull($lot->fresh()->klasa_kvaliteta);
    }

    #[Test]
    public function ne_dozvoljava_ponovnu_dodelu_kvaliteta(): void
    {
        $lot = $this->uskladistenLot();
        $servis = app(LotService::class);
        $servis->dodeliKlasuKvaliteta($lot, KlasaKvaliteta::KLASA_I, 'KD-2026-103');

        $this->ocekujDomainException(
            fn () => $servis->dodeliKlasuKvaliteta(
                $lot,
                KlasaKvaliteta::KLASA_II,
                'KD-2026-104'
            ),
            'Lot već ima dodeljenu klasu kvaliteta.'
        );

        $this->assertDatabaseCount('lot_dogadjajs', 3);
        $this->assertSame(KlasaKvaliteta::KLASA_I, $lot->fresh()->klasa_kvaliteta);
    }

    #[Test]
    public function zahteva_broj_dokumenta_prilikom_dodele_kvaliteta(): void
    {
        $lot = $this->uskladistenLot();

        try {
            app(LotService::class)->dodeliKlasuKvaliteta(
                $lot,
                KlasaKvaliteta::KLASA_I,
                '   '
            );
            $this->fail('Očekivan je izuzetak za prazan broj dokumenta.');
        } catch (InvalidArgumentException $exception) {
            $this->assertSame('Broj dokumenta kvaliteta je obavezan.', $exception->getMessage());
        }

        $this->assertNull($lot->fresh()->klasa_kvaliteta);
    }

    #[Test]
    public function odobrava_za_prodaju_uskladisten_lot_sa_dodeljenim_kvalitetom(): void
    {
        Carbon::setTestNow('2026-07-01 14:00:00');
        $zaposleni = User::factory()->create(['role' => UserRole::ZAPOSLENI]);
        $servis = app(LotService::class);
        $lot = $this->uskladistenLot();
        $servis->dodeliKlasuKvaliteta($lot, KlasaKvaliteta::KLASA_I, 'KD-2026-105');

        $odobrenLot = $servis->odobriZaProdaju($lot, $zaposleni);

        $this->assertSame(LotStatus::RASPOLOZIV, $odobrenLot->status);
        $dogadjaj = $odobrenLot->dogadjaji
            ->firstWhere('tip', LotDogadjajTip::ODOBREN_ZA_PRODAJU);

        $this->assertNotNull($dogadjaj);
        $this->assertSame(LotStatus::USKLADISTEN, $dogadjaj->prethodni_status);
        $this->assertSame(LotStatus::RASPOLOZIV, $dogadjaj->novi_status);
        $this->assertSame($zaposleni->id, $dogadjaj->evidentirao_user_id);
        $this->assertTrue($dogadjaj->vreme_dogadjaja->equalTo(now()));
    }

    #[Test]
    public function odbija_odobrenje_za_prodaju_bez_dodeljenog_kvaliteta(): void
    {
        $lot = $this->uskladistenLot();

        $this->ocekujDomainException(
            fn () => app(LotService::class)->odobriZaProdaju($lot),
            'Lot mora imati dodeljenu klasu i dokument kvaliteta.'
        );

        $this->assertSame(LotStatus::USKLADISTEN, $lot->fresh()->status);
    }

    #[Test]
    public function odbija_odobrenje_za_prodaju_na_neaktivnoj_lokaciji(): void
    {
        $servis = app(LotService::class);
        $lot = $this->uskladistenLot();
        $servis->dodeliKlasuKvaliteta($lot, KlasaKvaliteta::KLASA_I, 'KD-2026-106');
        $lot->trenutnaSkladisnaLokacija()->update(['aktivna' => false]);

        $this->ocekujDomainException(
            fn () => $servis->odobriZaProdaju($lot),
            'Lot mora biti na aktivnoj lokaciji u aktivnom skladištu.'
        );
    }

    #[Test]
    public function odbija_odobrenje_lota_bez_raspolozive_kolicine(): void
    {
        $servis = app(LotService::class);
        $lot = $this->uskladistenLot();
        $servis->dodeliKlasuKvaliteta($lot, KlasaKvaliteta::KLASA_I, 'KD-2026-107');
        $lot->update(['raspoloziva_kolicina_g' => 0]);

        $this->ocekujDomainException(
            fn () => $servis->odobriZaProdaju($lot),
            'Lot bez raspoložive količine ne može biti odobren za prodaju.'
        );
    }

    private function podaci(Sorta $sorta, Parcela $parcela, string $datumBerbe): array
    {
        return [
            'sorta_id' => $sorta->id,
            'parcela_id' => $parcela->id,
            'datum_berbe' => $datumBerbe,
            'pocetna_kolicina_g' => 5000,
        ];
    }

    private function kreiranLot(): Lot
    {
        $sorta = Sorta::factory()->create();
        $parcela = Parcela::factory()->create();

        return app(LotService::class)->kreiraj(
            $this->podaci($sorta, $parcela, '2026-07-01')
        );
    }

    private function uskladistenLot(): Lot
    {
        $lot = $this->kreiranLot();
        $lokacija = SkladisnaLokacija::factory()->create();

        return app(LotService::class)->primiUSkladiste($lot, $lokacija);
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

    private function ocekujOdbijenPrijem(
        Lot $lot,
        SkladisnaLokacija $lokacija,
        string $poruka
    ): void {
        try {
            app(LotService::class)->primiUSkladiste($lot, $lokacija);
            $this->fail('Očekivan je izuzetak za nedozvoljen prijem lota.');
        } catch (DomainException $exception) {
            $this->assertSame($poruka, $exception->getMessage());
        }

        $this->assertDatabaseMissing('lot_dogadjajs', [
            'lot_id' => $lot->id,
            'tip' => LotDogadjajTip::PRIJEM_U_SKLADISTE->value,
        ]);
    }
}
