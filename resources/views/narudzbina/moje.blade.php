<x-app-layout>
    <div class="min-h-screen bg-borovnica-light py-8 sm:py-10">
        <div class="mx-auto max-w-6xl px-4 sm:px-6">
            <div class="mb-8 text-center sm:mb-10">
                <h1 class="text-3xl font-bold italic uppercase tracking-widest text-white sm:text-4xl">Moje narudžbine</h1>
                <div class="mx-auto mt-2 h-1 w-20 bg-borovnica-dark"></div>
            </div>

            @if ($narudzbine->isEmpty())
                <div class="rounded-sm border border-borovnica-dark/20 bg-borovnica-table p-8 text-center shadow-xl">
                    <p class="font-semibold italic text-borovnica-dark">Još nemate nijednu narudžbinu.</p>
                    <a href="{{ route('home') }}" class="mt-6 inline-block rounded-md bg-borovnica-dark px-6 py-3 text-sm font-bold uppercase text-white transition hover:bg-borovnica-accent">Pogledaj proizvode</a>
                </div>
            @else
                <div class="hidden overflow-x-auto rounded-sm border border-borovnica-dark/20 bg-borovnica-table shadow-2xl md:block">
                    <table class="w-full min-w-[750px]">
                        <thead><tr class="border-b-2 border-borovnica-dark/30 text-left text-base font-bold italic"><th class="px-5 py-4">Narudžbina</th><th class="px-5 py-4">Datum</th><th class="px-5 py-4">Ukupno</th><th class="px-5 py-4">Status</th><th class="px-5 py-4 text-center">Akcija</th></tr></thead>
                        <tbody class="font-semibold">
                            @foreach ($narudzbine as $narudzbina)
                                <tr class="border-b border-borovnica-dark/10 last:border-b-0 hover:bg-white/20">
                                    <td class="px-5 py-4 font-bold">#{{ $narudzbina->id }}</td>
                                    <td class="whitespace-nowrap px-5 py-4">{{ $narudzbina->created_at->format('d.m.Y. H:i') }}</td>
                                    <td class="whitespace-nowrap px-5 py-4">{{ number_format($narudzbina->stavke->sum(fn ($stavka) => $stavka->kolicina * (float) $stavka->cena_po_jedinici), 2, ',', '.') }} RSD</td>
                                    <td class="px-5 py-4"><x-narudzbina-status-badge :status="$narudzbina->status" /></td>
                                    <td class="px-5 py-4 text-center"><a href="{{ route('user.orders.show', $narudzbina) }}" class="rounded bg-borovnica-dark px-4 py-2 text-xs font-bold uppercase text-white transition hover:bg-borovnica-accent">Detalji</a></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="space-y-4 md:hidden">
                    @foreach ($narudzbine as $narudzbina)
                        <article class="rounded-sm border border-borovnica-dark/20 bg-borovnica-table p-5 text-borovnica-dark shadow-xl">
                            <div class="flex items-start justify-between gap-3">
                                <div><h2 class="text-lg font-bold italic">Narudžbina #{{ $narudzbina->id }}</h2><p class="text-sm">{{ $narudzbina->created_at->format('d.m.Y. H:i') }}</p></div>
                                <x-narudzbina-status-badge :status="$narudzbina->status" />
                            </div>
                            <p class="mt-5 border-t border-borovnica-dark/15 pt-4 text-right text-lg font-bold">{{ number_format($narudzbina->stavke->sum(fn ($stavka) => $stavka->kolicina * (float) $stavka->cena_po_jedinici), 2, ',', '.') }} RSD</p>
                            <a href="{{ route('user.orders.show', $narudzbina) }}" class="mt-4 block rounded bg-borovnica-dark px-4 py-2.5 text-center text-xs font-bold uppercase text-white">Detalji narudžbine</a>
                        </article>
                    @endforeach
                </div>

                @if ($narudzbine->hasPages())
                    <div class="mt-6">{{ $narudzbine->links() }}</div>
                @endif
            @endif
        </div>
    </div>
</x-app-layout>
