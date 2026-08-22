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

class LotKorekcijaKolicineServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    #[Test]
    public function koriguje_kolicinu_i_belezi_potpisanu_razliku(): void
    {
        Carbon::setTestNow('2026-07-05 09:30:00');
        $zaposleni = User::factory()->create(['role' => UserRole::ZAPOSLENI]);
        $servis = app(LotService::class);
        $lot = $this->raspolozivLot();

        $korigovanLot = $servis->korigujKolicinu(
            $lot,
            4500,
            'Manjak utvrđen kontrolnim merenjem.',
            $zaposleni
        );

        $this->assertSame(4500, $korigovanLot->raspoloziva_kolicina_g);
        $this->assertSame(LotStatus::RASPOLOZIV, $korigovanLot->status);
        $dogadjaj = $korigovanLot->dogadjaji
            ->where('tip', LotDogadjajTip::KOREKCIJA_KOLICINE)
            ->last();

        $this->assertSame(-500, $dogadjaj->kolicina_g);
        $this->assertSame(LotStatus::RASPOLOZIV, $dogadjaj->prethodni_status);
        $this->assertSame(LotStatus::RASPOLOZIV, $dogadjaj->novi_status);
        $this->assertSame('Manjak utvrđen kontrolnim merenjem.', $dogadjaj->razlog);
        $this->assertSame($zaposleni->id, $dogadjaj->evidentirao_user_id);
        $this->assertTrue($dogadjaj->vreme_dogadjaja->equalTo(now()));

        $ponovoKorigovanLot = $servis->korigujKolicinu(
            $korigovanLot,
            4800,
            'Pronađeno dodatno pakovanje.'
        );

        $this->assertSame(300, $ponovoKorigovanLot->dogadjaji
            ->where('tip', LotDogadjajTip::KOREKCIJA_KOLICINE)
            ->last()
            ->kolicina_g);
    }

    #[Test]
    public function nulta_kolicina_automatski_postavlja_status_iscrpljen(): void
    {
        $lot = $this->raspolozivLot();

        $iscrpljenLot = app(LotService::class)->korigujKolicinu(
            $lot,
            0,
            'Potvrđeno da nema preostale količine.'
        );

        $this->assertSame(0, $iscrpljenLot->raspoloziva_kolicina_g);
        $this->assertSame(LotStatus::ISCRPLJEN, $iscrpljenLot->status);
        $dogadjaj = $iscrpljenLot->dogadjaji
            ->where('tip', LotDogadjajTip::KOREKCIJA_KOLICINE)
            ->last();
        $this->assertSame(LotStatus::RASPOLOZIV, $dogadjaj->prethodni_status);
        $this->assertSame(LotStatus::ISCRPLJEN, $dogadjaj->novi_status);
    }

    #[Test]
    public function pozitivna_korekcija_vraca_dokazivi_status_pre_iscrpljenja(): void
    {
        $servis = app(LotService::class);
        $lot = $servis->korigujKolicinu(
            $this->raspolozivLot(),
            0,
            'Privremeno utvrđena nulta količina.'
        );

        $obnovljenLot = $servis->korigujKolicinu(
            $lot,
            250,
            'Kontrolnim merenjem pronađena preostala količina.'
        );

        $this->assertSame(250, $obnovljenLot->raspoloziva_kolicina_g);
        $this->assertSame(LotStatus::RASPOLOZIV, $obnovljenLot->status);
    }

    #[Test]
    public function postuje_gornju_granicu_posle_rezervisanih_i_izdatih_pakovanja(): void
    {
        $lot = $this->raspolozivLot();
        $this->raspodela($lot, 2, 500, LotRaspodelaStatus::REZERVISANO);
        $this->raspodela($lot, 1, 500, LotRaspodelaStatus::IZDATO);
        $lot->update(['raspoloziva_kolicina_g' => 3500]);

        $this->ocekujDomainException(
            fn () => app(LotService::class)->korigujKolicinu(
                $lot,
                4000,
                'Nedozvoljeno vraćanje angažovane količine.'
            ),
            'Raspoloživa količina ne može biti veća od 3500 g.'
        );

        $this->assertSame(3500, $lot->fresh()->raspoloziva_kolicina_g);
    }

    #[Test]
    public function odbija_negativnu_nepromenjenu_i_kolicinu_vecu_od_pocetne(): void
    {
        $lot = $this->raspolozivLot();
        $servis = app(LotService::class);

        try {
            $servis->korigujKolicinu($lot, -1, 'Negativna količina.');
            $this->fail('Očekivan je izuzetak za negativnu količinu.');
        } catch (InvalidArgumentException $exception) {
            $this->assertSame('Raspoloživa količina ne može biti negativna.', $exception->getMessage());
        }

        $this->ocekujDomainException(
            fn () => $servis->korigujKolicinu($lot, 5000, 'Nema stvarne promene.'),
            'Nova raspoloživa količina mora se razlikovati od trenutne.'
        );
        $this->ocekujDomainException(
            fn () => $servis->korigujKolicinu($lot, 5001, 'Prekoračenje početne količine.'),
            'Raspoloživa količina ne može biti veća od 5000 g.'
        );
    }

    #[Test]
    public function odbija_korekciju_kreiranog_i_povucenog_lota(): void
    {
        $servis = app(LotService::class);
        $kreiranLot = $this->kreiranLot();
        $povucenLot = $servis->povuci($this->raspolozivLot(), 'Lot je povučen.');

        foreach ([$kreiranLot, $povucenLot] as $lot) {
            $this->ocekujDomainException(
                fn () => $servis->korigujKolicinu($lot, 100, 'Nedozvoljena korekcija.'),
                'Količinu lota u trenutnom statusu nije moguće korigovati.'
            );
        }
    }

    #[Test]
    public function zahteva_razlog_i_pouzdanu_istoriju_za_obnavljanje_iscrpljenog_lota(): void
    {
        $servis = app(LotService::class);
        $lot = $this->raspolozivLot();

        try {
            $servis->korigujKolicinu($lot, 4500, '   ');
            $this->fail('Očekivan je izuzetak za prazan razlog.');
        } catch (InvalidArgumentException $exception) {
            $this->assertSame('Razlog je obavezan.', $exception->getMessage());
        }

        $iscrpljenBezIstorije = Lot::factory()->create([
            'status' => LotStatus::ISCRPLJEN,
            'raspoloziva_kolicina_g' => 0,
        ]);
        $this->ocekujDomainException(
            fn () => $servis->korigujKolicinu(
                $iscrpljenBezIstorije,
                100,
                'Pokušaj obnove bez istorije.'
            ),
            'Status lota pre iscrpljenja nije moguće pouzdano utvrditi.'
        );
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

    private function raspolozivLot(): Lot
    {
        $servis = app(LotService::class);
        $lot = $servis->primiUSkladiste(
            $this->kreiranLot(),
            SkladisnaLokacija::factory()->create()
        );
        $servis->dodeliKlasuKvaliteta($lot, KlasaKvaliteta::KLASA_I, 'KD-2026-501');

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
