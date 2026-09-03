<x-guest-layout>
    <div class="mb-6 text-center">
        <h1 class="text-2xl font-bold italic uppercase tracking-widest">Potvrda e-mail adrese</h1>
        <p class="mt-3 text-sm">Poslali smo vam link za potvrdu adrese. Ako poruka nije stigla, možete zatražiti novu.</p>
    </div>
    @if (session('status') === 'verification-link-sent')
        <p class="mb-5 rounded-sm bg-emerald-100 p-3 text-sm font-semibold text-emerald-800">Novi link za potvrdu je poslat.</p>
    @endif
    <div class="flex flex-col gap-3 sm:flex-row sm:justify-between">
        <form method="POST" action="{{ route('verification.send') }}">@csrf<x-primary-button class="w-full sm:w-auto">Pošalji novi link</x-primary-button></form>
        <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="w-full rounded-md border border-borovnica-dark/30 px-4 py-2 text-xs font-bold uppercase transition hover:bg-white/40 sm:w-auto">Odjavi se</button></form>
    </div>
</x-guest-layout>
