<?php

namespace App\Http\Controllers;

use App\Enums\KlasaKvaliteta;
use App\Http\Requests\LotKorekcijaKolicineRequest;
use App\Http\Requests\LotKvalitetRequest;
use App\Http\Requests\LotPremestanjeRequest;
use App\Http\Requests\LotPrijemRequest;
use App\Http\Requests\LotStoreRequest;
use App\Http\Requests\ObavezanRazlogRequest;
use App\Models\Lot;
use App\Models\SkladisnaLokacija;
use App\Services\LotService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LotController extends Controller
{
    public function __construct(
        private readonly LotService $lotService
    ) {}

    public function store(LotStoreRequest $request): RedirectResponse
    {
        return $this->izvrsiServisnuOperaciju(
            fn () => $this->lotService->kreiraj($request->validated(), $request->user()),
            'Lot je uspešno kreiran.'
        );
    }

    public function primiUSkladiste(LotPrijemRequest $request, Lot $lot): RedirectResponse
    {
        $lokacija = SkladisnaLokacija::findOrFail($request->integer('skladisna_lokacija_id'));

        return $this->izvrsiServisnuOperaciju(
            fn () => $this->lotService->primiUSkladiste($lot, $lokacija, $request->user()),
            'Lot je uspešno primljen u skladište.'
        );
    }

    public function dodeliKlasuKvaliteta(LotKvalitetRequest $request, Lot $lot): RedirectResponse
    {
        return $this->izvrsiServisnuOperaciju(
            fn () => $this->lotService->dodeliKlasuKvaliteta(
                $lot,
                KlasaKvaliteta::from($request->string('klasa_kvaliteta')->value()),
                $request->string('broj_dokumenta_kvaliteta')->value(),
                $request->user()
            ),
            'Klasa kvaliteta je uspešno dodeljena.'
        );
    }

    public function odobriZaProdaju(Request $request, Lot $lot): RedirectResponse
    {
        return $this->izvrsiServisnuOperaciju(
            fn () => $this->lotService->odobriZaProdaju($lot, $request->user()),
            'Lot je uspešno odobren za prodaju.'
        );
    }

    public function premesti(LotPremestanjeRequest $request, Lot $lot): RedirectResponse
    {
        $lokacija = SkladisnaLokacija::findOrFail($request->integer('skladisna_lokacija_id'));

        return $this->izvrsiServisnuOperaciju(
            fn () => $this->lotService->premesti(
                $lot,
                $lokacija,
                $request->user(),
                $request->input('razlog')
            ),
            'Lot je uspešno premešten.'
        );
    }

    public function blokiraj(ObavezanRazlogRequest $request, Lot $lot): RedirectResponse
    {
        return $this->izvrsiServisnuOperaciju(
            fn () => $this->lotService->blokiraj($lot, $request->string('razlog')->value(), $request->user()),
            'Lot je uspešno blokiran.'
        );
    }

    public function odblokiraj(ObavezanRazlogRequest $request, Lot $lot): RedirectResponse
    {
        return $this->izvrsiServisnuOperaciju(
            fn () => $this->lotService->odblokiraj($lot, $request->string('razlog')->value(), $request->user()),
            'Lot je uspešno odblokiran.'
        );
    }

    public function povuci(ObavezanRazlogRequest $request, Lot $lot): RedirectResponse
    {
        return $this->izvrsiServisnuOperaciju(
            fn () => $this->lotService->povuci($lot, $request->string('razlog')->value(), $request->user()),
            'Lot je uspešno povučen.'
        );
    }

    public function korigujKolicinu(LotKorekcijaKolicineRequest $request, Lot $lot): RedirectResponse
    {
        return $this->izvrsiServisnuOperaciju(
            fn () => $this->lotService->korigujKolicinu(
                $lot,
                $request->integer('raspoloziva_kolicina_g'),
                $request->string('razlog')->value(),
                $request->user()
            ),
            'Količina lota je uspešno korigovana.'
        );
    }
}
