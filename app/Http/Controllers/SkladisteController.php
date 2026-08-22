<?php

namespace App\Http\Controllers;

use App\Http\Requests\SkladisteStoreRequest;
use App\Http\Requests\SkladisteUpdateRequest;
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

    public function store(SkladisteStoreRequest $request): RedirectResponse
    {
        Skladiste::create($request->validated());

        return redirect()->route('skladiste.index')->with('success', 'Skladište kreirano.');
    }

    public function show(Skladiste $skladiste): View
    {
        return view('skladiste.show', [
            'skladiste' => $skladiste,
        ]);
    }

    public function edit(Skladiste $skladiste): View
    {
        return view('skladiste.edit', [
            'skladiste' => $skladiste,
        ]);
    }

    public function update(SkladisteUpdateRequest $request, $id): RedirectResponse
    {
        Skladiste::findOrFail($id)->update($request->validated());

        return redirect()->route('skladiste.index')->with('success', 'Skladište ažurirano.');
    }

    public function destroy(Request $request, Skladiste $skladiste): RedirectResponse
    {
        $skladiste->delete();

        return redirect()->route('skladiste.index');
    }
}
