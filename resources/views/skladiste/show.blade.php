<x-app-layout>
    <div class="min-h-screen bg-borovnica-light py-8 sm:py-10">
        <div class="mx-auto max-w-5xl px-4 sm:px-6">
            <div class="mb-8 flex flex-col items-center">
                <h1 class="text-center text-3xl font-bold italic uppercase tracking-widest text-white">Detalji skladišta</h1>
                <div class="mt-2 h-1 w-20 bg-borovnica-dark"></div>
            </div>

            <section class="rounded-sm border border-borovnica-dark/20 bg-borovnica-table p-5 text-borovnica-dark shadow-2xl sm:p-8">
                <div class="flex flex-col gap-4 border-b border-borovnica-dark/20 pb-6 sm:flex-row sm:items-start sm:justify-between">
                    <div><h2 class="text-2xl font-bold italic">{{ $skladiste->naziv }}</h2><p class="mt-1">{{ $skladiste->lokacija }}</p></div>
                    <x-status-badge :active="$skladiste->aktivan" active-label="Aktivno" inactive-label="Neaktivno" />
                </div>
                <dl class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div><dt class="text-sm font-bold italic uppercase tracking-wide">Mesečni trošak</dt><dd class="mt-1 text-lg font-semibold">{{ number_format($skladiste->mesecni_trosak, 2, ',', '.') }} RSD</dd></div>
                    <div><dt class="text-sm font-bold italic uppercase tracking-wide">Broj skladišnih lokacija</dt><dd class="mt-1 text-lg font-semibold">{{ $skladiste->skladisneLokacije->count() }}</dd></div>
                </dl>
                <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:justify-end">
                    <a href="{{ route('skladiste.index') }}" class="rounded-md border border-borovnica-dark/30 bg-borovnica-soft px-5 py-3 text-center text-sm font-bold uppercase tracking-wide">Nazad</a>
                    <a href="{{ route('skladiste.edit', $skladiste) }}" class="rounded-md bg-borovnica-dark px-5 py-3 text-center text-sm font-bold uppercase tracking-wide text-white transition hover:bg-borovnica-accent">Izmeni skladište</a>
                </div>
            </section>

            <section class="mt-8">
                <div class="mb-4 flex flex-col items-center gap-4 sm:flex-row sm:justify-between">
                    <h2 class="text-2xl font-bold italic uppercase tracking-wide text-borovnica-dark">Skladišne lokacije</h2>
                    <a href="{{ route('skladisne-lokacije.create', $skladiste) }}" class="rounded-full bg-borovnica-dark px-5 py-3 text-sm font-bold uppercase tracking-wide text-white shadow-lg transition hover:bg-borovnica-accent">+ Dodaj lokaciju</a>
                </div>

                @if ($skladiste->skladisneLokacije->isEmpty())
                    <div class="rounded-sm border border-borovnica-dark/20 bg-borovnica-table p-6 text-center font-semibold italic shadow-xl">Ovo skladište još nema definisane lokacije.</div>
                @else
                    <div class="hidden overflow-x-auto rounded-sm border border-borovnica-dark/20 bg-borovnica-table shadow-xl md:block">
                        <table class="w-full min-w-[700px]">
                            <thead><tr class="border-b-2 border-borovnica-dark/30 text-left font-bold italic"><th class="px-4 py-4">Naziv</th><th class="px-4 py-4">Opis</th><th class="px-4 py-4">Lotovi</th><th class="px-4 py-4">Status</th><th class="px-4 py-4 text-center">Akcija</th></tr></thead>
                            <tbody class="font-semibold">
                                @foreach ($skladiste->skladisneLokacije as $lokacija)
                                    <tr class="border-b border-borovnica-dark/10 last:border-b-0 hover:bg-white/20"><td class="px-4 py-4">{{ $lokacija->naziv }}</td><td class="px-4 py-4 text-sm font-normal">{{ $lokacija->opis }}</td><td class="px-4 py-4">{{ $lokacija->lotovi_count }}</td><td class="px-4 py-4"><x-status-badge :active="$lokacija->aktivna" active-label="Aktivna" inactive-label="Neaktivna" /></td><td class="px-4 py-4 text-center"><a href="{{ route('skladisne-lokacije.edit', [$skladiste, $lokacija]) }}" class="rounded bg-borovnica-accent px-3 py-2 text-xs font-bold uppercase text-white transition hover:bg-borovnica-dark">Izmeni</a></td></tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="space-y-4 md:hidden">
                        @foreach ($skladiste->skladisneLokacije as $lokacija)
                            <article class="rounded-sm border border-borovnica-dark/20 bg-borovnica-table p-5 shadow-xl">
                                <div class="flex items-start justify-between gap-3"><h3 class="font-bold italic">{{ $lokacija->naziv }}</h3><x-status-badge :active="$lokacija->aktivna" active-label="Aktivna" inactive-label="Neaktivna" /></div>
                                <p class="mt-3 text-sm">{{ $lokacija->opis }}</p>
                                <p class="mt-3 text-sm font-semibold">Broj lotova: {{ $lokacija->lotovi_count }}</p>
                                <a href="{{ route('skladisne-lokacije.edit', [$skladiste, $lokacija]) }}" class="mt-4 block rounded bg-borovnica-accent px-3 py-2 text-center text-xs font-bold uppercase text-white">Izmeni</a>
                            </article>
                        @endforeach
                    </div>
                @endif
            </section>
        </div>
    </div>
</x-app-layout>
