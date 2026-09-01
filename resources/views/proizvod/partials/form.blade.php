<div class="grid grid-cols-1 gap-6 md:grid-cols-2">
    <div class="md:col-span-2">
        <x-input-label for="naziv" value="Naziv" class="mb-2" />
        <x-text-input id="naziv" name="naziv" type="text" class="w-full py-3" :value="old('naziv', $proizvod->naziv ?? '')" required autofocus />
        <x-input-error :messages="$errors->get('naziv')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="sorta_id" value="Sorta" class="mb-2" />
        <select id="sorta_id" name="sorta_id" class="w-full rounded-md border-borovnica-dark/20 bg-white/70 py-3 text-borovnica-dark focus:border-borovnica-accent focus:ring-borovnica-accent" required>
            <option value="">Izaberite sortu</option>
            @foreach ($sorte as $sorta)
                <option value="{{ $sorta->id }}" @selected((string) old('sorta_id', $proizvod->sorta_id ?? '') === (string) $sorta->id)>{{ $sorta->naziv }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('sorta_id')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="neto_kolicina_g" value="Neto količina (g)" class="mb-2" />
        <x-text-input id="neto_kolicina_g" name="neto_kolicina_g" type="number" min="1" step="1" class="w-full py-3" :value="old('neto_kolicina_g', $proizvod->neto_kolicina_g ?? '')" required />
        <x-input-error :messages="$errors->get('neto_kolicina_g')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="cena" value="Cena (RSD)" class="mb-2" />
        <x-text-input id="cena" name="cena" type="number" min="0" step="0.01" class="w-full py-3" :value="old('cena', $proizvod->cena ?? '')" required />
        <x-input-error :messages="$errors->get('cena')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="aktivan" value="Status" class="mb-2" />
        <select id="aktivan" name="aktivan" class="w-full rounded-md border-borovnica-dark/20 bg-white/70 py-3 text-borovnica-dark focus:border-borovnica-accent focus:ring-borovnica-accent" required>
            <option value="1" @selected((string) old('aktivan', isset($proizvod) ? (int) $proizvod->aktivan : 1) === '1')>Aktivan</option>
            <option value="0" @selected((string) old('aktivan', isset($proizvod) ? (int) $proizvod->aktivan : 1) === '0')>Neaktivan</option>
        </select>
        <x-input-error :messages="$errors->get('aktivan')" class="mt-2" />
    </div>

    <div class="md:col-span-2">
        <x-input-label for="opis" value="Opis" class="mb-2" />
        <textarea id="opis" name="opis" rows="4" class="w-full rounded-md border-borovnica-dark/20 bg-white/70 py-3 text-borovnica-dark focus:border-borovnica-accent focus:ring-borovnica-accent" required>{{ old('opis', $proizvod->opis ?? '') }}</textarea>
        <x-input-error :messages="$errors->get('opis')" class="mt-2" />
    </div>
</div>

<div class="mt-10 flex flex-col gap-4">
    <button type="submit" class="w-full rounded-full bg-borovnica-dark py-4 font-bold uppercase tracking-widest text-white shadow-lg transition hover:bg-borovnica-accent focus:outline-none focus:ring-2 focus:ring-borovnica-dark focus:ring-offset-2">{{ $submitLabel }}</button>
    <a href="{{ route('proizvod.index') }}" class="text-center font-semibold italic text-borovnica-dark/60 transition hover:text-borovnica-dark">Nazad na proizvode</a>
</div>
