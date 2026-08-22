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
    /** @return Collection<int, LotRaspodela> */
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
}
