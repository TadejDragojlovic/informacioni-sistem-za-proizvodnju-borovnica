@if ($errors->any())
    <div class="mb-6 rounded-sm border-s-4 border-red-800 bg-red-100 px-4 py-3 text-red-800" role="alert">
        <p class="font-bold italic">Proverite unete podatke:</p>
        <ul class="mt-1 list-inside list-disc text-sm">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
