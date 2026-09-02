<x-app-layout>
    <div class="min-h-screen bg-borovnica-light py-8 sm:py-10">
        <div class="mx-auto max-w-3xl px-4 sm:px-6">
            <div class="mb-8 text-center sm:mb-10">
                <h1 class="text-3xl font-bold italic uppercase tracking-widest text-white sm:text-4xl">Novi resurs</h1>
                <div class="mx-auto mt-2 h-1 w-20 bg-borovnica-dark"></div>
            </div>

            <x-form-errors />

            <div class="rounded-sm border border-borovnica-dark/20 bg-borovnica-table p-5 shadow-2xl sm:p-8">
                <form action="{{ route('resurs.store') }}" method="POST">
                    @csrf
                    @include('resurs.partials.form', ['submitLabel' => 'Evidentiraj resurs'])
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
