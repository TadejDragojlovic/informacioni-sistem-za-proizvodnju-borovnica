<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registracija - HiFi Borovnica</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#A779A7] min-h-screen flex flex-col items-center justify-center font-sans m-0">

    <div class="bg-[#E1C6C6] p-6 rounded-sm shadow-2xl w-full max-w-lg relative">
        <div class="absolute -top-10 -left-10 p-2 rounded-full shadow-xl">
             <img src="{{ asset('images/logo.png') }}" class="w-28 h-28" alt="Logo">
        </div>
        
        <h1 class="text-[#6B446B] text-center text-5xl font-bold mb-10 tracking-tight">Registracija</h1>

        <form method="POST" action="{{ route('register') }}" class="space-y-6">
            @csrf

            <div class="flex items-center">
                <label class="text-[#6B446B] font-bold w-40 text-right pr-4 text-xl">Ime:</label>
                <input type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                       class="flex-1 rounded-md border-none shadow-inner h-10 px-3 focus:ring-2 focus:ring-[#6B446B]">
            </div>
            <x-input-error :messages="$errors->get('name')" class="mt-1 text-right text-xs" />

            <div class="flex items-center">
                <label class="text-[#6B446B] font-bold w-40 text-right pr-4 text-xl">Email:</label>
                <input type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                       class="flex-1 rounded-md border-none shadow-inner h-10 px-3 focus:ring-2 focus:ring-[#6B446B]">
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-1 text-right text-xs" />

            <div class="flex items-center">
                <label class="text-[#6B446B] font-bold w-40 text-right pr-4 text-xl">Password:</label>
                <input type="password" name="password" required autocomplete="new-password"
                       class="flex-1 rounded-md border-none shadow-inner h-10 px-3 focus:ring-2 focus:ring-[#6B446B]">
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-1 text-right text-xs" />

            <div class="flex items-center">
                <label class="text-[#6B446B] font-bold w-40 text-right pr-4 text-xl leading-tight">Potvrda sifre:</label>
                <input type="password" name="password_confirmation" required autocomplete="new-password"
                       class="flex-1 rounded-md border-none shadow-inner h-10 px-3 focus:ring-2 focus:ring-[#6B446B]">
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 text-right text-xs" />

            <div class="flex justify-center pt-8">
                <button type="submit" class="bg-[#614261] text-white px-12 py-3 rounded-md font-bold hover:bg-[#4a324a] transition shadow-lg text-lg uppercase tracking-widest">
                    Registruj se
                </button>
            </div>
        </form>
    </div>

    <div class="mt-8 text-center">
        <p class="text-[#2D1B2D] text-2xl font-medium mb-4 italic">Ukoliko imate nalog</p>
        <a href="{{ route('login') }}" class="bg-[#614261] text-white px-10 py-2 rounded-md font-bold hover:bg-[#4a324a] transition text-sm uppercase">
            Login
        </a>
    </div>

</body>
</html>