<x-guest-layout>
    <div class="mb-7 text-center">
        <h1 class="text-3xl font-bold italic uppercase tracking-widest">Registracija</h1>
        <p class="mt-2 text-sm">Registracijom se kreira korisnički nalog kupca.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf
        <div>
            <x-input-label for="name" value="Ime i prezime" />
            <x-text-input id="name" class="mt-1 block w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="email" value="E-mail adresa" />
            <x-text-input id="email" class="mt-1 block w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="password" value="Lozinka" />
            <x-text-input id="password" class="mt-1 block w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="password_confirmation" value="Potvrda lozinke" />
            <x-text-input id="password_confirmation" class="mt-1 block w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>
        <x-primary-button class="w-full py-3">Registruj se</x-primary-button>
    </form>

    <p class="mt-6 border-t border-borovnica-dark/15 pt-5 text-center text-sm">Već imate nalog? <a href="{{ route('login') }}" class="font-bold uppercase underline decoration-borovnica-accent underline-offset-2">Prijavite se</a></p>
</x-guest-layout>
