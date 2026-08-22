<?php

namespace App\Services;

use App\Enums\LotDogadjajTip;
use App\Enums\LotRaspodelaStatus;
use App\Enums\LotStatus;
use App\Enums\NarudzbinaStatus;
use App\Models\Lot;
use App\Models\LotRaspodela;
use App\Models\Narudzbina;
use App\Models\NarudzbinaStavka;
use App\Models\Proizvod;
use App\Models\SkladisnaLokacija;
use App\Models\Skladiste;
use App\Models\User;
use DomainException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class NarudzbinaService
{
    /** FIFO redosledom rezerviše cela pakovanja iz odgovarajućih lotova i evidentira svaku raspodelu i promenu zalihe.
     *
     * @return Collection<int, LotRaspodela>
     */
    public function rezervisiFifo(NarudzbinaStavka $stavka, ?User $evidentirao = null): Collection
    {
        return DB::transaction(function () use ($stavka, $evidentirao): Collection {
            $narudzbina = Narudzbina::query()
                ->lockForUpdate()
                ->findOrFail($stavka->narudzbina_id);
            $zakljucanaStavka = NarudzbinaStavka::query()
                ->where('narudzbina_id', $narudzbina->id)
                ->lockForUpdate()
                ->findOrFail($stavka->id);
            $proizvod = Proizvod::query()
                ->lockForUpdate()
                ->findOrFail($zakljucanaStavka->proizvod_id);

            if ($narudzbina->status !== NarudzbinaStatus::POTVRDJENA) {
                throw new DomainException('Lotovi se mogu rezervisati samo za potvrđenu narudžbinu.');
            }

            $aktivnaRaspodela = LotRaspodela::query()
                ->where('narudzbina_stavka_id', $zakljucanaStavka->id)
                ->whereIn('status', [
                    LotRaspodelaStatus::REZERVISANO->value,
                    LotRaspodelaStatus::IZDATO->value,
                ])
                ->lockForUpdate()
                ->first();

            if ($aktivnaRaspodela !== null) {
                throw new DomainException('Stavka narudžbine već ima aktivnu raspodelu lotova.');
            }

            $preostaloPakovanja = $zakljucanaStavka->kolicina;
            $netoKolicinaG = $zakljucanaStavka->neto_kolicina_g;

            if ($preostaloPakovanja <= 0 || $netoKolicinaG <= 0) {
                throw new DomainException('Stavka narudžbine mora imati pozitivnu količinu i neto masu.');
            }

            $raspodele = new Collection;
            $lotovi = Lot::query()
                ->where('sorta_id', $proizvod->sorta_id)
                ->where('status', LotStatus::RASPOLOZIV->value)
                ->where('raspoloziva_kolicina_g', '>', 0)
                ->whereHas('trenutnaSkladisnaLokacija', function ($query): void {
                    $query->where('aktivna', true)
                        ->whereHas('skladiste', fn ($skladiste) => $skladiste->where('aktivan', true));
                })
                ->orderBy('datum_berbe')
                ->orderBy('id')
                ->lockForUpdate()
                ->get();
            $lokacije = SkladisnaLokacija::query()
                ->whereIn('id', $lotovi->pluck('trenutna_skladisna_lokacija_id'))
                ->lockForUpdate()
                ->get()
                ->keyBy('id');
            $skladista = Skladiste::query()
                ->whereIn('id', $lokacije->pluck('skladiste_id'))
                ->lockForUpdate()
                ->get()
                ->keyBy('id');
            $lotovi = $lotovi->filter(function (Lot $lot) use ($lokacije, $skladista): bool {
                $lokacija = $lokacije->get($lot->trenutna_skladisna_lokacija_id);
                $skladiste = $lokacija === null ? null : $skladista->get($lokacija->skladiste_id);

                return $lokacija?->aktivna === true && $skladiste?->aktivan === true;
            });

            foreach ($lotovi as $lot) {
                $raspolozivoPakovanja = intdiv($lot->raspoloziva_kolicina_g, $netoKolicinaG);

                if ($raspolozivoPakovanja === 0) {
                    continue;
                }

                $brojPakovanja = min($preostaloPakovanja, $raspolozivoPakovanja);
                $rezervisanaKolicinaG = $brojPakovanja * $netoKolicinaG;
                $novaKolicinaG = $lot->raspoloziva_kolicina_g - $rezervisanaKolicinaG;
                $noviStatus = $novaKolicinaG === 0
                    ? LotStatus::ISCRPLJEN
                    : LotStatus::RASPOLOZIV;

                $raspodela = LotRaspodela::updateOrCreate(
                    [
                        'lot_id' => $lot->id,
                        'narudzbina_stavka_id' => $zakljucanaStavka->id,
                    ],
                    [
                        'broj_pakovanja' => $brojPakovanja,
                        'status' => LotRaspodelaStatus::REZERVISANO,
                    ]
                );

                $lot->update([
                    'raspoloziva_kolicina_g' => $novaKolicinaG,
                    'status' => $noviStatus,
                ]);

                $lot->dogadjaji()->create([
                    'lot_raspodela_id' => $raspodela->id,
                    'tip' => LotDogadjajTip::KOLICINA_REZERVISANA,
                    'kolicina_g' => $rezervisanaKolicinaG,
                    'vreme_dogadjaja' => now(),
                    'evidentirao_user_id' => $evidentirao?->id,
                    'prethodni_status' => LotStatus::RASPOLOZIV,
                    'novi_status' => $noviStatus,
                    'razlog' => null,
                ]);

                $raspodele->push($raspodela);
                $preostaloPakovanja -= $brojPakovanja;

                if ($preostaloPakovanja === 0) {
                    break;
                }
            }

            if ($preostaloPakovanja > 0) {
                throw new DomainException('Nema dovoljno raspoložive količine za celu stavku narudžbine.');
            }

            return $raspodele;
        });
    }

    /** Otprema potpuno rezervisanu potvrđenu narudžbinu, označava raspodele kao izdate i beleži izdavanje lotova. */
    public function otpremi(Narudzbina $narudzbina, ?User $evidentirao = null): Narudzbina
    {
        return DB::transaction(function () use ($narudzbina, $evidentirao): Narudzbina {
            $zakljucanaNarudzbina = Narudzbina::query()
                ->lockForUpdate()
                ->findOrFail($narudzbina->id);

            if ($zakljucanaNarudzbina->status !== NarudzbinaStatus::POTVRDJENA) {
                throw new DomainException('Samo potvrđena narudžbina može biti otpremljena.');
            }

            $stavke = NarudzbinaStavka::query()
                ->where('narudzbina_id', $zakljucanaNarudzbina->id)
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            if ($stavke->isEmpty()) {
                throw new DomainException('Narudžbina bez stavki ne može biti otpremljena.');
            }

            $raspodele = LotRaspodela::query()
                ->whereIn('narudzbina_stavka_id', $stavke->pluck('id'))
                ->lockForUpdate()
                ->get();

            foreach ($stavke as $stavka) {
                $rezervisanoPakovanja = $raspodele
                    ->where('narudzbina_stavka_id', $stavka->id)
                    ->where('status', LotRaspodelaStatus::REZERVISANO)
                    ->sum('broj_pakovanja');

                if ($rezervisanoPakovanja !== $stavka->kolicina) {
                    throw new DomainException('Sve stavke moraju biti potpuno rezervisane pre otpreme.');
                }
            }

            $rezervisaneRaspodele = $raspodele
                ->where('status', LotRaspodelaStatus::REZERVISANO);
            $lotovi = Lot::query()
                ->whereIn('id', $rezervisaneRaspodele->pluck('lot_id'))
                ->lockForUpdate()
                ->get()
                ->keyBy('id');
            $lokacije = SkladisnaLokacija::query()
                ->whereIn('id', $lotovi->pluck('trenutna_skladisna_lokacija_id'))
                ->lockForUpdate()
                ->get()
                ->keyBy('id');
            $skladista = Skladiste::query()
                ->whereIn('id', $lokacije->pluck('skladiste_id'))
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            foreach ($rezervisaneRaspodele as $raspodela) {
                $lot = $lotovi->get($raspodela->lot_id);
                $stavka = $stavke->firstWhere('id', $raspodela->narudzbina_stavka_id);

                if ($lot === null || $stavka === null) {
                    throw new DomainException('Nije moguće utvrditi lot ili stavku za izdavanje.');
                }

                if (! in_array($lot->status, [LotStatus::RASPOLOZIV, LotStatus::ISCRPLJEN], true)) {
                    throw new DomainException('Blokiran, povučen ili neuskladišten lot ne može biti izdat.');
                }

                $lokacija = $lokacije->get($lot->trenutna_skladisna_lokacija_id);
                $skladiste = $lokacija === null ? null : $skladista->get($lokacija->skladiste_id);

                if ($lokacija?->aktivna !== true || $skladiste?->aktivan !== true) {
                    throw new DomainException('Lot za izdavanje mora biti na aktivnoj lokaciji u aktivnom skladištu.');
                }

                $raspodela->update(['status' => LotRaspodelaStatus::IZDATO]);
                $lot->dogadjaji()->create([
                    'lot_raspodela_id' => $raspodela->id,
                    'tip' => LotDogadjajTip::KOLICINA_IZDATA,
                    'kolicina_g' => $raspodela->broj_pakovanja * $stavka->neto_kolicina_g,
                    'vreme_dogadjaja' => now(),
                    'evidentirao_user_id' => $evidentirao?->id,
                    'prethodni_status' => $lot->status,
                    'novi_status' => $lot->status,
                    'razlog' => null,
                ]);
            }

            $zakljucanaNarudzbina->update(['status' => NarudzbinaStatus::OTPREMLJENA]);

            return $zakljucanaNarudzbina->load('stavke.raspodele');
        });
    }

    /** Otkazuje potvrđenu neizdatu narudžbinu, oslobađa rezervacije i vraća raspoložive količine lotovima. */
    public function otkazi(
        Narudzbina $narudzbina,
        string $razlog,
        ?User $evidentirao = null
    ): Narudzbina {
        $razlog = trim($razlog);

        if ($razlog === '') {
            throw new DomainException('Razlog otkazivanja narudžbine je obavezan.');
        }

        return DB::transaction(function () use ($narudzbina, $razlog, $evidentirao): Narudzbina {
            $zakljucanaNarudzbina = Narudzbina::query()
                ->lockForUpdate()
                ->findOrFail($narudzbina->id);

            if ($zakljucanaNarudzbina->status !== NarudzbinaStatus::POTVRDJENA) {
                throw new DomainException('Samo potvrđena narudžbina može biti otkazana.');
            }

            $stavke = NarudzbinaStavka::query()
                ->where('narudzbina_id', $zakljucanaNarudzbina->id)
                ->lockForUpdate()
                ->get();
            $raspodele = LotRaspodela::query()
                ->whereIn('narudzbina_stavka_id', $stavke->pluck('id'))
                ->lockForUpdate()
                ->get();

            if ($raspodele->contains('status', LotRaspodelaStatus::IZDATO)) {
                throw new DomainException('Narudžbina sa izdatom količinom ne može biti otkazana.');
            }

            $rezervisaneRaspodele = $raspodele
                ->where('status', LotRaspodelaStatus::REZERVISANO);
            $lotovi = Lot::query()
                ->whereIn('id', $rezervisaneRaspodele->pluck('lot_id'))
                ->lockForUpdate()
                ->get()
                ->keyBy('id');
            $stavkePoId = $stavke->keyBy('id');

            foreach ($rezervisaneRaspodele as $raspodela) {
                $lot = $lotovi->get($raspodela->lot_id);
                $stavka = $stavkePoId->get($raspodela->narudzbina_stavka_id);

                if ($lot === null || $stavka === null) {
                    throw new DomainException('Nije moguće utvrditi lot ili stavku rezervacije.');
                }

                if ($lot->status === LotStatus::POVUCEN) {
                    throw new DomainException('Rezervaciju povučenog lota nije moguće osloboditi ovim tokom.');
                }

                $oslobodjenaKolicinaG = $raspodela->broj_pakovanja * $stavka->neto_kolicina_g;
                $novaKolicinaG = $lot->raspoloziva_kolicina_g + $oslobodjenaKolicinaG;

                if ($novaKolicinaG > $lot->pocetna_kolicina_g) {
                    throw new DomainException('Oslobađanje rezervacije bi prekoračilo početnu količinu lota.');
                }

                $prethodniStatus = $lot->status;
                $noviStatus = $prethodniStatus === LotStatus::ISCRPLJEN
                    ? LotStatus::RASPOLOZIV
                    : $prethodniStatus;

                $lot->update([
                    'raspoloziva_kolicina_g' => $novaKolicinaG,
                    'status' => $noviStatus,
                ]);
                $raspodela->update(['status' => LotRaspodelaStatus::OTKAZANO]);

                $lot->dogadjaji()->create([
                    'lot_raspodela_id' => $raspodela->id,
                    'tip' => LotDogadjajTip::REZERVACIJA_OSLOBODJENA,
                    'kolicina_g' => -$oslobodjenaKolicinaG,
                    'vreme_dogadjaja' => now(),
                    'evidentirao_user_id' => $evidentirao?->id,
                    'prethodni_status' => $prethodniStatus,
                    'novi_status' => $noviStatus,
                    'razlog' => $razlog,
                ]);
            }

            $zakljucanaNarudzbina->update(['status' => NarudzbinaStatus::OTKAZANA]);

            return $zakljucanaNarudzbina->load('stavke.raspodele');
        });
    }
}
