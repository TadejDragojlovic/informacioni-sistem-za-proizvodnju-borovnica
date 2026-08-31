@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center border-b-2 border-borovnica-soft px-1 pt-1 text-sm font-semibold leading-5 text-white focus:outline-none focus:ring-2 focus:ring-inset focus:ring-borovnica-soft transition duration-150 ease-in-out'
            : 'inline-flex items-center border-b-2 border-transparent px-1 pt-1 text-sm font-medium leading-5 text-white/85 hover:border-borovnica-soft/70 hover:text-white focus:outline-none focus:ring-2 focus:ring-inset focus:ring-borovnica-soft transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
