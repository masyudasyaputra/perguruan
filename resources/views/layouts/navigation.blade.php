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

<nav x-data="{ open: false }" class="bg-white/90 backdrop-blur-md sticky top-0 z-50 border-b border-slate-100 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-20">
            <div class="flex">
                {{-- Logo Section --}}
                <div class="shrink-0 flex items-center">
                    <a href="{{ $isAdmin ? route('admin.dashboard') : route('dashboard') }}"
                        class="hover:opacity-80 transition-opacity">
                        <x-application-logo class="block h-10 w-auto fill-current text-red-600" />
                    </a>
                </div>

                {{-- Desktop Menu --}}
                <div class="hidden space-x-2 sm:-my-px sm:ms-10 sm:flex items-center">

                    {{-- 1. Dashboard --}}
                    <x-nav-link :href="$isAdmin ? route('admin.dashboard') : route('dashboard')" :active="request()->routeIs('admin.dashboard') || request()->routeIs('dashboard')"
                        class="px-4 py-2 rounded-xl text-sm font-bold tracking-tight transition-all duration-200 border-none {{ request()->routeIs('admin.dashboard') || request()->routeIs('dashboard') ? 'bg-red-50 text-red-700 shadow-sm' : 'text-slate-500 hover:bg-slate-50' }}">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    {{-- 2. Data Pengurus & Manajemen Dojo --}}
                    @if ($isStruktural)
                        <x-nav-link :href="route('admin.officials.index')" :active="request()->routeIs('admin.officials.*')"
                            class="px-4 py-2 rounded-xl text-sm font-bold tracking-tight transition-all duration-200 border-none {{ request()->routeIs('admin.officials.*') ? 'bg-red-50 text-red-700 shadow-sm' : 'text-slate-500 hover:bg-slate-50' }}">
                            {{ __('Data Pengurus') }}
                        </x-nav-link>

                        <x-nav-link :href="route('admin.dojos.index')" :active="request()->routeIs('admin.dojos.*')"
                            class="px-4 py-2 rounded-xl text-sm font-bold tracking-tight transition-all duration-200 border-none {{ request()->routeIs('admin.dojos.*') ? 'bg-red-50 text-red-700 shadow-sm' : 'text-slate-500 hover:bg-slate-50' }}">
                            {{ __('Manajemen Dojo') }}
                        </x-nav-link>
                    @endif

                    {{-- 3. Ujian Sabuk --}}
                    @if ($isAdmin)
                        <x-nav-link :href="route('admin.exams.index')" :active="request()->routeIs('admin.exams.*')"
                            class="px-4 py-2 rounded-xl text-sm font-bold tracking-tight transition-all duration-200 border-none {{ request()->routeIs('admin.exams.*') ? 'bg-red-50 text-red-700 shadow-sm' : 'text-slate-500 hover:bg-slate-50' }}">
                            <span class="flex items-center gap-2">
                                <div
                                    class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.exams.*') ? 'bg-red-600' : 'bg-slate-300' }}">
                                </div>
                                {{ __('Ujian Sabuk') }}
                            </span>
                        </x-nav-link>
                    @endif

                    {{-- 4. Sistem Dropdown --}}
                    @if ($isSistemAdmin)
                        <div class="hidden sm:flex sm:items-center sm:ms-2">
                            <x-dropdown align="left" width="56">
                                <x-slot name="trigger">
                                    <button
                                        class="inline-flex items-center px-4 py-2 border-none text-sm font-bold rounded-xl transition-all duration-200 focus:outline-none {{ request()->routeIs('admin.users.*') || request()->routeIs('admin.provinces.*') || request()->routeIs('admin.fees.*') || request()->routeIs('admin.exams.fees.*') ? 'bg-gray-900 text-white shadow-lg' : 'text-slate-500 hover:bg-slate-50' }}">
                                        <div>{{ __('Sistem') }}</div>
                                        <svg class="ms-1.5 h-4 w-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>
                                </x-slot>

                                <x-slot name="content">
                                    <div class="p-2 space-y-0.5">
                                        <div
                                            class="px-3 py-2 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">
                                            Pengaturan Utama</div>
                                        <x-dropdown-link :href="route('admin.users.index')"
                                            class="rounded-lg font-semibold {{ request()->routeIs('admin.users.*') ? 'text-red-600 bg-red-50' : '' }}">
                                            {{ __('Manajemen User') }}
                                        </x-dropdown-link>
                                        <x-dropdown-link :href="route('admin.exams.fees.index')"
                                            class="rounded-lg font-semibold {{ request()->routeIs('admin.exams.fees.*') ? 'text-red-600 bg-red-50' : '' }}">
                                            {{ __('Master Biaya Ujian') }}
                                        </x-dropdown-link>
                                        <x-dropdown-link :href="route('admin.fees.index')"
                                            class="rounded-lg font-semibold {{ request()->routeIs('admin.fees.*') ? 'text-red-600 bg-red-50' : '' }}">
                                            {{ __('Konfigurasi Iuran') }}
                                        </x-dropdown-link>
                                        @if ($hasRole('pb'))
                                            <x-dropdown-link :href="route('admin.provinces.index')"
                                                class="rounded-lg font-semibold {{ request()->routeIs('admin.provinces.*') ? 'text-red-600 bg-red-50' : '' }}">
                                                {{ __('Data Wilayah') }}
                                            </x-dropdown-link>
                                        @endif
                                    </div>
                                </x-slot>
                            </x-dropdown>
                        </div>
                    @endif
                </div>
            </div>

            {{-- User Settings Dropdown (Multi-Role Style Awal) --}}
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="64">
                    <x-slot name="trigger">
                        <button
                            class="group flex items-center gap-3 px-3 py-1.5 border border-slate-100 rounded-2xl hover:bg-slate-50 transition-all duration-200 focus:outline-none">
                            <div class="flex flex-col text-right">
                                <span
                                    class="text-sm font-black text-slate-900 leading-none">{{ Auth::user()->name }}</span>
                                {{-- Daftar Badge Role --}}
                                <div class="flex flex-wrap justify-end gap-1 mt-1.5 max-w-[180px]">
                                    @foreach ($userRoles as $role)
                                        <span
                                            class="text-[8px] uppercase font-bold text-white bg-red-600 px-1.5 py-0.5 rounded italic leading-tight shadow-sm">
                                            {{ str_replace('_', ' ', $role) }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                            <div
                                class="h-10 w-10 rounded-xl bg-gray-900 flex items-center justify-center text-white font-black text-sm shadow-md group-hover:rotate-3 transition-transform">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="p-2">
                            <div class="px-3 py-2 border-b border-slate-50 mb-1">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-none">
                                    Status Login</p>
                                <div class="flex flex-col gap-1 mt-2">
                                    @foreach ($userRoles as $role)
                                        <p class="text-[11px] font-black text-gray-800 uppercase italic leading-none">
                                            • {{ str_replace('_', ' ', $role) }}
                                        </p>
                                    @endforeach
                                </div>
                            </div>
                            <x-dropdown-link :href="route('profile.edit')" class="rounded-lg font-medium">
                                {{ __('Edit Profil') }}
                            </x-dropdown-link>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();"
                                    class="rounded-lg font-medium text-red-600 hover:bg-red-50">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </div>
                    </x-slot>
                </x-dropdown>
            </div>

            {{-- Hamburger Mobile --}}
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-xl text-slate-500 hover:bg-red-50 hover:text-red-600 transition-all">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile Menu --}}
    <div x-show="open" x-cloak class="sm:hidden bg-white border-t border-slate-100 pb-6 shadow-xl">
        <div class="pt-4 pb-3 space-y-1 px-4">
            <x-responsive-nav-link :href="$isAdmin ? route('admin.dashboard') : route('dashboard')" :active="request()->routeIs('admin.dashboard') || request()->routeIs('dashboard')" class="rounded-xl font-bold">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            @if ($isStruktural)
                <x-responsive-nav-link :href="route('admin.officials.index')" :active="request()->routeIs('admin.officials.*')" class="rounded-xl font-bold">
                    {{ __('Data Pengurus') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.dojos.index')" :active="request()->routeIs('admin.dojos.*')" class="rounded-xl font-bold">
                    {{ __('Manajemen Dojo') }}
                </x-responsive-nav-link>
            @endif

            @if ($isAdmin)
                <x-responsive-nav-link :href="route('admin.exams.index')" :active="request()->routeIs('admin.exams.*')" class="rounded-xl font-bold text-red-600">
                    {{ __('Ujian Sabuk') }}
                </x-responsive-nav-link>
            @endif

            @if ($isSistemAdmin)
                <div class="my-4 border-t border-slate-100 mx-2"></div>
                <x-responsive-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')" class="rounded-xl font-bold">
                    {{ __('Manajemen User') }}
                </x-responsive-nav-link>
            @endif
        </div>
    </div>
</nav>
