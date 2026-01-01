<x-app-layout>
<div class="bg-[#A779A7] min-h-screen py-10 font-sans flex items-center justify-center">
    <div class="bg-[#E1C6C6] p-10 shadow-2xl w-full max-w-5xl border-t-8 border-[#6B446B]">
        
        <div class="flex justify-between items-center mb-12">
            <h1 class="text-[#6B446B] text-5xl font-black italic tracking-tighter uppercase">Izveštaj</h1>
            <div class="text-right text-[#6B446B] text-xs font-bold uppercase">
                <p>Generisano: {{ now()->format('d.m.Y H:i') }}</p>
                <p>Period: {{ $od }} - {{ $do }}</p>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-16 mb-12">
            <div class="space-y-4">
                <div class="flex justify-between items-center bg-white/40 p-3 rounded-md">
                    <span class="text-[#6B446B] font-bold uppercase text-sm tracking-widest">Ukupni Prihod:</span>
                    <span class="text-xl font-black text-[#6B446B]">{{ number_format($ukupniPrihod, 2) }} RSD</span>
                </div>
                <div class="flex justify-between items-center bg-white/40 p-3 rounded-md">
                    <span class="text-[#6B446B] font-bold uppercase text-sm tracking-widest">Ukupni Rashod:</span>
                    <span class="text-xl font-black text-red-700">{{ number_format($ukupniRashod, 2) }} RSD</span>
                </div>
                <div class="flex justify-between items-center bg-[#6B446B] p-4 rounded-md shadow-lg">
                    <span class="text-white font-bold uppercase text-sm tracking-widest">Neto Dobit:</span>
                    <span class="text-2xl font-black text-white">{{ number_format($netoDobit, 2) }} RSD</span>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4">
                <div class="border-2 border-[#6B446B] p-4 flex flex-col items-center justify-center italic text-[#6B446B]">
                    <span class="text-4xl font-black">{{ $brojNarudzbina }}</span>
                    <span class="uppercase font-bold text-[10px] tracking-widest mt-1">Realizovanih Narudžbina</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-10">
            <div>
                <h3 class="font-bold text-[#6B446B] mb-2 uppercase italic text-sm">Troškovi Skladišta</h3>
                <table class="w-full text-left border-collapse border-2 border-[#6B446B]">
                    <thead class="bg-[#6B446B] text-white text-[10px] uppercase">
                        <tr>
                            <th class="p-2">Naziv / ID</th>
                            <th class="p-2 text-right">Iznos</th>
                        </tr>
                    </thead>
                    <tbody class="text-[#6B446B] text-xs font-bold">
                        @foreach($listaSkladista as $s)
                        <tr class="border-b border-[#6B446B]/20">
                            <td class="p-2">{{ $s->naziv ?? 'Skladiste #'.$s->id }}</td>
                            <td class="p-2 text-right">{{ number_format($s->trosak, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div>
                <h3 class="font-bold text-[#6B446B] mb-2 uppercase italic text-sm">Troškovi Resursa</h3>
                <table class="w-full text-left border-collapse border-2 border-[#6B446B]">
                    <thead class="bg-[#6B446B] text-white text-[10px] uppercase">
                        <tr>
                            <th class="p-2">Naziv / ID</th>
                            <th class="p-2 text-right">Iznos</th>
                        </tr>
                    </thead>
                    <tbody class="text-[#6B446B] text-xs font-bold">
                        @foreach($listaResursa as $r)
                        <tr class="border-b border-[#6B446B]/20">
                            <td class="p-2">{{ $r->naziv ?? 'Resurs #'.$r->id }}</td>
                            <td class="p-2 text-right">{{ number_format($r->trosak, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-12 flex justify-between">
            <a href="{{ route('admin.finansije.create') }}" class="text-[#6B446B] font-bold uppercase text-xs hover:underline italic">← Nazad na filtere</a>
            <button onclick="window.print()" class="bg-[#6B446B] text-white px-12 py-3 rounded-full font-bold uppercase tracking-widest text-xs shadow-2xl hover:scale-105 transition-transform">
                Štampaj Izveštaj
            </button>
        </div>
    </div>
</div>
</x-app-layout>