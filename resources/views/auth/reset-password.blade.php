<x-guest-layout>
    <div class="mb-6 text-center"><h1 class="text-2xl font-bold italic uppercase tracking-widest">Nova lozinka</h1></div>
    <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">
        <div><x-input-label for="email" value="E-mail adresa" /><x-text-input id="email" class="mt-1 block w-full" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" /><x-input-error :messages="$errors->get('email')" class="mt-2" /></div>
        <div><x-input-label for="password" value="Nova lozinka" /><x-text-input id="password" class="mt-1 block w-full" type="password" name="password" required autocomplete="new-password" /><x-input-error :messages="$errors->get('password')" class="mt-2" /></div>
        <div><x-input-label for="password_confirmation" value="Potvrda lozinke" /><x-text-input id="password_confirmation" class="mt-1 block w-full" type="password" name="password_confirmation" required autocomplete="new-password" /><x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" /></div>
        <x-primary-button class="w-full py-3">Sačuvaj novu lozinku</x-primary-button>
    </form>
</x-guest-layout>
