<x-app-layout>
    <div class="min-h-screen bg-borovnica-light py-8 sm:py-10">
        <div class="mx-auto max-w-3xl px-4 sm:px-6">
            <div class="mb-8 flex flex-col items-center">
                <h1 class="text-center text-3xl font-bold italic uppercase tracking-widest text-white">Novi proizvod</h1>
                <div class="mt-2 h-1 w-20 bg-borovnica-dark"></div>
            </div>

            <div class="rounded-sm border border-borovnica-dark/20 bg-borovnica-table p-5 font-semibold text-borovnica-dark shadow-2xl sm:p-8">
                <x-form-errors />

                <form action="{{ route('proizvod.store') }}" method="POST">
                    @csrf
                    @include('proizvod.partials.form', ['submitLabel' => 'Sačuvaj proizvod'])
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
