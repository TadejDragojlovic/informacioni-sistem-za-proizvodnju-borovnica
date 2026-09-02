<x-app-layout>
    <div class="min-h-screen bg-borovnica-light py-8 sm:py-10">
        <div class="mx-auto max-w-6xl px-4 sm:px-6">
            <div class="mb-8 text-center sm:mb-10">
                <h1 class="text-3xl font-bold italic uppercase tracking-widest text-borovnica-dark sm:text-4xl">Sveže borovnice</h1>
                <p class="mx-auto mt-3 max-w-2xl text-sm font-medium sm:text-base">Izaberite pakovanje i količinu, a poreklo proizvoda možete pratiti kroz lotove svoje narudžbine.</p>
            </div>

            <x-form-errors />

            @if ($proizvodi->isEmpty())
                <div class="rounded-sm border border-borovnica-dark/20 bg-borovnica-table p-8 text-center font-semibold italic shadow-xl">Trenutno nema proizvoda dostupnih za poručivanje.</div>
            @else
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($proizvodi as $proizvod)
                        <article class="flex flex-col rounded-sm border border-borovnica-dark/20 bg-borovnica-table p-5 text-borovnica-dark shadow-xl sm:p-6">
                            <div class="flex-1">
                                <h2 class="text-xl font-bold italic">{{ $proizvod->naziv }}</h2>
                                @if ($proizvod->opis)
                                    <p class="mt-3 text-sm leading-relaxed">{{ $proizvod->opis }}</p>
                                @endif
                            </div>

                            <dl class="mt-5 grid grid-cols-2 gap-2 border-t border-borovnica-dark/15 pt-4 text-sm">
                                <dt class="font-bold uppercase">Pakovanje</dt><dd class="text-right">{{ number_format($proizvod->neto_kolicina_g, 0, ',', '.') }} g</dd>
                                <dt class="font-bold uppercase">Cena</dt><dd class="text-right font-semibold">{{ number_format($proizvod->cena, 2, ',', '.') }} RSD</dd>
                            </dl>

                            @auth
                                @if (auth()->user()->role === \App\Enums\UserRole::KUPAC)
                                    <form action="{{ route('korpa.dodaj', $proizvod) }}" method="POST" class="mt-5 flex gap-2">
                                        @csrf
                                        <label for="kolicina-{{ $proizvod->id }}" class="sr-only">Količina za {{ $proizvod->naziv }}</label>
                                        <input id="kolicina-{{ $proizvod->id }}" type="number" name="kolicina" value="1" min="1" max="100" required class="w-20 rounded-md border-borovnica-dark/20 bg-white/70 py-2 text-center focus:border-borovnica-accent focus:ring-borovnica-accent">
                                        <button type="submit" class="flex-1 rounded-md bg-borovnica-dark px-4 py-2 text-sm font-bold uppercase text-white transition hover:bg-borovnica-accent">Dodaj u korpu</button>
                                    </form>
                                @endif
                            @else
                                <a href="{{ route('login') }}" class="mt-5 block rounded-md bg-borovnica-dark px-4 py-2.5 text-center text-sm font-bold uppercase text-white transition hover:bg-borovnica-accent">Prijavi se za poručivanje</a>
                            @endauth
                        </article>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
