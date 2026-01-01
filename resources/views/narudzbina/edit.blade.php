{{--
    @extends('layouts.app')

    @section('content')
        narudzbina.edit template
    @endsection
--}}

<x-app-layout>
    <div class="bg-borovnica-light min-h-screen py-10">
        <div class="max-w-2xl mx-auto px-4">
            
            <div class="flex flex-col items-center mb-10">
                <div class="text-white font-bold italic text-3xl uppercase tracking-widest opacity-90">
                    Ažuriraj Status
                </div>
                <div class="text-borovnica-accent font-semibold italic text-sm">Narudžbina #{{ $narudzbina->id }}</div>
                <div class="h-1 w-20 bg-borovnica-dark mt-2"></div>
            </div>

            <div class="bg-borovnica-table rounded-sm shadow-2xl p-8 border border-borovnica-dark/20">
                <form action="{{ route('narudzbine.update', $narudzbina->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-8">
                        <label for="status" class="block text-borovnica-dark font-bold italic text-xl mb-4 uppercase tracking-wider">
                            Izaberi status:
                        </label>
                        <select name="status" id="status" 
                                class="w-full bg-white/50 border-2 border-borovnica-dark/30 rounded-md py-3 px-4 text-borovnica-dark font-bold focus:ring-borovnica-accent focus:border-borovnica-accent transition">
                            @foreach($statusi as $status)
                                <option value="{{ $status }}" {{ $narudzbina->status == $status ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('_', ' ', $status)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex flex-col space-y-4">
                        <button type="submit" 
                                class="w-full bg-borovnica-dark text-white py-4 rounded-full font-bold uppercase tracking-widest hover:bg-borovnica-accent transition shadow-lg">
                            Potvrdi izmenu
                        </button>
                        
                        <a href="{{ route('narudzbine.index') }}" 
                           class="text-center text-borovnica-dark/60 hover:text-borovnica-dark italic font-semibold transition">
                            Odustani i vrati se nazad
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>