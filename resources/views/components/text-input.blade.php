@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'rounded-md border-borovnica-dark/20 bg-white/70 text-borovnica-dark shadow-sm placeholder:text-borovnica-dark/50 focus:border-borovnica-accent focus:ring-borovnica-accent disabled:cursor-not-allowed disabled:opacity-60']) }}>
