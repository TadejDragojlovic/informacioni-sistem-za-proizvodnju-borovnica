{{--
    @extends('layouts.app')

    @section('content')
        skladiste.index template
    @endsection
--}}

<x-app-layout>
    <div class="bg-borovnica-light min-h-screen py-10">
        <div class="max-w-6xl mx-auto px-4">
            
            <div class="flex flex-col items-center mb-10">
                <div class="text-white font-bold italic text-4xl uppercase tracking-widest opacity-90">
                    Skladišta
                </div>
                <div class="h-1 w-20 bg-borovnica-dark mt-2"></div>
            </div>

            <div class="bg-borovnica-table rounded-sm shadow-2xl p-6 border border-borovnica-dark/20">
                <table class="w-full">
                    <thead>
                        <tr class="text-left text-borovnica-dark text-xl font-bold italic border-b-2 border-borovnica-dark/30">
                            <th class="py-3 px-2">Naziv lokacije</th>
                            <th class="py-3 px-2">Kapacitet</th>
                            <th class="py-3 px-2">Temperatura (C)</th>
                            <th class="py-3 px-2">Dnevni trošak</th>
                            <th class="py-3 px-2 text-center">Akcije</th>
                        </tr>
                    </thead>
                    <tbody class="text-borovnica-dark font-semibold">
                        @foreach($skladista as $s)
                        <tr class="border-b border-borovnica-dark/10 hover:bg-white/10 transition duration-150">
                            <td class="py-4 px-2">{{ $s->lokacija }}</td>
                            <td class="py-4 px-2 italic">{{ $s->kapacitet }} kg</td>
                            <td class="py-4 px-2">{{ $s->temperatura }}</td>
                            <td class="py-4 px-2">{{ $s->trosak }}</td>
                            <td class="py-4 px-2 text-center">
                                <div class="flex flex-col space-y-1 items-center">
                                    <a href="{{ route('skladiste.edit', $s->id) }}" 
                                    class="w-24 bg-borovnica-accent text-white text-[10px] py-1 px-2 rounded hover:bg-borovnica-dark transition uppercase tracking-tighter text-center shadow-sm">
                                        Izmeni
                                    </a>

                                    <form action="{{ route('skladiste.destroy', ['skladiste' => $s]) }}" method="POST" class="w-24">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-full bg-red-600 text-white text-[10px] py-1 px-2 rounded hover:bg-red-800 transition uppercase tracking-tighter shadow-sm">
                                            Otkaži
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-8 flex justify-center">
                <a href="{{ route('skladiste.create') }}" class="bg-borovnica-dark text-white px-10 py-3 rounded-full font-bold uppercase tracking-widest hover:bg-borovnica-accent transition shadow-lg">
                    + Dodaj novo skladište
                </a>
            </div>
        </div>
    </div>
</x-app-layout>