<x-app-layout>
    <div class="min-h-screen bg-borovnica-light py-8 sm:py-10">
        <div class="mx-auto max-w-5xl px-4 sm:px-6">
            <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <a href="{{ route('user.orders') }}" class="font-semibold italic text-white transition hover:text-borovnica-soft">← Moje narudžbine</a>
                    <h1 class="mt-3 text-3xl font-bold italic uppercase tracking-widest text-white sm:text-4xl">Narudžbina #{{ $narudzbina->id }}</h1>
                    <p class="mt-1 font-semibold italic text-borovnica-soft">{{ $narudzbina->created_at->format('d.m.Y. H:i') }}</p>
                </div>
                <x-narudzbina-status-badge :status="$narudzbina->status" class="self-start sm:self-auto" />
            </div>

            <section class="mb-6 rounded-sm border border-borovnica-dark/20 bg-borovnica-table p-5 shadow-xl sm:p-6">
                <h2 class="border-b border-borovnica-dark/20 pb-2 font-bold italic uppercase">Isporuka</h2>
                <p class="mt-4 break-words font-semibold">{{ $narudzbina->adresa_isporuke }}</p>
            </section>

            <div class="space-y-4">
                @foreach ($narudzbina->stavke as $stavka)
                    <article class="rounded-sm border border-borovnica-dark/20 bg-borovnica-table p-5 text-borovnica-dark shadow-xl">
                        <h2 class="text-lg font-bold italic">{{ $stavka->proizvod->naziv }}</h2>
                        <p class="mt-1 text-sm">{{ $stavka->proizvod->sorta->naziv }} · {{ number_format($stavka->neto_kolicina_g, 0, ',', '.') }} g</p>
                        <dl class="mt-4 grid grid-cols-2 gap-x-4 gap-y-2 text-sm">
                            <dt class="font-bold uppercase">Količina</dt><dd class="text-right">{{ $stavka->kolicina }} pak.</dd>
                            <dt class="font-bold uppercase">Cena</dt><dd class="text-right">{{ number_format($stavka->cena_po_jedinici, 2, ',', '.') }} RSD</dd>
                            <dt class="font-bold uppercase">Ukupno</dt><dd class="text-right font-semibold">{{ number_format($stavka->kolicina * (float) $stavka->cena_po_jedinici, 2, ',', '.') }} RSD</dd>
                        </dl>

                        @if ($stavka->raspodele->whereIn('status', [\App\Enums\LotRaspodelaStatus::REZERVISANO, \App\Enums\LotRaspodelaStatus::IZDATO])->isNotEmpty())
                            <div class="mt-4 border-t border-borovnica-dark/15 pt-4">
                                <h3 class="text-xs font-bold uppercase tracking-wide">Lotovi sledljivosti</h3>
                                <div class="mt-2 flex flex-wrap gap-2">
                                    @foreach ($stavka->raspodele->whereIn('status', [\App\Enums\LotRaspodelaStatus::REZERVISANO, \App\Enums\LotRaspodelaStatus::IZDATO]) as $raspodela)
                                        <span class="rounded-full bg-white/50 px-3 py-1 text-xs font-semibold">{{ $raspodela->lot->oznaka }} · {{ $raspodela->broj_pakovanja }} pak.</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </article>
                @endforeach
            </div>

            <div class="mt-6 flex items-center justify-between gap-4 rounded-sm border border-borovnica-dark/20 bg-borovnica-table p-5 text-borovnica-dark shadow-2xl">
                <span class="text-lg font-bold uppercase">Ukupno</span>
                <span class="text-xl font-bold sm:text-2xl">{{ number_format($narudzbina->stavke->sum(fn ($stavka) => $stavka->kolicina * (float) $stavka->cena_po_jedinici), 2, ',', '.') }} RSD</span>
            </div>
        </div>
    </div>
</x-app-layout>
