{{--
    @extends('layouts.app')

    @section('content')
        proizvod.create template
    @endsection
--}}

<x-app-layout>
    <div class="bg-borovnica-light min-h-screen py-10">
        <div class="max-w-3xl mx-auto px-4">
            <div class="flex flex-col items-center mb-10">
                <div class="text-white font-bold italic text-3xl uppercase tracking-widest opacity-90">Novi Proizvod</div>
                <div class="h-1 w-20 bg-borovnica-dark mt-2"></div>
            </div>

            <div class="bg-borovnica-table rounded-sm shadow-2xl p-8 border border-borovnica-dark/20 text-borovnica-dark font-semibold">
                <form action="{{ route('proizvod.store') }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block font-bold italic uppercase text-sm mb-2 text-borovnica-dark">Naziv</label>
                            <input type="text" name="naziv" value="{{ old('naziv') }}" class="w-full bg-white/50 border-borovnica-dark/20 rounded py-3 focus:ring-borovnica-accent">
                        </div>

                        <div>
                            <label class="block font-bold italic uppercase text-sm mb-2 text-borovnica-dark">Količina</label>
                            <input type="number" name="kolicina" value="{{ old('kolicina') }}" class="w-full bg-white/50 border-borovnica-dark/20 rounded py-3">
                        </div>

                        <div>
                            <label class="block font-bold italic uppercase text-sm mb-2 text-borovnica-dark">Cena (RSD)</label>
                            <input type="number" step="0.01" name="cena" value="{{ old('cena') }}" class="w-full bg-white/50 border-borovnica-dark/20 rounded py-3">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block font-bold italic uppercase text-sm mb-2 text-borovnica-dark">Skladište</label>
                            <select name="skladiste_id" class="w-full bg-white/50 border-borovnica-dark/20 rounded py-3">
                                @foreach($skladista as $s)
                                    <option value="{{ $s->id }}">{{ $s->lokacija }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block font-bold italic uppercase text-sm mb-2 text-borovnica-dark">Opis</label>
                            <textarea name="opis" rows="4" class="w-full bg-white/50 border-borovnica-dark/20 rounded py-3 focus:ring-borovnica-accent">{{ old('opis') }}</textarea>
                        </div>
                    </div>

                    <div class="mt-10 flex flex-col space-y-4">
                        <button type="submit" class="w-full bg-borovnica-dark text-white py-4 rounded-full font-bold uppercase tracking-widest hover:bg-borovnica-accent transition shadow-lg">Sačuvaj Proizvod</button>
                        <a href="{{ route('proizvod.index') }}" class="text-center text-borovnica-dark/60 hover:text-borovnica-dark italic transition font-semibold">Otkaži</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>