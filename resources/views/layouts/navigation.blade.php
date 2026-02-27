@php
    use Illuminate\Support\Facades\Auth;

    $u = Auth::user();

    $extra = is_array($u->roles) ? $u->roles : json_decode($u->roles, true) ?? [];
    $userRoles = collect(array_merge([$u->role], $extra))
        ->filter()
        ->map(fn($r) => strtolower($r))
        ->unique()
        ->values()
        ->all();

    $hasRole = function ($roles) use ($userRoles) {
        $roles = collect((array) $roles)->map(fn($r) => strtolower($r))->all();
        return count(array_intersect($roles, $userRoles)) > 0;
    };

    $isAdmin = $hasRole(['pb', 'pengprov', 'pengcab', 'admin_dojo']);
    $isStruktural = $hasRole(['pb', 'pengprov', 'pengcab']);
    $isSistemAdmin = $hasRole(['pb', 'pengprov']);

    $homeHref = $isAdmin ? route('admin.dashboard') : route('dashboard');

    $activeDashboard = request()->routeIs('admin.dashboard') || request()->routeIs('dashboard');
    $activeOfficials = request()->routeIs('admin.officials.*');
    $activeDojos = request()->routeIs('admin.dojos.*');
    $activeExams = request()->routeIs('admin.exams.*');

    $activeUsers = request()->routeIs('admin.users.*');
    $activeProv = request()->routeIs('admin.provinces.*');
    $activeFees = request()->routeIs('admin.fees.*');
    $activeExamFees = request()->routeIs('admin.exams.fees.*');
    $activeSystem = $activeUsers || $activeProv || $activeFees || $activeExamFees;

    $roleLabel = str_replace('_', ' ', $u->role ?? 'member');
@endphp

