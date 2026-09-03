<x-app-layout>
    <div class="min-h-screen bg-borovnica-light py-8 sm:py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6">
            <div class="mb-8 flex flex-col gap-3 sm:mb-10 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-sm font-bold uppercase tracking-widest text-borovnica-dark">{{ auth()->user()->role->label() }}</p>
                    <h1 class="mt-1 text-3xl font-bold italic uppercase tracking-widest text-white sm:text-4xl">Dobro došli, {{ auth()->user()->name }}</h1>
                    <div class="mt-2 h-1 w-20 bg-borovnica-dark"></div>
                </div>
                <a href="{{ route('profile.edit') }}" class="self-start rounded-md border border-borovnica-dark/30 bg-borovnica-soft px-5 py-2.5 text-sm font-bold uppercase text-borovnica-dark transition hover:bg-white/60 sm:self-auto">Moj profil</a>
            </div>

            <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
                @foreach ($statistika as $podatak)
                    <section class="rounded-sm border border-borovnica-dark/20 bg-borovnica-table p-4 shadow-xl sm:p-5">
                        <h2 class="text-xs font-bold uppercase tracking-wide">{{ $podatak['naziv'] }}</h2>
                        <p class="mt-3 text-3xl font-black">{{ $podatak['vrednost'] }}</p>
                    </section>
                @endforeach
            </div>

            <section class="mt-7">
                <h2 class="text-xl font-bold italic uppercase tracking-widest text-white">Brze akcije</h2>
                <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($akcije as $akcija)
                        <a href="{{ $akcija['url'] }}" class="group rounded-sm border border-borovnica-dark/20 bg-borovnica-table p-5 shadow-xl transition hover:-translate-y-0.5 hover:bg-white/40 hover:shadow-2xl focus:outline-none focus:ring-2 focus:ring-borovnica-dark">
                            <div class="flex items-center justify-between gap-4">
                                <h3 class="text-lg font-bold italic">{{ $akcija['naziv'] }}</h3>
                                <span class="text-2xl transition group-hover:translate-x-1" aria-hidden="true">→</span>
                            </div>
                            <p class="mt-2 text-sm leading-relaxed">{{ $akcija['opis'] }}</p>
                        </a>
                    @endforeach
                </div>
            </section>

            <section class="mt-7 rounded-sm border border-borovnica-dark/20 bg-borovnica-table p-5 shadow-xl sm:p-6">
                <div class="flex items-center justify-between gap-4 border-b border-borovnica-dark/15 pb-3">
                    <h2 class="font-bold italic uppercase tracking-wide">{{ auth()->user()->role === \App\Enums\UserRole::KUPAC ? 'Nedavne narudžbine' : 'Narudžbine za obradu' }}</h2>
                    <a href="{{ auth()->user()->role === \App\Enums\UserRole::KUPAC ? route('user.orders') : route('narudzbine.index') }}" class="text-xs font-bold uppercase underline decoration-borovnica-accent underline-offset-2">Prikaži sve</a>
                </div>

                @if ($nedavneNarudzbine->isEmpty())
                    <p class="py-6 text-sm italic">Trenutno nema narudžbina za prikaz.</p>
                @else
                    <div class="mt-3 space-y-3">
                        @foreach ($nedavneNarudzbine as $narudzbina)
                            <a href="{{ auth()->user()->role === \App\Enums\UserRole::KUPAC ? route('user.orders.show', $narudzbina) : route('narudzbine.show', $narudzbina) }}" class="flex flex-col gap-2 rounded-sm bg-white/30 p-4 transition hover:bg-white/50 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <span class="font-bold">Narudžbina #{{ $narudzbina->id }}</span>
                                    @if (auth()->user()->role !== \App\Enums\UserRole::KUPAC)
                                        <span class="ms-2 text-sm">{{ $narudzbina->user->name }}</span>
                                    @else
                                        <span class="ms-2 text-sm">{{ $narudzbina->stavke->count() }} stavki</span>
                                    @endif
                                </div>
                                <div class="flex items-center justify-between gap-3 sm:justify-end">
                                    <span class="text-xs">{{ $narudzbina->created_at->format('d.m.Y.') }}</span>
                                    <x-narudzbina-status-badge :status="$narudzbina->status" />
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </section>
        </div>
    </div>
</x-app-layout>
