<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProizvodStoreRequest;
use App\Http\Requests\ProizvodUpdateRequest;
use App\Models\Proizvod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProizvodController extends Controller
{
    public function index(Request $request): Response
    {
        $proizvods = Proizvod::all();

        return view('proizvod.index', [
            'proizvods' => $proizvods,
        ]);
    }

    public function create(Request $request): Response
    {
        return view('proizvod.create');
    }

    public function store(ProizvodStoreRequest $request): Response
    {
        $proizvod = Proizvod::create($request->validated());

        $request->session()->flash('proizvod.id', $proizvod->id);

        return redirect()->route('proizvods.index');
    }

    public function show(Request $request, Proizvod $proizvod): Response
    {
        return view('proizvod.show', [
            'proizvod' => $proizvod,
        ]);
    }

    public function edit(Request $request, Proizvod $proizvod): Response
    {
        return view('proizvod.edit', [
            'proizvod' => $proizvod,
        ]);
    }

    public function update(ProizvodUpdateRequest $request, Proizvod $proizvod): Response
    {
        $proizvod->update($request->validated());

        $request->session()->flash('proizvod.id', $proizvod->id);

        return redirect()->route('proizvods.index');
    }

    public function destroy(Request $request, Proizvod $proizvod): Response
    {
        $proizvod->delete();

        return redirect()->route('proizvods.index');
    }
}
