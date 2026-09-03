<section>
    <header>
        <h2 class="text-lg font-bold italic uppercase text-red-900">Brisanje naloga</h2>
        <p class="mt-1 text-sm text-red-900/80">Nalog koji je povezan sa poslovnom istorijom sistema ne može biti obrisan. Za ostale naloge brisanje je trajno.</p>
    </header>

    <x-danger-button class="mt-6" x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')">Obriši nalog</x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="POST" action="{{ route('profile.destroy') }}" class="bg-borovnica-table p-5 sm:p-6">
            @csrf
            @method('DELETE')
            <h2 class="text-lg font-bold italic uppercase">Potvrdite brisanje naloga</h2>
            <p class="mt-2 text-sm">Unesite lozinku da biste potvrdili trajno brisanje naloga.</p>
            <div class="mt-5">
                <x-input-label for="delete_account_password" value="Lozinka" />
                <x-text-input id="delete_account_password" name="password" type="password" class="mt-1 block w-full" autocomplete="current-password" />
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>
            <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                <x-secondary-button type="button" x-on:click="$dispatch('close')">Odustani</x-secondary-button>
                <x-danger-button>Trajno obriši nalog</x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
