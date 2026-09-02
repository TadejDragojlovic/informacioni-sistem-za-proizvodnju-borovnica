<?php

namespace App\Http\Controllers;

use App\Enums\KlasaKvaliteta;
use App\Enums\LotStatus;
use App\Http\Requests\LotKorekcijaKolicineRequest;
use App\Http\Requests\LotKvalitetRequest;
use App\Http\Requests\LotPremestanjeRequest;
use App\Http\Requests\LotPrijemRequest;
use App\Http\Requests\LotStoreRequest;
use App\Http\Requests\ObavezanRazlogRequest;
use App\Models\Lot;
use App\Models\Parcela;
use App\Models\SkladisnaLokacija;
use App\Models\Sorta;
use App\Services\LotService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LotController extends Controller
{
    public function __construct(
        private readonly LotService $lotService
    ) {}

    public function index(Request $request): View
    {
        $status = LotStatus::tryFrom($request->string('status')->value());
        $pretraga = trim($request->string('pretraga')->value());

        $lotovi = Lot::query()
            ->with(['sorta', 'parcela', 'trenutnaSkladisnaLokacija.skladiste'])
            ->when($status, fn ($query) => $query->where('status', $status->value))
            ->when($request->filled('sorta_id'), fn ($query) => $query->where('sorta_id', $request->integer('sorta_id')))
            ->when($pretraga !== '', fn ($query) => $query->where('oznaka', 'like', "%{$pretraga}%"))
            ->orderByDesc('datum_berbe')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('lot.index', [
            'lotovi' => $lotovi,
            'sorte' => Sorta::query()->orderBy('naziv')->get(),
            'statusi' => LotStatus::cases(),
        ]);
    }

    public function create(): View
    {
        return view('lot.create', [
            'sorte' => Sorta::query()->orderBy('naziv')->get(),
            'parcele' => Parcela::query()->orderBy('oznaka')->get(),
        ]);
    }

    public function show(Lot $lot): View
    {
        $lot->load(['sorta', 'parcela', 'trenutnaSkladisnaLokacija.skladiste']);

        $dogadjaji = $lot->dogadjaji()
            ->with([
                'evidentiraoUser',
                'prethodnaSkladisnaLokacija.skladiste',
                'novaSkladisnaLokacija.skladiste',
            ])
            ->latest('vreme_dogadjaja')
            ->latest('id')
            ->get();

        $lokacije = SkladisnaLokacija::query()
            ->with('skladiste')
            ->where('aktivna', true)
            ->whereHas('skladiste', fn ($query) => $query->where('aktivan', true))
            ->orderBy('skladiste_id')
            ->orderBy('naziv')
            ->get();

        return view('lot.show', [
            'lot' => $lot,
            'dogadjaji' => $dogadjaji,
            'lokacije' => $lokacije,
            'klaseKvaliteta' => KlasaKvaliteta::cases(),
        ]);
    }

    public function store(LotStoreRequest $request): RedirectResponse
    {
        return $this->izvrsiServisnuOperaciju(
            fn () => $this->lotService->kreiraj($request->validated(), $request->user()),
            'Lot je uspešno kreiran.',
            'lotovi.index'
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
