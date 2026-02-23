<nav x-data="{ open: false }"
    class="bg-white/80 backdrop-blur-md sticky top-0 z-50 border-b border-slate-100 shadow-[0_2px_15px_rgba(0,0,0,0.02)]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-20">
            <div class="flex">
                {{-- Logo Section --}}
                <div class="shrink-0 flex items-center">
                    <a href="{{ in_array(Auth::user()->role, ['pb', 'pengprov', 'pengcab', 'admin_dojo']) ? route('admin.dashboard') : route('dashboard') }}"
                        class="hover:opacity-80 transition-opacity">
                        <x-application-logo class="block h-10 w-auto fill-current text-indigo-600" />
                    </a>
                </div>

                {{-- Desktop Menu --}}
                <div class="hidden space-x-2 sm:-my-px sm:ms-10 sm:flex items-center">

                    {{-- 1. Dashboard (Akses: Semua) --}}
                    <x-nav-link :href="in_array(Auth::user()->role, ['pb', 'pengprov', 'pengcab', 'admin_dojo'])
                        ? route('admin.dashboard')
                        : route('dashboard')" :active="request()->routeIs('admin.dashboard') || request()->routeIs('dashboard')"
                        class="px-4 py-2 rounded-xl text-sm font-bold tracking-tight transition-all duration-200 border-none {{ request()->routeIs('admin.dashboard') || request()->routeIs('dashboard') ? 'bg-indigo-50 text-indigo-700 shadow-sm shadow-indigo-100/50' : 'text-slate-500 hover:bg-slate-50' }}">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    {{-- 2. Data Pengurus & Manajemen Dojo (Hanya Struktural) --}}
                    @if (in_array(Auth::user()->role, ['pb', 'pengprov', 'pengcab']))
                        <x-nav-link :href="route('admin.officials.index')" :active="request()->routeIs('admin.officials.*')"
                            class="px-4 py-2 rounded-xl text-sm font-bold tracking-tight transition-all duration-200 border-none {{ request()->routeIs('admin.officials.*') ? 'bg-indigo-50 text-indigo-700 shadow-sm shadow-indigo-100/50' : 'text-slate-500 hover:bg-slate-50' }}">
                            {{ __('Data Pengurus') }}
                        </x-nav-link>

                        <x-nav-link :href="route('admin.dojos.index')" :active="request()->routeIs('admin.dojos.*')"
                            class="px-4 py-2 rounded-xl text-sm font-bold tracking-tight transition-all duration-200 border-none {{ request()->routeIs('admin.dojos.*') ? 'bg-indigo-50 text-indigo-700 shadow-sm shadow-indigo-100/50' : 'text-slate-500 hover:bg-slate-50' }}">
                            {{ __('Manajemen Dojo') }}
                        </x-nav-link>
                    @endif

                    {{-- 3. Ujian Sabuk (Akses: Semua Admin + Admin Dojo) --}}
                    @if (in_array(Auth::user()->role, ['pb', 'pengprov', 'pengcab', 'admin_dojo']))
                        <x-nav-link :href="route('admin.exams.index')" :active="request()->routeIs('admin.exams.*')"
                            class="px-4 py-2 rounded-xl text-sm font-bold tracking-tight transition-all duration-200 border-none {{ request()->routeIs('admin.exams.*') ? 'bg-indigo-50 text-indigo-700 shadow-sm shadow-indigo-100/50' : 'text-slate-500 hover:bg-slate-50' }}">
                            <span class="flex items-center gap-2">
                                <div class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.exams.*') ? 'bg-indigo-600' : 'bg-slate-300' }}"></div>
                                {{ __('Ujian Sabuk') }}
                            </span>
                        </x-nav-link>
                    @endif

                    {{-- 4. Dropdown Manajemen Sistem (Hanya PB & Pengprov) --}}
                    @if (in_array(Auth::user()->role, ['pb', 'pengprov']))
                        <div class="hidden sm:flex sm:items-center sm:ms-2">
                            <x-dropdown align="left" width="56">
                                <x-slot name="trigger">
                                    <button
                                        class="inline-flex items-center px-4 py-2 border-none text-sm font-bold rounded-xl transition-all duration-200 focus:outline-none {{ request()->routeIs('admin.users.*') || request()->routeIs('admin.provinces.*') || request()->routeIs('admin.fees.*') || request()->routeIs('admin.exams.fees.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-200' : 'text-slate-500 hover:bg-slate-50' }}">
                                        <div>{{ __('Sistem') }}</div>
                                        <svg class="ms-1.5 h-4 w-4 transition-transform duration-200"
                                            :class="{ 'rotate-180': open }" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>
                                </x-slot>

                                <x-slot name="content">
                                    <div class="p-2 space-y-0.5">
                                        <div class="px-3 py-2 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">
                                            Pengaturan Utama</div>

                                        <x-dropdown-link :href="route('admin.users.index')"
                                            class="rounded-lg font-semibold hover:bg-slate-50">
                                            {{ __('Manajemen User') }}
                                        </x-dropdown-link>

                                        <x-dropdown-link :href="route('admin.exams.fees.index')"
                                            class="rounded-lg font-semibold hover:bg-slate-50 {{ request()->routeIs('admin.exams.fees.*') ? 'text-indigo-600 bg-indigo-50' : '' }}">
                                            {{ __('Master Biaya Ujian') }}
                                        </x-dropdown-link>

                                        <x-dropdown-link :href="route('admin.fees.index')"
                                            class="rounded-lg font-semibold hover:bg-slate-50 {{ request()->routeIs('admin.fees.*') ? 'text-indigo-600 bg-indigo-50' : '' }}">
                                            {{ __('Konfigurasi Iuran') }}
                                        </x-dropdown-link>

                                        @if (Auth::user()->role === 'pb')
                                            <x-dropdown-link :href="route('admin.provinces.index')"
                                                class="rounded-lg font-semibold hover:bg-slate-50">
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

            {{-- User Settings Dropdown --}}
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            class="group flex items-center gap-3 px-3 py-1.5 border border-slate-100 rounded-2xl hover:bg-slate-50 transition-all duration-200 focus:outline-none">
                            <div class="flex flex-col text-right">
                                <span class="text-sm font-black text-slate-900 leading-none">{{ Auth::user()->name }}</span>
                                <span class="text-[9px] uppercase font-bold text-indigo-500 tracking-wider mt-1">{{ str_replace('_', ' ', Auth::user()->role) }}</span>
                            </div>
                            <div class="h-9 w-9 rounded-xl bg-indigo-600 flex items-center justify-center text-white font-black text-sm shadow-md shadow-indigo-200 group-hover:scale-105 transition-transform">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="p-2">
                            <x-dropdown-link :href="route('profile.edit')" class="rounded-lg font-medium">
                                {{ __('My Profile') }}
                            </x-dropdown-link>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();"
                                    class="rounded-lg font-medium text-rose-600 hover:bg-rose-50">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </div>
                    </x-slot>
                </x-dropdown>
            </div>

            {{-- Hamburger (Mobile) --}}
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2.5 rounded-xl text-slate-500 bg-slate-50 hover:bg-indigo-50 hover:text-indigo-600 transition-all duration-200">
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
    <div x-show="open" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
        class="sm:hidden bg-white border-t border-slate-100 pb-6 shadow-xl">
        <div class="pt-4 pb-3 space-y-1 px-4">
            {{-- Dashboard Mobile --}}
            <x-responsive-nav-link :href="in_array(Auth::user()->role, ['pb', 'pengprov', 'pengcab', 'admin_dojo'])
                ? route('admin.dashboard')
                : route('dashboard')" :active="request()->routeIs('admin.dashboard') || request()->routeIs('dashboard')" class="rounded-xl font-bold">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            {{-- Struktural Only Mobile --}}
            @if (in_array(Auth::user()->role, ['pb', 'pengprov', 'pengcab']))
                <x-responsive-nav-link :href="route('admin.officials.index')" :active="request()->routeIs('admin.officials.*')" class="rounded-xl font-bold">
                    {{ __('Data Pengurus') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.dojos.index')" :active="request()->routeIs('admin.dojos.*')" class="rounded-xl font-bold">
                    {{ __('Manajemen Dojo') }}
                </x-responsive-nav-link>
            @endif

            {{-- Ujian Sabuk Mobile (Terbuka untuk Admin Dojo) --}}
            @if (in_array(Auth::user()->role, ['pb', 'pengprov', 'pengcab', 'admin_dojo']))
                <x-responsive-nav-link :href="route('admin.exams.index')" :active="request()->routeIs('admin.exams.*')" class="rounded-xl font-bold text-indigo-600">
                    {{ __('Ujian Sabuk') }}
                </x-responsive-nav-link>
            @endif

            <div class="my-4 border-t border-slate-100 mx-2"></div>

            {{-- Sistem Management (Hanya PB & Pengprov) --}}
            @if (in_array(Auth::user()->role, ['pb', 'pengprov']))
                <x-responsive-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')" class="rounded-xl font-bold">
                    {{ __('Manajemen User') }}
                </x-responsive-nav-link>
                
                <x-responsive-nav-link :href="route('admin.exams.fees.index')" :active="request()->routeIs('admin.exams.fees.*')" class="rounded-xl font-bold">
                    {{ __('Master Biaya Ujian') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('admin.fees.index')" :active="request()->routeIs('admin.fees.*')" class="rounded-xl font-bold">
                    {{ __('Konfigurasi Iuran') }}
                </x-responsive-nav-link>

                @if (Auth::user()->role === 'pb')
                    <x-responsive-nav-link :href="route('admin.provinces.index')" :active="request()->routeIs('admin.provinces.*')" class="rounded-xl font-bold">
                        {{ __('Data Wilayah') }}
                    </x-responsive-nav-link>
                @endif
            @endif
        </div>
    </div>
</nav>