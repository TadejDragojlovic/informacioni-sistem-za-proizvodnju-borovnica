<?php

namespace App\Http\Controllers;

use App\Enums\NarudzbinaStatus;
use App\Http\Requests\NarudzbinaStoreRequest;
use App\Http\Requests\NarudzbinaUpdateRequest;
use App\Models\Narudzbina;
use App\Models\Proizvod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class NarudzbinaController extends Controller
{
    public function index(Request $request): View
    {
        $narudzbinas = Narudzbina::with('user')->latest()->get();

        return view('narudzbina.index', [
            'narudzbine' => $narudzbinas,
        ]);
    }

    public function create(): View
    {
        return view('narudzbina.create');
    }

    public function store(NarudzbinaStoreRequest $request): RedirectResponse
    {
        $narudzbina = Narudzbina::create($request->validated());

        $request->session()->flash('narudzbina.id', $narudzbina->id);

        return redirect()->route('narudzbine.index');
    }

    public function show($id): View
    {
        $narudzbina = Narudzbina::with(['user', 'stavke.proizvod'])->findOrFail($id);

        return view('narudzbina.show', compact('narudzbina'));
    }

    public function edit($id): View
    {
        $narudzbina = Narudzbina::findOrFail($id);
        $statusi = array_map(
            fn (NarudzbinaStatus $status) => $status->value,
            NarudzbinaStatus::cases()
        );

        return view('narudzbina.edit', [
            'narudzbina' => $narudzbina,
            'statusi' => $statusi,
        ]);
    }

    public function update(NarudzbinaUpdateRequest $request, $id): RedirectResponse
    {
        $narudzbina = Narudzbina::findOrFail($id);
        $narudzbina->update($request->validated());

        return redirect()->route('narudzbine.index')->with('success', 'Status uspesno izmenjen.');
    }

    public function destroy($id): RedirectResponse
    {
        $narudzbina = Narudzbina::findOrFail($id);
        $narudzbina->update(['status' => NarudzbinaStatus::OTKAZANA]);

        return redirect()->route('narudzbine.index');
    }

    public function mojeNarudzbine()
    {
        $narudzbine = Narudzbina::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->with('stavke')
            ->get();

        return view('narudzbina.moje', compact('narudzbine'));
    }

    // potvrdjivanje narudzbine / korpe
    public function potvrdi(Request $request): RedirectResponse
    {
        $korpa = session()->get('korpa');

        if (! $korpa) {
            return redirect()->back();
        }

        $validirano = $request->validate([
            'adresa_isporuke' => ['required', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($korpa, $validirano): void {
            $narudzbina = Narudzbina::create([
                'user_id' => auth()->id(),
                'status' => NarudzbinaStatus::POTVRDJENA,
                'adresa_isporuke' => $validirano['adresa_isporuke'],
            ]);

            foreach ($korpa as $proizvodId => $detalji) {
                $proizvod = Proizvod::findOrFail($proizvodId);

                $narudzbina->stavke()->create([
                    'proizvod_id' => $proizvod->id,
                    'kolicina' => $detalji['kolicina'],
                    'neto_kolicina_g' => $proizvod->neto_kolicina_g,
                    'cena_po_jedinici' => $proizvod->cena,
                ]);
            }
        });

        session()->forget('korpa');

        return redirect()->route('user.orders')->with('success', 'Narudžbina uspešno kreirana!');
    }
}
