{{--
    @extends('layouts.app')

    @section('content')
        skladiste.create template
    @endsection
--}}

<x-app-layout>
    <div class="bg-borovnica-light min-h-screen py-10">
        <div class="max-w-3xl mx-auto px-4">
            <div class="flex flex-col items-center mb-10">
                <div class="text-white font-bold italic text-3xl uppercase tracking-widest opacity-90">Novo Skladište</div>
                <div class="h-1 w-20 bg-borovnica-dark mt-2"></div>
            </div>

            <div class="bg-borovnica-table rounded-sm shadow-2xl p-8 border border-borovnica-dark/20 text-borovnica-dark font-semibold">
                @if ($errors->any())
                    <div class="bg-red-500/80 text-white p-4 rounded-sm mb-6 font-bold italic border-l-4 border-red-800">
                        <ul class="list-disc list-inside text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form action="{{ route('skladiste.store') }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label class="block font-bold italic uppercase text-sm mb-2">Lokacija</label>
                            <input type="text" name="lokacija" value="{{ old('lokacija') }}" class="w-full bg-white/50 border-borovnica-dark/20 rounded py-3" placeholder="npr. Kragujevac, Industrijska zona">
                        </div>

                        <div>
                            <label class="block font-bold italic uppercase text-sm mb-2">Kapacitet</label>
                            <input type="number" name="kapacitet" value="{{ old('kapacitet') }}" class="w-full bg-white/50 border-borovnica-dark/20 rounded py-3">
                        </div>

                        <div>
                            <label class="block font-bold italic uppercase text-sm mb-2">Temperatura (C)</label>
                            <input type="number" name="temperatura" value="{{ old('temperatura') }}" class="w-full bg-white/50 border-borovnica-dark/20 rounded py-3">
                        </div>

                        <div>
                            <label class="block font-bold italic uppercase text-sm mb-2">Dnevni trošak</label>
                            <input type="number" name="trosak" value="{{ old('trosak') }}" class="w-full bg-white/50 border-borovnica-dark/20 rounded py-3">
                        </div>
                    </div>

                    <div class="mt-10 flex flex-col space-y-4">
                        <button type="submit" class="w-full bg-borovnica-dark text-white py-4 rounded-full font-bold uppercase tracking-widest hover:bg-borovnica-accent transition shadow-lg">Sačuvaj Skladište</button>
                        <a href="{{ route('skladiste.index') }}" class="text-center text-borovnica-dark/60 hover:text-borovnica-dark italic transition font-semibold">Nazad</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>