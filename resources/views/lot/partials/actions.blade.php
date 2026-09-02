@php
    use App\Enums\LotStatus;

    $mozePrijem = $lot->status === LotStatus::KREIRAN;
    $mozeKvalitet = $lot->status === LotStatus::USKLADISTEN && $lot->klasa_kvaliteta === null;
    $mozeOdobrenje = $lot->status === LotStatus::USKLADISTEN && $lot->klasa_kvaliteta !== null;
    $mozePremestanje = in_array($lot->status, [LotStatus::USKLADISTEN, LotStatus::RASPOLOZIV, LotStatus::BLOKIRAN], true);
    $mozeBlokiranje = in_array($lot->status, [LotStatus::USKLADISTEN, LotStatus::RASPOLOZIV], true);
    $mozeOdblokiranje = $lot->status === LotStatus::BLOKIRAN;
    $mozeKorekcija = in_array($lot->status, [LotStatus::USKLADISTEN, LotStatus::RASPOLOZIV, LotStatus::BLOKIRAN, LotStatus::ISCRPLJEN], true);
    $mozePovlacenje = $lot->status !== LotStatus::POVUCEN;
    $ciljneLokacije = $lokacije->where('id', '!=', $lot->trenutna_skladisna_lokacija_id);
@endphp

<section class="mt-8" aria-labelledby="lot-akcije-naslov">
    <h2 id="lot-akcije-naslov" class="mb-4 text-2xl font-bold italic uppercase tracking-wide text-borovnica-dark">Dostupne akcije</h2>

    @if ($lot->status === LotStatus::POVUCEN)
        <div class="rounded-sm border border-borovnica-dark/20 bg-borovnica-table p-6 font-semibold italic shadow-xl">Povučeni lot je zaključen i nad njim nema dostupnih operacija.</div>
    @else
        <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
            @if ($mozePrijem)
                <details class="rounded-sm border border-borovnica-dark/20 bg-borovnica-table shadow-lg" @if(old('akcija') === 'prijem') open @endif>
                    <summary class="cursor-pointer px-5 py-4 font-bold italic uppercase">Prijem u skladište</summary>
                    <form action="{{ route('lotovi.prijem', $lot) }}" method="POST" class="border-t border-borovnica-dark/15 p-5">
                        @csrf @method('PATCH')
                        <input type="hidden" name="akcija" value="prijem">
                        <x-input-label for="prijem_lokacija" value="Skladišna lokacija" class="mb-2" />
                        <select id="prijem_lokacija" name="skladisna_lokacija_id" class="w-full rounded-md border-borovnica-dark/20 bg-white/70 py-3 focus:border-borovnica-accent focus:ring-borovnica-accent" required>
                            <option value="">Izaberite lokaciju</option>
                            @foreach ($lokacije as $lokacija)<option value="{{ $lokacija->id }}" @selected((string) old('skladisna_lokacija_id') === (string) $lokacija->id)>{{ $lokacija->skladiste->naziv }} — {{ $lokacija->naziv }}</option>@endforeach
                        </select>
                        <button class="mt-4 w-full rounded-md bg-borovnica-dark px-4 py-3 text-sm font-bold uppercase text-white hover:bg-borovnica-accent">Potvrdi prijem</button>
                    </form>
                </details>
            @endif

            @if ($mozeKvalitet)
                <details class="rounded-sm border border-borovnica-dark/20 bg-borovnica-table shadow-lg" @if(old('akcija') === 'kvalitet') open @endif>
                    <summary class="cursor-pointer px-5 py-4 font-bold italic uppercase">Dodela klase kvaliteta</summary>
                    <form action="{{ route('lotovi.kvalitet', $lot) }}" method="POST" class="space-y-4 border-t border-borovnica-dark/15 p-5">
                        @csrf @method('PATCH')
                        <input type="hidden" name="akcija" value="kvalitet">
                        <div><x-input-label for="klasa_kvaliteta" value="Klasa kvaliteta" class="mb-2" /><select id="klasa_kvaliteta" name="klasa_kvaliteta" class="w-full rounded-md border-borovnica-dark/20 bg-white/70 py-3" required><option value="">Izaberite klasu</option>@foreach ($klaseKvaliteta as $klasa)<option value="{{ $klasa->value }}" @selected(old('klasa_kvaliteta') === $klasa->value)>{{ $klasa->label() }}</option>@endforeach</select></div>
                        <div><x-input-label for="broj_dokumenta_kvaliteta" value="Broj dokumenta" class="mb-2" /><x-text-input id="broj_dokumenta_kvaliteta" name="broj_dokumenta_kvaliteta" type="text" class="w-full py-3" :value="old('broj_dokumenta_kvaliteta')" required /></div>
                        <button class="w-full rounded-md bg-borovnica-dark px-4 py-3 text-sm font-bold uppercase text-white hover:bg-borovnica-accent">Dodeli kvalitet</button>
                    </form>
                </details>
            @endif

            @if ($mozeOdobrenje)
                <div class="rounded-sm border border-borovnica-dark/20 bg-borovnica-table p-5 shadow-lg">
                    <h3 class="font-bold italic uppercase">Odobrenje za prodaju</h3>
                    <p class="mt-2 text-sm">Lot ima podatke o kvalitetu i može biti prosleđen na završnu proveru servisa.</p>
                    <form action="{{ route('lotovi.odobrenje-prodaje', $lot) }}" method="POST" class="mt-4" onsubmit="return confirm('Odobriti ovaj lot za prodaju?')">
                        @csrf @method('PATCH')
                        <input type="hidden" name="akcija" value="odobrenje">
                        <button class="w-full rounded-md bg-emerald-700 px-4 py-3 text-sm font-bold uppercase text-white hover:bg-emerald-600">Odobri za prodaju</button>
                    </form>
                </div>
            @endif

            @if ($mozePremestanje && $ciljneLokacije->isNotEmpty())
                <details class="rounded-sm border border-borovnica-dark/20 bg-borovnica-table shadow-lg" @if(old('akcija') === 'premestanje') open @endif>
                    <summary class="cursor-pointer px-5 py-4 font-bold italic uppercase">Premeštanje lota</summary>
                    <form action="{{ route('lotovi.premestanje', $lot) }}" method="POST" class="space-y-4 border-t border-borovnica-dark/15 p-5">
                        @csrf @method('PATCH')
                        <input type="hidden" name="akcija" value="premestanje">
                        <div><x-input-label for="nova_lokacija" value="Nova lokacija" class="mb-2" /><select id="nova_lokacija" name="skladisna_lokacija_id" class="w-full rounded-md border-borovnica-dark/20 bg-white/70 py-3" required><option value="">Izaberite lokaciju</option>@foreach ($ciljneLokacije as $lokacija)<option value="{{ $lokacija->id }}" @selected((string) old('skladisna_lokacija_id') === (string) $lokacija->id)>{{ $lokacija->skladiste->naziv }} — {{ $lokacija->naziv }}</option>@endforeach</select></div>
                        <div><x-input-label for="razlog_premestanja" value="Razlog (opciono)" class="mb-2" /><textarea id="razlog_premestanja" name="razlog" rows="3" class="w-full rounded-md border-borovnica-dark/20 bg-white/70">{{ old('razlog') }}</textarea></div>
                        <button class="w-full rounded-md bg-borovnica-dark px-4 py-3 text-sm font-bold uppercase text-white hover:bg-borovnica-accent">Premesti lot</button>
                    </form>
                </details>
            @endif

            @if ($mozeBlokiranje)
                <details class="rounded-sm border border-amber-800/30 bg-amber-50 shadow-lg" @if(old('akcija') === 'blokiranje') open @endif>
                    <summary class="cursor-pointer px-5 py-4 font-bold italic uppercase text-amber-900">Blokiranje lota</summary>
                    <form action="{{ route('lotovi.blokiranje', $lot) }}" method="POST" class="border-t border-amber-800/20 p-5">
                        @csrf @method('PATCH')<input type="hidden" name="akcija" value="blokiranje">
                        <x-input-label for="razlog_blokiranja" value="Razlog blokiranja" class="mb-2" /><textarea id="razlog_blokiranja" name="razlog" rows="3" class="w-full rounded-md border-amber-800/30 bg-white" required>{{ old('razlog') }}</textarea>
                        <button class="mt-4 w-full rounded-md bg-amber-700 px-4 py-3 text-sm font-bold uppercase text-white hover:bg-amber-600">Blokiraj lot</button>
                    </form>
                </details>
            @endif

            @if ($mozeOdblokiranje)
                <details class="rounded-sm border border-emerald-800/30 bg-emerald-50 shadow-lg" @if(old('akcija') === 'odblokiranje') open @endif>
                    <summary class="cursor-pointer px-5 py-4 font-bold italic uppercase text-emerald-900">Odblokiranje lota</summary>
                    <form action="{{ route('lotovi.odblokiranje', $lot) }}" method="POST" class="border-t border-emerald-800/20 p-5">
                        @csrf @method('PATCH')<input type="hidden" name="akcija" value="odblokiranje">
                        <x-input-label for="razlog_odblokiranja" value="Razlog odblokiranja" class="mb-2" /><textarea id="razlog_odblokiranja" name="razlog" rows="3" class="w-full rounded-md border-emerald-800/30 bg-white" required>{{ old('razlog') }}</textarea>
                        <button class="mt-4 w-full rounded-md bg-emerald-700 px-4 py-3 text-sm font-bold uppercase text-white hover:bg-emerald-600">Odblokiraj lot</button>
                    </form>
                </details>
            @endif

            @if ($mozeKorekcija)
                <details class="rounded-sm border border-borovnica-dark/20 bg-borovnica-table shadow-lg" @if(old('akcija') === 'korekcija') open @endif>
                    <summary class="cursor-pointer px-5 py-4 font-bold italic uppercase">Korekcija količine</summary>
                    <form action="{{ route('lotovi.korekcija-kolicine', $lot) }}" method="POST" class="space-y-4 border-t border-borovnica-dark/15 p-5">
                        @csrf @method('PATCH')<input type="hidden" name="akcija" value="korekcija">
                        <div><x-input-label for="raspoloziva_kolicina_g" value="Nova raspoloživa količina (g)" class="mb-2" /><x-text-input id="raspoloziva_kolicina_g" name="raspoloziva_kolicina_g" type="number" min="0" step="1" class="w-full py-3" :value="old('raspoloziva_kolicina_g', $lot->raspoloziva_kolicina_g)" required /></div>
                        <div><x-input-label for="razlog_korekcije" value="Razlog korekcije" class="mb-2" /><textarea id="razlog_korekcije" name="razlog" rows="3" class="w-full rounded-md border-borovnica-dark/20 bg-white/70" required>{{ old('razlog') }}</textarea></div>
                        <button class="w-full rounded-md bg-borovnica-dark px-4 py-3 text-sm font-bold uppercase text-white hover:bg-borovnica-accent">Koriguj količinu</button>
                    </form>
                </details>
            @endif

            @if ($mozePovlacenje)
                <details class="rounded-sm border border-red-800/30 bg-red-50 shadow-lg lg:col-span-2" @if(old('akcija') === 'povlacenje') open @endif>
                    <summary class="cursor-pointer px-5 py-4 font-bold italic uppercase text-red-800">Povlačenje lota</summary>
                    <form action="{{ route('lotovi.povlacenje', $lot) }}" method="POST" class="border-t border-red-800/20 p-5" onsubmit="return confirm('Povlačenje lota otkazuje aktivne rezervacije. Nastaviti?')">
                        @csrf @method('PATCH')<input type="hidden" name="akcija" value="povlacenje">
                        <x-input-label for="razlog_povlacenja" value="Razlog povlačenja" class="mb-2" /><textarea id="razlog_povlacenja" name="razlog" rows="3" class="w-full rounded-md border-red-800/30 bg-white" required>{{ old('razlog') }}</textarea>
                        <button class="mt-4 w-full rounded-md bg-red-700 px-4 py-3 text-sm font-bold uppercase text-white hover:bg-red-600">Povuci lot</button>
                    </form>
                </details>
            @endif
        </div>
    @endif
</section>
