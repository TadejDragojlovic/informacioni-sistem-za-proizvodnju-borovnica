<?php

namespace App\Http\Controllers;

use App\Models\Proizvod;
use App\Models\Resurs;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ResursController extends Controller
{
    public function index(Request $request): View
    {
        $resursi = Resurs::all();

        return view('resurs.index', [
            'resursi' => $resursi,
        ]);
    }

    public function create(Request $request): View
    {
        $proizvodi = Proizvod::all();

        return view('resurs.create', compact('proizvodi'));
    }

    public function store(Request $request): RedirectResponse
    {
        $valid = $request->validate([
            'naziv' => 'required|string',
            'kolicina' => 'required|numeric',
            'trosak' => 'required|numeric',
            'proizvod_id' => 'required|exists:proizvods,id',
        ]);

        Resurs::create($valid);

        return redirect()->route('resurs.index');
    }

    public function show(Request $request, Resurs $resurs): Response
    {
        return view('resurs.show', [
            'resurs' => $resurs,
        ]);
    }

    public function edit(Request $request, Resurs $resur): View
    {
        $proizvodi = Proizvod::all();

        return view('resurs.edit', compact('resur', 'proizvodi'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $valid = $request->validate([
            'naziv' => 'required|string',
            'kolicina' => 'required|numeric',
            'trosak' => 'required|numeric',
            'proizvod_id' => 'required|exists:proizvods,id',
        ]);

        $resurs = Resurs::findOrFail($id);
        $resurs->update($valid);

        return redirect()->route('resurs.index')->with('success', 'Resurs uspesno izmenjen.');
    }

    public function destroy(Request $request, Resurs $resur): RedirectResponse
    {
        $resur->delete();

        return redirect()->route('resurs.index');
    }
}
