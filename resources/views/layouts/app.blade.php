<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SHOKAIDO OS') }}</title>

    {{-- Font: Inter (standar modern untuk UI sistem informasi) --}}
    <link rel="preconnect" href="https://rsms.me/">
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            /* Pastikan Inter dipakai walau Tailwind belum extend */
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, "Apple Color Emoji", "Segoe UI Emoji";
            font-feature-settings: "ss01" 1, "ss02" 1, "cv01" 1, "cv02" 1, "cv03" 1;
        }

        .gold-gradient-text {
            background: linear-gradient(to right, #bf953f, #fcf6ba, #b38728, #fbf5b7, #aa771c);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .shokaido-light-bg {
            background-color: #ffffff;
            background-image: radial-gradient(#e5e7eb 0.5px, transparent 0.5px);
            background-size: 30px 30px;
        }

        /* Smooth Scrolling */
        html {
            scroll-behavior: smooth;
        }

        ::-webkit-scrollbar {
            width: 5px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #bf953f;
            border-radius: 10px;
        }
    </style>
</head>

<body class="font-sans antialiased text-slate-900 shokaido-light-bg">
    <div class="min-h-screen flex flex-col">

        @include('layouts.navigation')

        {{-- Header Section --}}
        @isset($header)
            <header class="bg-white/90 backdrop-blur-md border-b border-[#bf953f]/20 shadow-sm transition-all duration-300">
                <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">

                        {{-- Sisi Kiri: Judul Halaman --}}
                        <div class="flex items-center gap-3 min-w-0">
                            <div
                                class="h-8 sm:h-10 w-1.5 bg-gradient-to-b from-red-600 to-black rounded-full shadow-sm flex-shrink-0">
                            </div>

                            <div class="flex flex-col min-w-0">
                                {{-- Inter bagus tanpa tracking terlalu lebar; turunkan sedikit biar modern --}}
                                <h1
                                    class="text-lg sm:text-xl font-bold uppercase tracking-wide text-slate-900 leading-tight whitespace-normal break-words">
                                    {{ $header }}
                                </h1>
                                <div
                                    class="h-0.5 w-8 sm:w-12 bg-[#bf953f] mt-1 rounded-full shadow-sm shadow-yellow-500/20">
                                </div>
                            </div>
                        </div>

                        {{-- Sisi Kanan: Mode Akses --}}
                        <div
                            class="flex items-center justify-between sm:justify-end gap-3 bg-slate-50 border border-slate-200 py-2 sm:py-3 px-4 rounded-xl sm:rounded-2xl shadow-inner group hover:border-[#bf953f]/40 transition-all duration-300 shrink-0">
                            <div class="flex items-center gap-3">
                                <div class="relative flex h-2 w-2">
                                    <span
                                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-600 opacity-20"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-green-600"></span>
                                </div>
                                <div class="flex flex-col">
                                    <span
                                        class="text-[7px] sm:text-[8px] font-extrabold uppercase tracking-[0.2em] text-slate-400 leading-none mb-0.5">
                                        Sistem Otoritas
                                    </span>
                                    <span
                                        class="text-[9px] sm:text-[10px] font-semibold text-slate-800 uppercase tracking-tight group-hover:text-red-700 transition-colors">
                                        {{ str_replace('_', ' ', $role ?? Auth::user()->role) }} Mode
                                    </span>
                                </div>
                            </div>

                            <div class="sm:hidden">
                                <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                            </div>
                        </div>

                    </div>
                </div>
            </header>
        @endisset

        {{-- Main Content --}}
        <main class="py-4 sm:py-4 flex-grow bg-slate-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                {{ $slot }}
            </div>
        </main>

        {{-- Footer --}}
        <footer class="bg-slate-900 border-t-4 border-[#bf953f] pt-12 pb-8">
            <div class="max-w-7xl mx-auto px-6 lg:px-8 text-center sm:text-left">
                <div class="flex flex-col md:flex-row justify-between items-center gap-8">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('images/logo.png') }}" class="h-8 sm:h-10 w-auto">
                        <span class="font-extrabold text-white tracking-tight text-lg sm:text-xl">
                            SHOKAIDO<span class="gold-gradient-text">.OS</span>
                        </span>
                    </div>
                    <div class="flex flex-col items-center md:items-end gap-2">
                        <p class="text-[8px] sm:text-[10px] font-semibold text-slate-200 uppercase tracking-[0.3em]">
                            &copy; {{ date('Y') }} Shotokan Kandaga Indonesia
                        </p>
                        <p class="text-[7px] font-semibold text-slate-500 uppercase tracking-widest">
                            Built with Honor & Tradition
                        </p>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</body>

</html>
