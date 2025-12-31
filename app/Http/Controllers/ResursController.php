<?php

namespace App\Http\Controllers;

use App\Http\Requests\ResurStoreRequest;
use App\Http\Requests\ResurUpdateRequest;
use App\Models\Resurs;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ResursController extends Controller
{
    public function index(Request $request): Response
    {
        $resurs = Resur::all();

        return view('resur.index', [
            'resurs' => $resurs,
        ]);
    }

    public function create(Request $request): Response
    {
        return view('resur.create');
    }

    public function store(ResurStoreRequest $request): Response
    {
        $resur = Resur::create($request->validated());

        $request->session()->flash('resur.id', $resur->id);

        return redirect()->route('resurs.index');
    }

    public function show(Request $request, Resur $resur): Response
    {
        return view('resur.show', [
            'resur' => $resur,
        ]);
    }

    public function edit(Request $request, Resur $resur): Response
    {
        return view('resur.edit', [
            'resur' => $resur,
        ]);
    }

    public function update(ResurUpdateRequest $request, Resur $resur): Response
    {
        $resur->update($request->validated());

        $request->session()->flash('resur.id', $resur->id);

        return redirect()->route('resurs.index');
    }

    public function destroy(Request $request, Resur $resur): Response
    {
        $resur->delete();

        return redirect()->route('resurs.index');
    }
}
