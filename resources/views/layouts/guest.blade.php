<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Borovnica sistem') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-borovnica-dark antialiased">
        <div class="flex min-h-screen flex-col items-center justify-center bg-borovnica-light px-4 py-8">
            <div class="text-center">
                <a href="{{ route('home') }}" class="inline-flex flex-col items-center rounded-sm focus:outline-none focus:ring-2 focus:ring-borovnica-dark">
                    <img src="{{ asset('images/logo.png') }}" class="h-24 w-24" alt="Logo sistema Borovnica">
                    <span class="mt-2 text-xl font-bold italic">Borovnica sistem</span>
                </a>
            </div>

            <div class="mt-6 w-full max-w-lg overflow-hidden rounded-sm border border-borovnica-dark/10 bg-borovnica-table px-5 py-6 shadow-2xl sm:px-8 sm:py-8">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
