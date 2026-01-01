<x-app-layout>
    <div class="bg-[#A779A7] min-h-screen flex items-center justify-center p-4">
        <div class="bg-[#E1C6C6] p-12 rounded-sm shadow-2xl w-full max-w-lg border-2 border-[#6B446B]/20">
            <div class="flex flex-col items-center mb-10">
                <h1 class="text-[#6B446B] text-4xl font-bold italic uppercase tracking-tighter">Generisanje Izveštaja</h1>
                <div class="h-1 w-24 bg-[#6B446B] mt-2"></div>
            </div>
            
            <form action="{{ route('admin.finansije.generate') }}" method="POST" class="space-y-8">
                @csrf
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <label class="text-[#6B446B] font-bold uppercase text-sm">Datum od:</label>
                        <input type="date" name="datum_od" 
                               value="{{ $prva ? $prva->datum_narudzbine->format('Y-m-d') : date('Y-m-01') }}" 
                               class="rounded-md border-none shadow-inner h-10 w-64 px-4 text-[#6B446B]">
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="text-[#6B446B] font-bold uppercase text-sm">Datum do:</label>
                        <input type="date" name="datum_do" 
                               value="{{ $poslednja ? $poslednja->datum_narudzbine->format('Y-m-d') : date('Y-m-d') }}" 
                               class="rounded-md border-none shadow-inner h-10 w-64 px-4 text-[#6B446B]">
                    </div>
                </div>

                <div class="pt-6">
                    <button type="submit" class="w-full bg-[#6B446B] text-white py-4 rounded-md font-bold uppercase tracking-widest hover:bg-[#5A395A] transition shadow-xl italic">
                        Prikaži Finansijski Pregled
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>