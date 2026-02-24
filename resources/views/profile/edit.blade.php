<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight flex items-center gap-2">
            <span class="w-2 h-8 bg-red-600 rounded-full"></span>
            {{ __('Manajemen Profil Pengurus') }}
        </h2>
    </x-slot>

    <div class="py-10 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Top Branding Card: Visual Akun & Label Role --}}
            <div
                class="bg-gray-900 rounded-2xl p-6 mb-8 text-white shadow-xl flex flex-col md:flex-row items-center gap-6 border-b-4 border-red-600">
                <div class="flex-shrink-0">
                    <div
                        class="h-20 w-20 rounded-full bg-red-600 flex items-center justify-center text-3xl font-black shadow-lg border-4 border-gray-800">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                </div>
                <div class="text-center md:text-left flex-grow">
                    <h3 class="text-2xl font-bold uppercase tracking-tight">{{ auth()->user()->name }}</h3>
                    <p class="text-gray-400 text-sm font-medium tracking-widest mb-3">{{ auth()->user()->email }}</p>

                    {{-- Badge Roles Dinamis --}}
                    <div class="flex flex-wrap justify-center md:justify-start gap-2">
                        @php
                            // Mengambil array roles atau fallback ke string role tunggal
                            $userRoles = is_array(auth()->user()->roles)
                                ? auth()->user()->roles
                                : [auth()->user()->role];

                            // Mapping Label & Warna
                            $roleData = [
                                'pb' => ['label' => 'Pengurus Besar', 'class' => 'bg-white text-black'],
                                'pengprov' => ['label' => 'Pengprov', 'class' => 'bg-red-600 text-white'],
                                'pengcab' => ['label' => 'Pengcab', 'class' => 'bg-red-500 text-white'],
                                'admin_dojo' => ['label' => 'Admin Dojo', 'class' => 'bg-gray-700 text-white'],
                                'penguji' => ['label' => 'Penguji', 'class' => 'bg-yellow-500 text-black'],
                                'member' => ['label' => 'Member', 'class' => 'bg-gray-500 text-white'],
                            ];
                        @endphp

                        @foreach ($userRoles as $roleKey)
                            @if (isset($roleData[$roleKey]))
                                <span
                                    class="px-3 py-1 {{ $roleData[$roleKey]['class'] }} text-[10px] font-bold rounded-md uppercase tracking-widest border border-white/10 shadow-sm">
                                    {{ $roleData[$roleKey]['label'] }}
                                </span>
                            @endif
                        @endforeach
                    </div>
                </div>
                <div class="hidden lg:block opacity-10">
                    <svg class="w-20 h-20" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M12 3L1 9l4 2.18v6L12 21l7-3.82v-6l2-1.09V9L12 3zm6.82 6L12 12.72 5.18 9 12 5.28 18.82 9zM17 16l-5 2.72L7 16v-3.73l5 2.72 5-2.72V16z" />
                    </svg>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                {{-- Bagian Kiri: Form Update --}}
                <div class="lg:col-span-8 space-y-8">
                    {{-- Form Informasi Dasar --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-200">
                        <div class="px-6 py-4 bg-gray-50 border-b border-gray-100 flex items-center justify-between">
                            <h3 class="font-bold text-gray-800 uppercase text-xs tracking-widest">Informasi Identitas
                            </h3>
                            <span class="text-[10px] text-gray-400 font-medium">Update terakhir:
                                {{ auth()->user()->updated_at->diffForHumans() }}</span>
                        </div>
                        <div class="p-6 sm:p-8">
                            <div class="max-w-xl">
                                @include('profile.partials.update-profile-information-form')
                            </div>
                        </div>
                    </div>

                    {{-- Form Ganti Password --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-200">
                        <div class="px-6 py-4 bg-gray-50 border-b border-gray-100 flex items-center justify-between">
                            <h3 class="font-bold text-gray-800 uppercase text-xs tracking-widest">Keamanan Akun</h3>
                            <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="p-6 sm:p-8">
                            <div class="max-w-xl">
                                @include('profile.partials.update-password-form')
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Bagian Kanan: Info & Danger Zone --}}
                <div class="lg:col-span-4 space-y-8">
                    {{-- Info Wilayah Wewenang --}}
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Wilayah Tugas</h4>
                        <div class="space-y-3">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center text-gray-500 text-xs">
                                    PR</div>
                                <div>
                                    <p class="text-[10px] text-gray-400 leading-none">Provinsi</p>
                                    <p class="text-sm font-bold text-gray-700">
                                        {{ auth()->user()->province->name ?? 'Seluruh Indonesia' }}</p>
                                </div>
                            </div>
                            @if (auth()->user()->city)
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center text-gray-500 text-xs">
                                        KT</div>
                                    <div>
                                        <p class="text-[10px] text-gray-400 leading-none">Kota/Kab</p>
                                        <p class="text-sm font-bold text-gray-700">{{ auth()->user()->city->name }}</p>
                                    </div>
                                </div>
                            @endif
                            @if (auth()->user()->dojo)
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center text-gray-500 text-xs">
                                        DJ</div>
                                    <div>
                                        <p class="text-[10px] text-gray-400 leading-none">Dojo</p>
                                        <p class="text-sm font-bold text-gray-700">{{ auth()->user()->dojo->name }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Danger Zone --}}
                    <div class="bg-red-50 rounded-2xl p-6 border border-red-100">
                        <h4 class="text-xs font-bold text-red-700 uppercase tracking-widest mb-2 text-center">Tutup Akun
                        </h4>
                        <p class="text-[10px] text-red-500/70 text-center mb-4 leading-tight italic">Tindakan ini
                            permanen. Seluruh data prestasi dan kepengurusan akan dihapus.</p>
                        <div class="flex justify-center">
                            @include('profile.partials.delete-user-form')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
