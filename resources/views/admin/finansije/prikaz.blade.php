<x-app-layout>
    <style media="print">
        @page { margin: 12mm; }
        body, main { background: #fff !important; }
        nav, .no-print { display: none !important; }
        .print-report { max-width: none !important; padding: 0 !important; }
        .print-card { box-shadow: none !important; break-inside: avoid; }
    </style>

    <div class="min-h-screen bg-borovnica-light py-8 sm:py-10 print-report">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 print-report">
            <div class="mb-8 flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <a href="{{ route('admin.finansije.create') }}" class="no-print font-semibold italic text-white transition hover:text-borovnica-soft">← Promeni mesec</a>
                    <h1 class="mt-3 text-3xl font-bold italic uppercase tracking-widest text-white sm:text-4xl print:text-borovnica-dark">Finansijski izveštaj</h1>
                    <p class="mt-2 font-semibold text-borovnica-soft print:text-borovnica-dark">Mesec obračuna: {{ $datumOd->format('m/Y') }}</p>
                </div>
                <button type="button" onclick="window.print()" class="no-print self-start rounded-md bg-borovnica-dark px-6 py-3 text-sm font-bold uppercase text-white shadow-lg transition hover:bg-borovnica-accent sm:self-auto">Štampaj izveštaj</button>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <section class="print-card rounded-sm border border-borovnica-dark/20 bg-borovnica-table p-5 shadow-xl">
                    <h2 class="text-xs font-bold uppercase tracking-widest">Ukupni prihod</h2>
                    <p class="mt-3 text-2xl font-black">{{ number_format($ukupniPrihod, 2, ',', '.') }} RSD</p>
                </section>
                <section class="print-card rounded-sm border border-red-900/20 bg-red-50/70 p-5 shadow-xl">
                    <h2 class="text-xs font-bold uppercase tracking-widest text-red-900">Ukupni rashod</h2>
                    <p class="mt-3 text-2xl font-black text-red-800">{{ number_format($ukupniRashod, 2, ',', '.') }} RSD</p>
                </section>
                <section class="print-card rounded-sm border border-borovnica-dark/20 bg-borovnica-dark p-5 text-white shadow-xl">
                    <h2 class="text-xs font-bold uppercase tracking-widest">Neto rezultat</h2>
                    <p class="mt-3 text-2xl font-black {{ $netoDobit < 0 ? 'text-red-200' : 'text-emerald-200' }}">{{ number_format($netoDobit, 2, ',', '.') }} RSD</p>
                </section>
                <section class="print-card rounded-sm border border-borovnica-dark/20 bg-borovnica-table p-5 text-center shadow-xl">
                    <h2 class="text-xs font-bold uppercase tracking-widest">Otpremljene narudžbine</h2>
                    <p class="mt-3 text-3xl font-black">{{ $brojNarudzbina }}</p>
                </section>
            </div>

            <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
                <section class="print-card overflow-hidden rounded-sm border border-borovnica-dark/20 bg-borovnica-table shadow-xl">
                    <div class="flex items-center justify-between gap-3 border-b border-borovnica-dark/20 px-5 py-4">
                        <h2 class="font-bold italic uppercase">Troškovi skladišta</h2>
                        <span class="whitespace-nowrap text-sm font-bold">{{ number_format($trosakSkladista, 2, ',', '.') }} RSD</span>
                    </div>
                    @if ($listaSkladista->isEmpty())
                        <p class="p-5 text-sm italic">Nema skladišnih troškova za izabrani mesec.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full min-w-[430px] text-sm">
                                <thead><tr class="border-b border-borovnica-dark/20 text-left text-xs font-bold uppercase"><th class="px-5 py-3">Skladište</th><th class="px-5 py-3">Lokacija</th><th class="px-5 py-3 text-right">Mesečni trošak</th></tr></thead>
                                <tbody>
                                    @foreach ($listaSkladista as $skladiste)
                                        <tr class="border-b border-borovnica-dark/10 last:border-b-0"><td class="px-5 py-3 font-semibold">{{ $skladiste->naziv }}</td><td class="px-5 py-3">{{ $skladiste->lokacija }}</td><td class="whitespace-nowrap px-5 py-3 text-right font-semibold">{{ number_format($skladiste->mesecni_trosak, 2, ',', '.') }} RSD</td></tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </section>

                <section class="print-card overflow-hidden rounded-sm border border-borovnica-dark/20 bg-borovnica-table shadow-xl">
                    <div class="flex items-center justify-between gap-3 border-b border-borovnica-dark/20 px-5 py-4">
                        <h2 class="font-bold italic uppercase">Troškovi resursa</h2>
                        <span class="whitespace-nowrap text-sm font-bold">{{ number_format($ukupniTrosakResursa, 2, ',', '.') }} RSD</span>
                    </div>
                    @if ($listaResursa->isEmpty())
                        <p class="p-5 text-sm italic">Nema troškova resursa za izabrani mesec.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full min-w-[480px] text-sm">
                                <thead><tr class="border-b border-borovnica-dark/20 text-left text-xs font-bold uppercase"><th class="px-5 py-3">Resurs</th><th class="px-5 py-3">Lot</th><th class="px-5 py-3">Količina</th><th class="px-5 py-3 text-right">Trošak</th></tr></thead>
                                <tbody>
                                    @foreach ($listaResursa as $resurs)
                                        <tr class="border-b border-borovnica-dark/10 last:border-b-0">
                                            <td class="px-5 py-3 font-semibold">{{ $resurs->naziv }}</td>
                                            <td class="px-5 py-3">{{ $resurs->lot->oznaka }}</td>
                                            <td class="whitespace-nowrap px-5 py-3">{{ number_format($resurs->kolicina, 2, ',', '.') }} {{ $resurs->jedinica_mere }}</td>
                                            <td class="whitespace-nowrap px-5 py-3 text-right font-semibold">{{ number_format((float) $resurs->kolicina * (float) $resurs->cena_po_jedinici, 2, ',', '.') }} RSD</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </section>
            </div>

            <p class="mt-6 text-center text-xs font-medium text-borovnica-dark/75">Izveštaj generisan {{ now()->format('d.m.Y. H:i') }}</p>
        </div>
    </div>
</x-app-layout>
