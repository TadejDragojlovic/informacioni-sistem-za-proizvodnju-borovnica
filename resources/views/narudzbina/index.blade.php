{{--
    @extends('layouts.app')

    @section('content')
        narudzbina.index template
    @endsection
--}}

<x-app-layout>
    <div class="bg-borovnica-light min-h-screen py-10">
        <div class="max-w-6xl mx-auto px-4">
            
            <div class="flex flex-col items-center mb-10">
                <div class="text-white font-bold italic text-4xl uppercase tracking-widest opacity-90 text-center">
                    Sve Narudžbine
                </div>
                <div class="h-1 w-20 bg-borovnica-dark mt-2"></div>
            </div>

            <div class="bg-borovnica-table rounded-sm shadow-2xl p-6 border border-borovnica-dark/20 overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="text-left text-borovnica-dark text-xl font-bold italic border-b-2 border-borovnica-dark/30">
                            <th class="py-3 px-2">ID</th>
                            <th class="py-3 px-2">Kupac</th>
                            <th class="py-3 px-2">Status</th>
                            <th class="py-3 px-2 text-center">Akcije</th>
                        </tr>
                    </thead>
                    <tbody class="text-borovnica-dark font-semibold">
                        @foreach($narudzbine as $n)
                        <tr class="border-b border-borovnica-dark/10 hover:bg-white/10 transition duration-150">
                            <td class="py-4 px-2 font-mono">#{{ $n->id }}</td>
                            <td class="py-4 px-2 font-bold">{{ $n->user->name ?? 'Gost' }}</td>
                            <td class="py-4 px-2 italic">{{ $n->status ?? 'Obrađuje se' }}</td>
                            <td class="py-4 px-2 text-center">
                                <div class="flex flex-col space-y-1 items-center">
                                    <a href="{{ route('narudzbine.show', $n->id) }}" 
                                    class="w-24 bg-borovnica-dark text-white text-[10px] py-1 px-2 rounded hover:bg-black transition uppercase tracking-tighter text-center">
                                        Prikaži
                                    </a>

                                    <a href="{{ route('narudzbine.edit', $n->id) }}" 
                                    class="w-24 bg-borovnica-accent text-white text-[10px] py-1 px-2 rounded hover:bg-borovnica-dark transition uppercase tracking-tighter text-center shadow-sm">
                                        Status
                                    </a>

                                    <form action="{{ route('narudzbine.destroy', $n) }}" method="POST" class="w-24" onsubmit="return confirm('Da li ste sigurni da želite da otkažete ovu narudžbinu?')">
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
        </div>
    </div>
</x-app-layout>