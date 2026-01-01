<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - HiFi Borovnica</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#A779A7] min-h-screen flex flex-col items-center justify-center font-sans m-0">
    
    <div class="bg-[#E1C6C6] p-12 rounded-sm shadow-2xl w-full max-w-md relative">
        <div class="absolute -top-14 -left-14 p-2 rounded-full shadow-xl">
             <img src="{{ asset('images/logo.png') }}" class="w-28 h-28" alt="Logo">
        </div>
        
        <h1 class="text-[#6B446B] text-center text-5xl font-bold mb-12 tracking-tight">Login</h1>

        <form method="POST" action="{{ route('login') }}" class="space-y-8">
            @csrf
            
            <div class="flex items-center">
                <label class="text-[#6B446B] font-bold w-28 text-right pr-4 text-xl">Email:</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus 
                       class="flex-1 rounded-md border-none shadow-inner h-10 px-3 text-[#6B446B] focus:ring-2 focus:ring-[#6B446B]">
            </div>

            <div class="flex items-center">
                <label class="text-[#6B446B] font-bold w-28 text-right pr-4 text-xl">Password:</label>
                <input type="password" name="password" required 
                       class="flex-1 rounded-md border-none shadow-inner h-10 px-3 focus:ring-2 focus:ring-[#6B446B]">
            </div>

            @if ($errors->any())
                <div class="text-red-600 text-sm text-center font-bold">
                    Pogrešan email ili lozinka.
                </div>
            @endif

            <div class="flex justify-center pt-4">
                <button type="submit" class="bg-[#614261] text-white px-12 py-3 rounded-md font-bold hover:bg-[#4a324a] transition shadow-lg text-lg uppercase tracking-widest">
                    Login
                </button>
            </div>
        </form>
    </div>

    <div class="mt-12 text-center">
        <p class="text-[#2D1B2D] text-2xl font-medium mb-4 italic">Ukoliko nemate nalog</p>
        <a href="{{ route('register') }}" class="bg-[#614261] text-white px-10 py-2 rounded-md font-bold hover:bg-[#4a324a] transition shadow-lg text-sm uppercase">
            Registracija
        </a>
    </div>

</body>
</html>