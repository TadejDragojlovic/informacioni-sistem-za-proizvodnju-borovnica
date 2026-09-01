<?php

namespace App\Http\Controllers;

use App\Http\Requests\SkladisnaLokacijaStoreRequest;
use App\Http\Requests\SkladisnaLokacijaUpdateRequest;
use App\Models\SkladisnaLokacija;
use App\Models\Skladiste;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SkladisnaLokacijaController extends Controller
{
    public function create(Skladiste $skladiste): View
    {
        return view('skladisna-lokacija.create', compact('skladiste'));
    }

    public function store(SkladisnaLokacijaStoreRequest $request, Skladiste $skladiste): RedirectResponse
    {
        $skladiste->skladisneLokacije()->create($request->validated());

        return redirect()
            ->route('skladiste.show', $skladiste)
            ->with('success', 'Skladišna lokacija kreirana.');
    }

    public function edit(Skladiste $skladiste, SkladisnaLokacija $skladisnaLokacija): View
    {
        $this->ensureBelongsToSkladiste($skladiste, $skladisnaLokacija);

        return view('skladisna-lokacija.edit', compact('skladiste', 'skladisnaLokacija'));
    }

    public function update(
        SkladisnaLokacijaUpdateRequest $request,
        Skladiste $skladiste,
        SkladisnaLokacija $skladisnaLokacija
    ): RedirectResponse {
        $this->ensureBelongsToSkladiste($skladiste, $skladisnaLokacija);
        $skladisnaLokacija->update($request->validated());

        return redirect()
            ->route('skladiste.show', $skladiste)
            ->with('success', 'Skladišna lokacija ažurirana.');
    }

    private function ensureBelongsToSkladiste(
        Skladiste $skladiste,
        SkladisnaLokacija $skladisnaLokacija
    ): void {
        abort_unless($skladisnaLokacija->skladiste_id === $skladiste->id, 404);
    }
}
