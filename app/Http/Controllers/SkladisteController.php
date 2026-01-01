<?php

namespace App\Http\Controllers;

use App\Models\Skladiste;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SkladisteController extends Controller
{
    public function index(Request $request): View
    {
        $skladistes = Skladiste::all();

        return view('skladiste.index', [
            'skladista' => $skladistes,
        ]);
    }

    public function create(Request $request): View
    {
        return view('skladiste.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $valid = $request->validate([
            'lokacija' => 'required|string',
            'kapacitet' => 'required|numeric',
            'temperatura' => 'required|numeric',
            'trosak' => 'required|numeric'
        ]);
        
        Skladiste::create($valid);
        return redirect()->route('skladiste.index')->with('success', 'Skladište kreirano.');
    }

    public function show(Request $request, Skladiste $skladiste): Response
    {
        return view('skladiste.show', [
            'skladiste' => $skladiste,
        ]);
    }

    public function edit(Request $request, Skladiste $skladiste): View
    {
        return view('skladiste.edit', [
            'skladiste' => $skladiste,
        ]);
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $valid = $request->validate([
            'lokacija' => 'required|string',
            'kapacitet' => 'required|numeric',
            'temperatura' => 'required|numeric',
            'trosak' => 'required|numeric'
        ]);
        
        Skladiste::findOrFail($id)->update($valid);
        return redirect()->route('skladiste.index')->with('success', 'Skladište ažurirano.');
    }

    public function destroy(Request $request, Skladiste $skladiste): RedirectResponse
    {
        $skladiste->delete();

        return redirect()->route('skladiste.index');
    }
}
