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
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LotPremestanjeServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    #[Test]
    public function premesta_raspoloziv_lot_na_drugu_aktivnu_lokaciju(): void
    {
        Carbon::setTestNow('2026-07-02 09:15:00');
        $zaposleni = User::factory()->create(['role' => UserRole::ZAPOSLENI]);
        $lot = $this->raspolozivLot();
        $prethodnaLokacijaId = $lot->trenutna_skladisna_lokacija_id;
        $novaLokacija = SkladisnaLokacija::factory()->create();

        $premestenLot = app(LotService::class)->premesti(
            $lot,
            $novaLokacija,
            $zaposleni,
            'Premeštanje u zonu za otpremu.'
        );

        $this->assertSame(LotStatus::RASPOLOZIV, $premestenLot->status);
        $this->assertSame($novaLokacija->id, $premestenLot->trenutna_skladisna_lokacija_id);

        $dogadjaj = $premestenLot->dogadjaji
            ->firstWhere('tip', LotDogadjajTip::PREMESTANJE);

        $this->assertNotNull($dogadjaj);
        $this->assertSame($prethodnaLokacijaId, $dogadjaj->prethodna_skladisna_lokacija_id);
        $this->assertSame($novaLokacija->id, $dogadjaj->nova_skladisna_lokacija_id);
        $this->assertSame($zaposleni->id, $dogadjaj->evidentirao_user_id);
        $this->assertSame('Premeštanje u zonu za otpremu.', $dogadjaj->razlog);
        $this->assertTrue($dogadjaj->vreme_dogadjaja->equalTo(now()));
    }

    #[Test]
    public function dozvoljava_premestanje_blokiranog_lota(): void
    {
        $lot = $this->uskladistenLot();
        $lot->update(['status' => LotStatus::BLOKIRAN]);
        $novaLokacija = SkladisnaLokacija::factory()->create();

        $premestenLot = app(LotService::class)->premesti($lot, $novaLokacija);

        $this->assertSame(LotStatus::BLOKIRAN, $premestenLot->status);
        $this->assertSame($novaLokacija->id, $premestenLot->trenutna_skladisna_lokacija_id);
    }

    #[Test]
    public function odbija_premestanje_lota_u_nedozvoljenom_statusu(): void
    {
        foreach ([LotStatus::KREIRAN, LotStatus::ISCRPLJEN, LotStatus::POVUCEN] as $status) {
            $lot = Lot::factory()->create(['status' => $status]);
            $novaLokacija = SkladisnaLokacija::factory()->create();

            $this->ocekujDomainException(
                fn () => app(LotService::class)->premesti($lot, $novaLokacija),
                'Lot u trenutnom statusu nije moguće premestiti.'
            );

            $this->assertDatabaseMissing('lot_dogadjajs', [
                'lot_id' => $lot->id,
                'tip' => LotDogadjajTip::PREMESTANJE->value,
            ]);
        }
    }

    #[Test]
    public function odbija_premestanje_na_istu_lokaciju(): void
    {
        $lot = $this->uskladistenLot();
        $trenutnaLokacija = $lot->trenutnaSkladisnaLokacija;

        $this->ocekujDomainException(
            fn () => app(LotService::class)->premesti($lot, $trenutnaLokacija),
            'Nova lokacija mora biti različita od trenutne lokacije lota.'
        );
    }

    #[Test]
    public function odbija_premestanje_na_neaktivnu_lokaciju(): void
    {
        $lot = $this->uskladistenLot();
        $prethodnaLokacijaId = $lot->trenutna_skladisna_lokacija_id;
        $novaLokacija = SkladisnaLokacija::factory()->create(['aktivna' => false]);

        $this->ocekujDomainException(
            fn () => app(LotService::class)->premesti($lot, $novaLokacija),
            'Ciljna lokacija i njeno skladište moraju biti aktivni.'
        );

        $this->assertSame($prethodnaLokacijaId, $lot->fresh()->trenutna_skladisna_lokacija_id);
    }

    #[Test]
    public function odbija_premestanje_u_neaktivno_skladiste(): void
    {
        $lot = $this->uskladistenLot();
        $skladiste = Skladiste::factory()->create(['aktivan' => false]);
        $novaLokacija = SkladisnaLokacija::factory()->create([
            'skladiste_id' => $skladiste->id,
            'aktivna' => true,
        ]);

        $this->ocekujDomainException(
            fn () => app(LotService::class)->premesti($lot, $novaLokacija),
            'Ciljna lokacija i njeno skladište moraju biti aktivni.'
        );
    }

    private function uskladistenLot(): Lot
    {
        $servis = app(LotService::class);
        $lot = $servis->kreiraj([
            'sorta_id' => Sorta::factory()->create()->id,
            'parcela_id' => Parcela::factory()->create()->id,
            'datum_berbe' => '2026-07-01',
            'pocetna_kolicina_g' => 5000,
        ]);

        return $servis->primiUSkladiste($lot, SkladisnaLokacija::factory()->create());
    }

    private function raspolozivLot(): Lot
    {
        $servis = app(LotService::class);
        $lot = $this->uskladistenLot();
        $servis->dodeliKlasuKvaliteta($lot, KlasaKvaliteta::KLASA_I, 'KD-2026-201');

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
