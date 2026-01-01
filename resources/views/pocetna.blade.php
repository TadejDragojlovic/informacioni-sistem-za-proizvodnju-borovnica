<x-app-layout>
    <div class="bg-borovnica-light min-h-screen">
        <div class="max-w-6xl mx-auto py-10 px-6">
            
            <div class="flex flex-col items-center mb-10">
                <h1 class="text-4xl font-bold text-borovnica-dark">Dobrodošli!</h1>
            </div>

            <div class="mb-12">
                <h2 class="text-2xl italic text-borovnica-dark mb-4 uppercase tracking-wider">Proizvodi na akciji</h2>
                <div class="bg-borovnica-accent rounded-lg p-6 shadow-xl">
                    <table class="w-full text-white">
                        <thead>
                            <tr class="border-b border-borovnica-light text-left italic">
                                <th class="pb-2">Naziv</th>
                                <th class="pb-2">Cena</th>
                                <th class="pb-2 text-center">Količina</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-borovnica-light/30">
                            @foreach($proizvodi as $proizvod)
                            <tr>
                                <td class="py-4">{{ $proizvod->naziv }}</td>
                                <td class="py-4 font-semibold text-borovnica-dark">{{ $proizvod->cena }}rsd</td>
                                <td class="py-4 text-center">
                                    <form action="{{ route('korpa.dodaj', $proizvod->id) }}" method="POST">
                                        @csrf
                                        <input type="number" name="kolicina" value="1" min="1" class="w-16 bg-white/50 border-borovnica-dark/20 rounded py-1 text-center">
                                        <button type="submit" class="bg-borovnica-dark hover:bg-black text-white px-6 py-2 rounded-md transition text-sm">
                                            Dodaj
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div>
                <h2 class="text-2xl italic text-borovnica-dark mb-4 uppercase tracking-wider">Najprodavaniji proizvodi</h2>
                <div class="bg-borovnica-accent rounded-lg p-6 shadow-xl">
                    <table class="w-full text-white text-left">
                        <thead>
                            <tr class="border-b border-borovnica-light italic">
                                <th class="pb-2">Naziv</th>
                                <th class="pb-2">Opis</th>
                                <th class="pb-2">Cena</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-borovnica-light/30">
                            @foreach($proizvodi->shuffle()->take(2) as $proizvod)
                            <tr>
                                <td class="py-4">{{ $proizvod->naziv }}</td>
                                <td class="py-4 text-sm text-gray-200">{{ $proizvod->opis }}</td>
                                <td class="py-4 font-semibold text-borovnica-dark">{{ $proizvod->cena }}rsd</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>