<x-app-layout>
    <div class="bg-borovnica-light min-h-screen py-16 flex items-center justify-center">
        <div class="max-w-5xl w-full mx-auto px-4">
            
            <div class="bg-[#D9C4C4] rounded-sm shadow-2xl p-10 min-h-[500px] flex flex-col">
                
                <h1 class="text-[#6B446B] text-center text-5xl font-bold mb-12">Korpa</h1>

                <div class="flex-grow space-y-6">
                    @foreach($korpa as $id => $detalji)
                        <div class="flex justify-between items-center text-[#6B446B] text-lg border-b border-[#6B446B]/10 pb-4">
                            <div class="w-1/3">
                                <span class="font-semibold">{{ Str::limit($detalji['naziv'], 30) }}</span>
                            </div>
                            
                            <div class="flex items-center space-x-2 w-1/3 justify-center">
                                <form action="{{ route('korpa.azuriraj', $id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="akcija" value="minus">
                                    <button class="bg-[#6B446B] text-white w-6 h-6 rounded-full flex items-center justify-center font-bold">-</button>
                                </form>
                                
                                <span class="font-bold text-xl">{{ $detalji['kolicina'] }}</span>
                                
                                <form action="{{ route('korpa.azuriraj', $id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="akcija" value="plus">
                                    <button class="bg-[#6B446B] text-white w-6 h-6 rounded-full flex items-center justify-center font-bold">+</button>
                                </form>
                            </div>

                            <div class="w-1/3 flex justify-end items-center space-x-6">
                                <span class="font-bold">{{ number_format($detalji['cena'] * $detalji['kolicina'], 2) }} rsd</span>
                                
                                <form action="{{ route('korpa.obrisi', $id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-red-600 hover:text-red-800 transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-10 border-t-2 border-[#6B446B] pt-6">
                    <div class="flex items-center justify-between mb-8">
                        <span class="text-[#6B446B] text-2xl font-bold">Ukupan iznos:</span>
                        <div class="flex items-center space-x-2">
                            <input type="text" readonly value="{{ $ukupno }}" 
                                   class="bg-white border-none rounded-md text-center font-bold text-[#6B446B] w-32 py-2">
                            <span class="text-[#6B446B] font-bold text-xl">rsd</span>
                        </div>
                    </div>

                    <div class="flex justify-center space-x-6">
                        <a href="/" class="bg-[#6B446B] text-white px-10 py-3 rounded-md font-bold hover:bg-[#5A395A] transition shadow-md">
                            Odustani
                        </a>
                        <form action="{{ route('narudzbine.potvrdi') }}" method="POST">
                            @csrf
                            <button type="submit" class="bg-[#6B446B] text-white px-10 py-3 rounded-md font-bold hover:bg-[#5A395A] transition shadow-md">
                                Potvrdi
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>