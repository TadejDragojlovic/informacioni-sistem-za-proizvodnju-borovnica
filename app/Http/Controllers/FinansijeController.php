<?php

namespace App\Http\Controllers;

use App\Models\Narudzbina;
use App\Models\Resurs;
use App\Models\Skladiste;
use Illuminate\Http\Request;

class FinansijeController extends Controller
{
    public function index()
    {
        return '[ADMIN] Stranica za finansijski pregled';
    }

    public function create()
    {
        $prva = Narudzbina::orderBy('datum_narudzbine', 'asc')->first();
        $poslednja = Narudzbina::orderBy('datum_narudzbine', 'desc')->first();

        return view('admin.finansije.create', compact('prva', 'poslednja'));
    }

    public function generate(Request $request)
    {
        $od = $request->input('datum_od');
        $do = $request->input('datum_do');

        $narudzbine = Narudzbina::with('stavke')
            ->where('status', 'isporucena')
            ->whereBetween('datum_narudzbine', [$od, $do])
            ->get();

        $brojNarudzbina = $narudzbine->count();

        // racunanje prihoda za svaku stavku, tako sto mnozimo kolicinu proizvoda * cena proizvoda
        $ukupniPrihod = $narudzbine->sum(function ($narudzbina) {
            return $narudzbina->stavke->sum(function ($stavka) {
                return $stavka->kolicina * $stavka->proizvod->cena;
            });
        });

        // id prodatih proizvoda
        $prodatiProizvodiIds = $narudzbine->flatMap(function ($n) {
            return $n->stavke->pluck('proizvod_id');
        })->unique();

        // trosak skladista
        $listaSkladista = Skladiste::whereHas('proizvods', function ($q) use ($prodatiProizvodiIds) {
            $q->whereIn('id', $prodatiProizvodiIds);
        })->get();
        $trosakSkladista = (float) $listaSkladista->sum('trosak');

        // trosak resursa
        $listaResursa = Resurs::whereIn('proizvod_id', $prodatiProizvodiIds)->get();
        $ukupniTrosakResursa = $listaResursa->sum(function ($resurs) {
            return $resurs->trosak * $resurs->kolicina;
        });

        // izracunavanje
        $ukupniRashod = $trosakSkladista + $ukupniTrosakResursa;
        $netoDobit = $ukupniPrihod - $ukupniRashod;

        return view('admin.finansije.prikaz', compact(
            'ukupniPrihod', 'ukupniRashod', 'netoDobit',
            'od', 'do', 'brojNarudzbina',
            'listaSkladista', 'listaResursa'
        ));
    }
}
