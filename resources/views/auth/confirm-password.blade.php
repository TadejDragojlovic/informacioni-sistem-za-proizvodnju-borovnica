<x-guest-layout>
    <div class="mb-6 text-center">
        <h1 class="text-2xl font-bold italic uppercase tracking-widest">Potvrda lozinke</h1>
        <p class="mt-3 text-sm">Ovo je zaštićeni deo aplikacije. Potvrdite lozinku pre nastavka.</p>
    </div>
    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf
        <x-input-label for="password" value="Lozinka" />
        <x-text-input id="password" class="mt-1 block w-full" type="password" name="password" required autocomplete="current-password" />
        <x-input-error :messages="$errors->get('password')" class="mt-2" />
        <x-primary-button class="mt-6 w-full py-3">Potvrdi</x-primary-button>
    </form>
</x-guest-layout>
