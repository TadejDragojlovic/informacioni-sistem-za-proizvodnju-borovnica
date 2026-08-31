<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center justify-center rounded-md border border-borovnica-dark/30 bg-borovnica-soft px-4 py-2 text-xs font-semibold uppercase tracking-widest text-borovnica-dark shadow-sm transition duration-150 ease-in-out hover:bg-white focus:outline-none focus:ring-2 focus:ring-borovnica-dark focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50']) }}>
    {{ $slot }}
</button>
