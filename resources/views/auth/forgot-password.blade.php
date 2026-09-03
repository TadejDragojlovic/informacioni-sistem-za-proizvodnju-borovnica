<x-guest-layout>
    <div class="mb-6 text-center">
        <h1 class="text-2xl font-bold italic uppercase tracking-widest">Zaboravljena lozinka</h1>
        <p class="mt-3 text-sm">Unesite e-mail adresu i poslaćemo vam link za postavljanje nove lozinke.</p>
    </div>
    <x-auth-session-status class="mb-5 rounded-sm bg-emerald-100 p-3 text-emerald-800" :status="session('status')" />
    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <x-input-label for="email" value="E-mail adresa" />
        <x-text-input id="email" class="mt-1 block w-full" type="email" name="email" :value="old('email')" required autofocus />
        <x-input-error :messages="$errors->get('email')" class="mt-2" />
        <x-primary-button class="mt-6 w-full py-3">Pošalji link za novu lozinku</x-primary-button>
    </form>
    <a href="{{ route('login') }}" class="mt-5 block text-center text-sm font-semibold underline decoration-borovnica-accent underline-offset-2">Nazad na prijavu</a>
</x-guest-layout>
