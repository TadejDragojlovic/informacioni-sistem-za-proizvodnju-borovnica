@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-sm font-bold italic uppercase tracking-wide text-borovnica-dark']) }}>
    {{ $value ?? $slot }}
</label>
