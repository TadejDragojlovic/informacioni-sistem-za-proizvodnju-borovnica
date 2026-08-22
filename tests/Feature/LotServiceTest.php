<?php

namespace Tests\Feature;

use App\Enums\LotDogadjajTip;
use App\Enums\LotStatus;
use App\Enums\UserRole;
use App\Models\Lot;
use App\Models\Parcela;
use App\Models\Sorta;
use App\Models\User;
use App\Services\LotService;
use Carbon\Carbon;
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

    private function podaci(Sorta $sorta, Parcela $parcela, string $datumBerbe): array
    {
        return [
            'sorta_id' => $sorta->id,
            'parcela_id' => $parcela->id,
            'datum_berbe' => $datumBerbe,
            'pocetna_kolicina_g' => 5000,
        ];
    }
}
