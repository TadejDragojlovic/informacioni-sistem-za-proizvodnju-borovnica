<x-app-layout>
    <div class="min-h-screen bg-borovnica-light py-8 sm:py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6">
            <div class="mb-8 flex flex-col items-center gap-5 sm:flex-row sm:justify-between">
                <div class="text-center sm:text-left">
                    <h1 class="text-3xl font-bold italic uppercase tracking-widest text-white sm:text-4xl">Lotovi</h1>
                    <div class="mx-auto mt-2 h-1 w-20 bg-borovnica-dark sm:mx-0"></div>
                </div>
                <a href="{{ route('lotovi.create') }}" class="rounded-full bg-borovnica-dark px-6 py-3 text-sm font-bold uppercase tracking-widest text-white shadow-lg transition hover:bg-borovnica-accent focus:outline-none focus:ring-2 focus:ring-borovnica-dark focus:ring-offset-2">
                    + Kreiraj lot
                </a>
            </div>

            <form method="GET" action="{{ route('lotovi.index') }}" class="mb-6 grid grid-cols-1 gap-4 rounded-sm border border-borovnica-dark/20 bg-borovnica-table p-4 shadow-lg sm:grid-cols-2 lg:grid-cols-[1fr_1fr_1fr_auto]">
                <div>
                    <x-input-label for="pretraga" value="Oznaka lota" class="mb-1" />
                    <x-text-input id="pretraga" name="pretraga" type="search" class="w-full py-2" :value="request('pretraga')" placeholder="npr. BL-2026" />
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
                <div>
                    <x-input-label for="sorta_id" value="Sorta" class="mb-1" />
                    <select id="sorta_id" name="sorta_id" class="w-full rounded-md border-borovnica-dark/20 bg-white/70 py-2 text-borovnica-dark focus:border-borovnica-accent focus:ring-borovnica-accent">
                        <option value="">Sve sorte</option>
                        @foreach ($sorte as $sorta)
                            <option value="{{ $sorta->id }}" @selected((string) request('sorta_id') === (string) $sorta->id)>{{ $sorta->naziv }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 rounded-md bg-borovnica-dark px-4 py-2.5 text-sm font-bold uppercase text-white transition hover:bg-borovnica-accent">Filtriraj</button>
                    <a href="{{ route('lotovi.index') }}" class="rounded-md border border-borovnica-dark/30 bg-borovnica-soft px-4 py-2.5 text-sm font-bold uppercase">Očisti</a>
                </div>
            </form>

            @if ($lotovi->isEmpty())
                <div class="rounded-sm border border-borovnica-dark/20 bg-borovnica-table p-8 text-center font-semibold italic shadow-xl">Nema lotova koji odgovaraju izabranim kriterijumima.</div>
            @else
                <div class="hidden overflow-x-auto rounded-sm border border-borovnica-dark/20 bg-borovnica-table shadow-2xl lg:block">
                    <table class="w-full min-w-[1000px]">
                        <thead><tr class="border-b-2 border-borovnica-dark/30 text-left text-base font-bold italic"><th class="px-4 py-4">Oznaka</th><th class="px-4 py-4">Berba</th><th class="px-4 py-4">Sorta / parcela</th><th class="px-4 py-4">Raspoloživo</th><th class="px-4 py-4">Lokacija</th><th class="px-4 py-4">Status</th><th class="px-4 py-4 text-center">Akcija</th></tr></thead>
                        <tbody class="font-semibold">
                            @foreach ($lotovi as $lot)
                                <tr class="border-b border-borovnica-dark/10 last:border-b-0 hover:bg-white/20">
                                    <td class="px-4 py-4 font-bold">{{ $lot->oznaka }}</td>
                                    <td class="whitespace-nowrap px-4 py-4">{{ $lot->datum_berbe->format('d.m.Y.') }}</td>
                                    <td class="px-4 py-4"><span class="block">{{ $lot->sorta->naziv }}</span><span class="text-sm font-normal">{{ $lot->parcela->oznaka }}</span></td>
                                    <td class="whitespace-nowrap px-4 py-4">{{ number_format($lot->raspoloziva_kolicina_g, 0, ',', '.') }} / {{ number_format($lot->pocetna_kolicina_g, 0, ',', '.') }} g</td>
                                    <td class="px-4 py-4 text-sm">{{ $lot->trenutnaSkladisnaLokacija ? $lot->trenutnaSkladisnaLokacija->skladiste->naziv.' — '.$lot->trenutnaSkladisnaLokacija->naziv : 'Nije uskladišten' }}</td>
                                    <td class="px-4 py-4"><x-lot-status-badge :status="$lot->status" /></td>
                                    <td class="px-4 py-4 text-center"><a href="{{ route('lotovi.show', $lot) }}" class="rounded bg-borovnica-dark px-4 py-2 text-xs font-bold uppercase text-white transition hover:bg-borovnica-accent">Detalji</a></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="space-y-4 lg:hidden">
                    @foreach ($lotovi as $lot)
                        <article class="rounded-sm border border-borovnica-dark/20 bg-borovnica-table p-5 shadow-xl">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                <div><h2 class="text-lg font-bold italic">{{ $lot->oznaka }}</h2><p class="text-sm">Berba: {{ $lot->datum_berbe->format('d.m.Y.') }}</p></div>
                                <x-lot-status-badge :status="$lot->status" class="self-start" />
                            </div>
                            <dl class="mt-4 grid grid-cols-2 gap-x-4 gap-y-2 text-sm">
                                <dt class="font-bold uppercase">Sorta</dt><dd class="text-right">{{ $lot->sorta->naziv }}</dd>
                                <dt class="font-bold uppercase">Parcela</dt><dd class="text-right">{{ $lot->parcela->oznaka }}</dd>
                                <dt class="font-bold uppercase">Raspoloživo</dt><dd class="text-right">{{ number_format($lot->raspoloziva_kolicina_g, 0, ',', '.') }} g</dd>
                                <dt class="font-bold uppercase">Lokacija</dt><dd class="text-right">{{ $lot->trenutnaSkladisnaLokacija?->naziv ?? '—' }}</dd>
                            </dl>
                            <a href="{{ route('lotovi.show', $lot) }}" class="mt-5 block rounded bg-borovnica-dark px-4 py-2.5 text-center text-xs font-bold uppercase text-white">Detalji i sledljivost</a>
                        </article>
                    @endforeach
                </div>

                @if ($lotovi->hasPages())
                    <div class="mt-6">{{ $lotovi->links() }}</div>
                @endif
            @endif
        </div>
    </div>
</x-app-layout>
