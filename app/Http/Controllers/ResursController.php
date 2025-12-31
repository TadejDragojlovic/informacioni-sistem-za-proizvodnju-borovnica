<?php

namespace App\Http\Controllers;

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

    public function create(Request $request): Response
    {
        return view('resurs.create');
    }

    public function store(Request $request): Response
    {
        $resurs = Resurs::create($request->validated());

        $request->session()->flash('resurs.id', $resurs->id);

        return redirect()->route('resurs.index');
    }

    public function show(Request $request, Resurs $resurs): Response
    {
        return view('resurs.show', [
            'resurs' => $resurs,
        ]);
    }

    public function edit(Request $request, Resurs $resurs): Response
    {
        return view('resurs.edit', [
            'resurs' => $resurs,
        ]);
    }

    public function update(Request $request, Resurs $resurs): Response
    {
        $resurs->update($request->validated());

        $request->session()->flash('resurs.id', $resurs->id);

        return redirect()->route('resurs.index');
    }

    public function destroy(Request $request, Resurs $resurs): Response
    {
        $resurs->delete();

        return redirect()->route('resurs.index');
    }
}
