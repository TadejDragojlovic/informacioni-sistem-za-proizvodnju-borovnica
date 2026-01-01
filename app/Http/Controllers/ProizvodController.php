<?php

namespace App\Http\Controllers;

use App\Models\Proizvod;
use App\Models\Skladiste;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProizvodController extends Controller
{
    public function pocetna(): View
    {
        $proizvodi = Proizvod::all();

        return view('pocetna', compact('proizvodi'));
    }

    public function index(Request $request): View
    {
        $proizvods = Proizvod::all();

        return view('proizvod.index', [
            'proizvodi' => $proizvods,
        ]);
    }

    public function create(Request $request): View
    {
        $skladista = Skladiste::all();
        return view('proizvod.create', compact('skladista'));
    }

    public function store(Request $request): RedirectResponse
    {
        $valid = $request->validate([
            'naziv' => 'required|string|max:255',
            'opis' => 'nullable|string',
            'kolicina' => 'required|numeric|min:0',
            'cena' => 'required|numeric|min:0',
            'skladiste_id' => 'required|exists:skladistes,id',
        ]);

        Proizvod::create($valid);
        return redirect()->route('proizvod.index')->with('success', 'Proizvod dodat.');
    }

    public function show(Request $request, Proizvod $proizvod): Response
    {
        return view('proizvod.show', [
            'proizvod' => $proizvod,
        ]);
    }

    public function edit(Request $request, $id): View
    {
        $proizvod = Proizvod::findOrFail($id);
        $skladista = Skladiste::all();
        return view('proizvod.edit', compact('proizvod', 'skladista'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $valid = $request->validate([
            'naziv' => 'required|string|max:255',
            'opis' => 'nullable|string',
            'kolicina' => 'required|numeric|min:0',
            'cena' => 'required|numeric|min:0',
            'skladiste_id' => 'required|exists:skladistes,id',
        ]);

        $proizvod = Proizvod::findOrFail($id);
        $proizvod->update($valid);
        return redirect()->route('proizvod.index')->with('success', 'Proizvod ažuriran.');
    }

    public function destroy(Request $request, Proizvod $proizvod): RedirectResponse
    {
        $proizvod->delete();

        return redirect()->route('proizvod.index');
    }
}
