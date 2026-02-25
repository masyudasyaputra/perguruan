<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SHOKAIDO OS') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap"
        rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .gold-gradient-text {
            background: linear-gradient(to right, #bf953f, #fcf6ba, #b38728, #fbf5b7, #aa771c);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Menyesuaikan dengan gaya shokaido-nav-active di navigasi */
        .shokaido-nav-active {
            background: linear-gradient(135deg, #8b0000 0%, #5a0000 100%);
            color: white !important;
        }

        .dark-pattern {
            background-color: #050505;
            background-image: radial-gradient(#1a1a1a 0.5px, transparent 0.5px);
            background-size: 30px 30px;
        }

        /* Scrollbar kustom agar senada dengan tema dark & gold */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #050505;
        }

        ::-webkit-scrollbar-thumb {
            background: #1a1a1a;
            border-radius: 10px;
            border: 2px solid #050505;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #bf953f;
        }
    </style>
</head>

<body class="font-sans antialiased text-slate-300 dark-pattern">
    <div class="min-h-screen flex flex-col">

        {{-- Navigation Bar (Sesuai dengan snippet yang Anda berikan) --}}
        @include('layouts.navigation')

        {{-- Header Section --}}
        @isset($header)
            <header
                class="bg-white/5 dark:bg-[#050505]/40 backdrop-blur-md border-b border-[#bf953f]/10 sticky top-[80px] z-40">
                <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center gap-5">
                        <div
                            class="h-10 w-1.5 bg-gradient-to-b from-red-600 to-red-900 rounded-full shadow-[0_0_15px_rgba(220,38,38,0.3)]">
                        </div>
                        <div class="flex flex-col">
                            <h1 class="text-2xl font-black uppercase tracking-[0.1em] text-white">
                                {{ $header }}
                            </h1>
                            <div class="h-0.5 w-12 bg-[#bf953f]/40 mt-1 rounded-full"></div>
                        </div>
                    </div>
                </div>
            </header>
        @endisset

        {{-- Main Content --}}
        <main class="py-12 flex-grow">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="animate-in fade-in slide-in-from-bottom-3 duration-700">
                    {{ $slot }}
                </div>
            </div>
        </main>

        {{-- Footer Section --}}
        <footer class="bg-[#030303] border-t border-[#bf953f]/10 pt-20 pb-10 mt-20">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                {{-- Grid Utama --}}
                <div class="grid grid-cols-1 md:grid-cols-12 gap-16 mb-20">

                    {{-- Kolom 1: Branding --}}
                    <div class="md:col-span-5 flex flex-col items-center md:items-start text-center md:text-left">
                        <div class="flex items-center gap-4 mb-8">
                            <img src="{{ asset('images/logo.png') }}" alt="Logo"
                                class="h-14 w-auto brightness-110 drop-shadow-[0_0_8px_rgba(191,149,63,0.2)]">
                            <div class="flex flex-col">
                                <span class="font-black tracking-tighter text-2xl uppercase text-white leading-none">
                                    SHOKAIDO<span class="gold-gradient-text">.OS</span>
                                </span>
                                <span
                                    class="text-[9px] font-bold text-red-700 uppercase tracking-[0.25em] mt-1">Shotokan
                                    Kandaga Indonesia</span>
                            </div>
                        </div>
                        <p class="text-slate-400 text-sm leading-relaxed mb-8 max-w-sm">
                            Sistem manajemen terpadu untuk pengelolaan Dojo secara profesional. Mengusung nilai <span
                                class="text-slate-200 italic font-medium">Bushido</span> ke dalam ekosistem digital
                            modern.
                        </p>
                    </div>

                    {{-- Kolom 2: Navigasi --}}
                    <div class="md:col-span-4 grid grid-cols-2 gap-8">
                        <div>
                            <h4 class="text-[10px] font-black uppercase tracking-[0.3em] text-[#bf953f] mb-8">
                                Administrasi</h4>
                            <ul class="space-y-4">
                                <li><a href="{{ route('admin.dashboard') }}"
                                        class="text-slate-500 hover:text-white text-[11px] font-bold uppercase tracking-wider transition-colors">Dashboard</a>
                                </li>
                                <li><a href="{{ route('admin.dojos.index') }}"
                                        class="text-slate-500 hover:text-white text-[11px] font-bold uppercase tracking-wider transition-colors">Dojo
                                        List</a></li>
                                <li><a href="{{ route('admin.officials.index') }}"
                                        class="text-slate-500 hover:text-white text-[11px] font-bold uppercase tracking-wider transition-colors">Database</a>
                                </li>
                            </ul>
                        </div>
                        <div>
                            <h4 class="text-[10px] font-black uppercase tracking-[0.3em] text-[#bf953f] mb-8">Support
                            </h4>
                            <ul class="space-y-4">
                                <li><a href="#"
                                        class="text-slate-500 hover:text-white text-[11px] font-bold uppercase tracking-wider transition-colors">Help
                                        Desk</a></li>
                                <li><a href="#"
                                        class="text-slate-500 hover:text-white text-[11px] font-bold uppercase tracking-wider transition-colors">PB
                                        Shokaido</a></li>
                            </ul>
                        </div>
                    </div>

                    {{-- Kolom 3: Status --}}
                    <div class="md:col-span-3">
                        <h4 class="text-[10px] font-black uppercase tracking-[0.3em] text-red-700 mb-8">System Status
                        </h4>
                        <div class="bg-white/[0.02] border border-white/5 rounded-2xl p-6 backdrop-blur-sm">
                            <div class="flex items-center gap-3 mb-4">
                                <span class="relative flex h-2 w-2">
                                    <span
                                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-500 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                                </span>
                                <span class="text-[9px] font-black text-white uppercase tracking-widest">Server
                                    Operational</span>
                            </div>
                            <p class="text-[9px] text-slate-500 font-bold uppercase tracking-widest mb-1">Version
                                Control</p>
                            <p class="text-xs font-black text-[#bf953f] italic tracking-tighter uppercase">v2.4.0-Stable
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Bottom Footer --}}
                <div class="pt-8 border-t border-white/5 flex flex-col md:flex-row justify-between items-center gap-6">
                    <p class="text-[9px] font-bold text-slate-600 uppercase tracking-[0.3em]">
                        &copy; {{ date('Y') }} <span class="text-slate-400">Shokaido</span> <span
                            class="text-red-800 font-black">Operating System</span>.
                    </p>
                    <div class="flex gap-8">
                        <a href="#"
                            class="text-[9px] font-black uppercase text-slate-600 hover:text-[#bf953f] transition-colors tracking-widest">Privacy
                            Policy</a>
                        <a href="#"
                            class="text-[9px] font-black uppercase text-slate-600 hover:text-[#bf953f] transition-colors tracking-widest">Terms
                            of Service</a>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</body>

</html>
