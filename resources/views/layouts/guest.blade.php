<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased bg-slate-50">
        {{-- Container Utama --}}
        <div class="min-h-screen flex flex-col items-center pt-4 sm:pt-4 pb-12 px-2 sm:px-6">
            
            {{-- Wrapper Form --}}
            {{-- Mobile: w-full (lebar penuh) | Desktop: lg:max-w-5xl atau 6xl --}}
            <div class="w-full lg:max-w-5xl xl:max-w-6xl transition-all duration-500">
                <div class="w-full">
                    {{ $slot }}
                </div>
            </div>

        </div>

        <style>
            /* Efek Dot Background yang lebih halus */
            body {
                background-image: radial-gradient(#cbd5e1 0.7px, transparent 0.7px);
                background-size: 24px 24px;
            }

            /* Memastikan input tidak zoom otomatis di iPhone saat fokus */
            @media screen and (max-width: 768px) {
                input, select, textarea {
                    font-size: 16px !important;
                }
            }
        </style>
    </body>
</html>