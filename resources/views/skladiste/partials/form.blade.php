<div class="grid grid-cols-1 gap-6 md:grid-cols-2">
    <div class="md:col-span-2">
        <x-input-label for="naziv" value="Naziv" class="mb-2" />
        <x-text-input id="naziv" name="naziv" type="text" class="w-full py-3" :value="old('naziv', $skladiste->naziv ?? '')" required autofocus />
        <x-input-error :messages="$errors->get('naziv')" class="mt-2" />
    </div>
    <div class="md:col-span-2">
        <x-input-label for="lokacija" value="Lokacija" class="mb-2" />
        <x-text-input id="lokacija" name="lokacija" type="text" class="w-full py-3" :value="old('lokacija', $skladiste->lokacija ?? '')" placeholder="npr. Valjevo" required />
        <x-input-error :messages="$errors->get('lokacija')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="mesecni_trosak" value="Mesečni trošak (RSD)" class="mb-2" />
        <x-text-input id="mesecni_trosak" name="mesecni_trosak" type="number" min="0" step="0.01" class="w-full py-3" :value="old('mesecni_trosak', $skladiste->mesecni_trosak ?? '')" required />
        <x-input-error :messages="$errors->get('mesecni_trosak')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="aktivan" value="Status" class="mb-2" />
        <select id="aktivan" name="aktivan" class="w-full rounded-md border-borovnica-dark/20 bg-white/70 py-3 text-borovnica-dark focus:border-borovnica-accent focus:ring-borovnica-accent" required>
            <option value="1" @selected((string) old('aktivan', isset($skladiste) ? (int) $skladiste->aktivan : 1) === '1')>Aktivno</option>
            <option value="0" @selected((string) old('aktivan', isset($skladiste) ? (int) $skladiste->aktivan : 1) === '0')>Neaktivno</option>
        </select>
        <x-input-error :messages="$errors->get('aktivan')" class="mt-2" />
    </div>
</div>
<div class="mt-10 flex flex-col gap-4">
    <button type="submit" class="w-full rounded-full bg-borovnica-dark py-4 font-bold uppercase tracking-widest text-white shadow-lg transition hover:bg-borovnica-accent focus:outline-none focus:ring-2 focus:ring-borovnica-dark focus:ring-offset-2">{{ $submitLabel }}</button>
    <a href="{{ route('skladiste.index') }}" class="text-center font-semibold italic text-borovnica-dark/60 transition hover:text-borovnica-dark">Nazad na skladišta</a>
</div>
