<x-app-layout>
    <div class="min-h-screen bg-borovnica-light py-8 sm:py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6">
            <div class="mb-8 flex flex-col items-center gap-5 sm:flex-row sm:justify-between">
                <div class="text-center sm:text-left">
                    <h1 class="text-3xl font-bold italic uppercase tracking-widest text-white sm:text-4xl">Resursi</h1>
                    <div class="mx-auto mt-2 h-1 w-20 bg-borovnica-dark sm:mx-0"></div>
                </div>
                <a href="{{ route('resurs.create') }}" class="rounded-full bg-borovnica-dark px-6 py-3 text-sm font-bold uppercase tracking-widest text-white shadow-lg transition hover:bg-borovnica-accent focus:outline-none focus:ring-2 focus:ring-borovnica-dark focus:ring-offset-2">+ Evidentiraj resurs</a>
            </div>

            <form method="GET" action="{{ route('resurs.index') }}" class="mb-6 grid grid-cols-1 gap-4 rounded-sm border border-borovnica-dark/20 bg-borovnica-table p-4 shadow-lg md:grid-cols-[1fr_1fr_auto]">
                <div>
                    <x-input-label for="pretraga" value="Naziv resursa" class="mb-1" />
                    <x-text-input id="pretraga" name="pretraga" type="search" class="w-full py-2" :value="request('pretraga')" placeholder="Pretražite resurse" />
                </div>
                <div>
                    <x-input-label for="lot_id" value="Lot" class="mb-1" />
                    <select id="lot_id" name="lot_id" class="w-full rounded-md border-borovnica-dark/20 bg-white/70 py-2 text-borovnica-dark focus:border-borovnica-accent focus:ring-borovnica-accent">
                        <option value="">Svi lotovi</option>
                        @foreach ($lotovi as $lot)
                            <option value="{{ $lot->id }}" @selected((string) request('lot_id') === (string) $lot->id)>{{ $lot->oznaka }} — {{ $lot->sorta->naziv }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 rounded-md bg-borovnica-dark px-4 py-2.5 text-sm font-bold uppercase text-white transition hover:bg-borovnica-accent">Filtriraj</button>
                    <a href="{{ route('resurs.index') }}" class="rounded-md border border-borovnica-dark/30 bg-borovnica-soft px-4 py-2.5 text-sm font-bold uppercase">Očisti</a>
                </div>
            </form>

            @if ($resursi->isEmpty())
                <div class="rounded-sm border border-borovnica-dark/20 bg-borovnica-table p-8 text-center font-semibold italic shadow-xl">Nema resursa koji odgovaraju izabranim kriterijumima.</div>
            @else
                <div class="hidden overflow-x-auto rounded-sm border border-borovnica-dark/20 bg-borovnica-table shadow-2xl lg:block">
                    <table class="w-full min-w-[1000px]">
                        <thead>
                            <tr class="border-b-2 border-borovnica-dark/30 text-left text-base font-bold italic">
                                <th class="px-4 py-4">Resurs</th><th class="px-4 py-4">Lot</th><th class="px-4 py-4">Datum</th><th class="px-4 py-4">Količina</th><th class="px-4 py-4">Cena/jed.</th><th class="px-4 py-4">Ukupan trošak</th><th class="px-4 py-4 text-center">Akcija</th>
                            </tr>
                        </thead>
                        <tbody class="font-semibold">
                            @foreach ($resursi as $resurs)
                                <tr class="border-b border-borovnica-dark/10 last:border-b-0 hover:bg-white/20">
                                    <td class="px-4 py-4 font-bold">{{ $resurs->naziv }}</td>
                                    <td class="px-4 py-4"><span class="block font-bold">{{ $resurs->lot->oznaka }}</span><span class="text-sm font-normal">{{ $resurs->lot->sorta->naziv }}</span></td>
                                    <td class="whitespace-nowrap px-4 py-4">{{ $resurs->datum_upotrebe->format('d.m.Y.') }}</td>
                                    <td class="whitespace-nowrap px-4 py-4">{{ number_format($resurs->kolicina, 2, ',', '.') }} {{ $resurs->jedinica_mere }}</td>
                                    <td class="whitespace-nowrap px-4 py-4">{{ number_format($resurs->cena_po_jedinici, 2, ',', '.') }} RSD</td>
                                    <td class="whitespace-nowrap px-4 py-4 font-bold">{{ number_format((float) $resurs->kolicina * (float) $resurs->cena_po_jedinici, 2, ',', '.') }} RSD</td>
                                    <td class="px-4 py-4 text-center"><a href="{{ route('resurs.show', $resurs) }}" class="rounded bg-borovnica-dark px-4 py-2 text-xs font-bold uppercase text-white transition hover:bg-borovnica-accent">Detalji</a></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="space-y-4 lg:hidden">
                    @foreach ($resursi as $resurs)
                        <article class="rounded-sm border border-borovnica-dark/20 bg-borovnica-table p-5 shadow-xl">
                            <div class="flex flex-col gap-1 border-b border-borovnica-dark/15 pb-4 sm:flex-row sm:items-start sm:justify-between">
                                <h2 class="text-lg font-bold italic">{{ $resurs->naziv }}</h2>
                                <span class="text-sm font-semibold">{{ $resurs->datum_upotrebe->format('d.m.Y.') }}</span>
                            </div>
                            <dl class="mt-4 grid grid-cols-2 gap-x-4 gap-y-2 text-sm">
                                <dt class="font-bold uppercase">Lot</dt><dd class="text-right">{{ $resurs->lot->oznaka }}</dd>
                                <dt class="font-bold uppercase">Sorta</dt><dd class="text-right">{{ $resurs->lot->sorta->naziv }}</dd>
                                <dt class="font-bold uppercase">Količina</dt><dd class="text-right">{{ number_format($resurs->kolicina, 2, ',', '.') }} {{ $resurs->jedinica_mere }}</dd>
                                <dt class="font-bold uppercase">Trošak</dt><dd class="text-right font-semibold">{{ number_format((float) $resurs->kolicina * (float) $resurs->cena_po_jedinici, 2, ',', '.') }} RSD</dd>
                            </dl>
                            <a href="{{ route('resurs.show', $resurs) }}" class="mt-5 block rounded bg-borovnica-dark px-4 py-2.5 text-center text-xs font-bold uppercase text-white">Detalji resursa</a>
                        </article>
                    @endforeach
                </div>

                @if ($resursi->hasPages())
                    <div class="mt-6">{{ $resursi->links() }}</div>
                @endif
            @endif
        </div>
    </div>
</x-app-layout>
