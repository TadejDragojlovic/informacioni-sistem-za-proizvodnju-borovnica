{{--
    @extends('layouts.app')

    @section('content')
        resur.index template
    @endsection
--}}

<x-app-layout>
    <div class="bg-borovnica-light min-h-screen py-10">
        <div class="max-w-6xl mx-auto px-4">
            
            <div class="flex flex-col items-center mb-10">
                <div class="text-white font-bold italic text-4xl uppercase tracking-widest opacity-90">
                    Resursi
                </div>
                <div class="h-1 w-20 bg-borovnica-dark mt-2"></div>
            </div>

            <div class="bg-borovnica-table rounded-sm shadow-2xl p-6 border border-borovnica-dark/20">
                <table class="w-full">
                    <thead>
                        <tr class="text-left text-borovnica-dark text-xl font-bold italic border-b-2 border-borovnica-dark/30">
                            <th class="py-3 px-2">Naziv resursa</th>
                            <th class="py-3 px-2">Količina</th>
                            <th class="py-3 px-2 text-center">Trošak</th>
                            <th class="py-3 px-2 text-center">Za proizvod</th>
                            <th class="py-3 px-2 text-center">Akcije</th>
                        </tr>
                    </thead>
                    <tbody class="text-borovnica-dark font-semibold">
                        @foreach($resursi as $r)
                        <tr class="border-b border-borovnica-dark/10 hover:bg-white/10 transition duration-150">
                            <td class="py-4 px-2 font-bold uppercase tracking-tighter">{{ $r->naziv }}</td>
                            <td class="py-4 px-2 text-left">{{ $r->kolicina }}</td>
                            <td class="py-4 px-2 text-center">{{ $r->trosak }} RSD</td>
                            <td class="py-4 px-2 text-center">
                                <span class="bg-borovnica-dark/10 px-2 py-1 rounded text-sm italic">
                                    {{ $r->proizvod->naziv ?? 'Svi proizvodi' }} 
                                    <span class="text-[10px] opacity-50 ml-1">#{{ $r->proizvod_id ?? '0' }}</span>
                                </span>
                            </td>
                            <td class="py-4 px-2 text-center">
                                <div class="flex flex-col space-y-1 items-center">
                                    <a href="{{ route('resurs.edit', $r->id) }}" 
                                    class="w-24 bg-borovnica-accent text-white text-[10px] py-1 px-2 rounded hover:bg-borovnica-dark transition uppercase tracking-tighter text-center shadow-sm">
                                        Izmeni
                                    </a>

                                    <form action="{{ route('resurs.destroy', ['resur' => $r]) }}" method="POST" class="w-24">
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
                <a href="{{ route('resurs.create') }}" class="bg-borovnica-dark text-white px-10 py-3 rounded-full font-bold uppercase tracking-widest hover:bg-borovnica-accent transition shadow-lg">
                    + Dodaj novi resurs
                </a>
            </div>
        </div>
    </div>
</x-app-layout>