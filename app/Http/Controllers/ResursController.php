<?php

namespace App\Http\Controllers;

use App\Http\Requests\ResurStoreRequest;
use App\Http\Requests\ResurUpdateRequest;
use App\Models\Lot;
use App\Models\Resurs;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ResursController extends Controller
{
    public function index(Request $request): View
    {
        $resursi = Resurs::with(['lot', 'evidentiraoUser'])->get();

        return view('resurs.index', [
            'resursi' => $resursi,
        ]);
    }

    public function create(Request $request): View
    {
        $lotovi = Lot::orderBy('oznaka')->get();

        return view('resurs.create', compact('lotovi'));
    }

    public function store(ResurStoreRequest $request): RedirectResponse
    {
        Resurs::create(array_merge($request->validated(), [
            'evidentirao_user_id' => $request->user()->id,
        ]));

        return redirect()->route('resurs.index');
    }

    public function show(Resurs $resurs): View
    {
        return view('resurs.show', [
            'resurs' => $resurs,
        ]);
    }

    public function edit(Resurs $resur): View
    {
        $lotovi = Lot::orderBy('oznaka')->get();

        return view('resurs.edit', compact('resur', 'lotovi'));
    }

    public function update(ResurUpdateRequest $request, $id): RedirectResponse
    {
        $resurs = Resurs::findOrFail($id);
        $resurs->update($request->validated());

        return redirect()->route('resurs.index')->with('success', 'Resurs uspesno izmenjen.');
    }

    public function destroy(Request $request, Resurs $resur): RedirectResponse
    {
        $resur->delete();

        return redirect()->route('resurs.index');
    }
}
