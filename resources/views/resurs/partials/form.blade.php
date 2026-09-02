@php($resurs = $resurs ?? null)

<div class="grid grid-cols-1 gap-5 md:grid-cols-2">
    <div class="md:col-span-2">
        <x-input-label for="lot_id" value="Lot" />
        <select id="lot_id" name="lot_id" required class="mt-1 block w-full rounded-md border-borovnica-dark/20 bg-white/70 text-borovnica-dark focus:border-borovnica-accent focus:ring-borovnica-accent">
            <option value="">Izaberite lot</option>
            @foreach ($lotovi as $lot)
                <option value="{{ $lot->id }}" @selected((string) old('lot_id', $resurs?->lot_id) === (string) $lot->id)>
                    {{ $lot->oznaka }} — {{ $lot->sorta->naziv }} (berba {{ $lot->datum_berbe->format('d.m.Y.') }})
                </option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('lot_id')" class="mt-2" />
    </div>

    <div class="md:col-span-2">
        <x-input-label for="naziv" value="Naziv resursa" />
        <x-text-input id="naziv" name="naziv" type="text" class="mt-1 block w-full" :value="old('naziv', $resurs?->naziv)" required maxlength="255" placeholder="npr. Ambalažne kutije 500 g" />
        <x-input-error :messages="$errors->get('naziv')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="kolicina" value="Količina" />
        <x-text-input id="kolicina" name="kolicina" type="number" class="mt-1 block w-full" :value="old('kolicina', $resurs?->kolicina)" required min="0.01" max="99999999.99" step="0.01" inputmode="decimal" />
        <x-input-error :messages="$errors->get('kolicina')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="jedinica_mere" value="Jedinica mere" />
        <x-text-input id="jedinica_mere" name="jedinica_mere" type="text" class="mt-1 block w-full" :value="old('jedinica_mere', $resurs?->jedinica_mere)" required maxlength="50" placeholder="kg, l, kom..." />
        <x-input-error :messages="$errors->get('jedinica_mere')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="cena_po_jedinici" value="Cena po jedinici (RSD)" />
        <x-text-input id="cena_po_jedinici" name="cena_po_jedinici" type="number" class="mt-1 block w-full" :value="old('cena_po_jedinici', $resurs?->cena_po_jedinici)" required min="0" max="99999999.99" step="0.01" inputmode="decimal" />
        <x-input-error :messages="$errors->get('cena_po_jedinici')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="datum_upotrebe" value="Datum upotrebe" />
        <x-text-input id="datum_upotrebe" name="datum_upotrebe" type="date" class="mt-1 block w-full" :value="old('datum_upotrebe', $resurs?->datum_upotrebe?->format('Y-m-d'))" required />
        <x-input-error :messages="$errors->get('datum_upotrebe')" class="mt-2" />
    </div>
</div>

<div class="mt-8 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
    <a href="{{ $resurs ? route('resurs.show', $resurs) : route('resurs.index') }}" class="rounded-md border border-borovnica-dark/30 bg-borovnica-soft px-6 py-3 text-center text-sm font-bold uppercase text-borovnica-dark transition hover:bg-white/50">Otkaži</a>
    <button type="submit" class="rounded-md bg-borovnica-dark px-6 py-3 text-sm font-bold uppercase text-white shadow-md transition hover:bg-borovnica-accent">{{ $submitLabel }}</button>
</div>
