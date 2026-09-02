@props(['status'])

@php
    $classes = match ($status) {
        \App\Enums\NarudzbinaStatus::POTVRDJENA => 'bg-sky-100 text-sky-800',
        \App\Enums\NarudzbinaStatus::OTPREMLJENA => 'bg-emerald-100 text-emerald-800',
        \App\Enums\NarudzbinaStatus::OTKAZANA => 'bg-red-100 text-red-800',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex rounded-full px-3 py-1 text-xs font-bold uppercase tracking-wide {$classes}"]) }}>
    {{ $status->label() }}
</span>
