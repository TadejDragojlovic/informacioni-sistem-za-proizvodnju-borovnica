@php
    use App\Enums\LotStatus;
@endphp

<x-app-layout>
    <div class="min-h-screen bg-borovnica-light py-8 sm:py-10">
        <div class="mx-auto max-w-6xl px-4 sm:px-6">
            <div class="mb-8 flex flex-col items-center gap-4 sm:flex-row sm:justify-between">
                <div class="text-center sm:text-left">
                    <p class="text-sm font-bold uppercase tracking-widest text-borovnica-dark/70">Lot</p>
                    <h1 class="text-3xl font-bold italic uppercase tracking-widest text-white sm:text-4xl">{{ $lot->oznaka }}</h1>
                    <div class="mx-auto mt-2 h-1 w-20 bg-borovnica-dark sm:mx-0"></div>
                </div>
                <x-lot-status-badge :status="$lot->status" class="text-sm" />
            </div>

            <x-form-errors />

            <section class="rounded-sm border border-borovnica-dark/20 bg-borovnica-table p-5 shadow-2xl sm:p-8">
                <dl class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    <div><dt class="text-xs font-bold italic uppercase tracking-wide">Sorta</dt><dd class="mt-1 text-lg font-semibold">{{ $lot->sorta->naziv }}</dd></div>
                    <div><dt class="text-xs font-bold italic uppercase tracking-wide">Parcela</dt><dd class="mt-1 text-lg font-semibold">{{ $lot->parcela->oznaka }}</dd></div>
                    <div><dt class="text-xs font-bold italic uppercase tracking-wide">Datum berbe</dt><dd class="mt-1 text-lg font-semibold">{{ $lot->datum_berbe->format('d.m.Y.') }}</dd></div>
                    <div><dt class="text-xs font-bold italic uppercase tracking-wide">Klasa kvaliteta</dt><dd class="mt-1 text-lg font-semibold">{{ $lot->klasa_kvaliteta?->label() ?? 'Nije dodeljena' }}</dd></div>
                    <div><dt class="text-xs font-bold italic uppercase tracking-wide">Početna količina</dt><dd class="mt-1 text-lg font-semibold">{{ number_format($lot->pocetna_kolicina_g, 0, ',', '.') }} g</dd></div>
                    <div><dt class="text-xs font-bold italic uppercase tracking-wide">Raspoloživa količina</dt><dd class="mt-1 text-lg font-semibold">{{ number_format($lot->raspoloziva_kolicina_g, 0, ',', '.') }} g</dd></div>
                    <div class="sm:col-span-2"><dt class="text-xs font-bold italic uppercase tracking-wide">Trenutna lokacija</dt><dd class="mt-1 text-lg font-semibold">{{ $lot->trenutnaSkladisnaLokacija ? $lot->trenutnaSkladisnaLokacija->skladiste->naziv.' — '.$lot->trenutnaSkladisnaLokacija->naziv : 'Nije uskladišten' }}</dd></div>
                    <div class="sm:col-span-2"><dt class="text-xs font-bold italic uppercase tracking-wide">Dokument kvaliteta</dt><dd class="mt-1">{{ $lot->broj_dokumenta_kvaliteta ?? '—' }}</dd></div>
                    <div class="sm:col-span-2"><dt class="text-xs font-bold italic uppercase tracking-wide">Napomena</dt><dd class="mt-1 whitespace-pre-line">{{ $lot->napomena ?? '—' }}</dd></div>
                </dl>
                <div class="mt-8"><a href="{{ route('lotovi.index') }}" class="inline-block rounded-md border border-borovnica-dark/30 bg-borovnica-soft px-5 py-3 text-sm font-bold uppercase tracking-wide">Nazad na lotove</a></div>
            </section>

            @include('lot.partials.actions')
            @include('lot.partials.timeline')
        </div>
    </div>
</x-app-layout>
