<?php

namespace App\Http\Controllers;

use App\Models\Proizvod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
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

    public function dodaj(Request $request, int $id): RedirectResponse
    {
        $validirano = $request->validate([
            'kolicina' => ['required', 'integer', 'min:1', 'max:100'],
        ]);
        $proizvod = Proizvod::query()->whereKey($id)->where('aktivan', true)->first();

        if ($proizvod === null) {
            return redirect()->back()->withErrors(['korpa' => 'Izabrani proizvod više nije dostupan.']);
        }

        $korpa = session()->get('korpa', []);
        $dodataKolicina = (int) $validirano['kolicina'];

        if (isset($korpa[$id])) {
            $korpa[$id]['kolicina'] = min(100, $korpa[$id]['kolicina'] + $dodataKolicina);
        } else {
            $korpa[$id] = [
                'naziv' => $proizvod->naziv,
                'kolicina' => $dodataKolicina,
                'cena' => $proizvod->cena,
                'neto_kolicina_g' => $proizvod->neto_kolicina_g,
            ];
        }

        session()->put('korpa', $korpa);

        return redirect()->back()->with('success', 'Dodato u korpu!');
    }

    public function azuriraj(Request $request, int $id): RedirectResponse
    {
        $validirano = $request->validate([
            'akcija' => ['required', Rule::in(['plus', 'minus'])],
        ]);
        $korpa = session()->get('korpa', []);
        $akcija = $validirano['akcija'];

        if (isset($korpa[$id])) {
            if ($akcija === 'plus' && $korpa[$id]['kolicina'] < 100) {
                $korpa[$id]['kolicina']++;
            } elseif ($akcija === 'minus' && $korpa[$id]['kolicina'] > 1) {
                $korpa[$id]['kolicina']--;
            }
            session()->put('korpa', $korpa);
        }

        return redirect()->back();
    }

    public function obrisi(int $id): RedirectResponse
    {
        $korpa = session()->get('korpa', []);

        if (isset($korpa[$id])) {
            unset($korpa[$id]);
            session()->put('korpa', $korpa);
        }

        return redirect()->back()->with('success', 'Stavka uklonjena iz korpe.');
    }
}
