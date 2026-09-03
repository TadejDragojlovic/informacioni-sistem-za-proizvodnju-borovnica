<section>
    <header>
        <h2 class="text-lg font-bold italic uppercase">Podaci profila</h2>
        <p class="mt-1 text-sm">Izmenite ime ili e-mail adresu svog naloga.</p>
    </header>

    <form id="send-verification" method="POST" action="{{ route('verification.send') }}">@csrf</form>

    <form method="POST" action="{{ route('profile.update') }}" class="mt-6 space-y-5">
        @csrf
        @method('PATCH')
        <div>
            <x-input-label for="name" value="Ime i prezime" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>
        <div>
            <x-input-label for="email" value="E-mail adresa" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <p class="mt-2 text-sm">E-mail adresa nije potvrđena. <button form="send-verification" class="font-semibold underline decoration-borovnica-accent underline-offset-2">Pošalji novi link za potvrdu.</button></p>
                @if (session('status') === 'verification-link-sent')
                    <p class="mt-2 text-sm font-semibold text-emerald-700">Novi link za potvrdu je poslat.</p>
                @endif
            @endif
        </div>
        <div class="flex items-center gap-4">
            <x-primary-button>Sačuvaj podatke</x-primary-button>
            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)" class="text-sm font-semibold text-emerald-700">Podaci su sačuvani.</p>
            @endif
        </div>
    </form>
</section>
