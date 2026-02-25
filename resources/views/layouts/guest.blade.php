<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'SHOKAIDO OS') }}</title>
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            background-color: #050505;
            background-image:
                radial-gradient(at 0% 0%, rgba(139, 0, 0, 0.18) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(191, 149, 63, 0.12) 0px, transparent 50%);
            background-attachment: fixed;
            /* height: 100vh; dihapus agar mobile bisa scroll */
            min-height: 100vh;
        }

        /* Desktop: Lock scroll body agar hanya card yang scroll */
        @media (min-width: 1024px) {
            body {
                height: 100vh;
                overflow: hidden;
            }
        }

        .gold-gradient-text {
            background: linear-gradient(to right, #bf953f, #fcf6ba, #b38728, #fbf5b7, #aa771c);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.02);
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(25px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .shokaido-gradient {
            background: linear-gradient(135deg, #4a0000 0%, #110000 100%);
        }

        .form-card-shadow {
            box-shadow: 0 50px 100px -20px rgba(0, 0, 0, 0.6);
        }

        .custom-scroll::-webkit-scrollbar {
            width: 4px;
        }

        .custom-scroll::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scroll::-webkit-scrollbar-thumb {
            background: rgba(191, 149, 63, 0.3);
            border-radius: 10px;
        }
    </style>
</head>

<body class="font-sans text-white antialiased flex flex-col items-center lg:justify-center p-4 lg:p-6">

    {{-- Floating Badge --}}
    <a href="{{ url('/') }}"
        class="mt-4 lg:mt-0 mb-6 px-5 py-2 bg-white/5 border border-white/10 rounded-full flex items-center gap-4 shadow-2xl hover:bg-white/10 transition-all active:scale-95 group backdrop-blur-md shrink-0">
        <img src="{{ asset('images/logo.png') }}" alt="Logo"
            class="h-6 w-auto transition-transform group-hover:rotate-12 drop-shadow-[0_0_8px_rgba(139,0,0,0.8)]">
        <div class="h-4 w-px bg-white/20"></div>
        <span class="text-[10px] font-black text-white uppercase tracking-[0.4em]">
            System <span class="gold-gradient-text italic">v2.0</span>
        </span>
    </a>

    {{-- Container Card --}}
    <div class="w-full max-w-7xl shrink-0">
        <div
            class="glass-card rounded-[2rem] lg:rounded-[3.5rem] overflow-hidden form-card-shadow transition-all duration-700 
                    h-auto lg:h-[75vh] min-h-0 lg:min-h-[600px] flex flex-col lg:flex-row">

            {{-- Sisi Kiri: Branding (Hidden on Mobile) --}}
            <div
                class="hidden lg:flex lg:w-[35%] shokaido-gradient p-12 flex-col justify-between relative overflow-hidden border-r border-white/5 shrink-0">
                <div
                    class="absolute top-0 right-0 w-[400px] h-[400px] bg-red-600/10 rounded-full -mr-52 -mt-52 blur-[100px]">
                </div>

                <div class="relative z-10">
                    <a href="{{ url('/') }}" class="block group">
                        <h1 class="text-5xl font-black text-white tracking-tighter uppercase leading-[0.9]">
                            SHOKAIDO<br><span
                                class="gold-gradient-text group-hover:brightness-125 transition-all">OS.</span>
                        </h1>
                    </a>
                    <div class="h-1 w-16 bg-gradient-to-r from-red-700 to-transparent mt-6 rounded-full"></div>
                </div>

                <div class="relative z-10 space-y-6">
                    <div class="flex items-center gap-4">
                        <div
                            class="w-10 h-10 rounded-xl bg-white/5 flex items-center justify-center border border-white/10 shadow-inner">
                            <div class="w-2 h-2 bg-red-600 rounded-full animate-pulse shadow-[0_0_8px_#dc2626]"></div>
                        </div>
                        <div class="space-y-0.5">
                            <p class="text-white text-[10px] font-black uppercase tracking-widest">Auth Active</p>
                            <p class="text-slate-500 text-[9px] font-bold uppercase tracking-tight">Secure Connection
                            </p>
                        </div>
                    </div>
                    <p
                        class="text-slate-400 text-xs font-medium leading-relaxed italic opacity-70 border-l border-red-900/50 pl-4">
                        "Perfection of character."
                    </p>
                </div>
            </div>

            {{-- Sisi Kanan: Area Form --}}
            <div class="flex-1 flex flex-col min-h-0 bg-transparent">
                {{-- Di mobile, overflow-y-auto dinonaktifkan agar scroll mengikuti body --}}
                <div class="flex-grow lg:overflow-y-auto custom-scroll p-4 md:p-8 lg:p-12 flex flex-col justify-center">
                    <div class="mx-auto w-full">
                        <div class="dark-form-context">
                            {{ $slot }}
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Footer --}}
    <div class="mt-8 mb-6 flex flex-col items-center gap-3 shrink-0">
        <p class="text-center text-[9px] font-black text-slate-500 uppercase tracking-[0.5em]">
            &copy; {{ date('Y') }} <span class="gold-gradient-text">Shotokan Kandaga</span> Indonesia
        </p>
        <div class="flex gap-2">
            <div class="w-1 h-1 bg-red-900 rounded-full"></div>
            <div class="w-1 h-1 bg-red-600 rounded-full"></div>
            <div class="w-1 h-1 bg-red-900 rounded-full"></div>
        </div>
    </div>

</body>

</html>
