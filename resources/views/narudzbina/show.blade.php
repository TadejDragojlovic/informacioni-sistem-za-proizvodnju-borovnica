{{--
    @extends('layouts.app')

    @section('content')
        narudzbina.show template
    @endsection
--}}

<x-app-layout>
    <div class="bg-borovnica-light min-h-screen py-10">
        <div class="max-w-5xl mx-auto px-4">
            
            <div class="flex justify-between items-center mb-10">
                <a href="{{ route('narudzbine.index') }}" class="text-white hover:text-borovnica-accent transition italic">
                    ← Povratak na listu
                </a>
                <div class="text-right">
                    <h2 class="text-white font-bold italic text-3xl uppercase tracking-widest opacity-90">
                        Narudžbina #{{ $narudzbina->id }}
                    </h2>
                    <p class="text-borovnica-accent font-semibold italic text-sm">{{ $narudzbina->created_at->format('d.m.Y. H:i') }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="bg-borovnica-table p-6 rounded-sm border border-borovnica-dark/20 shadow-xl">
                    <h3 class="text-borovnica-dark font-bold italic uppercase border-b border-borovnica-dark/20 mb-4 pb-2">Kupac</h3>
                    <p class="text-borovnica-dark font-semibold text-lg">{{ $narudzbina->user->name ?? 'Gost' }}</p>
                    <p class="text-borovnica-dark/70 text-sm italic">{{ $narudzbina->user->email ?? '/' }}</p>
                </div>
                <div class="bg-borovnica-table p-6 rounded-sm border border-borovnica-dark/20 shadow-xl">
                    <h3 class="text-borovnica-dark font-bold italic uppercase border-b border-borovnica-dark/20 mb-4 pb-2">Status & Isporuka</h3>
                    <span class="bg-borovnica-dark text-white px-3 py-1 rounded-full text-xs uppercase tracking-widest font-bold">
                        {{ $narudzbina->status ?? 'U obradi' }}
                    </span>
                </div>
            </div>

            <div class="bg-borovnica-table rounded-sm shadow-2xl p-8 border border-borovnica-dark/20">
                <h3 class="text-borovnica-dark font-bold italic text-xl mb-6 uppercase tracking-widest">Sadržaj korpe</h3>
                <table class="w-full">
                    <thead>
                        <tr class="text-left text-borovnica-dark text-lg font-bold italic border-b-2 border-borovnica-dark/30">
                            <th class="py-3 px-2">Proizvod</th>
                            <th class="py-3 px-2 text-center">Količina</th>
                            <th class="py-3 px-2 text-right">Cena (Jed.)</th>
                            <th class="py-3 px-2 text-right">Ukupno</th>
                        </tr>
                    </thead>
                    <tbody class="text-borovnica-dark font-semibold">
                        @foreach($narudzbina->stavke as $stavka)
                        <tr class="border-b border-borovnica-dark/10 hover:bg-white/10 transition">
                            <td class="py-5 px-2 font-bold">{{ $stavka->proizvod->naziv }}</td>
                            
                            <td class="py-5 px-2 text-center italic">{{ $stavka->kolicina }} kom</td>
                            
                            <td class="py-5 px-2 text-right">{{ number_format($stavka->proizvod->cena, 2) }} RSD</td>
                            
                            <td class="py-5 px-2 text-right text-borovnica-accent">
                                {{ number_format($stavka->kolicina * $stavka->proizvod->cena, 2) }} RSD
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="text-borovnica-dark font-bold text-2xl italic">
                            <td colspan="3" class="pt-10 text-right uppercase tracking-widest">Ukupno za uplatu:</td>
                            <td class="pt-10 text-right text-borovnica-dark border-t-2 border-borovnica-dark/50">
                                <!-- racunanje ukup racuna -->
                                {{ 
                                    number_format($narudzbina->stavke->sum(function($stavka) {
                                        return $stavka->kolicina * $stavka->proizvod->cena;
                                    }), 2) 
                                }} RSD
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

        </div>
    </div>
</x-app-layout>