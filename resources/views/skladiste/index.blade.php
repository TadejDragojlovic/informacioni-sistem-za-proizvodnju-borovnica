<x-app-layout>
    <div class="min-h-screen bg-borovnica-light py-8 sm:py-10">
        <div class="mx-auto max-w-6xl px-4 sm:px-6">
            <div class="mb-8 flex flex-col items-center gap-5 sm:mb-10 sm:flex-row sm:justify-between">
                <div class="text-center sm:text-left">
                    <h1 class="text-3xl font-bold italic uppercase tracking-widest text-white sm:text-4xl">Skladišta</h1>
                    <div class="mx-auto mt-2 h-1 w-20 bg-borovnica-dark sm:mx-0"></div>
                </div>
                <a href="{{ route('skladiste.create') }}" class="rounded-full bg-borovnica-dark px-6 py-3 text-sm font-bold uppercase tracking-widest text-white shadow-lg transition hover:bg-borovnica-accent focus:outline-none focus:ring-2 focus:ring-borovnica-dark focus:ring-offset-2">
                    + Dodaj skladište
                </a>
            </div>

            @if ($skladista->isEmpty())
                <div class="rounded-sm border border-borovnica-dark/20 bg-borovnica-table p-8 text-center shadow-xl">
                    <p class="font-semibold italic">Još nema evidentiranih skladišta.</p>
                </div>
            @else
                <div class="hidden overflow-x-auto rounded-sm border border-borovnica-dark/20 bg-borovnica-table shadow-2xl md:block">
                    <table class="w-full min-w-[800px]">
                        <thead>
                            <tr class="border-b-2 border-borovnica-dark/30 text-left text-lg font-bold italic text-borovnica-dark">
                                <th class="px-4 py-4">Naziv</th>
                                <th class="px-4 py-4">Lokacija</th>
                                <th class="px-4 py-4">Mesečni trošak</th>
                                <th class="px-4 py-4">Broj lokacija</th>
                                <th class="px-4 py-4">Status</th>
                                <th class="px-4 py-4 text-center">Akcije</th>
                            </tr>
                        </thead>
                        <tbody class="font-semibold text-borovnica-dark">
                            @foreach ($skladista as $skladiste)
                                <tr class="border-b border-borovnica-dark/10 transition last:border-b-0 hover:bg-white/20">
                                    <td class="px-4 py-4">{{ $skladiste->naziv }}</td>
                                    <td class="px-4 py-4">{{ $skladiste->lokacija }}</td>
                                    <td class="whitespace-nowrap px-4 py-4">{{ number_format($skladiste->mesecni_trosak, 2, ',', '.') }} RSD</td>
                                    <td class="px-4 py-4">{{ $skladiste->skladisne_lokacije_count }}</td>
                                    <td class="px-4 py-4"><x-status-badge :active="$skladiste->aktivan" /></td>
                                    <td class="px-4 py-4">
                                        <div class="flex justify-center gap-2">
                                            <a href="{{ route('skladiste.show', $skladiste) }}" class="rounded bg-borovnica-dark px-3 py-2 text-xs font-bold uppercase text-white transition hover:bg-borovnica-accent">Detalji</a>
                                            <a href="{{ route('skladiste.edit', $skladiste) }}" class="rounded bg-borovnica-accent px-3 py-2 text-xs font-bold uppercase text-white transition hover:bg-borovnica-dark">Izmeni</a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="space-y-4 md:hidden">
                    @foreach ($skladista as $skladiste)
                        <article class="rounded-sm border border-borovnica-dark/20 bg-borovnica-table p-5 text-borovnica-dark shadow-xl">
                            <div class="flex items-start justify-between gap-3">
                                <h2 class="text-lg font-bold italic">{{ $skladiste->naziv }}</h2>
                                <x-status-badge :active="$skladiste->aktivan" />
                            </div>
                            <dl class="mt-4 grid grid-cols-2 gap-x-4 gap-y-2 text-sm">
                                <dt class="font-bold uppercase">Lokacija</dt><dd class="text-right">{{ $skladiste->lokacija }}</dd>
                                <dt class="font-bold uppercase">Mesečni trošak</dt><dd class="text-right">{{ number_format($skladiste->mesecni_trosak, 2, ',', '.') }} RSD</dd>
                                <dt class="font-bold uppercase">Lokacije</dt><dd class="text-right">{{ $skladiste->skladisne_lokacije_count }}</dd>
                            </dl>
                            <div class="mt-5 flex gap-2">
                                <a href="{{ route('skladiste.show', $skladiste) }}" class="flex-1 rounded bg-borovnica-dark px-3 py-2 text-center text-xs font-bold uppercase text-white">Detalji</a>
                                <a href="{{ route('skladiste.edit', $skladiste) }}" class="flex-1 rounded bg-borovnica-accent px-3 py-2 text-center text-xs font-bold uppercase text-white">Izmeni</a>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
