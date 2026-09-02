@props(['status'])

@php
    $classes = match ($status) {
        \App\Enums\LotRaspodelaStatus::REZERVISANO => 'bg-sky-100 text-sky-800',
        \App\Enums\LotRaspodelaStatus::IZDATO => 'bg-emerald-100 text-emerald-800',
        \App\Enums\LotRaspodelaStatus::OTKAZANO => 'bg-gray-200 text-gray-700',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex rounded-full px-3 py-1 text-xs font-bold uppercase tracking-wide {$classes}"]) }}>
    {{ $status->label() }}
</span>
