<x-app-layout>
    <div class="min-h-screen bg-borovnica-light py-8 sm:py-10">
        <div class="mx-auto max-w-6xl px-4 sm:px-6">
            <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <a href="{{ route('narudzbine.index') }}" class="font-semibold italic text-white transition hover:text-borovnica-soft">← Povratak na listu</a>
                    <h1 class="mt-3 text-3xl font-bold italic uppercase tracking-widest text-white sm:text-4xl">Narudžbina #{{ $narudzbina->id }}</h1>
                    <p class="mt-1 font-semibold italic text-borovnica-soft">{{ $narudzbina->created_at->format('d.m.Y. H:i') }}</p>
                </div>
                <x-narudzbina-status-badge :status="$narudzbina->status" class="self-start sm:self-auto" />
            </div>

            <x-form-errors />

            <div class="mb-6 grid grid-cols-1 gap-5 md:grid-cols-2">
                <section class="rounded-sm border border-borovnica-dark/20 bg-borovnica-table p-5 shadow-xl">
                    <h2 class="border-b border-borovnica-dark/20 pb-2 font-bold italic uppercase">Kupac</h2>
                    <p class="mt-4 text-lg font-bold">{{ $narudzbina->user->name }}</p>
                    <p class="break-all text-sm">{{ $narudzbina->user->email }}</p>
                </section>
                <section class="rounded-sm border border-borovnica-dark/20 bg-borovnica-table p-5 shadow-xl">
                    <h2 class="border-b border-borovnica-dark/20 pb-2 font-bold italic uppercase">Isporuka</h2>
                    <p class="mt-4 break-words font-semibold">{{ $narudzbina->adresa_isporuke }}</p>
                    <p class="mt-3 text-sm">Ukupno: <strong>{{ number_format($narudzbina->stavke->sum(fn ($stavka) => $stavka->kolicina * (float) $stavka->cena_po_jedinici), 2, ',', '.') }} RSD</strong></p>
                </section>
            </div>

            <div class="space-y-5">
                @foreach ($narudzbina->stavke as $stavka)
                    @php
                        $aktivneRaspodele = $stavka->raspodele->whereIn('status', [
                            \App\Enums\LotRaspodelaStatus::REZERVISANO,
                            \App\Enums\LotRaspodelaStatus::IZDATO,
                        ]);
                        $rasporedjenoPakovanja = $aktivneRaspodele->sum('broj_pakovanja');
                        $imaIzdatihRaspodela = $aktivneRaspodele->contains('status', \App\Enums\LotRaspodelaStatus::IZDATO);
                        $mozeRezervacija = $narudzbina->status === \App\Enums\NarudzbinaStatus::POTVRDJENA
                            && ! $imaIzdatihRaspodela
                            && $rasporedjenoPakovanja < $stavka->kolicina;
                    @endphp

                    <article class="rounded-sm border border-borovnica-dark/20 bg-borovnica-table p-5 shadow-xl sm:p-6">
                        <div class="flex flex-col gap-4 border-b border-borovnica-dark/15 pb-5 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <h2 class="text-xl font-bold italic">{{ $stavka->proizvod->naziv }}</h2>
                                <p class="mt-1 text-sm">Sorta: {{ $stavka->proizvod->sorta->naziv }}</p>
                            </div>
                            @if ($mozeRezervacija)
                                <form action="{{ route('narudzbine.stavke.fifo-rezervacija', $stavka) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full rounded-md bg-borovnica-dark px-4 py-2.5 text-xs font-bold uppercase text-white transition hover:bg-borovnica-accent sm:w-auto" onclick="return confirm('Pokrenuti FIFO rezervaciju za ovu stavku?')">FIFO rezervacija</button>
                                </form>
                            @endif
                        </div>

                        <dl class="mt-5 grid grid-cols-2 gap-x-4 gap-y-3 text-sm sm:grid-cols-4">
                            <div><dt class="font-bold uppercase">Naručeno</dt><dd class="mt-1">{{ $stavka->kolicina }} pak.</dd></div>
                            <div><dt class="font-bold uppercase">Neto masa</dt><dd class="mt-1">{{ number_format($stavka->neto_kolicina_g, 0, ',', '.') }} g</dd></div>
                            <div><dt class="font-bold uppercase">Cena</dt><dd class="mt-1">{{ number_format($stavka->cena_po_jedinici, 2, ',', '.') }} RSD</dd></div>
                            <div><dt class="font-bold uppercase">Vrednost</dt><dd class="mt-1 font-semibold">{{ number_format($stavka->kolicina * (float) $stavka->cena_po_jedinici, 2, ',', '.') }} RSD</dd></div>
                        </dl>

                        <div class="mt-5 rounded-sm bg-white/35 p-4">
                            <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                                <h3 class="font-bold uppercase">Raspodela lotova</h3>
                                <span class="text-sm font-semibold">Aktivno raspoređeno: {{ $rasporedjenoPakovanja }} / {{ $stavka->kolicina }} pak.</span>
                            </div>

                            @if ($stavka->raspodele->isEmpty())
                                <p class="mt-3 text-sm italic">Za stavku još nije izvršena rezervacija.</p>
                            @else
                                <div class="mt-3 space-y-2">
                                    @foreach ($stavka->raspodele as $raspodela)
                                        <div class="flex flex-col gap-2 rounded border border-borovnica-dark/10 bg-white/35 p-3 sm:flex-row sm:items-center sm:justify-between">
                                            <div><a href="{{ route('lotovi.show', $raspodela->lot) }}" class="font-bold underline decoration-borovnica-accent underline-offset-2">{{ $raspodela->lot->oznaka }}</a><span class="ms-2 text-sm">{{ $raspodela->broj_pakovanja }} pak.</span></div>
                                            <x-raspodela-status-badge :status="$raspodela->status" class="self-start sm:self-auto" />
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>

            @if ($narudzbina->status === \App\Enums\NarudzbinaStatus::POTVRDJENA)
                <section class="mt-6 rounded-sm border border-borovnica-dark/20 bg-borovnica-table p-5 shadow-2xl sm:p-6">
                    <h2 class="font-bold italic uppercase">Obrada narudžbine</h2>
                    <div class="mt-5 grid grid-cols-1 gap-5 lg:grid-cols-2">
                        <div class="rounded-sm border border-borovnica-dark/15 bg-white/25 p-4">
                            <h3 class="font-bold">Otprema</h3>
                            @if ($potpunoRezervisana)
                                <p class="mt-2 text-sm">Sve stavke su potpuno rezervisane i narudžbina je spremna za otpremu.</p>
                                <form action="{{ route('narudzbine.otprema', $narudzbina) }}" method="POST" class="mt-4">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="w-full rounded-md bg-emerald-700 px-4 py-2.5 text-sm font-bold uppercase text-white transition hover:bg-emerald-800" onclick="return confirm('Potvrditi otpremu narudžbine?')">Označi kao otpremljenu</button>
                                </form>
                            @else
                                <p class="mt-2 text-sm italic">Pre otpreme je potrebno potpuno rezervisati svaku stavku.</p>
                            @endif
                        </div>

                        <div class="rounded-sm border border-red-900/20 bg-red-50/45 p-4">
                            <h3 class="font-bold text-red-900">Otkazivanje</h3>
                            <form action="{{ route('narudzbine.otkazivanje', $narudzbina) }}" method="POST" class="mt-3">
                                @csrf
                                @method('PATCH')
                                <x-input-label for="razlog" value="Razlog otkazivanja" />
                                <textarea id="razlog" name="razlog" rows="3" required maxlength="1000" class="mt-1 block w-full rounded-md border-borovnica-dark/20 bg-white/80 text-borovnica-dark focus:border-borovnica-accent focus:ring-borovnica-accent">{{ old('razlog') }}</textarea>
                                <button type="submit" class="mt-3 w-full rounded-md bg-red-700 px-4 py-2.5 text-sm font-bold uppercase text-white transition hover:bg-red-800" onclick="return confirm('Da li sigurno želite da otkažete narudžbinu?')">Otkaži narudžbinu</button>
                            </form>
                        </div>
                    </div>
                </section>
            @endif
        </div>
    </div>
</x-app-layout>
