<?php

namespace App\Http\Controllers;

use App\Enums\NarudzbinaStatus;
use App\Models\Narudzbina;
use App\Models\Resurs;
use App\Models\Skladiste;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FinansijeController extends Controller
{
    public function index()
    {
        return '[ADMIN] Stranica za finansijski pregled';
    }

    public function create(): View
    {
        $prva = Narudzbina::oldest()->first();
        $poslednja = Narudzbina::latest()->first();

        return view('admin.finansije.create', compact('prva', 'poslednja'));
    }

    public function generate(Request $request): View
    {
        $period = $request->validate([
            'datum_od' => ['required', 'date'],
            'datum_do' => ['required', 'date', 'after_or_equal:datum_od'],
        ]);
        $od = $period['datum_od'];
        $do = $period['datum_do'];

        $narudzbine = Narudzbina::with('stavke.raspodele.lot')
            ->where('status', NarudzbinaStatus::OTPREMLJENA)
            ->whereBetween('created_at', ["{$od} 00:00:00", "{$do} 23:59:59"])
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
            ->pluck('lot_id')
            ->unique();

        $listaSkladista = Skladiste::whereHas('skladisneLokacije.lotovi', function ($query) use ($lotIds) {
            $query->whereIn('lots.id', $lotIds);
        })->get();
        $trosakSkladista = (float) $listaSkladista->sum('mesecni_trosak');

        $listaResursa = Resurs::whereIn('lot_id', $lotIds)->get();
        $ukupniTrosakResursa = $listaResursa->sum(function ($resurs) {
            return (float) $resurs->cena_po_jedinici * (float) $resurs->kolicina;
        });

        $ukupniRashod = $trosakSkladista + $ukupniTrosakResursa;
        $netoDobit = $ukupniPrihod - $ukupniRashod;

        return view('admin.finansije.prikaz', compact(
            'ukupniPrihod', 'ukupniRashod', 'netoDobit',
            'od', 'do', 'brojNarudzbina',
            'listaSkladista', 'listaResursa'
        ));
    }
}
