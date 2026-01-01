<?php

namespace App\Http\Controllers;

use App\Models\Proizvod;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KorpaController extends Controller
{
    // prikaz korpe za kupca
    public function index(): View
    {
        $korpa = session()->get('korpa', []);
        $ukupno = 0;
        foreach ($korpa as $stavka) {
            $ukupno += $stavka['cena'] * $stavka['kolicina'];
        }

        return view('korpa.index', compact('korpa', 'ukupno'));
    }

    public function dodaj(Request $request, $id)
    {
        $proizvod = Proizvod::findOrFail($id);
        $korpa = session()->get('korpa', []);

        $dodatakolicina = $request->input('kolicina', 1);

        if (isset($korpa[$id])) {
            $korpa[$id]['kolicina'] += $dodatakolicina;
        } else {
            $korpa[$id] = [
                'naziv' => $proizvod->naziv,
                'kolicina' => $dodatakolicina,
                'cena' => $proizvod->cena,
            ];
        }

        session()->put('korpa', $korpa);

        return redirect()->back()->with('success', 'Dodato u korpu!');
    }

    public function azuriraj(Request $request, $id)
    {
        $korpa = session()->get('korpa');
        $akcija = $request->input('akcija');

        if (isset($korpa[$id])) {
            if ($akcija == 'plus') {
                $korpa[$id]['kolicina']++;
            } elseif ($akcija == 'minus' && $korpa[$id]['kolicina'] > 1) {
                $korpa[$id]['kolicina']--;
            }
            session()->put('korpa', $korpa);
        }

        return redirect()->back();
    }

    public function obrisi($id)
    {
        $korpa = session()->get('korpa');

        if (isset($korpa[$id])) {
            unset($korpa[$id]);
            session()->put('korpa', $korpa);
        }

        return redirect()->back()->with('success', 'Stavka uklonjena iz korpe.');
    }
}
