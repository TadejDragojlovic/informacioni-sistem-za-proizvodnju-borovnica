{{--
    @extends('layouts.app')

    @section('content')
        proizvod.index template
    @endsection
--}}

<x-app-layout>
    <div class="bg-borovnica-light min-h-screen py-10">
        <div class="max-w-6xl mx-auto px-4">
            
            <div class="flex flex-col items-center mb-10">
                <div class="text-white font-bold italic text-4xl uppercase tracking-widest opacity-90">
                    Proizvodi
                </div>
                <div class="h-1 w-20 bg-borovnica-dark mt-2"></div>
            </div>

            <div class="bg-borovnica-table rounded-sm shadow-2xl p-6 border border-borovnica-dark/20">
                <table class="w-full">
                    <thead>
                        <tr class="text-left text-borovnica-dark text-xl font-bold italic border-b-2 border-borovnica-dark/30">
                            <th class="py-3 px-2">Naziv</th>
                            <th class="py-3 px-2">Opis</th>
                            <th class="py-3 px-2">Zaliha</th>
                            <th class="py-3 px-2">Cena</th>
                            <th class="py-3 px-2 text-center">Akcije</th>
                        </tr>
                    </thead>
                    <tbody class="text-borovnica-dark font-semibold">
                        @foreach($proizvodi as $proizvod)
                        <tr class="border-b border-borovnica-dark/10 hover:bg-white/10 transition duration-150">
                            <td class="py-4 px-2">{{ $proizvod->naziv }}</td>
                            <td class="py-4 px-2 text-sm opacity-80">{{ Str::limit($proizvod->opis, 50) }}</td>
                            <td class="py-4 px-2">{{ $proizvod->kolicina }}</td>
                            <td class="py-4 px-2">{{ number_format($proizvod->cena, 2) }} RSD</td>
                            <td class="py-4 px-2 text-center">
                                <div class="flex flex-col space-y-1 items-center">
                                    <button class="w-24 bg-borovnica-accent text-white text-xs py-1 px-2 rounded hover:bg-borovnica-dark transition">Izmeni</button>
                                    <button class="w-24 bg-borovnica-dark text-white text-xs py-1 px-2 rounded hover:bg-black transition">Obriši</button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-8 flex justify-center">
                <a href="#" class="bg-borovnica-dark text-white px-10 py-3 rounded-full font-bold uppercase tracking-widest hover:bg-borovnica-accent transition shadow-lg">
                    + Dodaj novi proizvod
                </a>
            </div>
        </div>
    </div>
</x-app-layout>