<x-app-layout>
    <div class="min-h-screen bg-borovnica-light py-8 sm:py-10">
        <div class="mx-auto max-w-4xl px-4 sm:px-6">
            <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <a href="{{ route('resurs.index') }}" class="font-semibold italic text-white transition hover:text-borovnica-soft">← Povratak na listu</a>
                    <h1 class="mt-3 break-words text-3xl font-bold italic uppercase tracking-widest text-white sm:text-4xl">{{ $resurs->naziv }}</h1>
                </div>
                <a href="{{ route('resurs.edit', $resurs) }}" class="self-start rounded-md bg-borovnica-dark px-5 py-2.5 text-sm font-bold uppercase text-white transition hover:bg-borovnica-accent sm:self-auto">Izmeni</a>
            </div>

            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                <section class="rounded-sm border border-borovnica-dark/20 bg-borovnica-table p-5 shadow-xl sm:p-6">
                    <h2 class="border-b border-borovnica-dark/20 pb-2 font-bold italic uppercase">Upotreba resursa</h2>
                    <dl class="mt-5 grid grid-cols-2 gap-x-4 gap-y-3 text-sm">
                        <dt class="font-bold uppercase">Datum</dt><dd class="text-right">{{ $resurs->datum_upotrebe->format('d.m.Y.') }}</dd>
                        <dt class="font-bold uppercase">Količina</dt><dd class="text-right">{{ number_format($resurs->kolicina, 2, ',', '.') }} {{ $resurs->jedinica_mere }}</dd>
                        <dt class="font-bold uppercase">Cena/jed.</dt><dd class="text-right">{{ number_format($resurs->cena_po_jedinici, 2, ',', '.') }} RSD</dd>
                        <dt class="font-bold uppercase">Ukupan trošak</dt><dd class="text-right font-bold">{{ number_format((float) $resurs->kolicina * (float) $resurs->cena_po_jedinici, 2, ',', '.') }} RSD</dd>
                    </dl>
                </section>

                <section class="rounded-sm border border-borovnica-dark/20 bg-borovnica-table p-5 shadow-xl sm:p-6">
                    <h2 class="border-b border-borovnica-dark/20 pb-2 font-bold italic uppercase">Sledljivost</h2>
                    <dl class="mt-5 grid grid-cols-2 gap-x-4 gap-y-3 text-sm">
                        <dt class="font-bold uppercase">Lot</dt><dd class="text-right"><a href="{{ route('lotovi.show', $resurs->lot) }}" class="font-bold underline decoration-borovnica-accent underline-offset-2">{{ $resurs->lot->oznaka }}</a></dd>
                        <dt class="font-bold uppercase">Sorta</dt><dd class="text-right">{{ $resurs->lot->sorta->naziv }}</dd>
                        <dt class="font-bold uppercase">Parcela</dt><dd class="text-right">{{ $resurs->lot->parcela->oznaka }}</dd>
                        <dt class="font-bold uppercase">Evidentirao</dt><dd class="text-right">{{ $resurs->evidentiraoUser->name }}</dd>
                    </dl>
                </section>
            </div>

            <section class="mt-6 rounded-sm border border-red-900/20 bg-red-50/55 p-5 shadow-xl">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div><h2 class="font-bold text-red-900">Brisanje zapisa</h2><p class="mt-1 text-sm text-red-900/80">Brisanjem se uklanja evidentirani trošak iz finansijskog obračuna.</p></div>
                    <form action="{{ route('resurs.destroy', $resurs) }}" method="POST" onsubmit="return confirm('Da li sigurno želite da obrišete ovaj zapis resursa?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full rounded-md bg-red-700 px-5 py-2.5 text-sm font-bold uppercase text-white transition hover:bg-red-800 sm:w-auto">Obriši resurs</button>
                    </form>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
