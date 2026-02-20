<nav x-data="{ open: false }" class="bg-white border-b border-gray-100 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a
                        href="{{ in_array(Auth::user()->role, ['pb', 'pengprov', 'pengcab', 'admin_dojo']) ? route('admin.dashboard') : route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    {{-- 1. Dashboard Dinamis --}}
                    <x-nav-link :href="in_array(Auth::user()->role, ['pb', 'pengprov', 'pengcab', 'admin_dojo'])
                        ? route('admin.dashboard')
                        : route('dashboard')" :active="request()->routeIs('admin.dashboard') || request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    {{-- 2. Dropdown Manajemen Data (Struktural Only) --}}
                    @if (in_array(Auth::user()->role, ['pb', 'pengprov', 'pengcab']))
                        <div class="hidden sm:flex sm:items-center sm:ms-4">
                            <x-dropdown align="left" width="48">
                                <x-slot name="trigger">
                                    <button
                                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150 {{ request()->routeIs('admin.*') && !request()->routeIs('admin.dashboard') ? 'text-indigo-700 font-bold' : '' }}">
                                        <div>{{ __('Manajemen Data') }}</div>
                                        <div class="ms-1">
                                            <svg class="fill-current h-4 w-4" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </button>
                                </x-slot>

                                <x-slot name="content">
                                    {{-- PB & PENGPROV: Bisa Kelola User --}}
                                    @if (in_array(Auth::user()->role, ['pb', 'pengprov']))
                                        <x-dropdown-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                                            {{ __('Manajemen User') }}
                                        </x-dropdown-link>
                                    @endif

                                    {{-- HANYA PB: Bisa Lihat Data Wilayah --}}
                                    @if (Auth::user()->role === 'pb')
                                        <x-dropdown-link :href="route('admin.provinces.index')" :active="request()->routeIs('admin.provinces.*')">
                                            {{ __('Data Wilayah (Provinsi)') }}
                                        </x-dropdown-link>
                                    @endif

                                    <hr class="border-gray-100">

                                    {{-- PB, PENGPROV, PENGCAB: Kelola Inti --}}
                                    <x-dropdown-link :href="route('admin.officials.index')" :active="request()->routeIs('admin.officials.*')">
                                        {{ __('Data Pengurus') }}
                                    </x-dropdown-link>

                                    <x-dropdown-link :href="route('admin.dojos.index')" :active="request()->routeIs('admin.dojos.*')">
                                        {{ __('Manajemen Dojo') }}
                                    </x-dropdown-link>
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
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div class="flex flex-col items-end">
                                <div class="font-bold text-gray-800">{{ Auth::user()->name }}</div>
                                <div class="text-[9px] uppercase font-black text-indigo-600 tracking-tighter">
                                    {{ str_replace('_', ' ', Auth::user()->role) }}
                                </div>
                            </div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            {{-- Hamburger --}}
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Responsive Menu --}}
    <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden shadow-inner bg-gray-50">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="in_array(Auth::user()->role, ['pb', 'pengprov', 'pengcab', 'admin_dojo'])
                ? route('admin.dashboard')
                : route('dashboard')" :active="request()->routeIs('admin.dashboard') || request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            @if (Auth::user()->role === 'pb')
                <x-responsive-nav-link :href="route('admin.provinces.index')" :active="request()->routeIs('admin.provinces.*')">
                    {{ __('Data Wilayah (Provinsi)') }}
                </x-responsive-nav-link>
            @endif

            @if (in_array(Auth::user()->role, ['pb', 'pengprov']))
                <x-responsive-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                    {{ __('Manajemen User') }}
                </x-responsive-nav-link>
            @endif

            @if (in_array(Auth::user()->role, ['pb', 'pengprov', 'pengcab']))
                <x-responsive-nav-link :href="route('admin.officials.index')" :active="request()->routeIs('admin.officials.*')">
                    {{ __('Data Pengurus') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('admin.dojos.index')" :active="request()->routeIs('admin.dojos.*')">
                    {{ __('Manajemen Dojo') }}
                </x-responsive-nav-link>
            @endif
        </div>
    </div>
</nav>
