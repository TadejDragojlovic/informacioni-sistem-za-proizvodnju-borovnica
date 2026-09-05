<?php

namespace App\Http\Controllers;

use App\Enums\LotRaspodelaStatus;
use App\Enums\NarudzbinaStatus;
use App\Http\Requests\ObavezanRazlogRequest;
use App\Models\Narudzbina;
use App\Models\NarudzbinaStavka;
use App\Models\Proizvod;
use App\Services\NarudzbinaService;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class NarudzbinaController extends Controller
{
    public function __construct(
        private readonly NarudzbinaService $narudzbinaService
    ) {}

    public function index(Request $request): View
    {
        $status = NarudzbinaStatus::tryFrom($request->string('status')->value());
        $pretraga = trim($request->string('pretraga')->value());

        $narudzbinas = Narudzbina::query()
            ->with(['user', 'stavke:id,narudzbina_id,kolicina,cena_po_jedinici'])
            ->withCount('stavke')
            ->when($status, fn ($query) => $query->where('status', $status->value))
            ->when($pretraga !== '', function ($query) use ($pretraga): void {
                $query->where(function ($query) use ($pretraga): void {
                    $query->whereHas('user', fn ($user) => $user
                        ->where('name', 'like', "%{$pretraga}%")
                        ->orWhere('email', 'like', "%{$pretraga}%"));

                    if (ctype_digit($pretraga)) {
                        $query->orWhereKey((int) $pretraga);
                    }
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('narudzbina.index', [
            'narudzbine' => $narudzbinas,
            'statusi' => NarudzbinaStatus::cases(),
        ]);
    }

    public function show(Narudzbina $narudzbine): View
    {
        $narudzbina = $this->ucitajDetalje($narudzbine);

        return view('narudzbina.show', [
            'narudzbina' => $narudzbina,
            'potpunoRezervisana' => $this->potpunoRezervisana($narudzbina),
        ]);
    }

    public function mojeNarudzbine(): View
    {
        $narudzbine = Narudzbina::query()
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->with('stavke.proizvod')
            ->paginate(10);

        return view('narudzbina.moje', compact('narudzbine'));
    }

    public function mojaNarudzbina(Narudzbina $narudzbina): View
    {
        abort_unless($narudzbina->user_id === auth()->id(), 404);

        return view('narudzbina.moja', [
            'narudzbina' => $this->ucitajDetalje($narudzbina),
        ]);
    }

    // potvrdjivanje narudzbine / korpe
    public function potvrdi(Request $request): RedirectResponse
    {
        $korpa = session()->get('korpa');

        if (empty($korpa)) {
            return redirect()->back()->withErrors(['korpa' => 'Korpa je prazna.']);
        }

        $validirano = $request->validate([
            'adresa_isporuke' => ['required', 'string', 'max:255'],
        ]);

        try {
            DB::transaction(function () use ($korpa, $validirano): void {
                $proizvodi = Proizvod::query()
                    ->whereIn('id', array_keys($korpa))
                    ->where('aktivan', true)
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                if ($proizvodi->count() !== count($korpa)) {
                    throw new DomainException('Jedan ili više proizvoda iz korpe više nisu dostupni.');
                }

                $narudzbina = Narudzbina::create([
                    'user_id' => auth()->id(),
                    'status' => NarudzbinaStatus::POTVRDJENA,
                    'adresa_isporuke' => $validirano['adresa_isporuke'],
                ]);

                foreach ($korpa as $proizvodId => $detalji) {
                    $proizvod = $proizvodi->get($proizvodId);
                    $kolicina = (int) ($detalji['kolicina'] ?? 0);

                    if ($kolicina < 1 || $kolicina > 100) {
                        throw new DomainException('Količina proizvoda u korpi nije validna.');
                    }

                    $narudzbina->stavke()->create([
                        'proizvod_id' => $proizvod->id,
                        'kolicina' => $kolicina,
                        'neto_kolicina_g' => $proizvod->neto_kolicina_g,
                        'cena_po_jedinici' => $proizvod->cena,
                    ]);
                }
            });
        } catch (DomainException $exception) {
            return redirect()->back()->withInput()->withErrors(['korpa' => $exception->getMessage()]);
        }

        session()->forget('korpa');

        return redirect()->route('user.orders')->with('success', 'Narudžbina uspešno kreirana!');
    }

    public function rezervisiFifo(Request $request, NarudzbinaStavka $stavka): RedirectResponse
    {
        return $this->izvrsiServisnuOperaciju(
            fn () => $this->narudzbinaService->rezervisiFifo($stavka, $request->user()),
            'Stavka narudžbine je uspešno rezervisana.'
        );
    }

    public function otpremi(Request $request, Narudzbina $narudzbina): RedirectResponse
    {
        return $this->izvrsiServisnuOperaciju(
            fn () => $this->narudzbinaService->otpremi($narudzbina, $request->user()),
            'Narudžbina je uspešno otpremljena.'
        );
    }

    public function otkazi(ObavezanRazlogRequest $request, Narudzbina $narudzbina): RedirectResponse
    {
        return $this->izvrsiServisnuOperaciju(
            fn () => $this->narudzbinaService->otkazi(
                $narudzbina,
                $request->string('razlog')->value(),
                $request->user()
            ),
            'Narudžbina je uspešno otkazana.'
        );
    }

    private function ucitajDetalje(Narudzbina $narudzbina): Narudzbina
    {
        return $narudzbina->load([
            'user',
            'stavke.proizvod.sorta',
            'stavke.raspodele.lot',
        ]);
    }

    private function potpunoRezervisana(Narudzbina $narudzbina): bool
    {
        return $narudzbina->status === NarudzbinaStatus::POTVRDJENA
            && $narudzbina->stavke->isNotEmpty()
            && $narudzbina->stavke->every(fn (NarudzbinaStavka $stavka) => $stavka->raspodele
                ->where('status', LotRaspodelaStatus::REZERVISANO)
                ->sum('broj_pakovanja') === $stavka->kolicina);
    }
}
