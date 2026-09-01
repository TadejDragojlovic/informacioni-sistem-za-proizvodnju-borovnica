<div class="space-y-6">
    <div>
        <x-input-label for="naziv" value="Naziv" class="mb-2" />
        <x-text-input id="naziv" name="naziv" type="text" class="w-full py-3" :value="old('naziv', $skladisnaLokacija->naziv ?? '')" required autofocus />
        <x-input-error :messages="$errors->get('naziv')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="opis" value="Opis" class="mb-2" />
        <textarea id="opis" name="opis" rows="4" class="w-full rounded-md border-borovnica-dark/20 bg-white/70 py-3 text-borovnica-dark focus:border-borovnica-accent focus:ring-borovnica-accent" required>{{ old('opis', $skladisnaLokacija->opis ?? '') }}</textarea>
        <x-input-error :messages="$errors->get('opis')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="aktivna" value="Status" class="mb-2" />
        <select id="aktivna" name="aktivna" class="w-full rounded-md border-borovnica-dark/20 bg-white/70 py-3 text-borovnica-dark focus:border-borovnica-accent focus:ring-borovnica-accent" required>
            <option value="1" @selected((string) old('aktivna', isset($skladisnaLokacija) ? (int) $skladisnaLokacija->aktivna : 1) === '1')>Aktivna</option>
            <option value="0" @selected((string) old('aktivna', isset($skladisnaLokacija) ? (int) $skladisnaLokacija->aktivna : 1) === '0')>Neaktivna</option>
        </select>
        <x-input-error :messages="$errors->get('aktivna')" class="mt-2" />
    </div>
</div>
<div class="mt-10 flex flex-col gap-4">
    <button type="submit" class="w-full rounded-full bg-borovnica-dark py-4 font-bold uppercase tracking-widest text-white shadow-lg transition hover:bg-borovnica-accent focus:outline-none focus:ring-2 focus:ring-borovnica-dark focus:ring-offset-2">{{ $submitLabel }}</button>
    <a href="{{ route('skladiste.show', $skladiste) }}" class="text-center font-semibold italic text-borovnica-dark/60 transition hover:text-borovnica-dark">Nazad na skladište</a>
</div>
