<?php

namespace Tests\Feature;

use App\Enums\KlasaKvaliteta;
use App\Enums\LotDogadjajTip;
use App\Enums\LotRaspodelaStatus;
use App\Enums\LotStatus;
use App\Models\Lot;
use App\Models\LotRaspodela;
use App\Models\NarudzbinaStavka;
use App\Models\Proizvod;
use App\Models\SkladisnaLokacija;
use App\Models\Sorta;
use App\Services\NarudzbinaService;
use DomainException;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use RuntimeException;
use Tests\TestCase;
use Throwable;

/**
 * Pokreće dve FIFO rezervacije u odvojenim procesima nad istim MySQL lotom dok prvi proces drži zaključavanje reda.
 * Proverava da samo jedna rezervacija uspe, da nema prekomerne potrošnje i da lot ispravno postane ISCRPLJEN.
 * Test se izvršava samo nad izolovanom bazom čiji se naziv završava sa _test.
 */
#[Group('mysql-concurrency')]
class KonkurentnaFifoRezervacijaTest extends TestCase
{
    #[Test]
    public function dve_istovremene_rezervacije_ne_mogu_prekomerno_rezervisati_isti_lot(): void
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            $this->markTestSkipped('Konkurentno zaključavanje se proverava samo nad MySQL bazom.');
        }

        if (! extension_loaded('pcntl')) {
            $this->markTestSkipped('PCNTL ekstenzija je neophodna za konkurentni test.');
        }

        $nazivBaze = DB::connection()->getDatabaseName();

        if (! str_ends_with($nazivBaze, '_test')) {
            throw new RuntimeException('Konkurentni test sme da koristi samo izolovanu bazu čiji naziv završava sa _test.');
        }

        $sorta = Sorta::factory()->create();
        $proizvod = Proizvod::factory()->create([
            'sorta_id' => $sorta->id,
            'neto_kolicina_g' => 500,
        ]);
        $lot = Lot::factory()->create([
            'sorta_id' => $sorta->id,
            'trenutna_skladisna_lokacija_id' => SkladisnaLokacija::factory()->create()->id,
            'pocetna_kolicina_g' => 1000,
            'raspoloziva_kolicina_g' => 1000,
            'status' => LotStatus::RASPOLOZIV,
            'klasa_kvaliteta' => KlasaKvaliteta::KLASA_I,
            'broj_dokumenta_kvaliteta' => 'KVAL-CONC-001',
        ]);
        $stavke = NarudzbinaStavka::factory()->count(2)->create([
            'proizvod_id' => $proizvod->id,
            'kolicina' => 2,
            'neto_kolicina_g' => 500,
        ]);
        $privremeniDirektorijum = sys_get_temp_dir().'/borovnice-konkurentnost-'.bin2hex(random_bytes(6));
        mkdir($privremeniDirektorijum, 0700);
        $signalZakljucavanja = $privremeniDirektorijum.'/lot-zakljucan';
        $rezultati = [
            $privremeniDirektorijum.'/prva-rezervacija',
            $privremeniDirektorijum.'/druga-rezervacija',
        ];

        DB::disconnect();

        try {
            $prviProces = $this->pokreniProcesRezervacije(
                $stavke[0]->id,
                $rezultati[0],
                $signalZakljucavanja
            );
            $this->sacekajSignalZakljucavanja($signalZakljucavanja, $prviProces);
            $drugiProces = $this->pokreniProcesRezervacije($stavke[1]->id, $rezultati[1]);

            $this->sacekajProces($prviProces);
            $this->sacekajProces($drugiProces);

            $ishodi = array_map(
                fn (string $putanja): string => explode(':', trim((string) file_get_contents($putanja)), 2)[0],
                $rezultati
            );
            sort($ishodi);

            $this->assertSame(['odbijena', 'uspesna'], $ishodi);

            DB::reconnect();
            $lot->refresh();

            $this->assertSame(0, $lot->raspoloziva_kolicina_g);
            $this->assertSame(LotStatus::ISCRPLJEN, $lot->status);
            $this->assertSame(2, (int) LotRaspodela::query()
                ->where('status', LotRaspodelaStatus::REZERVISANO)
                ->sum('broj_pakovanja'));
            $this->assertDatabaseCount('lot_raspodela', 1);
            $this->assertDatabaseHas('lot_dogadjajs', [
                'lot_id' => $lot->id,
                'tip' => LotDogadjajTip::KOLICINA_REZERVISANA->value,
                'kolicina_g' => 1000,
            ]);
        } finally {
            DB::reconnect();

            foreach ([$signalZakljucavanja, ...$rezultati] as $putanja) {
                if (is_file($putanja)) {
                    unlink($putanja);
                }
            }

            if (is_dir($privremeniDirektorijum)) {
                rmdir($privremeniDirektorijum);
            }
        }
    }

    private function pokreniProcesRezervacije(
        int $stavkaId,
        string $rezultatPutanja,
        ?string $signalZakljucavanja = null
    ): int {
        $pid = pcntl_fork();

        if ($pid === -1) {
            throw new RuntimeException('Nije moguće pokrenuti proces za konkurentnu rezervaciju.');
        }

        if ($pid !== 0) {
            return $pid;
        }

        DB::purge();

        if ($signalZakljucavanja !== null) {
            Lot::updating(function () use ($signalZakljucavanja): void {
                touch($signalZakljucavanja);
                usleep(750_000);
            });
        }

        try {
            $stavka = NarudzbinaStavka::findOrFail($stavkaId);
            app(NarudzbinaService::class)->rezervisiFifo($stavka);
            file_put_contents($rezultatPutanja, 'uspesna');
        } catch (DomainException $exception) {
            file_put_contents($rezultatPutanja, 'odbijena:'.$exception->getMessage());
        } catch (Throwable $exception) {
            file_put_contents($rezultatPutanja, 'greska:'.$exception->getMessage());
        } finally {
            DB::disconnect();
        }

        exit(0);
    }

    private function sacekajSignalZakljucavanja(string $signalPutanja, int $pid): void
    {
        $rok = microtime(true) + 5;

        while (! is_file($signalPutanja) && microtime(true) < $rok) {
            usleep(10_000);
        }

        if (! is_file($signalPutanja)) {
            pcntl_waitpid($pid, $status);

            throw new RuntimeException('Prva rezervacija nije na vreme zaključala lot.');
        }
    }

    private function sacekajProces(int $pid): void
    {
        pcntl_waitpid($pid, $status);

        if (! pcntl_wifexited($status) || pcntl_wexitstatus($status) !== 0) {
            throw new RuntimeException('Proces konkurentne rezervacije nije uspešno završen.');
        }
    }
}