<style>
    .gold-gradient-text {
        background: linear-gradient(to right, #bf953f, #fcf6ba, #b38728, #fbf5b7, #aa771c);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    /* ACTIVE (lebih bagus & kontras) */
    .shokaido-nav-active {
        background: linear-gradient(135deg, #b91c1c 0%, #7f1d1d 55%, #450a0a 100%);
        color: #fff !important;
        border-color: rgba(220, 38, 38, 0.55) !important;
        box-shadow:
            0 16px 32px rgba(127, 29, 29, 0.28),
            inset 0 0 0 1px rgba(255, 255, 255, 0.06);
    }

    .shokaido-nav-active svg {
        color: #fff !important;
    }

    .shokaido-nav-active .nav-label {
        color: #fff !important;
        opacity: 1 !important;
    }

    /* Inactive */
    .nav-idle {
        background: rgba(15, 23, 42, 0.35);
        border-color: rgba(255, 255, 255, 0.10);
        color: rgba(226, 232, 240, 0.90);
    }

    .nav-idle .nav-label {
        color: rgba(226, 232, 240, 0.82);
    }

    .nav-idle svg {
        color: rgba(226, 232, 240, 0.92);
    }

    /* Indicator gold kecil */
    .nav-indicator {
        height: 3px;
        border-radius: 999px;
        background: linear-gradient(to right, #bf953f, #fcf6ba, #b38728);
        box-shadow: 0 6px 18px rgba(191, 149, 63, 0.25);
    }

    /* Floating menu button safe */
    .mobile-fab-bottom {
        bottom: calc(18px + env(safe-area-inset-bottom));
    }

    /* Tap feedback */
    .tap-press:active {
        transform: scale(0.96);
    }
</style>

<nav x-data="{ openMenu: false, openSystem: false }"
    class="bg-slate-900 text-white sticky top-0 z-50 border-b border-[#bf953f]/20 shadow-lg shadow-red-900/5">

    {{-- TOP BAR --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">

            {{-- Logo --}}
            <div class="flex items-center min-w-0">
                <a href="{{ $homeHref }}" class="flex items-center gap-3 group active:scale-95 transition-transform">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo"
                        class="h-9 sm:h-10 w-auto drop-shadow-sm group-hover:drop-shadow-md transition-all">

                    <div class="flex flex-col min-w-0">
                        <span
                            class="font-black tracking-tighter text-[13px] sm:text-base leading-none uppercase italic">
                            SHOKAIDO<span class="gold-gradient-text">.OS</span>
                        </span>
                        <span
                            class="text-[6px] sm:text-[8px] font-bold text-red-600 uppercase tracking-[0.14em] leading-tight mt-0.5">
                            Shotokan Kandaga Indonesia
                        </span>
                    </div>
                </a>
            </div>

            {{-- DESKTOP MENU --}}
            <div class="hidden sm:flex items-center gap-1.5">
                <a href="{{ $homeHref }}"
                    class="px-4 py-2 rounded-xl text-[11px] font-black uppercase tracking-widest transition-all border-2
                    {{ $activeDashboard ? 'shokaido-nav-active' : 'border-white/10 text-slate-200 hover:bg-white/5' }}">
                    Dashboard
                </a>

                @if ($isStruktural)
                    <a href="{{ route('admin.officials.index') }}"
                        class="px-4 py-2 rounded-xl text-[11px] font-black uppercase tracking-widest transition-all border-2
                        {{ $activeOfficials ? 'shokaido-nav-active' : 'border-white/10 text-slate-200 hover:bg-white/5' }}">
                        Pengurus
                    </a>

                    <a href="{{ route('admin.dojos.index') }}"
                        class="px-4 py-2 rounded-xl text-[11px] font-black uppercase tracking-widest transition-all border-2
                        {{ $activeDojos ? 'shokaido-nav-active' : 'border-white/10 text-slate-200 hover:bg-white/5' }}">
                        Dojo
                    </a>
                @endif

                @if ($isAdmin)
                    <a href="{{ route('admin.exams.index') }}"
                        class="px-4 py-2 rounded-xl text-[11px] font-black uppercase tracking-widest transition-all border-2
                        {{ $activeExams ? 'shokaido-nav-active' : 'border-white/10 text-slate-200 hover:bg-white/5' }}">
                        <span class="inline-flex items-center gap-2">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M2,11L12,16L22,11L12,6L2,11M12,18L2,13V15L12,20L22,15V13L12,18Z" />
                            </svg>
                            Ujian
                        </span>
                    </a>
                @endif

                @if ($isSistemAdmin)
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-[11px] font-black uppercase tracking-widest transition-all border-2
                            {{ $activeSystem ? 'shokaido-nav-active' : 'border-white/10 text-slate-200 hover:bg-white/5' }}">
                            Sistem
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="square" stroke-linejoin="square" stroke-width="3"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="open" x-cloak @click.outside="open = false"
                            class="absolute left-0 mt-2 w-64 bg-white text-slate-900 rounded-2xl shadow-2xl border border-slate-200 overflow-hidden">
                            <div class="p-2">
                                <div
                                    class="px-3 py-2 text-[9px] font-black uppercase tracking-[0.3em] text-red-700 opacity-80">
                                    Core Settings
                                </div>

                                <a href="{{ route('admin.users.index') }}"
                                    class="block px-3 py-2 rounded-xl text-xs font-black uppercase tracking-tight
                                    {{ $activeUsers ? 'bg-red-50 text-red-700' : 'hover:bg-slate-50' }}">
                                    User Management
                                </a>

                                <a href="{{ route('admin.exams.fees.index') }}"
                                    class="block px-3 py-2 rounded-xl text-xs font-black uppercase tracking-tight
                                    {{ $activeExamFees ? 'bg-red-50 text-red-700' : 'hover:bg-slate-50' }}">
                                    Biaya Ujian
                                </a>

                                <a href="{{ route('admin.fees.index') }}"
                                    class="block px-3 py-2 rounded-xl text-xs font-black uppercase tracking-tight
                                    {{ $activeFees ? 'bg-red-50 text-red-700' : 'hover:bg-slate-50' }}">
                                    Konfigurasi Iuran
                                </a>

                                @if ($hasRole('pb'))
                                    <div class="my-2 border-t border-slate-100"></div>
                                    <a href="{{ route('admin.provinces.index') }}"
                                        class="block px-3 py-2 rounded-xl text-xs font-black uppercase tracking-tight
                                        {{ $activeProv ? 'bg-red-50 text-red-700' : 'hover:bg-slate-50' }}">
                                        Wilayah / Pengprov
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- DESKTOP USER --}}
            <div class="hidden sm:flex items-center">
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open"
                        class="group flex items-center gap-3 px-3 py-1.5 rounded-2xl border-2 border-white/10 hover:border-red-600/30 hover:bg-white/5 transition-all">
                        <div class="flex flex-col text-right">
                            <span
                                class="text-xs font-black uppercase tracking-tight leading-none">{{ $u->name }}</span>
                            <div class="flex flex-wrap justify-end gap-1 mt-1">
                                @foreach ($userRoles as $r)
                                    <span
                                        class="text-[7px] uppercase font-black text-white bg-red-600 px-1.5 py-0.5 rounded shadow-sm">
                                        {{ str_replace('_', ' ', $r) }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                        <div
                            class="h-10 w-10 rounded-xl bg-gradient-to-br from-slate-800 to-black flex items-center justify-center text-[#bf953f] border border-[#bf953f]/30 font-black text-sm shadow-md group-hover:scale-105 transition-transform">
                            {{ strtoupper(substr($u->name, 0, 1)) }}
                        </div>
                    </button>

                    <div x-show="open" x-cloak @click.outside="open = false"
                        class="absolute right-0 mt-2 w-72 bg-white text-slate-900 rounded-2xl shadow-2xl border border-slate-200 overflow-hidden">
                        <div class="p-2">
                            <div class="px-3 py-3 bg-slate-50 rounded-xl border border-slate-100 mb-2">
                                <p class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-400">Account</p>
                                <p class="text-[11px] font-bold text-slate-700 truncate mt-1">{{ $u->email }}</p>
                            </div>

                            <a href="{{ route('profile.edit') }}"
                                class="block px-3 py-2 rounded-xl text-xs font-black uppercase tracking-tight hover:bg-slate-50">
                                Pengaturan Profil
                            </a>

                            <form method="POST" action="{{ route('logout') }}" class="mt-1">
                                @csrf
                                <button type="submit"
                                    class="w-full text-left px-3 py-2 rounded-xl text-xs font-black uppercase tracking-tight text-red-700 hover:bg-red-50">
                                    Keluar Sistem
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- MOBILE FLOATING MENU BUTTON (CENTER) --}}
    <div class="sm:hidden" x-cloak>
        <button type="button" @click="openMenu = true"
            class="fixed left-1/2 -translate-x-1/2 mobile-fab-bottom z-[70]
               inline-flex items-center justify-center gap-2
               h-14 px-6 rounded-[999px]
               bg-slate-900/95 backdrop-blur
               text-white border border-white/10
               shadow-[0_16px_40px_rgba(2,6,23,0.35)]
               tap-press transition-all"
            aria-label="Buka Menu">
            <span class="relative flex h-2.5 w-2.5">
                <span
                    class="absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-40 animate-ping"></span>
                <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-emerald-400"></span>
            </span>

            <span class="text-[10px] font-black uppercase tracking-[0.22em]">Menu</span>

            <span class="w-px h-6 bg-white/10"></span>

            <svg class="w-5 h-5 text-[#bf953f]" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                <path d="M4 4h7v7H4V4zm9 0h7v7h-7V4zM4 13h7v7H4v-7zm9 0h7v7h-7v-7z" />
            </svg>
        </button>
    </div>

    {{-- MOBILE MENU SHEET --}}
    <div class="sm:hidden" x-cloak>
        <div x-show="openMenu" class="fixed inset-0 z-[80]">
            <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="openMenu = false"></div>

            <div class="absolute inset-x-0 bottom-0 bg-white rounded-t-[2.5rem] shadow-2xl overflow-hidden"
                style="padding-bottom: env(safe-area-inset-bottom);">

                {{-- header sheet --}}
                <div class="px-5 pt-5 pb-4">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.22em]">
                                Hi, {{ $u->name }}!
                            </p>
                            <h3 class="mt-1 text-xl font-black text-slate-900 uppercase tracking-tight">
                                Fitur Pilihan Kamu
                            </h3>
                            <p class="mt-1 text-[10px] font-bold text-slate-500 uppercase tracking-widest">
                                {{ strtoupper(str_replace('_', ' ', $u->role ?? 'member')) }} MODE
                            </p>
                        </div>

                        <button type="button" @click="openMenu = false"
                            class="p-2 rounded-2xl border-2 border-slate-200 text-slate-700 hover:bg-slate-50 active:scale-95">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="square" stroke-linejoin="square" stroke-width="3"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- grid menu ala mobile banking --}}
                <div class="px-5 pb-5">
                    <div class="grid grid-cols-4 gap-4">

                        {{-- HOME --}}
                        <a href="{{ $homeHref }}"
                            class="group flex flex-col items-center gap-2 tap-press transition-transform">
                            <div
                                class="w-14 h-14 rounded-2xl border flex items-center justify-center transition-all
                                {{ $activeDashboard ? 'shokaido-nav-active' : 'bg-slate-100 border-slate-200 text-slate-700 group-hover:bg-slate-900 group-hover:text-white' }}">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="square" stroke-linejoin="square" stroke-width="2.5"
                                        d="M3 10.5L12 3l9 7.5V21a1.5 1.5 0 0 1-1.5 1.5H4.5A1.5 1.5 0 0 1 3 21v-10.5z" />
                                    <path stroke-linecap="square" stroke-linejoin="square" stroke-width="2.5"
                                        d="M9 22V12h6v10" />
                                </svg>
                            </div>
                            <span class="text-[10px] font-black text-slate-900">Home</span>
                            @if ($activeDashboard)
                                <span class="w-10 nav-indicator"></span>
                            @endif
                        </a>

                        {{-- PENGURUS --}}
                        @if ($isStruktural)
                            <a href="{{ route('admin.officials.index') }}"
                                class="group flex flex-col items-center gap-2 tap-press transition-transform">
                                <div
                                    class="w-14 h-14 rounded-2xl border flex items-center justify-center transition-all
                                    {{ $activeOfficials ? 'shokaido-nav-active' : 'bg-slate-100 border-slate-200 text-slate-700 group-hover:bg-slate-900 group-hover:text-white' }}">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="square" stroke-linejoin="square" stroke-width="2.5"
                                            d="M17 20h5v-2a3 3 0 0 0-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 0 1 5.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 0 1 9.288 0M15 7a3 3 0 1 1-6 0 3 3 0 0 1 6 0z" />
                                    </svg>
                                </div>
                                <span class="text-[10px] font-black text-slate-900">Pengurus</span>
                                @if ($activeOfficials)
                                    <span class="w-10 nav-indicator"></span>
                                @endif
                            </a>
                        @else
                            <div class="flex flex-col items-center gap-2 opacity-40">
                                <div
                                    class="w-14 h-14 rounded-2xl bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-700">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="square" stroke-linejoin="square" stroke-width="2.5"
                                            d="M17 20h5v-2a3 3 0 0 0-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 0 1 5.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 0 1 9.288 0M15 7a3 3 0 1 1-6 0 3 3 0 0 1 6 0z" />
                                    </svg>
                                </div>
                                <span class="text-[10px] font-black text-slate-900">Pengurus</span>
                            </div>
                        @endif

                        {{-- DOJO --}}
                        <a href="{{ $isAdmin ? route('admin.dojos.index') : $homeHref }}"
                            class="group flex flex-col items-center gap-2 tap-press transition-transform">
                            <div
                                class="w-14 h-14 rounded-2xl border flex items-center justify-center transition-all
                                {{ $activeDojos ? 'shokaido-nav-active' : 'bg-slate-100 border-slate-200 text-slate-700 group-hover:bg-slate-900 group-hover:text-white' }}">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="square" stroke-linejoin="square" stroke-width="2.5"
                                        d="M3 21V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v16" />
                                    <path stroke-linecap="square" stroke-linejoin="square" stroke-width="2.5"
                                        d="M7 21V11h10v10" />
                                </svg>
                            </div>
                            <span class="text-[10px] font-black text-slate-900">Dojo</span>
                            @if ($activeDojos)
                                <span class="w-10 nav-indicator"></span>
                            @endif
                        </a>

                        {{-- UJIAN --}}
                        @if ($isAdmin)
                            <a href="{{ route('admin.exams.index') }}"
                                class="group flex flex-col items-center gap-2 tap-press transition-transform">
                                <div
                                    class="w-14 h-14 rounded-2xl border flex items-center justify-center transition-all
                                    {{ $activeExams ? 'shokaido-nav-active' : 'bg-slate-100 border-slate-200 text-slate-700 group-hover:bg-slate-900 group-hover:text-white' }}">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M2,11L12,16L22,11L12,6L2,11M12,18L2,13V15L12,20L22,15V13L12,18Z" />
                                    </svg>
                                </div>
                                <span class="text-[10px] font-black text-slate-900">Ujian</span>
                                @if ($activeExams)
                                    <span class="w-10 nav-indicator"></span>
                                @endif
                            </a>
                        @else
                            <div class="flex flex-col items-center gap-2 opacity-40">
                                <div
                                    class="w-14 h-14 rounded-2xl bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-700">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M2,11L12,16L22,11L12,6L2,11M12,18L2,13V15L12,20L22,15V13L12,18Z" />
                                    </svg>
                                </div>
                                <span class="text-[10px] font-black text-slate-900">Ujian</span>
                            </div>
                        @endif

                        {{-- PROFIL --}}
                        <a href="{{ route('profile.edit') }}"
                            class="group flex flex-col items-center gap-2 tap-press transition-transform">
                            <div
                                class="w-14 h-14 rounded-2xl bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-700 group-hover:bg-slate-900 group-hover:text-white transition-all">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="square" stroke-linejoin="square" stroke-width="2.5"
                                        d="M20 21a8 8 0 1 0-16 0" />
                                    <path stroke-linecap="square" stroke-linejoin="square" stroke-width="2.5"
                                        d="M12 13a4 4 0 1 0-4-4 4 4 0 0 0 4 4z" />
                                </svg>
                            </div>
                            <span class="text-[10px] font-black text-slate-900">Profil</span>
                        </a>

                        {{-- SISTEM --}}
                        <button type="button" @click="openSystem = !openSystem"
                            class="group flex flex-col items-center gap-2 tap-press transition-transform">
                            <div
                                class="w-14 h-14 rounded-2xl bg-white border-2 border-slate-200 flex items-center justify-center text-slate-800 group-hover:bg-slate-50 transition-all">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="square" stroke-linejoin="square" stroke-width="2.5"
                                        d="M12 15.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z" />
                                    <path stroke-linecap="square" stroke-linejoin="square" stroke-width="2.5"
                                        d="M19.4 15a7.97 7.97 0 0 0 .1-1 7.97 7.97 0 0 0-.1-1l2-1.6-2-3.4-2.4 1a8.3 8.3 0 0 0-1.7-1L15 3h-6l-.3 2.9a8.3 8.3 0 0 0-1.7 1l-2.4-1-2 3.4 2 1.6a7.97 7.97 0 0 0-.1 1c0 .34.03.67.1 1l-2 1.6 2 3.4 2.4-1c.52.42 1.09.77 1.7 1L9 21h6l.3-2.9c.61-.23 1.18-.58 1.7-1l2.4 1 2-3.4-2-1.6z" />
                                </svg>
                            </div>
                            <span class="text-[10px] font-black text-slate-900">Sistem</span>
                            @if ($activeSystem)
                                <span class="w-10 nav-indicator"></span>
                            @endif
                        </button>

                        {{-- LOGOUT --}}
                        <form method="POST" action="{{ route('logout') }}" class="col-span-2">
                            @csrf
                            <button type="submit"
                                class="w-full h-14 rounded-2xl bg-red-600 border-b-4 border-red-800 text-white text-[10px] font-black uppercase tracking-widest active:translate-y-1">
                                Logout
                            </button>
                        </form>
                    </div>

                    {{-- sistem links --}}
                    <div x-show="openSystem" x-transition x-cloak class="mt-5">
                        <div class="flex items-center justify-between">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.22em]">Sistem</p>
                            <button type="button" @click="openSystem = false"
                                class="text-[10px] font-black uppercase tracking-widest text-red-600">
                                Tutup
                            </button>
                        </div>

                        <div class="mt-3 grid grid-cols-1 gap-2">
                            @if ($isSistemAdmin)
                                <a href="{{ route('admin.users.index') }}"
                                    class="px-4 py-4 rounded-2xl bg-slate-50 border border-slate-200 text-slate-900 text-[10px] font-black uppercase tracking-widest">
                                    User Management
                                </a>
                                <a href="{{ route('admin.exams.fees.index') }}"
                                    class="px-4 py-4 rounded-2xl bg-slate-50 border border-slate-200 text-slate-900 text-[10px] font-black uppercase tracking-widest">
                                    Biaya Ujian
                                </a>
                                <a href="{{ route('admin.fees.index') }}"
                                    class="px-4 py-4 rounded-2xl bg-slate-50 border border-slate-200 text-slate-900 text-[10px] font-black uppercase tracking-widest">
                                    Konfigurasi Iuran
                                </a>
                                @if ($hasRole('pb'))
                                    <a href="{{ route('admin.provinces.index') }}"
                                        class="px-4 py-4 rounded-2xl bg-slate-50 border border-slate-200 text-slate-900 text-[10px] font-black uppercase tracking-widest">
                                        Wilayah / Pengprov
                                    </a>
                                @endif
                            @else
                                <div
                                    class="px-4 py-4 rounded-2xl bg-slate-50 border border-slate-200 text-slate-500 text-[10px] font-black uppercase tracking-widest">
                                    Tidak ada akses sistem
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="h-3"></div>
            </div>
        </div>
    </div>
</nav>
