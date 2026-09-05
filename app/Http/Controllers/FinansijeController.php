<?php

namespace App\Http\Controllers;

use App\Enums\LotDogadjajTip;
use App\Enums\LotRaspodelaStatus;
use App\Enums\NarudzbinaStatus;
use App\Models\LotDogadjaj;
use App\Models\Narudzbina;
use App\Models\Resurs;
use App\Models\SkladisnaLokacija;
use App\Models\Skladiste;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FinansijeController extends Controller
{
    public function create(): View
    {
        $poslednjaOtprema = LotDogadjaj::query()
            ->where('tip', LotDogadjajTip::KOLICINA_IZDATA->value)
            ->whereHas('raspodela.narudzbinaStavka.narudzbina', function ($query) {
                $query->where('status', NarudzbinaStatus::OTPREMLJENA->value);
            })
            ->latest('vreme_dogadjaja')
            ->first(['vreme_dogadjaja']);

        return view('admin.finansije.create', [
            'podrazumevaniMesec' => $poslednjaOtprema?->vreme_dogadjaja->format('Y-m') ?? now()->format('Y-m'),
        ]);
    }

    public function generate(Request $request): View
    {
        $period = $request->validate([
            'mesec' => ['required', 'date_format:Y-m'],
        ]);
        $datumOd = CarbonImmutable::createFromFormat('Y-m', $period['mesec'])->startOfMonth();
        $datumDo = $datumOd->endOfMonth();

        $narudzbine = Narudzbina::with('stavke.raspodele.lot')
            ->where('status', NarudzbinaStatus::OTPREMLJENA->value)
            ->whereHas('stavke.raspodele.dogadjaji', function ($query) use ($datumOd, $datumDo) {
                $query
                    ->where('tip', LotDogadjajTip::KOLICINA_IZDATA->value)
                    ->whereBetween('vreme_dogadjaja', [$datumOd, $datumDo]);
            })
            ->get();

        $brojNarudzbina = $narudzbine->count();

        $ukupniPrihod = $narudzbine->sum(function ($narudzbina) {
            return $narudzbina->stavke->sum(function ($stavka) {
                return $stavka->kolicina * (float) $stavka->cena_po_jedinici;
            });
        });

        $izdateRaspodele = $narudzbine
            ->flatMap(fn ($narudzbina) => $narudzbina->stavke)
            ->flatMap(fn ($stavka) => $stavka->raspodele)
            ->where('status', LotRaspodelaStatus::IZDATO);
        $lotIds = $izdateRaspodele
            ->pluck('lot_id')
            ->unique()
            ->values();
        $lokacijaIds = LotDogadjaj::query()
            ->whereIn('lot_raspodela_id', $izdateRaspodele->pluck('id'))
            ->where('tip', LotDogadjajTip::KOLICINA_IZDATA->value)
            ->whereNotNull('prethodna_skladisna_lokacija_id')
            ->pluck('prethodna_skladisna_lokacija_id')
            ->unique();
        $skladisteIds = SkladisnaLokacija::query()
            ->whereIn('id', $lokacijaIds)
            ->pluck('skladiste_id')
            ->unique();

        $listaSkladista = Skladiste::query()
            ->whereIn('id', $skladisteIds)
            ->orderBy('naziv')
            ->get();
        $trosakSkladista = (float) $listaSkladista->sum('mesecni_trosak');

        $listaResursa = Resurs::query()
            ->with('lot:id,oznaka')
            ->whereIn('lot_id', $lotIds)
            ->orderBy('datum_upotrebe')
            ->orderBy('naziv')
            ->get();
        $ukupniTrosakResursa = $listaResursa->sum(function ($resurs) {
            return (float) $resurs->cena_po_jedinici * (float) $resurs->kolicina;
        });

        $ukupniRashod = $trosakSkladista + $ukupniTrosakResursa;
        $netoDobit = $ukupniPrihod - $ukupniRashod;

        return view('admin.finansije.prikaz', compact(
            'ukupniPrihod', 'ukupniRashod', 'netoDobit',
            'datumOd', 'datumDo', 'brojNarudzbina',
            'listaSkladista', 'listaResursa',
            'trosakSkladista', 'ukupniTrosakResursa'
        ));
    }
}
