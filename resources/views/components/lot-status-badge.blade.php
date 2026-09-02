@props(['status'])

@php
    $classes = match ($status) {
        \App\Enums\LotStatus::KREIRAN => 'bg-sky-100 text-sky-800',
        \App\Enums\LotStatus::USKLADISTEN => 'bg-indigo-100 text-indigo-800',
        \App\Enums\LotStatus::RASPOLOZIV => 'bg-emerald-100 text-emerald-800',
        \App\Enums\LotStatus::BLOKIRAN => 'bg-amber-100 text-amber-900',
        \App\Enums\LotStatus::ISCRPLJEN => 'bg-gray-200 text-gray-700',
        \App\Enums\LotStatus::POVUCEN => 'bg-red-100 text-red-800',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex rounded-full px-3 py-1 text-xs font-bold uppercase tracking-wide {$classes}"]) }}>
    {{ $status->label() }}
</span>
