<?php

namespace App\Services;

use App\Enums\KlasaKvaliteta;
use App\Enums\LotDogadjajTip;
use App\Enums\LotRaspodelaStatus;
use App\Enums\LotStatus;
use App\Models\Lot;
use App\Models\LotDogadjaj;
use App\Models\LotRaspodela;
use App\Models\NarudzbinaStavka;
use App\Models\SkladisnaLokacija;
use App\Models\Skladiste;
use App\Models\User;
use Carbon\CarbonImmutable;
use DomainException;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class LotService
{
    public function __construct(
        private readonly LotOznakaGenerator $oznakaGenerator
    ) {}

    /** Kreira lot sa početnom količinom i statusom KREIRAN, generiše oznaku i beleži početni događaj. */
    public function kreiraj(array $podaci, ?User $evidentirao = null): Lot
    {
        $pocetnaKolicina = (int) ($podaci['pocetna_kolicina_g'] ?? 0);

        if ($pocetnaKolicina <= 0) {
            throw new InvalidArgumentException('Početna količina lota mora biti veća od nule.');
        }

        $datumBerbe = CarbonImmutable::parse($podaci['datum_berbe']);

        return DB::transaction(function () use ($podaci, $datumBerbe, $pocetnaKolicina, $evidentirao): Lot {
            $lot = Lot::create([
                'oznaka' => $this->oznakaGenerator->generisi($datumBerbe),
                'sorta_id' => $podaci['sorta_id'],
                'parcela_id' => $podaci['parcela_id'],
                'trenutna_skladisna_lokacija_id' => null,
                'datum_berbe' => $datumBerbe,
                'pocetna_kolicina_g' => $pocetnaKolicina,
                'raspoloziva_kolicina_g' => $pocetnaKolicina,
                'status' => LotStatus::KREIRAN,
                'klasa_kvaliteta' => null,
                'broj_dokumenta_kvaliteta' => null,
                'napomena' => $podaci['napomena'] ?? null,
            ]);

            $lot->dogadjaji()->create([
                'tip' => LotDogadjajTip::LOT_KREIRAN,
                'kolicina_g' => $pocetnaKolicina,
                'vreme_dogadjaja' => now(),
                'evidentirao_user_id' => $evidentirao?->id,
                'prethodni_status' => null,
                'novi_status' => LotStatus::KREIRAN,
                'razlog' => null,
            ]);

            return $lot->load('dogadjaji');
        });
    }

    /** Prima kreirani lot na aktivnu skladišnu lokaciju, postavlja status USKLADISTEN i beleži prijem. */
    public function primiUSkladiste(
        Lot $lot,
        SkladisnaLokacija $skladisnaLokacija,
        ?User $evidentirao = null
    ): Lot {
        return DB::transaction(function () use ($lot, $skladisnaLokacija, $evidentirao): Lot {
            $zakljucanLot = Lot::query()->lockForUpdate()->findOrFail($lot->id);
            $zakljucanaLokacija = SkladisnaLokacija::query()
                ->lockForUpdate()
                ->findOrFail($skladisnaLokacija->id);
            $skladiste = Skladiste::query()
                ->lockForUpdate()
                ->findOrFail($zakljucanaLokacija->skladiste_id);

            if ($zakljucanLot->status !== LotStatus::KREIRAN) {
                throw new DomainException('Samo lot u statusu KREIRAN može biti primljen u skladište.');
            }

            if (! $zakljucanaLokacija->aktivna) {
                throw new DomainException('Lot nije moguće primiti na neaktivnu skladišnu lokaciju.');
            }

            if (! $skladiste->aktivan) {
                throw new DomainException('Lot nije moguće primiti u neaktivno skladište.');
            }

            $zakljucanLot->update([
                'trenutna_skladisna_lokacija_id' => $zakljucanaLokacija->id,
                'status' => LotStatus::USKLADISTEN,
            ]);

            $zakljucanLot->dogadjaji()->create([
                'tip' => LotDogadjajTip::PRIJEM_U_SKLADISTE,
                'kolicina_g' => null,
                'vreme_dogadjaja' => now(),
                'evidentirao_user_id' => $evidentirao?->id,
                'prethodni_status' => LotStatus::KREIRAN,
                'novi_status' => LotStatus::USKLADISTEN,
                'prethodna_skladisna_lokacija_id' => null,
                'nova_skladisna_lokacija_id' => $zakljucanaLokacija->id,
                'razlog' => null,
            ]);

            return $zakljucanLot->load(['trenutnaSkladisnaLokacija', 'dogadjaji']);
        });
    }

    /** Dodeljuje uskladištenom lotu klasu i dokument kvaliteta i evidentira tu promenu. */
    public function dodeliKlasuKvaliteta(
        Lot $lot,
        KlasaKvaliteta $klasaKvaliteta,
        string $brojDokumenta,
        ?User $evidentirao = null
    ): Lot {
        $brojDokumenta = trim($brojDokumenta);

        if ($brojDokumenta === '') {
            throw new InvalidArgumentException('Broj dokumenta kvaliteta je obavezan.');
        }

        if (mb_strlen($brojDokumenta) > 255) {
            throw new InvalidArgumentException('Broj dokumenta kvaliteta može imati najviše 255 znakova.');
        }

        return DB::transaction(function () use ($lot, $klasaKvaliteta, $brojDokumenta, $evidentirao): Lot {
            $zakljucanLot = Lot::query()->lockForUpdate()->findOrFail($lot->id);

            if ($zakljucanLot->status !== LotStatus::USKLADISTEN) {
                throw new DomainException('Klasa kvaliteta može biti dodeljena samo uskladištenom lotu.');
            }

            if ($zakljucanLot->klasa_kvaliteta !== null || $zakljucanLot->broj_dokumenta_kvaliteta !== null) {
                throw new DomainException('Lot već ima dodeljenu klasu kvaliteta.');
            }

            $zakljucanLot->update([
                'klasa_kvaliteta' => $klasaKvaliteta,
                'broj_dokumenta_kvaliteta' => $brojDokumenta,
            ]);

            $zakljucanLot->dogadjaji()->create([
                'tip' => LotDogadjajTip::KLASA_KVALITETA_DODELJENA,
                'kolicina_g' => null,
                'vreme_dogadjaja' => now(),
                'evidentirao_user_id' => $evidentirao?->id,
                'prethodni_status' => null,
                'novi_status' => null,
                'razlog' => "Klasa: {$klasaKvaliteta->value}; dokument: {$brojDokumenta}",
            ]);

            return $zakljucanLot->load('dogadjaji');
        });
    }

    /** Odobrava za prodaju uskladišten lot sa validnim kvalitetom, količinom i aktivnom lokacijom. */
    public function odobriZaProdaju(Lot $lot, ?User $evidentirao = null): Lot
    {
        return DB::transaction(function () use ($lot, $evidentirao): Lot {
            $zakljucanLot = Lot::query()->lockForUpdate()->findOrFail($lot->id);

            if ($zakljucanLot->status !== LotStatus::USKLADISTEN) {
                throw new DomainException('Samo uskladišten lot može biti odobren za prodaju.');
            }

            if ($zakljucanLot->trenutna_skladisna_lokacija_id === null) {
                throw new DomainException('Lot mora imati trenutnu skladišnu lokaciju.');
            }

            $skladisnaLokacija = SkladisnaLokacija::query()
                ->lockForUpdate()
                ->findOrFail($zakljucanLot->trenutna_skladisna_lokacija_id);
            $skladiste = Skladiste::query()
                ->lockForUpdate()
                ->findOrFail($skladisnaLokacija->skladiste_id);

            if (! $skladisnaLokacija->aktivna || ! $skladiste->aktivan) {
                throw new DomainException('Lot mora biti na aktivnoj lokaciji u aktivnom skladištu.');
            }

            if ($zakljucanLot->klasa_kvaliteta === null || $zakljucanLot->broj_dokumenta_kvaliteta === null) {
                throw new DomainException('Lot mora imati dodeljenu klasu i dokument kvaliteta.');
            }

            if ($zakljucanLot->raspoloziva_kolicina_g <= 0) {
                throw new DomainException('Lot bez raspoložive količine ne može biti odobren za prodaju.');
            }

            $zakljucanLot->update(['status' => LotStatus::RASPOLOZIV]);

            $zakljucanLot->dogadjaji()->create([
                'tip' => LotDogadjajTip::ODOBREN_ZA_PRODAJU,
                'kolicina_g' => null,
                'vreme_dogadjaja' => now(),
                'evidentirao_user_id' => $evidentirao?->id,
                'prethodni_status' => LotStatus::USKLADISTEN,
                'novi_status' => LotStatus::RASPOLOZIV,
                'razlog' => null,
            ]);

            return $zakljucanLot->load(['trenutnaSkladisnaLokacija', 'dogadjaji']);
        });
    }

    /** Premešta lot dozvoljenog statusa na drugu aktivnu lokaciju i beleži trag premeštanja. */
    public function premesti(
        Lot $lot,
        SkladisnaLokacija $novaLokacija,
        ?User $evidentirao = null,
        ?string $razlog = null
    ): Lot {
        return DB::transaction(function () use ($lot, $novaLokacija, $evidentirao, $razlog): Lot {
            $zakljucanLot = Lot::query()->lockForUpdate()->findOrFail($lot->id);
            $dozvoljeniStatusi = [
                LotStatus::USKLADISTEN,
                LotStatus::RASPOLOZIV,
                LotStatus::BLOKIRAN,
            ];

            if (! in_array($zakljucanLot->status, $dozvoljeniStatusi, true)) {
                throw new DomainException('Lot u trenutnom statusu nije moguće premestiti.');
            }

            if ($zakljucanLot->trenutna_skladisna_lokacija_id === null) {
                throw new DomainException('Lot nema trenutnu skladišnu lokaciju.');
            }

            if ($zakljucanLot->trenutna_skladisna_lokacija_id === $novaLokacija->id) {
                throw new DomainException('Nova lokacija mora biti različita od trenutne lokacije lota.');
            }

            $prethodnaLokacija = SkladisnaLokacija::query()
                ->lockForUpdate()
                ->findOrFail($zakljucanLot->trenutna_skladisna_lokacija_id);
            $zakljucanaNovaLokacija = SkladisnaLokacija::query()
                ->lockForUpdate()
                ->findOrFail($novaLokacija->id);
            $novoSkladiste = Skladiste::query()
                ->lockForUpdate()
                ->findOrFail($zakljucanaNovaLokacija->skladiste_id);

            if (! $zakljucanaNovaLokacija->aktivna || ! $novoSkladiste->aktivan) {
                throw new DomainException('Ciljna lokacija i njeno skladište moraju biti aktivni.');
            }

            $zakljucanLot->update([
                'trenutna_skladisna_lokacija_id' => $zakljucanaNovaLokacija->id,
            ]);

            $zakljucanLot->dogadjaji()->create([
                'tip' => LotDogadjajTip::PREMESTANJE,
                'kolicina_g' => null,
                'vreme_dogadjaja' => now(),
                'evidentirao_user_id' => $evidentirao?->id,
                'prethodni_status' => null,
                'novi_status' => null,
                'prethodna_skladisna_lokacija_id' => $prethodnaLokacija->id,
                'nova_skladisna_lokacija_id' => $zakljucanaNovaLokacija->id,
                'razlog' => $razlog,
            ]);

            return $zakljucanLot->load(['trenutnaSkladisnaLokacija', 'dogadjaji']);
        });
    }

    /** Blokira uskladišten ili raspoloživ lot uz obavezan razlog i beleži prethodni status. */
    public function blokiraj(Lot $lot, string $razlog, ?User $evidentirao = null): Lot
    {
        $razlog = $this->normalizujObavezanRazlog($razlog);

        return DB::transaction(function () use ($lot, $razlog, $evidentirao): Lot {
            $zakljucanLot = Lot::query()->lockForUpdate()->findOrFail($lot->id);

            if (! in_array($zakljucanLot->status, [LotStatus::USKLADISTEN, LotStatus::RASPOLOZIV], true)) {
                throw new DomainException('Samo uskladišten ili raspoloživ lot može biti blokiran.');
            }

            $prethodniStatus = $zakljucanLot->status;
            $zakljucanLot->update(['status' => LotStatus::BLOKIRAN]);

            $zakljucanLot->dogadjaji()->create([
                'tip' => LotDogadjajTip::LOT_BLOKIRAN,
                'kolicina_g' => null,
                'vreme_dogadjaja' => now(),
                'evidentirao_user_id' => $evidentirao?->id,
                'prethodni_status' => $prethodniStatus,
                'novi_status' => LotStatus::BLOKIRAN,
                'razlog' => $razlog,
            ]);

            return $zakljucanLot->load('dogadjaji');
        });
    }

    /** Odblokira lot vraćanjem pouzdano utvrđenog prethodnog statusa kada su njegovi uslovi i dalje ispunjeni. */
    public function odblokiraj(Lot $lot, string $razlog, ?User $evidentirao = null): Lot
    {
        $razlog = $this->normalizujObavezanRazlog($razlog);

        return DB::transaction(function () use ($lot, $razlog, $evidentirao): Lot {
            $zakljucanLot = Lot::query()->lockForUpdate()->findOrFail($lot->id);

            if ($zakljucanLot->status !== LotStatus::BLOKIRAN) {
                throw new DomainException('Samo blokiran lot može biti odblokiran.');
            }

            $dogadjajBlokiranja = LotDogadjaj::query()
                ->where('lot_id', $zakljucanLot->id)
                ->where('tip', LotDogadjajTip::LOT_BLOKIRAN)
                ->latest('vreme_dogadjaja')
                ->latest('id')
                ->lockForUpdate()
                ->first();
            $statusZaVracanje = $dogadjajBlokiranja?->prethodni_status;

            if (! in_array($statusZaVracanje, [LotStatus::USKLADISTEN, LotStatus::RASPOLOZIV], true)) {
                throw new DomainException('Prethodni status lota nije moguće pouzdano utvrditi.');
            }

            $this->proveriAktivnuTrenutnuLokaciju($zakljucanLot);

            if ($statusZaVracanje === LotStatus::RASPOLOZIV) {
                if ($zakljucanLot->klasa_kvaliteta === null || $zakljucanLot->broj_dokumenta_kvaliteta === null) {
                    throw new DomainException('Lot nema podatke o kvalitetu potrebne za status RASPOLOZIV.');
                }

                if ($zakljucanLot->raspoloziva_kolicina_g <= 0) {
                    throw new DomainException('Lot bez raspoložive količine ne može ponovo biti raspoloživ.');
                }
            }

            $zakljucanLot->update(['status' => $statusZaVracanje]);

            $zakljucanLot->dogadjaji()->create([
                'tip' => LotDogadjajTip::LOT_ODBLOKIRAN,
                'kolicina_g' => null,
                'vreme_dogadjaja' => now(),
                'evidentirao_user_id' => $evidentirao?->id,
                'prethodni_status' => LotStatus::BLOKIRAN,
                'novi_status' => $statusZaVracanje,
                'razlog' => $razlog,
            ]);

            return $zakljucanLot->load(['trenutnaSkladisnaLokacija', 'dogadjaji']);
        });
    }

    /** Povlači lot, otkazuje njegove aktivne rezervacije, uklanja ga sa lokacije i beleži sledljivost. */
    public function povuci(Lot $lot, string $razlog, ?User $evidentirao = null): Lot
    {
        $razlog = $this->normalizujObavezanRazlog($razlog);

        return DB::transaction(function () use ($lot, $razlog, $evidentirao): Lot {
            $zakljucanLot = Lot::query()->lockForUpdate()->findOrFail($lot->id);

            if ($zakljucanLot->status === LotStatus::POVUCEN) {
                throw new DomainException('Lot je već povučen.');
            }

            $rezervisaneRaspodele = LotRaspodela::query()
                ->where('lot_id', $zakljucanLot->id)
                ->where('status', LotRaspodelaStatus::REZERVISANO)
                ->lockForUpdate()
                ->get();
            $stavke = NarudzbinaStavka::query()
                ->whereIn('id', $rezervisaneRaspodele->pluck('narudzbina_stavka_id'))
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            foreach ($rezervisaneRaspodele as $raspodela) {
                $stavka = $stavke->get($raspodela->narudzbina_stavka_id);

                if ($stavka === null) {
                    throw new DomainException('Nije moguće utvrditi količinu aktivne rezervacije.');
                }

                $rezervisanaKolicina = $raspodela->broj_pakovanja * $stavka->neto_kolicina_g;
                $raspodela->update(['status' => LotRaspodelaStatus::OTKAZANO]);

                $zakljucanLot->dogadjaji()->create([
                    'lot_raspodela_id' => $raspodela->id,
                    'tip' => LotDogadjajTip::REZERVACIJA_OSLOBODJENA,
                    'kolicina_g' => -$rezervisanaKolicina,
                    'vreme_dogadjaja' => now(),
                    'evidentirao_user_id' => $evidentirao?->id,
                    'prethodni_status' => null,
                    'novi_status' => null,
                    'razlog' => 'Rezervacija otkazana zbog povlačenja lota.',
                ]);
            }

            $prethodniStatus = $zakljucanLot->status;
            $prethodnaLokacijaId = $zakljucanLot->trenutna_skladisna_lokacija_id;

            $zakljucanLot->update([
                'raspoloziva_kolicina_g' => 0,
                'trenutna_skladisna_lokacija_id' => null,
                'status' => LotStatus::POVUCEN,
            ]);

            $zakljucanLot->dogadjaji()->create([
                'tip' => LotDogadjajTip::LOT_POVUCEN,
                'kolicina_g' => null,
                'vreme_dogadjaja' => now(),
                'evidentirao_user_id' => $evidentirao?->id,
                'prethodni_status' => $prethodniStatus,
                'novi_status' => LotStatus::POVUCEN,
                'prethodna_skladisna_lokacija_id' => $prethodnaLokacijaId,
                'nova_skladisna_lokacija_id' => null,
                'razlog' => $razlog,
            ]);

            return $zakljucanLot->load(['raspodele', 'dogadjaji']);
        });
    }

    /** Korigovanjem raspoložive količine poštuje angažovane zalihe, a po potrebi menja status i beleži razliku. */
    public function korigujKolicinu(
        Lot $lot,
        int $novaRaspolozivaKolicinaG,
        string $razlog,
        ?User $evidentirao = null
    ): Lot {
        $razlog = $this->normalizujObavezanRazlog($razlog);

        if ($novaRaspolozivaKolicinaG < 0) {
            throw new InvalidArgumentException('Raspoloživa količina ne može biti negativna.');
        }

        return DB::transaction(function () use ($lot, $novaRaspolozivaKolicinaG, $razlog, $evidentirao): Lot {
            $zakljucanLot = Lot::query()->lockForUpdate()->findOrFail($lot->id);
            $dozvoljeniStatusi = [
                LotStatus::USKLADISTEN,
                LotStatus::RASPOLOZIV,
                LotStatus::BLOKIRAN,
                LotStatus::ISCRPLJEN,
            ];

            if (! in_array($zakljucanLot->status, $dozvoljeniStatusi, true)) {
                throw new DomainException('Količinu lota u trenutnom statusu nije moguće korigovati.');
            }

            if ($novaRaspolozivaKolicinaG === $zakljucanLot->raspoloziva_kolicina_g) {
                throw new DomainException('Nova raspoloživa količina mora se razlikovati od trenutne.');
            }

            $maksimalnaKolicina = $this->maksimalnaRaspolozivaKolicina($zakljucanLot);

            if ($novaRaspolozivaKolicinaG > $maksimalnaKolicina) {
                throw new DomainException("Raspoloživa količina ne može biti veća od {$maksimalnaKolicina} g.");
            }

            $prethodniStatus = $zakljucanLot->status;
            $noviStatus = $prethodniStatus;

            if ($novaRaspolozivaKolicinaG === 0) {
                $noviStatus = LotStatus::ISCRPLJEN;
            } elseif ($prethodniStatus === LotStatus::ISCRPLJEN) {
                $noviStatus = $this->statusPreIscrpljenja($zakljucanLot);

                if ($noviStatus === LotStatus::RASPOLOZIV) {
                    if ($zakljucanLot->klasa_kvaliteta === null || $zakljucanLot->broj_dokumenta_kvaliteta === null) {
                        throw new DomainException('Lot nema podatke o kvalitetu potrebne za status RASPOLOZIV.');
                    }

                    $this->proveriAktivnuTrenutnuLokaciju($zakljucanLot);
                }
            }

            $razlika = $novaRaspolozivaKolicinaG - $zakljucanLot->raspoloziva_kolicina_g;
            $zakljucanLot->update([
                'raspoloziva_kolicina_g' => $novaRaspolozivaKolicinaG,
                'status' => $noviStatus,
            ]);

            $zakljucanLot->dogadjaji()->create([
                'tip' => LotDogadjajTip::KOREKCIJA_KOLICINE,
                'kolicina_g' => $razlika,
                'vreme_dogadjaja' => now(),
                'evidentirao_user_id' => $evidentirao?->id,
                'prethodni_status' => $prethodniStatus,
                'novi_status' => $noviStatus,
                'razlog' => $razlog,
            ]);

            return $zakljucanLot->load(['raspodele', 'dogadjaji']);
        });
    }

    private function normalizujObavezanRazlog(string $razlog): string
    {
        $razlog = trim($razlog);

        if ($razlog === '') {
            throw new InvalidArgumentException('Razlog je obavezan.');
        }

        return $razlog;
    }

    private function proveriAktivnuTrenutnuLokaciju(Lot $lot): void
    {
        if ($lot->trenutna_skladisna_lokacija_id === null) {
            throw new DomainException('Lot nema trenutnu skladišnu lokaciju.');
        }

        $lokacija = SkladisnaLokacija::query()
            ->lockForUpdate()
            ->findOrFail($lot->trenutna_skladisna_lokacija_id);
        $skladiste = Skladiste::query()
            ->lockForUpdate()
            ->findOrFail($lokacija->skladiste_id);

        if (! $lokacija->aktivna || ! $skladiste->aktivan) {
            throw new DomainException('Lot mora biti na aktivnoj lokaciji u aktivnom skladištu.');
        }
    }

    private function maksimalnaRaspolozivaKolicina(Lot $lot): int
    {
        $raspodele = LotRaspodela::query()
            ->where('lot_id', $lot->id)
            ->whereIn('status', [
                LotRaspodelaStatus::REZERVISANO->value,
                LotRaspodelaStatus::IZDATO->value,
            ])
            ->lockForUpdate()
            ->get();
        $stavke = NarudzbinaStavka::query()
            ->whereIn('id', $raspodele->pluck('narudzbina_stavka_id'))
            ->lockForUpdate()
            ->get()
            ->keyBy('id');

        $angazovanaKolicina = $raspodele->sum(function (LotRaspodela $raspodela) use ($stavke): int {
            $stavka = $stavke->get($raspodela->narudzbina_stavka_id);

            if ($stavka === null) {
                throw new DomainException('Nije moguće utvrditi angažovanu količinu lota.');
            }

            return $raspodela->broj_pakovanja * $stavka->neto_kolicina_g;
        });

        return max(0, $lot->pocetna_kolicina_g - $angazovanaKolicina);
    }

    private function statusPreIscrpljenja(Lot $lot): LotStatus
    {
        $dogadjaj = LotDogadjaj::query()
            ->where('lot_id', $lot->id)
            ->where('novi_status', LotStatus::ISCRPLJEN->value)
            ->latest('vreme_dogadjaja')
            ->latest('id')
            ->lockForUpdate()
            ->first();
        $prethodniStatus = $dogadjaj?->prethodni_status;

        if (! in_array($prethodniStatus, [
            LotStatus::USKLADISTEN,
            LotStatus::RASPOLOZIV,
            LotStatus::BLOKIRAN,
        ], true)) {
            throw new DomainException('Status lota pre iscrpljenja nije moguće pouzdano utvrditi.');
        }

        return $prethodniStatus;
    }
}
