<x-app-layout>
    <div class="bg-borovnica-light min-h-screen py-10">
        <div class="max-w-5xl mx-auto px-4">
            <div class="flex flex-col items-center mb-10">
                <div class="text-white font-bold italic text-3xl uppercase tracking-widest opacity-90">Moje Narudžbine</div>
                <div class="h-1 w-20 bg-borovnica-dark mt-2"></div>
            </div>

            <div class="bg-borovnica-table rounded-sm shadow-2xl overflow-hidden border border-borovnica-dark/20">
                <table class="w-full text-left border-collapse text-borovnica-dark">
                    <thead class="bg-borovnica-dark/10">
                        <tr class="uppercase text-xs italic tracking-widest">
                            <th class="px-6 py-4">ID Narudžbine</th>
                            <th class="px-6 py-4">Datum</th>
                            <th class="px-6 py-4">Ukupan Iznos</th>
                            <th class="px-6 py-4 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($narudzbine as $n)
                            <tr class="border-b border-borovnica-dark/5 hover:bg-white/20 transition">
                                <td class="px-6 py-4 font-bold">#{{ $n->id }}</td>
                                <td class="px-6 py-4">{{ $n->created_at->format('d.m.Y H:i') }}</td>
                                <td class="px-6 py-4 font-bold">{{ number_format($n->stavke->sum(fn($s) => $s->kolicina * $s->proizvod->cena), 2) }} RSD</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-4 py-1 rounded-full text-[10px] font-black uppercase tracking-tighter
                                        @if($n->status == 'isporucena') bg-green-200 text-green-800 
                                        @elseif($n->status == 'otkazana') bg-red-200 text-red-800
                                        @else bg-blue-100 text-blue-800 @endif">
                                        {{ $n->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center italic opacity-60">
                                    Trenutno nemate istoriju narudžbina.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>