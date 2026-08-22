<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProizvodStoreRequest;
use App\Http\Requests\ProizvodUpdateRequest;
use App\Models\Proizvod;
use App\Models\Sorta;
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
        $sorte = Sorta::orderBy('naziv')->get();

        return view('proizvod.create', compact('sorte'));
    }

    public function store(ProizvodStoreRequest $request): RedirectResponse
    {
        Proizvod::create($request->validated());

        return redirect()->route('proizvod.index')->with('success', 'Proizvod dodat.');
    }

    public function show(Proizvod $proizvod): View
    {
        return view('proizvod.show', [
            'proizvod' => $proizvod,
        ]);
    }

    public function edit($id): View
    {
        $proizvod = Proizvod::findOrFail($id);
        $sorte = Sorta::orderBy('naziv')->get();

        return view('proizvod.edit', compact('proizvod', 'sorte'));
    }

    public function update(ProizvodUpdateRequest $request, $id): RedirectResponse
    {
        $proizvod = Proizvod::findOrFail($id);
        $proizvod->update($request->validated());

        return redirect()->route('proizvod.index')->with('success', 'Proizvod ažuriran.');
    }

    public function destroy(Request $request, Proizvod $proizvod): RedirectResponse
    {
        $proizvod->delete();

        return redirect()->route('proizvod.index');
    }
}
