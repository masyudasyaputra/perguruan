<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SHOKAIDO OS - Digital Organization System</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,800" rel="stylesheet" />

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif

    <style>
        body {
            font-family: 'Instrument Sans', sans-serif;
        }

        .shokaido-gradient {
            background: linear-gradient(135deg, #8b0000 0%, #3a0000 100%);
        }

        .gold-text {
            background: linear-gradient(to right, #bf953f, #fcf6ba, #b38728, #fbf5b7, #aa771c);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .glass-gold {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(191, 149, 63, 0.2);
        }

        .shokaido-shadow {
            box-shadow: 0 20px 50px -12px rgba(139, 0, 0, 0.3);
        }
    </style>
</head>

<body
    class="bg-[#FDFDFC] dark:bg-[#050505] text-[#1b1b18] dark:text-[#EDEDEC] min-h-screen flex flex-col selection:bg-red-600/30">

    {{-- HEADER --}}
    <header
        class="fixed top-0 w-full z-50 bg-white/80 dark:bg-[#050505]/90 backdrop-blur-md border-b border-slate-100 dark:border-white/5">
        <div class="max-w-7xl mx-auto px-4 md:px-12 py-3 flex justify-between items-center">
            <div class="flex items-center gap-2 md:gap-3">
                <img src="{{ asset('images/logo.png') }}" alt="Logo Shokaido" class="h-8 md:h-12 w-auto object-contain">
                <span class="font-black tracking-tighter text-base md:text-xl uppercase">
                    SHOKAIDO<span class="gold-text">.OS</span>
                </span>
            </div>

            <nav class="flex items-center gap-1 md:gap-4">
                @auth
                    <a href="{{ url('/dashboard') }}"
                        class="px-4 py-2 text-[10px] md:text-xs font-black uppercase tracking-widest bg-red-700 text-white rounded-full">Dashboard</a>
                @else
                    <a href="{{ route('login') }}"
                        class="px-2 md:px-3 py-2 text-[10px] md:text-xs font-black uppercase tracking-widest hover:text-red-600 transition-colors">Login</a>
                    @if (Route::has('register'))
                        {{-- PERBAIKAN: Menghapus class 'hidden' agar muncul di mobile --}}
                        <a href="{{ route('register') }}"
                            class="px-3 md:px-5 py-2 text-[9px] md:text-xs font-black uppercase tracking-widest border-2 border-red-700 text-red-700 dark:text-white dark:border-[#bf953f] rounded-full hover:bg-red-700 hover:text-white transition-all">
                            Daftar
                        </a>
                    @endif
                @endauth
            </nav>
        </div>
    </header>

    {{-- MAIN --}}
    <main class="flex-grow flex items-center justify-center pt-32 pb-16 px-6">
        <div class="max-w-7xl mx-auto w-full grid lg:grid-cols-12 gap-12 items-center">
            <div class="lg:col-span-7 space-y-8 text-center lg:text-left order-2 lg:order-1">
                <div>
                    <span
                        class="inline-block px-4 py-1.5 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 text-[10px] font-black uppercase tracking-[0.4em] rounded-full mb-6 border border-red-100 dark:border-red-900/30">
                        Perguruan Karate-Do Shokaido
                    </span>
                    <h1 class="text-4xl md:text-6xl lg:text-7xl font-black leading-[1] tracking-tighter uppercase mb-6">
                        Integritas & <br /> <span class="gold-text italic">Kehormatan Digital.</span>
                    </h1>
                    <p
                        class="max-w-xl mx-auto lg:mx-0 text-slate-500 dark:text-slate-400 leading-relaxed text-sm md:text-base">
                        Platform manajemen terpadu Shokaido. Mengelola data anggota, standarisasi kurikulum ujian, dan
                        administrasi organisasi secara transparan.
                    </p>
                </div>
                <div class="flex flex-col sm:flex-row gap-4 pt-4 justify-center lg:justify-start">
                    <a href="{{ route('login') }}"
                        class="px-8 py-4 bg-red-700 text-white rounded-2xl font-black uppercase tracking-[0.2em] text-xs shadow-xl shadow-red-900/20 hover:bg-red-800 transition-all flex items-center justify-center gap-2">
                        Masuk Portal
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                </div>
            </div>

            <div class="lg:col-span-5 order-1 lg:order-2">
                <div
                    class="relative shokaido-gradient rounded-[3rem] p-8 md:p-12 aspect-square flex flex-col justify-between overflow-hidden shokaido-shadow border border-white/10">
                    <div class="absolute -top-10 -left-10 w-40 h-40 bg-[#bf953f] rounded-full blur-[80px] opacity-20">
                    </div>
                    <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-red-600 rounded-full blur-[80px] opacity-30">
                    </div>

                    <div class="relative z-10 flex justify-between items-start">
                        <div class="space-y-1">
                            <span class="text-[10px] font-black text-[#bf953f] uppercase tracking-[0.3em]">Official
                                Platform</span>
                            <div class="flex items-center gap-2">
                                <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                                <span class="text-xs font-bold text-white uppercase tracking-widest">Server
                                    Secure</span>
                            </div>
                        </div>
                        <img src="{{ asset('images/shotokan.png') }}" alt="Logo"
                            class="h-12 md:h-16 w-auto brightness-0 invert opacity-90">
                    </div>

                    <div class="relative z-10 text-center">
                        <h2 class="text-4xl md:text-5xl font-black text-white uppercase tracking-tighter mb-2">
                            SHO<span class="gold-text text-5xl md:text-6xl italic">KAIDO</span>
                        </h2>
                        <p class="text-[10px] font-bold text-white/60 uppercase tracking-[0.5em]">Shotokan Karate-Do
                            Indonesia</p>
                    </div>

                    <div class="relative z-10">
                        <div class="glass-gold rounded-[2rem] p-6 space-y-4">
                            <div class="flex justify-between items-center border-b border-white/5 pb-3">
                                <span class="text-[9px] font-bold uppercase text-white/50 tracking-widest">Region
                                    Integrated</span>
                                <span class="text-[10px] font-black text-white uppercase">38 Provinsi</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span
                                    class="text-[9px] font-bold uppercase text-white/50 tracking-widest">Certification</span>
                                <span class="gold-text text-[10px] font-black uppercase italic">ISO Standard</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    {{-- FOOTER --}}
    <footer class="w-full border-t border-slate-100 dark:border-white/5 bg-white dark:bg-[#050505]">
        <div class="max-w-7xl mx-auto px-6 py-10 flex flex-col md:flex-row justify-between items-center gap-8">
            <div class="text-center md:text-left">
                <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest italic">&copy; 2026
                    PERGURUAN SHOKAIDO</span>
                <p class="text-[9px] text-slate-400/60 uppercase font-bold mt-1">Sistem Informasi Pengelolaan Database
                    Terpusat</p>
            </div>
            <div class="flex gap-6">
                <a href="#"
                    class="text-[10px] font-black text-slate-400 uppercase hover:text-red-700 transition-colors">Privacy</a>
                <a href="#"
                    class="text-[10px] font-black text-slate-400 uppercase hover:text-red-700 transition-colors">Terms</a>
                <a href="#"
                    class="text-[10px] font-black text-slate-400 uppercase hover:text-red-700 transition-colors">Developer</a>
            </div>
        </div>
    </footer>
</body>

</html>
