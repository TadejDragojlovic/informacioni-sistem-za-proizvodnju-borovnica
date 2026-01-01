<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
        @if(session('success'))
        <div id="toast" class="fixed bottom-5 right-5 z-50 animate-bounce">
            <div class="bg-borovnica-dark text-white px-6 py-3 rounded-sm shadow-2xl border-b-4 border-borovnica-accent font-bold italic">
                🫐 {{ session('success') }}
            </div>
        </div>

        <script>
            // Automatski ukloni poruku nakon 3 sekunde
            setTimeout(() => {
                const toast = document.getElementById('toast');
                if(toast) toast.style.display = 'none';
            }, 3000);
        </script>
        @endif
    </body>
</html>
