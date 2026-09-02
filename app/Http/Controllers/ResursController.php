<?php

namespace App\Http\Controllers;

use App\Http\Requests\ResurStoreRequest;
use App\Http\Requests\ResurUpdateRequest;
use App\Models\Lot;
use App\Models\Resurs;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ResursController extends Controller
{
    public function index(Request $request): View
    {
        $pretraga = trim($request->string('pretraga')->value());
        $lotId = $request->integer('lot_id');

        $resursi = Resurs::query()
            ->with(['lot.sorta', 'evidentiraoUser'])
            ->when($pretraga !== '', fn ($query) => $query->where('naziv', 'like', "%{$pretraga}%"))
            ->when($lotId > 0, fn ($query) => $query->where('lot_id', $lotId))
            ->orderByDesc('datum_upotrebe')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $lotovi = $this->lotoviZaIzbor();

        return view('resurs.index', [
            'resursi' => $resursi,
            'lotovi' => $lotovi,
        ]);
    }

    public function create(): View
    {
        $lotovi = $this->lotoviZaIzbor();

        return view('resurs.create', compact('lotovi'));
    }

    public function store(ResurStoreRequest $request): RedirectResponse
    {
        Resurs::create(array_merge($request->validated(), [
            'evidentirao_user_id' => $request->user()->id,
        ]));

        return redirect()->route('resurs.index')->with('success', 'Resurs je uspešno evidentiran.');
    }

    public function show(Resurs $resur): View
    {
        return view('resurs.show', [
            'resurs' => $resur->load(['lot.sorta', 'lot.parcela', 'evidentiraoUser']),
        ]);
    }

    public function edit(Resurs $resur): View
    {
        $lotovi = $this->lotoviZaIzbor();

        return view('resurs.edit', compact('resur', 'lotovi'));
    }

    public function update(ResurUpdateRequest $request, Resurs $resur): RedirectResponse
    {
        $resur->update($request->validated());

        return redirect()->route('resurs.show', $resur)->with('success', 'Resurs je uspešno izmenjen.');
    }

    public function destroy(Resurs $resur): RedirectResponse
    {
        $resur->delete();

        return redirect()->route('resurs.index')->with('success', 'Resurs je obrisan.');
    }

    /** @return Collection<int, Lot> */
    private function lotoviZaIzbor(): Collection
    {
        return Lot::query()
            ->with('sorta')
            ->orderByDesc('datum_berbe')
            ->orderBy('oznaka')
            ->get();
    }
}
