<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h2 class="font-black text-2xl text-slate-800 leading-tight tracking-tight">
                    {{ $title }}
                </h2>
                <p class="text-sm text-slate-500 font-medium">Selamat datang kembali, {{ Auth::user()->name }}</p>
            </div>
            <div class="flex items-center gap-3 bg-white p-1.5 pr-4 rounded-2xl shadow-sm border border-slate-100">
                <span class="flex h-2 w-2 ml-2">
                    <span class="animate-ping absolute inline-flex h-2 w-2 rounded-full bg-indigo-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-indigo-500"></span>
                </span>
                <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">Akses:</span>
                <span class="text-xs font-bold text-indigo-700 uppercase">{{ str_replace('_', ' ', $role) }}</span>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-slate-50/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Infografis Statistik --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">

                {{-- Card 1: Total Anggota --}}
                <div
                    class="bg-white rounded-3xl p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100 transition-hover hover:shadow-lg duration-300">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs font-black text-slate-400 uppercase tracking-[0.1em] mb-1">Total Anggota
                            </p>
                            <h3 class="text-3xl font-black text-slate-800">{{ number_format($stats['total_members']) }}
                            </h3>
                        </div>
                        <div class="p-3 bg-blue-50 rounded-2xl text-blue-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center text-xs font-bold text-emerald-500">
                        <span class="bg-emerald-50 px-2 py-0.5 rounded-lg mr-2">Data Terkini</span>
                    </div>
                </div>

                {{-- Card 2: Anggota Aktif --}}
                <div
                    class="bg-white rounded-3xl p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100 transition-hover hover:shadow-lg duration-300">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs font-black text-slate-400 uppercase tracking-[0.1em] mb-1">Status Aktif
                            </p>
                            <h3 class="text-3xl font-black text-slate-800">{{ number_format($stats['active_members']) }}
                            </h3>
                        </div>
                        <div class="p-3 bg-emerald-50 rounded-2xl text-emerald-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 text-xs font-bold text-slate-400">
                        Rasio: <span
                            class="text-slate-700">{{ $stats['total_members'] > 0 ? round(($stats['active_members'] / $stats['total_members']) * 100) : 0 }}%
                            Aktif</span>
                    </div>
                </div>

                {{-- Card 3: Tagihan Pending --}}
                <div
                    class="bg-white rounded-3xl p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100 transition-hover hover:shadow-lg duration-300">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs font-black text-slate-400 uppercase tracking-[0.1em] mb-1">Perlu
                                Konfirmasi</p>
                            <h3 class="text-3xl font-black text-slate-800">
                                {{ number_format($stats['pending_payments']) }}</h3>
                        </div>
                        <div class="p-3 bg-amber-50 rounded-2xl text-amber-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 text-xs font-bold text-amber-600 uppercase tracking-tighter">
                        Menunggu Tindakan
                    </div>
                </div>

                {{-- Card 4: Total Kas --}}
                <div
                    class="bg-indigo-600 rounded-3xl p-6 shadow-xl shadow-indigo-100 border border-indigo-500 transition-hover hover:scale-[1.02] duration-300 text-white">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-[10px] font-black text-indigo-200 uppercase tracking-[0.2em] mb-1">Kas Iuran
                            </p>
                            <h3 class="text-2xl font-black">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}
                            </h3>
                        </div>
                        <div class="p-3 bg-white/10 rounded-2xl text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 text-[10px] font-bold text-indigo-200 uppercase">
                        Akumulasi Periode Ini
                    </div>
                </div>
            </div>

            {{-- Table Section --}}
            <div
                class="bg-white shadow-[0_8px_30px_rgb(0,0,0,0.04)] sm:rounded-[2rem] border border-slate-100 overflow-hidden">
                <div class="p-8">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
                        <div>
                            <h3 class="font-black text-xl text-slate-800 tracking-tight">Daftar Anggota</h3>
                            <p class="text-sm text-slate-500 font-medium mt-1">Mengelola database murid dan status sabuk
                            </p>
                        </div>

                        @if ($role === 'admin_dojo' || $role === 'pb')
                            <a href="#"
                                class="inline-flex items-center px-6 py-3 bg-indigo-600 rounded-2xl font-black text-xs text-white uppercase tracking-[0.1em] hover:bg-indigo-700 shadow-lg shadow-indigo-100 transition-all active:scale-95">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                                Tambah Anggota
                            </a>
                        @endif
                    </div>

                    <div class="overflow-x-auto rounded-2xl border border-slate-50">
                        <table class="w-full text-sm text-left">
                            <thead
                                class="text-[10px] text-slate-400 uppercase font-black tracking-[0.2em] bg-slate-50/50">
                                <tr>
                                    <th class="px-6 py-5">Identitas Murid</th>
                                    <th class="px-6 py-5">Tingkatan</th>
                                    @if ($role === 'pb')
                                        <th class="px-6 py-5">Wilayah</th>
                                    @endif
                                    @if ($role === 'pb' || $role === 'pengprov')
                                        <th class="px-6 py-5">Cabang</th>
                                    @endif
                                    @if ($role !== 'admin_dojo')
                                        <th class="px-6 py-5">Dojo</th>
                                    @endif
                                    <th class="px-6 py-5">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @forelse($members as $member)
                                    <tr class="hover:bg-slate-50/50 transition-colors">
                                        <td class="px-6 py-5">
                                            <div class="font-bold text-slate-700">{{ $member->name }}</div>
                                            <div
                                                class="text-[10px] text-slate-400 mt-0.5 font-bold uppercase tracking-tighter">
                                                ID: #{{ str_pad($member->id, 5, '0', STR_PAD_LEFT) }}</div>
                                        </td>
                                        <td class="px-6 py-5">
                                            <span
                                                class="inline-flex items-center px-3 py-1 rounded-xl text-[10px] font-black uppercase tracking-wider bg-slate-100 text-slate-600 border border-slate-200">
                                                {{ $member->beltLevel->name ?? '-' }}
                                            </span>
                                        </td>

                                        @if ($role === 'pb')
                                            <td class="px-6 py-5 font-semibold text-slate-600">
                                                {{ $member->province->name ?? '-' }}</td>
                                        @endif
                                        @if ($role === 'pb' || $role === 'pengprov')
                                            <td class="px-6 py-5 font-semibold text-slate-600">
                                                {{ $member->city->name ?? '-' }}</td>
                                        @endif
                                        @if ($role !== 'admin_dojo')
                                            <td class="px-6 py-5 font-semibold text-slate-600">
                                                {{ $member->dojo->name ?? '-' }}</td>
                                        @endif

                                        <td class="px-6 py-5">
                                            @if ($member->is_active)
                                                <span
                                                    class="inline-flex items-center px-3 py-1 bg-emerald-50 text-emerald-700 text-[10px] font-black uppercase tracking-widest rounded-full">
                                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 mr-2"></span>
                                                    Aktif
                                                </span>
                                            @else
                                                <span
                                                    class="inline-flex items-center px-3 py-1 bg-rose-50 text-rose-700 text-[10px] font-black uppercase tracking-widest rounded-full">
                                                    <span class="h-1.5 w-1.5 rounded-full bg-rose-500 mr-2"></span>
                                                    Off
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="100%" class="px-6 py-12 text-center">
                                            <div class="flex flex-col items-center">
                                                <div class="p-4 bg-slate-50 rounded-full mb-3 text-slate-300">
                                                    <svg class="w-8 h-8" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                                    </svg>
                                                </div>
                                                <p class="text-sm font-bold text-slate-400 uppercase tracking-widest">
                                                    Belum ada data anggota</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-8 border-t border-slate-50 pt-6">
                        {{ $members->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
