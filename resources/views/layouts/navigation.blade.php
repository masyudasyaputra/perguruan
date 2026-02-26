@php
    // Helper untuk cek akses multi-role
    $userRoles = array_unique(is_array(Auth::user()->roles) ? Auth::user()->roles : [Auth::user()->role]);

    $hasRole = function ($roles) use ($userRoles) {
        return count(array_intersect((array) $roles, $userRoles)) > 0;
    };

    $isAdmin = $hasRole(['pb', 'pengprov', 'pengcab', 'admin_dojo']);
    $isStruktural = $hasRole(['pb', 'pengprov', 'pengcab']);
    $isSistemAdmin = $hasRole(['pb', 'pengprov']);
@endphp

<style>
    .gold-gradient-text {
        background: linear-gradient(to right, #bf953f, #fcf6ba, #b38728, #fbf5b7, #aa771c);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .shokaido-nav-active {
        background: linear-gradient(135deg, #8b0000 0%, #5a0000 100%);
        color: white !important;
    }
</style>

<nav x-data="{ open: false }"
    class="bg-slate-900 text-white backdrop-blur-xl sticky top-0 z-50 border-b border-[#bf953f]/20 shadow-lg shadow-red-900/5">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-20">
            <div class="flex">
                {{-- Logo Section --}}
                <div class="shrink-0 flex items-center">
                    <a href="{{ $isAdmin ? route('admin.dashboard') : route('dashboard') }}"
                        class="flex items-center gap-3 group transition-transform active:scale-95">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo"
                            class="h-10 sm:h-12 w-auto drop-shadow-sm group-hover:drop-shadow-md transition-all">

                        {{-- Perbaikan: Menghapus 'hidden md:flex' agar teks logo muncul di mobile --}}
                        <div class="flex flex-col min-w-0">
                            <span
                                class="font-black tracking-tighter text-sm sm:text-lg leading-none text-white uppercase italic">
                                SHOKAIDO<span class="gold-gradient-text">.OS</span>
                            </span>
                            <span
                                class="text-[6px] sm:text-[8px] font-bold text-red-600 uppercase tracking-[0.1em] sm:tracking-[0.2em] leading-tight mt-0.5">
                                Shotokan Kandaga Indonesia
                            </span>
                        </div>
                    </a>
                </div>

                {{-- Desktop Menu --}}
                <div class="hidden space-x-1.5 sm:-my-px sm:ms-10 sm:flex items-center">

                    {{-- 1. Dashboard --}}
                    <x-nav-link :href="$isAdmin ? route('admin.dashboard') : route('dashboard')" :active="request()->routeIs('admin.dashboard') || request()->routeIs('dashboard')"
                        class="px-4 py-2 rounded-xl text-[11px] font-black uppercase tracking-widest transition-all duration-200 border-none {{ request()->routeIs('admin.dashboard') || request()->routeIs('dashboard') ? 'shokaido-nav-active shadow-md shadow-red-900/20' : 'text-slate-500 hover:bg-red-50 hover:text-red-700' }}">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    {{-- 2. Data Pengurus & Manajemen Dojo --}}
                    @if ($isStruktural)
                        <x-nav-link :href="route('admin.officials.index')" :active="request()->routeIs('admin.officials.*')"
                            class="px-4 py-2 rounded-xl text-[11px] font-black uppercase tracking-widest transition-all duration-200 border-none {{ request()->routeIs('admin.officials.*') ? 'shokaido-nav-active shadow-md shadow-red-900/20' : 'text-slate-500 hover:bg-red-50 hover:text-red-700' }}">
                            {{ __('Pengurus') }}
                        </x-nav-link>

                        <x-nav-link :href="route('admin.dojos.index')" :active="request()->routeIs('admin.dojos.*')"
                            class="px-4 py-2 rounded-xl text-[11px] font-black uppercase tracking-widest transition-all duration-200 border-none {{ request()->routeIs('admin.dojos.*') ? 'shokaido-nav-active shadow-md shadow-red-900/20' : 'text-slate-500 hover:bg-red-50 hover:text-red-700' }}">
                            {{ __('Dojo') }}
                        </x-nav-link>
                    @endif

                    {{-- 3. Ujian Sabuk --}}
                    @if ($isAdmin)
                        <x-nav-link :href="route('admin.exams.index')" :active="request()->routeIs('admin.exams.*')"
                            class="px-4 py-2 rounded-xl text-[11px] font-black uppercase tracking-widest transition-all duration-200 border-none {{ request()->routeIs('admin.exams.*') ? 'shokaido-nav-active shadow-md shadow-red-900/20' : 'text-slate-500 hover:bg-red-50 hover:text-red-700' }}">
                            <span class="flex items-center gap-2">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M2,11L12,16L22,11L12,6L2,11M12,18L2,13V15L12,20L22,15V13L12,18Z" />
                                </svg>
                                {{ __('Ujian') }}
                            </span>
                        </x-nav-link>
                    @endif

                    {{-- 4. Sistem Dropdown --}}
                    @if ($isSistemAdmin)
                        <div class="hidden sm:flex sm:items-center sm:ms-2">
                            <x-dropdown align="left" width="56">
                                <x-slot name="trigger">
                                    <button
                                        class="inline-flex items-center px-4 py-2 border border-slate-100 dark:border-white/10 text-[11px] font-black uppercase tracking-widest rounded-xl transition-all duration-200 focus:outline-none {{ request()->routeIs('admin.users.*') || request()->routeIs('admin.provinces.*') || request()->routeIs('admin.fees.*') || request()->routeIs('admin.exams.fees.*') ? 'bg-slate-900 text-white shadow-lg' : 'text-slate-500 hover:bg-slate-50' }}">
                                        <div>{{ __('Sistem') }}</div>
                                        <svg class="ms-1.5 h-3 w-3 transition-transform group-hover:rotate-180"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>
                                </x-slot>

                                <x-slot name="content">
                                    <div class="p-2 space-y-0.5 bg-white dark:bg-zinc-900">
                                        <div
                                            class="px-3 py-2 text-[9px] font-black uppercase tracking-[0.3em] text-red-700 dark:text-[#bf953f] opacity-80">
                                            Core Settings</div>
                                        <x-dropdown-link :href="route('admin.users.index')"
                                            class="rounded-lg text-xs font-bold uppercase tracking-tight {{ request()->routeIs('admin.users.*') ? 'bg-red-50 text-red-700' : '' }}">
                                            {{ __('User Management') }}
                                        </x-dropdown-link>
                                        <x-dropdown-link :href="route('admin.exams.fees.index')"
                                            class="rounded-lg text-xs font-bold uppercase tracking-tight {{ request()->routeIs('admin.exams.fees.*') ? 'bg-red-50 text-red-700' : '' }}">
                                            {{ __('Biaya Ujian') }}
                                        </x-dropdown-link>
                                        <x-dropdown-link :href="route('admin.fees.index')"
                                            class="rounded-lg text-xs font-bold uppercase tracking-tight {{ request()->routeIs('admin.fees.*') ? 'bg-red-50 text-red-700' : '' }}">
                                            {{ __('Konfigurasi Iuran') }}
                                        </x-dropdown-link>
                                        @if ($hasRole('pb'))
                                            <div class="my-1 border-t border-slate-100 dark:border-white/5"></div>
                                            <x-dropdown-link :href="route('admin.provinces.index')"
                                                class="rounded-lg text-xs font-bold uppercase tracking-tight {{ request()->routeIs('admin.provinces.*') ? 'bg-red-50 text-red-700' : '' }}">
                                                {{ __('Wilayah / Pengprov') }}
                                            </x-dropdown-link>
                                        @endif
                                    </div>
                                </x-slot>
                            </x-dropdown>
                        </div>
                    @endif
                </div>
            </div>

            {{-- User Settings Dropdown --}}
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="64">
                    <x-slot name="trigger">
                        <button
                            class="group flex items-center gap-3 px-3 py-1.5 border-2 border-slate-100 dark:border-white/5 rounded-2xl hover:border-red-600/30 hover:bg-red-50/50 transition-all duration-300 focus:outline-none">
                            <div class="flex flex-col text-right">
                                <span
                                    class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-tight leading-none">{{ Auth::user()->name }}</span>
                                <div class="flex flex-wrap justify-end gap-1 mt-1">
                                    @foreach ($userRoles as $role)
                                        <span
                                            class="text-[7px] uppercase font-black text-white bg-gradient-to-r from-red-700 to-red-600 px-1.5 py-0.5 rounded shadow-sm">
                                            {{ str_replace('_', ' ', $role) }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                            <div
                                class="h-10 w-10 rounded-xl bg-gradient-to-br from-slate-800 to-black flex items-center justify-center text-[#bf953f] border border-[#bf953f]/30 font-black text-sm shadow-md group-hover:scale-105 transition-transform">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="p-2 bg-white dark:bg-zinc-900">
                            <div
                                class="px-3 py-3 border-b border-slate-50 dark:border-white/5 mb-1 bg-slate-50/50 dark:bg-white/5 rounded-t-lg">
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">Account
                                    Secure</p>
                                <p class="text-[11px] font-bold text-slate-700 dark:text-slate-300 truncate">
                                    {{ Auth::user()->email }}</p>
                            </div>
                            <x-dropdown-link :href="route('profile.edit')"
                                class="rounded-lg text-xs font-bold uppercase tracking-tight">
                                {{ __('Pengaturan Profil') }}
                            </x-dropdown-link>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();"
                                    class="rounded-lg text-xs font-black uppercase tracking-tight text-red-600 hover:bg-red-50">
                                    {{ __('Keluar Sistem') }}
                                </x-dropdown-link>
                            </form>
                        </div>
                    </x-slot>
                </x-dropdown>
            </div>

            {{-- Hamburger Mobile --}}
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2.5 rounded-xl text-slate-500 hover:bg-red-600 hover:text-white transition-all shadow-sm border border-slate-100">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="3" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile Menu --}}
    <div x-show="open" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-cloak
        class="sm:hidden bg-white dark:bg-[#050505] border-t border-[#bf953f]/20 pb-8 shadow-2xl">
        <div class="pt-4 pb-3 space-y-1 px-4">
            <x-responsive-nav-link :href="$isAdmin ? route('admin.dashboard') : route('dashboard')" :active="request()->routeIs('admin.dashboard') || request()->routeIs('dashboard')"
                class="rounded-xl font-black uppercase text-xs tracking-widest py-3 {{ request()->routeIs('admin.dashboard') || request()->routeIs('dashboard') ? 'bg-red-700 text-white' : '' }}">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            @if ($isStruktural)
                <x-responsive-nav-link :href="route('admin.officials.index')" :active="request()->routeIs('admin.officials.*')"
                    class="rounded-xl font-black uppercase text-xs tracking-widest py-3">
                    {{ __('Data Pengurus') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.dojos.index')" :active="request()->routeIs('admin.dojos.*')"
                    class="rounded-xl font-black uppercase text-xs tracking-widest py-3">
                    {{ __('Manajemen Dojo') }}
                </x-responsive-nav-link>
            @endif

            <div class="my-4 border-t border-slate-100 dark:border-white/5"></div>

            <div class="px-4 py-2 bg-slate-50 dark:bg-white/5 rounded-2xl">
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-3">User Authenticated</p>
                <div class="flex items-center gap-3 mb-4">
                    <div
                        class="h-10 w-10 rounded-lg bg-black text-[#bf953f] flex items-center justify-center font-black border border-[#bf953f]/30">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                    <div>
                        <p class="text-xs font-black uppercase dark:text-white">{{ Auth::user()->name }}</p>
                        <p class="text-[10px] font-bold text-red-700 uppercase">Shokaido Member</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full py-3 bg-red-50 text-red-700 rounded-xl text-[10px] font-black uppercase tracking-widest">Logout</button>
                </form>
            </div>
        </div>
    </div>
</nav>
