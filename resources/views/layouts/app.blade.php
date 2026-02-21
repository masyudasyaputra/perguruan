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
        <footer class="bg-white border-t border-slate-100 pt-12 pb-8">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                {{-- Grid Utama --}}
                <div class="grid grid-cols-1 md:grid-cols-4 gap-10 mb-12">

                    {{-- Kolom 1: Branding (Tengah di Mobile, Kiri di Desktop) --}}
                    <div
                        class="col-span-1 md:col-span-1 flex flex-col items-center md:items-start text-center md:text-left">
                        <x-application-logo class="h-10 w-auto fill-current text-indigo-600 mb-6" />
                        <p class="text-slate-500 text-sm leading-relaxed mb-6 max-w-xs">
                            Sistem manajemen terpadu untuk pengelolaan Dojo secara profesional dan transparan.
                        </p>
                        <div class="flex gap-4">
                            <a href="#"
                                class="p-2.5 bg-slate-50 rounded-xl text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 transition-all">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z" />
                                </svg>
                            </a>
                            <a href="#"
                                class="p-2.5 bg-slate-50 rounded-xl text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 transition-all">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" />
                                </svg>
                            </a>
                        </div>
                    </div>

                    {{-- Kolom 2 & 3: Sejajar 2 kolom di mobile --}}
                    <div class="grid grid-cols-2 gap-4 md:col-span-2">
                        <div>
                            <h4 class="text-xs font-black uppercase tracking-widest text-slate-900 mb-6">Navigasi</h4>
                            <ul class="space-y-4">
                                <li><a href="{{ route('admin.dashboard') }}"
                                        class="text-slate-500 hover:text-indigo-600 text-sm font-semibold transition-colors">Dashboard</a>
                                </li>
                                <li><a href="{{ route('admin.dojos.index') }}"
                                        class="text-slate-500 hover:text-indigo-600 text-sm font-semibold transition-colors">Dojo</a>
                                </li>
                                <li><a href="{{ route('admin.officials.index') }}"
                                        class="text-slate-500 hover:text-indigo-600 text-sm font-semibold transition-colors">Pengurus</a>
                                </li>
                            </ul>
                        </div>
                        <div>
                            <h4 class="text-xs font-black uppercase tracking-widest text-slate-900 mb-6">Bantuan</h4>
                            <ul class="space-y-4">
                                <li><a href="#"
                                        class="text-slate-500 hover:text-indigo-600 text-sm font-semibold transition-colors">Pusat
                                        Bantuan</a></li>
                                <li><a href="#"
                                        class="text-slate-500 hover:text-indigo-600 text-sm font-semibold transition-colors">Panduan</a>
                                </li>
                                <li><a href="#"
                                        class="text-slate-500 hover:text-indigo-600 text-sm font-semibold transition-colors">Kontak</a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    {{-- Kolom 4: Info Status (Lebar penuh di mobile) --}}
                    <div class="col-span-1">
                        <h4 class="text-xs font-black uppercase tracking-widest text-slate-900 mb-6 md:block hidden">
                            Status Sistem</h4>
                        <div class="bg-indigo-50/50 rounded-2xl p-5 border border-indigo-100/50">
                            <div class="flex items-center gap-3 mb-2">
                                <span class="relative flex h-3 w-3">
                                    <span
                                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                                </span>
                                <span class="text-xs font-black text-indigo-900 uppercase tracking-tight">System
                                    Online</span>
                            </div>
                            <p class="text-[10px] text-indigo-600/70 font-bold uppercase tracking-widest">Version v2.4.0
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Bottom Footer --}}
                <div
                    class="pt-8 border-t border-slate-100 flex flex-col md:flex-row justify-between items-center gap-6 text-center md:text-left">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.1em]">
                        &copy; {{ date('Y') }} Dojo <span class="text-indigo-600 font-black">Mastery</span>. All
                        Rights Reserved.
                    </p>
                    <div class="flex gap-8">
                        <a href="#"
                            class="text-[10px] font-black uppercase text-slate-400 hover:text-indigo-600 transition-colors">Privacy</a>
                        <a href="#"
                            class="text-[10px] font-black uppercase text-slate-400 hover:text-indigo-600 transition-colors">Terms</a>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</body>

</html>
