<?php

namespace App\Http\Controllers;

use App\Http\Requests\NarudzbinaStoreRequest;
use App\Http\Requests\NarudzbinaUpdateRequest;
use App\Models\Narudzbina;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NarudzbinaController extends Controller
{
    public function index(Request $request): Response
    {
        $narudzbinas = Narudzbina::all();

        return view('narudzbina.index', [
            'narudzbinas' => $narudzbinas,
        ]);
    }

    public function create(Request $request): Response
    {
        return view('narudzbina.create');
    }

    public function store(NarudzbinaStoreRequest $request): Response
    {
        $narudzbina = Narudzbina::create($request->validated());

        $request->session()->flash('narudzbina.id', $narudzbina->id);

        return redirect()->route('narudzbinas.index');
    }

    public function show(Request $request, Narudzbina $narudzbina): Response
    {
        return view('narudzbina.show', [
            'narudzbina' => $narudzbina,
        ]);
    }

    public function edit(Request $request, Narudzbina $narudzbina): Response
    {
        return view('narudzbina.edit', [
            'narudzbina' => $narudzbina,
        ]);
    }

    public function update(NarudzbinaUpdateRequest $request, Narudzbina $narudzbina): Response
    {
        $narudzbina->update($request->validated());

        $request->session()->flash('narudzbina.id', $narudzbina->id);

        return redirect()->route('narudzbinas.index');
    }

    public function destroy(Request $request, Narudzbina $narudzbina): Response
    {
        $narudzbina->delete();

        return redirect()->route('narudzbinas.index');
    }
}
