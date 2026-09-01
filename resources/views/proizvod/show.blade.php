<x-app-layout>
    <div class="min-h-screen bg-borovnica-light py-8 sm:py-10">
        <div class="mx-auto max-w-3xl px-4 sm:px-6">
            <div class="mb-8 flex flex-col items-center">
                <h1 class="text-center text-3xl font-bold italic uppercase tracking-widest text-white">Detalji proizvoda</h1>
                <div class="mt-2 h-1 w-20 bg-borovnica-dark"></div>
            </div>

            <article class="rounded-sm border border-borovnica-dark/20 bg-borovnica-table p-5 text-borovnica-dark shadow-2xl sm:p-8">
                <div class="flex flex-col gap-3 border-b border-borovnica-dark/20 pb-5 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <h2 class="text-2xl font-bold italic">{{ $proizvod->naziv }}</h2>
                        <p class="mt-1">{{ $proizvod->sorta->naziv }}</p>
                    </div>
                    <x-status-badge :active="$proizvod->aktivan" />
                </div>

                <dl class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div><dt class="text-sm font-bold italic uppercase tracking-wide">Neto količina</dt><dd class="mt-1 text-lg font-semibold">{{ number_format($proizvod->neto_kolicina_g) }} g</dd></div>
                    <div><dt class="text-sm font-bold italic uppercase tracking-wide">Cena</dt><dd class="mt-1 text-lg font-semibold">{{ number_format($proizvod->cena, 2, ',', '.') }} RSD</dd></div>
                    <div class="sm:col-span-2"><dt class="text-sm font-bold italic uppercase tracking-wide">Opis</dt><dd class="mt-1 whitespace-pre-line">{{ $proizvod->opis }}</dd></div>
                </dl>

                <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:justify-end">
                    <a href="{{ route('proizvod.index') }}" class="rounded-md border border-borovnica-dark/30 bg-borovnica-soft px-5 py-3 text-center text-sm font-bold uppercase tracking-wide">Nazad</a>
                    <a href="{{ route('proizvod.edit', $proizvod) }}" class="rounded-md bg-borovnica-dark px-5 py-3 text-center text-sm font-bold uppercase tracking-wide text-white transition hover:bg-borovnica-accent">Izmeni proizvod</a>
                </div>
            </article>
        </div>
    </div>
</x-app-layout>
