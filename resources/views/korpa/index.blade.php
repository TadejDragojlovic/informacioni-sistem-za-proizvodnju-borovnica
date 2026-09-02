<x-app-layout>
    <div class="min-h-screen bg-borovnica-light py-8 sm:py-10">
        <div class="mx-auto max-w-5xl px-4 sm:px-6">
            <div class="mb-8 text-center sm:mb-10">
                <h1 class="text-3xl font-bold italic uppercase tracking-widest text-white sm:text-4xl">Korpa</h1>
                <div class="mx-auto mt-2 h-1 w-20 bg-borovnica-dark"></div>
            </div>

            <x-form-errors />

            @if (empty($korpa))
                <div class="rounded-sm border border-borovnica-dark/20 bg-borovnica-table p-8 text-center shadow-xl">
                    <p class="font-semibold italic text-borovnica-dark">Vaša korpa je prazna.</p>
                    <a href="{{ route('home') }}" class="mt-6 inline-block rounded-md bg-borovnica-dark px-6 py-3 text-sm font-bold uppercase text-white transition hover:bg-borovnica-accent">Pogledaj proizvode</a>
                </div>
            @else
                <div class="space-y-4">
                    @foreach ($korpa as $id => $detalji)
                        <article class="rounded-sm border border-borovnica-dark/20 bg-borovnica-table p-5 text-borovnica-dark shadow-xl">
                            <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
                                <div class="min-w-0 sm:flex-1">
                                    <h2 class="break-words text-lg font-bold italic">{{ $detalji['naziv'] }}</h2>
                                    @isset($detalji['neto_kolicina_g'])
                                        <p class="mt-1 text-sm">Pakovanje: {{ number_format($detalji['neto_kolicina_g'], 0, ',', '.') }} g</p>
                                    @endisset
                                    <p class="mt-1 text-sm">{{ number_format($detalji['cena'], 2, ',', '.') }} RSD po pakovanju</p>
                                </div>

                                <div class="flex items-center justify-between gap-4 sm:justify-end">
                                    <div class="flex items-center gap-3" aria-label="Količina proizvoda">
                                        <form action="{{ route('korpa.azuriraj', $id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="akcija" value="minus">
                                            <button type="submit" class="flex h-9 w-9 items-center justify-center rounded-full bg-borovnica-dark text-lg font-bold text-white transition hover:bg-borovnica-accent" aria-label="Smanji količinu">−</button>
                                        </form>
                                        <span class="min-w-8 text-center text-xl font-bold">{{ $detalji['kolicina'] }}</span>
                                        <form action="{{ route('korpa.azuriraj', $id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="akcija" value="plus">
                                            <button type="submit" class="flex h-9 w-9 items-center justify-center rounded-full bg-borovnica-dark text-lg font-bold text-white transition hover:bg-borovnica-accent disabled:cursor-not-allowed disabled:opacity-40" aria-label="Povećaj količinu" @disabled($detalji['kolicina'] >= 100)>+</button>
                                        </form>
                                    </div>

                                    <form action="{{ route('korpa.obrisi', $id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="rounded-md border border-red-700 px-3 py-2 text-xs font-bold uppercase text-red-800 transition hover:bg-red-100" aria-label="Ukloni {{ $detalji['naziv'] }} iz korpe">Ukloni</button>
                                    </form>
                                </div>
                            </div>

                            <div class="mt-4 border-t border-borovnica-dark/15 pt-4 text-right font-bold">
                                {{ number_format($detalji['cena'] * $detalji['kolicina'], 2, ',', '.') }} RSD
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="mt-6 rounded-sm border border-borovnica-dark/20 bg-borovnica-table p-5 shadow-2xl sm:p-7">
                    <div class="flex items-center justify-between gap-4 border-b-2 border-borovnica-dark/20 pb-5 text-borovnica-dark">
                        <span class="text-lg font-bold uppercase sm:text-xl">Ukupan iznos</span>
                        <span class="text-xl font-bold sm:text-2xl">{{ number_format($ukupno, 2, ',', '.') }} RSD</span>
                    </div>

                    <form action="{{ route('narudzbine.potvrdi') }}" method="POST" class="mt-6">
                        @csrf
                        <div>
                            <x-input-label for="adresa_isporuke" value="Adresa isporuke" class="mb-1" />
                            <x-text-input id="adresa_isporuke" name="adresa_isporuke" type="text" class="w-full" :value="old('adresa_isporuke')" required maxlength="255" autocomplete="street-address" placeholder="Ulica i broj, mesto" />
                            <x-input-error :messages="$errors->get('adresa_isporuke')" class="mt-2" />
                        </div>

                        <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                            <a href="{{ route('home') }}" class="rounded-md border border-borovnica-dark/30 bg-borovnica-soft px-6 py-3 text-center text-sm font-bold uppercase text-borovnica-dark transition hover:bg-white/50">Nastavi kupovinu</a>
                            <button type="submit" class="rounded-md bg-borovnica-dark px-6 py-3 text-sm font-bold uppercase text-white shadow-md transition hover:bg-borovnica-accent">Potvrdi narudžbinu</button>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
