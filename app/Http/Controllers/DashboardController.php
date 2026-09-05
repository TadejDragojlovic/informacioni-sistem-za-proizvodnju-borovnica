<?php

namespace App\Http\Controllers;

use App\Enums\LotStatus;
use App\Enums\NarudzbinaStatus;
use App\Enums\UserRole;
use App\Models\Lot;
use App\Models\Narudzbina;
use App\Models\Skladiste;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        if ($user->role === UserRole::KUPAC) {
            $osnovniUpit = $user->narudzbine();

            return view('dashboard', [
                'statistika' => [
                    ['naziv' => 'Sve narudžbine', 'vrednost' => (clone $osnovniUpit)->count()],
                    ['naziv' => 'Potvrđene', 'vrednost' => (clone $osnovniUpit)->where('status', NarudzbinaStatus::POTVRDJENA->value)->count()],
                    ['naziv' => 'Otpremljene', 'vrednost' => (clone $osnovniUpit)->where('status', NarudzbinaStatus::OTPREMLJENA->value)->count()],
                    ['naziv' => 'Pakovanja u korpi', 'vrednost' => collect($request->session()->get('korpa', []))->sum('kolicina')],
                ],
                'akcije' => [
                    ['naziv' => 'Ponuda proizvoda', 'opis' => 'Pregledajte dostupna pakovanja borovnica.', 'url' => route('home')],
                    ['naziv' => 'Moja korpa', 'opis' => 'Proverite proizvode pre potvrđivanja narudžbine.', 'url' => route('korpa.index')],
                    ['naziv' => 'Moje narudžbine', 'opis' => 'Pratite status i lotove svojih narudžbina.', 'url' => route('user.orders')],
                ],
                'nedavneNarudzbine' => (clone $osnovniUpit)->with('stavke')->latest()->limit(3)->get(),
            ]);
        }

        $akcije = [
            ['naziv' => 'Narudžbine', 'opis' => 'Rezervišite robu i obradite otpremu.', 'url' => route('narudzbine.index')],
            ['naziv' => 'Lotovi', 'opis' => 'Vodite proizvodnju, skladištenje i sledljivost.', 'url' => route('lotovi.index')],
            ['naziv' => 'Resursi', 'opis' => 'Evidentirajte resurse i troškove po lotu.', 'url' => route('resurs.index')],
            ['naziv' => 'Skladišta', 'opis' => 'Pregledajte skladišta i njihove lokacije.', 'url' => route('skladiste.index')],
            ['naziv' => 'Proizvodi', 'opis' => 'Upravljajte prodajnim pakovanjima.', 'url' => route('proizvod.index')],
        ];

        if ($user->role === UserRole::ADMIN) {
            $akcije[] = ['naziv' => 'Finansije', 'opis' => 'Generišite finansijski pregled za izabrani mesec.', 'url' => route('admin.finansije.create')];
        }

        return view('dashboard', [
            'statistika' => [
                ['naziv' => 'Potvrđene narudžbine', 'vrednost' => Narudzbina::where('status', NarudzbinaStatus::POTVRDJENA->value)->count()],
                ['naziv' => 'Raspoloživi lotovi', 'vrednost' => Lot::where('status', LotStatus::RASPOLOZIV->value)->count()],
                ['naziv' => 'Blokirani lotovi', 'vrednost' => Lot::where('status', LotStatus::BLOKIRAN->value)->count()],
                ['naziv' => 'Aktivna skladišta', 'vrednost' => Skladiste::where('aktivan', true)->count()],
            ],
            'akcije' => $akcije,
            'nedavneNarudzbine' => Narudzbina::query()
                ->with('user')
                ->where('status', NarudzbinaStatus::POTVRDJENA->value)
                ->latest()
                ->limit(3)
                ->get(),
        ]);
    }
}
