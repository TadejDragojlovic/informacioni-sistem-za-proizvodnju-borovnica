<x-app-layout>
    <div class="min-h-screen bg-borovnica-light py-8 sm:py-10">
        <div class="mx-auto max-w-3xl px-4 sm:px-6">
            <div class="mb-8 flex flex-col items-center"><h1 class="text-center text-3xl font-bold italic uppercase tracking-widest text-white">Kreiranje lota</h1><div class="mt-2 h-1 w-20 bg-borovnica-dark"></div></div>
            <div class="rounded-sm border border-borovnica-dark/20 bg-borovnica-table p-5 font-semibold shadow-2xl sm:p-8">
                <x-form-errors />
                <p class="mb-6 text-sm italic text-borovnica-dark/75">Oznaka lota i početni događaj biće generisani automatski.</p>
                <form action="{{ route('lotovi.store') }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div><x-input-label for="sorta_id" value="Sorta" class="mb-2" /><select id="sorta_id" name="sorta_id" class="w-full rounded-md border-borovnica-dark/20 bg-white/70 py-3 focus:border-borovnica-accent focus:ring-borovnica-accent" required><option value="">Izaberite sortu</option>@foreach ($sorte as $sorta)<option value="{{ $sorta->id }}" @selected((string) old('sorta_id') === (string) $sorta->id)>{{ $sorta->naziv }}</option>@endforeach</select><x-input-error :messages="$errors->get('sorta_id')" class="mt-2" /></div>
                        <div><x-input-label for="parcela_id" value="Parcela" class="mb-2" /><select id="parcela_id" name="parcela_id" class="w-full rounded-md border-borovnica-dark/20 bg-white/70 py-3 focus:border-borovnica-accent focus:ring-borovnica-accent" required><option value="">Izaberite parcelu</option>@foreach ($parcele as $parcela)<option value="{{ $parcela->id }}" @selected((string) old('parcela_id') === (string) $parcela->id)>{{ $parcela->oznaka }} — {{ $parcela->zemlja_porekla }}</option>@endforeach</select><x-input-error :messages="$errors->get('parcela_id')" class="mt-2" /></div>
                        <div><x-input-label for="datum_berbe" value="Datum berbe" class="mb-2" /><x-text-input id="datum_berbe" name="datum_berbe" type="date" class="w-full py-3" :value="old('datum_berbe', now()->format('Y-m-d'))" required /><x-input-error :messages="$errors->get('datum_berbe')" class="mt-2" /></div>
                        <div><x-input-label for="pocetna_kolicina_g" value="Početna količina (g)" class="mb-2" /><x-text-input id="pocetna_kolicina_g" name="pocetna_kolicina_g" type="number" min="1" step="1" class="w-full py-3" :value="old('pocetna_kolicina_g')" required /><x-input-error :messages="$errors->get('pocetna_kolicina_g')" class="mt-2" /></div>
                        <div class="md:col-span-2"><x-input-label for="napomena" value="Napomena" class="mb-2" /><textarea id="napomena" name="napomena" rows="4" class="w-full rounded-md border-borovnica-dark/20 bg-white/70 py-3 focus:border-borovnica-accent focus:ring-borovnica-accent">{{ old('napomena') }}</textarea><x-input-error :messages="$errors->get('napomena')" class="mt-2" /></div>
                    </div>
                    <div class="mt-10 flex flex-col gap-4"><button type="submit" class="w-full rounded-full bg-borovnica-dark py-4 font-bold uppercase tracking-widest text-white shadow-lg transition hover:bg-borovnica-accent">Kreiraj lot</button><a href="{{ route('lotovi.index') }}" class="text-center font-semibold italic text-borovnica-dark/60 hover:text-borovnica-dark">Nazad na lotove</a></div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
