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

    public function create(Request $request): Response
    {
        return view('skladiste.create');
    }

    public function store(Request $request): Response
    {
        $skladiste = Skladiste::create($request->validated());

        $request->session()->flash('skladiste.id', $skladiste->id);

        return redirect()->route('skladistes.index');
    }

    public function show(Request $request, Skladiste $skladiste): Response
    {
        return view('skladiste.show', [
            'skladiste' => $skladiste,
        ]);
    }

    public function edit(Request $request, Skladiste $skladiste): Response
    {
        return view('skladiste.edit', [
            'skladiste' => $skladiste,
        ]);
    }

    public function update(Request $request, Skladiste $skladiste): Response
    {
        $skladiste->update($request->validated());

        $request->session()->flash('skladiste.id', $skladiste->id);

        return redirect()->route('skladistes.index');
    }

    public function destroy(Request $request, Skladiste $skladiste): Response
    {
        $skladiste->delete();

        return redirect()->route('skladistes.index');
    }
}
