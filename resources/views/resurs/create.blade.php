{{--
    @extends('layouts.app')

    @section('content')
        resur.create template
    @endsection
--}}

<x-app-layout>
    <div class="bg-borovnica-light min-h-screen py-10">
        <div class="max-w-3xl mx-auto px-4">
            <div class="flex flex-col items-center mb-10">
                <div class="text-white font-bold italic text-3xl uppercase tracking-widest opacity-90">Novi Resurs</div>
                <div class="h-1 w-20 bg-borovnica-dark mt-2"></div>
            </div>

            <div class="bg-borovnica-table rounded-sm shadow-2xl p-8 border border-borovnica-dark/20 text-borovnica-dark font-semibold">
                <form action="{{ route('resurs.store') }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block font-bold italic uppercase text-sm mb-2">Naziv (npr. Tegle 500g)</label>
                            <input type="text" name="naziv" value="{{ old('naziv') }}" class="w-full bg-white/50 border-borovnica-dark/20 rounded py-3 shadow-sm">
                        </div>

                        <div>
                            <label class="block font-bold italic uppercase text-sm mb-2">Količina</label>
                            <input type="number" name="kolicina" value="{{ old('kolicina') }}" class="w-full bg-white/50 border-borovnica-dark/20 rounded py-3">
                        </div>

                        <div>
                            <label class="block font-bold italic uppercase text-sm mb-2">Trošak</label>
                            <input type="number" name="trosak" value="{{ old('trosak') }}" class="w-full bg-white/50 border-borovnica-dark/20 rounded py-3">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block font-bold italic uppercase text-sm mb-2">Povezano sa proizvodom</label>
                            <select name="proizvod_id" class="w-full bg-white/50 border-borovnica-dark/20 rounded py-3">
                                <option value="">-- Izaberi proizvod --</option>
                                @foreach($proizvodi as $p)
                                    <option value="{{ $p->id }}" {{ old('proizvod_id') == $p->id ? 'selected' : '' }}>
                                        {{ $p->naziv }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mt-10 flex flex-col space-y-4">
                        <button type="submit" class="w-full bg-borovnica-dark text-white py-4 rounded-full font-bold uppercase tracking-widest hover:bg-borovnica-accent transition shadow-lg">Sačuvaj Resurs</button>
                        <a href="{{ route('resurs.index') }}" class="text-center text-borovnica-dark/60 hover:text-borovnica-dark italic transition font-semibold">Otkaži</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>