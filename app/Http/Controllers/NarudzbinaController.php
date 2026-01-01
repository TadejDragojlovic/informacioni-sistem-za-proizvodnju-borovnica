<?php

namespace App\Http\Controllers;

use App\Models\Narudzbina;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NarudzbinaController extends Controller
{
    public function index(Request $request): View
    {
        $narudzbinas = Narudzbina::all();

        return view('narudzbina.index', [
            'narudzbine' => $narudzbinas,
        ]);
    }

    public function create(Request $request): Response
    {
        return view('narudzbina.create');
    }

    public function store(Request $request): Response
    {
        $narudzbina = Narudzbina::create($request->validated());

        $request->session()->flash('narudzbina.id', $narudzbina->id);

        return redirect()->route('narudzbinas.index');
    }

    public function show($id): View
    {
        $narudzbina = Narudzbina::with(['user', 'stavke.proizvod'])->findOrFail($id);

        return view('narudzbina.show', compact('narudzbina'));
    }

    public function edit(Request $request, $id): View
    {
        $narudzbina = Narudzbina::findOrFail($id);
        $statusi = ['kreirana', 'potvrdjena', 'u_obradi', 'otpremljena', 'isporucena', 'otkazana', 'vracena'];
        
        return view('narudzbina.edit', [
            'narudzbina' => $narudzbina,
            'statusi' => $statusi
        ]);
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $valid = $request->validate([
            'status' => 'required|string'
        ]);

        $narudzbina = Narudzbina::findOrFail($id);
        $narudzbina->update($valid);

        return redirect()->route('narudzbine.index')->with('success', 'Status uspesno izmenjen.');
    }

    public function destroy($id): RedirectResponse
    {
        $narudzbina = Narudzbina::findOrFail($id);
        $narudzbina->delete();

        return redirect()->route('narudzbine.index');
    }
}
