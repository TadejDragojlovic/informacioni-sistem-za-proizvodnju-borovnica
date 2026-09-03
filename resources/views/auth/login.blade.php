<x-guest-layout>
    <div class="mb-7 text-center">
        <h1 class="text-3xl font-bold italic uppercase tracking-widest">Prijava</h1>
        <p class="mt-2 text-sm">Prijavite se da biste pristupili svom delu sistema.</p>
    </div>

    <x-auth-session-status class="mb-5 rounded-sm bg-emerald-100 p-3 text-emerald-800" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf
        <div>
            <x-input-label for="email" value="E-mail adresa" />
            <x-text-input id="email" class="mt-1 block w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="password" value="Lozinka" />
            <x-text-input id="password" class="mt-1 block w-full" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>
        <label for="remember_me" class="inline-flex items-center gap-2 text-sm">
            <input id="remember_me" type="checkbox" class="rounded border-borovnica-dark/30 text-borovnica-dark shadow-sm focus:ring-borovnica-accent" name="remember">
            <span>Zapamti me</span>
        </label>
        <x-primary-button class="w-full py-3">Prijavi se</x-primary-button>
    </form>

    <div class="mt-6 flex flex-col items-center gap-3 border-t border-borovnica-dark/15 pt-5 text-sm sm:flex-row sm:justify-between">
        @if (Route::has('password.request'))
            <a href="{{ route('password.request') }}" class="font-semibold underline decoration-borovnica-accent underline-offset-2">Zaboravili ste lozinku?</a>
        @endif
        <a href="{{ route('register') }}" class="font-bold uppercase">Napravi nalog</a>
    </div>
</x-guest-layout>
