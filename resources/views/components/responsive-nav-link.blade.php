@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full border-s-4 border-borovnica-soft bg-borovnica-accent px-4 py-2 text-start text-base font-semibold text-white focus:outline-none focus:ring-2 focus:ring-inset focus:ring-borovnica-soft transition duration-150 ease-in-out'
            : 'block w-full border-s-4 border-transparent px-4 py-2 text-start text-base font-medium text-borovnica-soft hover:border-borovnica-soft/70 hover:bg-borovnica-accent/60 hover:text-white focus:outline-none focus:ring-2 focus:ring-inset focus:ring-borovnica-soft transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
