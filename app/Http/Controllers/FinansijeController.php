<?php

namespace App\Http\Controllers;

use App\Enums\LotRaspodelaStatus;
use App\Enums\NarudzbinaStatus;
use App\Models\Narudzbina;
use App\Models\Resurs;
use App\Models\Skladiste;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FinansijeController extends Controller
{
    public function create(): View
    {
        $prva = Narudzbina::query()
            ->where('status', NarudzbinaStatus::OTPREMLJENA->value)
            ->oldest('created_at')
            ->first(['created_at']);
        $poslednja = Narudzbina::query()
            ->where('status', NarudzbinaStatus::OTPREMLJENA->value)
            ->latest('created_at')
            ->first(['created_at']);

        return view('admin.finansije.create', [
            'podrazumevaniDatumOd' => $prva?->created_at->toDateString() ?? now()->startOfMonth()->toDateString(),
            'podrazumevaniDatumDo' => $poslednja?->created_at->toDateString() ?? now()->toDateString(),
        ]);
    }

    public function generate(Request $request): View
    {
        $period = $request->validate([
            'datum_od' => ['required', 'date'],
            'datum_do' => ['required', 'date', 'after_or_equal:datum_od'],
        ]);
        $datumOd = CarbonImmutable::parse($period['datum_od'])->startOfDay();
        $datumDo = CarbonImmutable::parse($period['datum_do'])->endOfDay();

        $narudzbine = Narudzbina::with('stavke.raspodele.lot')
            ->where('status', NarudzbinaStatus::OTPREMLJENA->value)
            ->whereBetween('created_at', [$datumOd, $datumDo])
            ->get();

        $brojNarudzbina = $narudzbine->count();

        $ukupniPrihod = $narudzbine->sum(function ($narudzbina) {
            return $narudzbina->stavke->sum(function ($stavka) {
                return $stavka->kolicina * (float) $stavka->cena_po_jedinici;
            });
        });

        $lotIds = $narudzbine
            ->flatMap(fn ($narudzbina) => $narudzbina->stavke)
            ->flatMap(fn ($stavka) => $stavka->raspodele)
            ->where('status', LotRaspodelaStatus::IZDATO)
            ->pluck('lot_id')
            ->unique()
            ->values();

        $listaSkladista = Skladiste::query()
            ->whereHas('skladisneLokacije.lotovi', function ($query) use ($lotIds): void {
                $query->whereIn('lots.id', $lotIds);
            })
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
