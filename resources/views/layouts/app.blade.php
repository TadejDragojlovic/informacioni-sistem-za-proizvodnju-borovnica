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
    <body class="bg-borovnica-light font-sans text-borovnica-dark antialiased">
        <div class="min-h-screen bg-borovnica-light">
            @include('layouts.navigation')

            @isset($header)
                <header class="border-b border-borovnica-dark/10 bg-borovnica-table shadow">
                    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <main>
                {{ $slot }}
            </main>
        </div>

        @if(session('success'))
        <div
            x-data="{ show: true }"
            x-init="setTimeout(() => show = false, 3000)"
            x-show="show"
            x-transition
            class="fixed inset-x-4 bottom-5 z-50 sm:left-auto sm:right-5"
            role="status"
            aria-live="polite"
        >
            <div class="rounded-sm border-b-4 border-borovnica-accent bg-borovnica-dark px-6 py-3 font-bold italic text-white shadow-2xl">
                🫐 {{ session('success') }}
            </div>
        </div>
        @endif
    </body>
</html>
