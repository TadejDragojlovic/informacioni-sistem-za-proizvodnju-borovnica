<x-app-layout>
    <div class="min-h-screen bg-borovnica-light py-8 sm:py-10">
        <div class="mx-auto max-w-3xl px-4 sm:px-6">
            <div class="mb-8 text-center"><h1 class="text-3xl font-bold italic uppercase tracking-widest text-white">Izmena skladišne lokacije</h1><p class="mt-2 font-semibold text-borovnica-dark">{{ $skladiste->naziv }}</p></div>
            <div class="rounded-sm border border-borovnica-dark/20 bg-borovnica-table p-5 font-semibold shadow-2xl sm:p-8">
                <x-form-errors />
                <form action="{{ route('skladisne-lokacije.update', [$skladiste, $skladisnaLokacija]) }}" method="POST">
                    @csrf
                    @method('PUT')
                    @include('skladisna-lokacija.partials.form', ['submitLabel' => 'Sačuvaj izmene'])
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
