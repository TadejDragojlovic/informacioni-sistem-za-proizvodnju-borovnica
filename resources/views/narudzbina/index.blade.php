<x-app-layout>
    <div class="min-h-screen bg-borovnica-light py-8 sm:py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6">
            <div class="mb-8 text-center sm:text-left">
                <h1 class="text-3xl font-bold italic uppercase tracking-widest text-white sm:text-4xl">Narudžbine</h1>
                <div class="mx-auto mt-2 h-1 w-20 bg-borovnica-dark sm:mx-0"></div>
            </div>

            <form method="GET" action="{{ route('narudzbine.index') }}" class="mb-6 grid grid-cols-1 gap-4 rounded-sm border border-borovnica-dark/20 bg-borovnica-table p-4 shadow-lg md:grid-cols-[1fr_1fr_auto]">
                <div>
                    <x-input-label for="pretraga" value="ID, ime ili e-mail kupca" class="mb-1" />
                    <x-text-input id="pretraga" name="pretraga" type="search" class="w-full py-2" :value="request('pretraga')" placeholder="Pretraga narudžbina" />
                </div>
                <div>
                    <x-input-label for="status" value="Status" class="mb-1" />
                    <select id="status" name="status" class="w-full rounded-md border-borovnica-dark/20 bg-white/70 py-2 text-borovnica-dark focus:border-borovnica-accent focus:ring-borovnica-accent">
                        <option value="">Svi statusi</option>
                        @foreach ($statusi as $status)
                            <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 rounded-md bg-borovnica-dark px-4 py-2.5 text-sm font-bold uppercase text-white transition hover:bg-borovnica-accent">Filtriraj</button>
                    <a href="{{ route('narudzbine.index') }}" class="rounded-md border border-borovnica-dark/30 bg-borovnica-soft px-4 py-2.5 text-sm font-bold uppercase">Očisti</a>
                </div>
            </form>

            @if ($narudzbine->isEmpty())
                <div class="rounded-sm border border-borovnica-dark/20 bg-borovnica-table p-8 text-center font-semibold italic shadow-xl">Nema narudžbina koje odgovaraju izabranim kriterijumima.</div>
            @else
                <div class="hidden overflow-x-auto rounded-sm border border-borovnica-dark/20 bg-borovnica-table shadow-2xl lg:block">
                    <table class="w-full min-w-[950px]">
                        <thead>
                            <tr class="border-b-2 border-borovnica-dark/30 text-left text-base font-bold italic text-borovnica-dark">
                                <th class="px-4 py-4">Narudžbina</th>
                                <th class="px-4 py-4">Kupac</th>
                                <th class="px-4 py-4">Datum</th>
                                <th class="px-4 py-4">Stavke</th>
                                <th class="px-4 py-4">Ukupno</th>
                                <th class="px-4 py-4">Status</th>
                                <th class="px-4 py-4 text-center">Akcija</th>
                            </tr>
                        </thead>
                        <tbody class="font-semibold text-borovnica-dark">
                            @foreach ($narudzbine as $narudzbina)
                                <tr class="border-b border-borovnica-dark/10 last:border-b-0 hover:bg-white/20">
                                    <td class="px-4 py-4 font-bold">#{{ $narudzbina->id }}</td>
                                    <td class="px-4 py-4"><span class="block font-bold">{{ $narudzbina->user->name }}</span><span class="text-sm font-normal">{{ $narudzbina->user->email }}</span></td>
                                    <td class="whitespace-nowrap px-4 py-4">{{ $narudzbina->created_at->format('d.m.Y. H:i') }}</td>
                                    <td class="px-4 py-4">{{ $narudzbina->stavke_count }}</td>
                                    <td class="whitespace-nowrap px-4 py-4">{{ number_format($narudzbina->stavke->sum(fn ($stavka) => $stavka->kolicina * (float) $stavka->cena_po_jedinici), 2, ',', '.') }} RSD</td>
                                    <td class="px-4 py-4"><x-narudzbina-status-badge :status="$narudzbina->status" /></td>
                                    <td class="px-4 py-4 text-center"><a href="{{ route('narudzbine.show', $narudzbina) }}" class="rounded bg-borovnica-dark px-4 py-2 text-xs font-bold uppercase text-white transition hover:bg-borovnica-accent">Detalji</a></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="space-y-4 lg:hidden">
                    @foreach ($narudzbine as $narudzbina)
                        <article class="rounded-sm border border-borovnica-dark/20 bg-borovnica-table p-5 text-borovnica-dark shadow-xl">
                            <div class="flex items-start justify-between gap-3">
                                <div><h2 class="text-lg font-bold italic">Narudžbina #{{ $narudzbina->id }}</h2><p class="text-sm">{{ $narudzbina->created_at->format('d.m.Y. H:i') }}</p></div>
                                <x-narudzbina-status-badge :status="$narudzbina->status" />
                            </div>
                            <div class="mt-4 border-t border-borovnica-dark/15 pt-4"><p class="font-bold">{{ $narudzbina->user->name }}</p><p class="break-all text-sm">{{ $narudzbina->user->email }}</p></div>
                            <dl class="mt-4 grid grid-cols-2 gap-x-4 gap-y-2 text-sm">
                                <dt class="font-bold uppercase">Broj stavki</dt><dd class="text-right">{{ $narudzbina->stavke_count }}</dd>
                                <dt class="font-bold uppercase">Ukupno</dt><dd class="text-right font-semibold">{{ number_format($narudzbina->stavke->sum(fn ($stavka) => $stavka->kolicina * (float) $stavka->cena_po_jedinici), 2, ',', '.') }} RSD</dd>
                            </dl>
                            <a href="{{ route('narudzbine.show', $narudzbina) }}" class="mt-5 block rounded bg-borovnica-dark px-4 py-2.5 text-center text-xs font-bold uppercase text-white">Detalji i obrada</a>
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
