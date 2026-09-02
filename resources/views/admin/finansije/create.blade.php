<x-app-layout>
    <div class="min-h-screen bg-borovnica-light py-8 sm:py-10">
        <div class="mx-auto max-w-2xl px-4 sm:px-6">
            <div class="mb-8 text-center sm:mb-10">
                <h1 class="text-3xl font-bold italic uppercase tracking-widest text-white sm:text-4xl">Finansijski izveštaj</h1>
                <div class="mx-auto mt-2 h-1 w-20 bg-borovnica-dark"></div>
                <p class="mx-auto mt-4 max-w-xl text-sm font-medium text-borovnica-dark">Izaberite period za obračun prihoda otpremljenih narudžbina i povezanih troškova.</p>
            </div>

            <x-form-errors />

            <div class="rounded-sm border border-borovnica-dark/20 bg-borovnica-table p-5 shadow-2xl sm:p-8">
                <form action="{{ route('admin.finansije.generate') }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <div>
                            <x-input-label for="datum_od" value="Datum od" />
                            <x-text-input id="datum_od" name="datum_od" type="date" class="mt-1 block w-full" :value="old('datum_od', $podrazumevaniDatumOd)" required />
                            <x-input-error :messages="$errors->get('datum_od')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="datum_do" value="Datum do" />
                            <x-text-input id="datum_do" name="datum_do" type="date" class="mt-1 block w-full" :value="old('datum_do', $podrazumevaniDatumDo)" required />
                            <x-input-error :messages="$errors->get('datum_do')" class="mt-2" />
                        </div>
                    </div>

                    <button type="submit" class="mt-7 w-full rounded-md bg-borovnica-dark px-6 py-3 text-sm font-bold uppercase tracking-widest text-white shadow-md transition hover:bg-borovnica-accent">Prikaži finansijski pregled</button>
                </form>
            </div>

            <div class="mt-5 rounded-sm border border-borovnica-dark/15 bg-white/20 p-4 text-sm text-borovnica-dark">
                <p><strong>Prihod:</strong> vrednost otpremljenih narudžbina u izabranom periodu.</p>
                <p class="mt-1"><strong>Rashod:</strong> mesečni trošak relevantnih skladišta, obračunat jednom po skladištu, i evidentirani resursi korišćenih lotova.</p>
            </div>
        </div>
    </div>
</x-app-layout>
