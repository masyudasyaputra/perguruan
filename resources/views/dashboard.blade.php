<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 px-4 sm:px-0">
            <div>
                <h2 class="font-black text-2xl text-white leading-tight tracking-tight uppercase">
                    {{ $title }}
                </h2>
                <p class="text-sm text-slate-400 font-medium italic">
                    Dashboard Overview • {{ now()->format('d M Y') }}
                </p>
            </div>

            {{-- Badge Role Akses --}}
            <div class="flex items-center gap-3 bg-slate-900 p-2 pr-5 rounded-2xl shadow-xl border border-slate-800">
                <div class="relative flex h-3 w-3 ml-2">
                    <span
                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-500 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-indigo-500"></span>
                </div>
                <div class="flex flex-col">
                    <span class="text-[9px] font-black uppercase tracking-[0.15em] text-slate-500 leading-none mb-1">
                        Mode Akses @if (count(array_merge([Auth::user()->role], Auth::user()->roles ?? [])) > 1)
                            <span class="text-indigo-400">(Multi)</span>
                        @endif
                    </span>
                    <span
                        class="text-xs font-bold text-indigo-300 uppercase tracking-wider">{{ str_replace('_', ' ', $role) }}</span>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-slate-950 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- NOTIFIKASI STATUS DOJO --}}
            @if ($role === 'admin_dojo')
                @if (!Auth::user()->dojo_id)
                    <div
                        class="mb-8 bg-rose-950/20 rounded-[2.5rem] p-8 text-white border border-rose-500/30 relative overflow-hidden group">
                        <div
                            class="absolute -right-10 -top-10 opacity-10 group-hover:scale-110 transition-transform duration-700">
                            <svg class="w-64 h-64 text-rose-500" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M12 2L1 21h22L12 2zm0 3.45l8.27 14.55H3.73L12 5.45zM11 10v4h2v-4h-2zm0 6v2h2v-2h-2z" />
                            </svg>
                        </div>
                        <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-6">
                            <div class="flex items-center gap-6">
                                <div class="p-5 bg-rose-500/20 rounded-2xl border border-rose-500/20 backdrop-blur-md">
                                    <svg class="w-10 h-10 text-rose-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-2xl font-black uppercase tracking-tight text-rose-100">Akun Belum
                                        Terhubung</h4>
                                    <p class="text-rose-200/60 font-medium">Anda belum memiliki otoritas pada Dojo
                                        manapun. Hubungi Admin Pusat untuk sinkronisasi data.</p>
                                </div>
                            </div>
                            <a href="#"
                                class="px-8 py-4 bg-rose-600 hover:bg-rose-500 text-white rounded-2xl font-black text-xs uppercase tracking-widest transition-all shadow-lg shadow-rose-900/40 shrink-0">Hubungi
                                Bantuan</a>
                        </div>
                    </div>
                @else
                    <div
                        class="mb-8 bg-slate-900 rounded-[2rem] p-6 border border-slate-800 shadow-2xl flex items-center justify-between overflow-hidden relative">
                        <div class="flex items-center gap-5 relative z-10">
                            <div
                                class="h-14 w-14 bg-indigo-500/10 rounded-2xl flex items-center justify-center text-indigo-400 border border-indigo-500/20 shadow-inner">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-1">
                                    Administrator Dojo</p>
                                <h4 class="text-xl font-black text-indigo-100 tracking-tight uppercase">
                                    {{ Auth::user()->dojo->name ?? 'Dojo Terdaftar' }}</h4>
                            </div>
                        </div>
                        <div class="hidden md:block relative z-10">
                            <span
                                class="text-[10px] font-black text-emerald-400 uppercase bg-emerald-500/10 px-5 py-2 rounded-full border border-emerald-500/20">Status:
                                Aktif Kelola</span>
                        </div>
                        <div
                            class="absolute right-0 top-0 bottom-0 w-48 bg-gradient-to-l from-indigo-500/10 to-transparent">
                        </div>
                    </div>
                @endif
            @endif

            {{-- INFOGRAFIS STATISTIK --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
                @php
                    $statCards = [
                        [
                            'label' => 'Total Anggota',
                            'value' => $stats['total_members'],
                            'icon' =>
                                'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
                            'color' => 'blue',
                            'footer' => 'Database Terdaftar',
                        ],
                        [
                            'label' => 'Status Aktif',
                            'value' => $stats['active_members'],
                            'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                            'color' => 'emerald',
                            'footer' => 'Anggota Terverifikasi',
                        ],
                        [
                            'label' => 'Konfirmasi',
                            'value' => $stats['pending_payments'],
                            'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                            'color' => 'amber',
                            'footer' => 'Menunggu Review',
                        ],
                    ];
                @endphp

                @foreach ($statCards as $card)
                    <div
                        class="bg-slate-900 rounded-[2rem] p-7 border border-slate-800 flex flex-col justify-between hover:border-slate-600 transition-all duration-300 group shadow-lg">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">
                                    {{ $card['label'] }}</p>
                                <h3 class="text-4xl font-black text-white tracking-tighter">
                                    {{ number_format($card['value']) }}</h3>
                            </div>
                            <div
                                class="p-4 bg-{{ $card['color'] }}-500/10 rounded-2xl text-{{ $card['color'] }}-400 border border-{{ $card['color'] }}-500/20 group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="{{ $card['icon'] }}" />
                                </svg>
                            </div>
                        </div>
                        <div
                            class="mt-6 flex items-center text-[10px] font-bold text-slate-500 uppercase tracking-widest">
                            <span class="flex h-2 w-2 rounded-full bg-{{ $card['color'] }}-500 mr-3"></span>
                            {{ $card['footer'] }}
                        </div>
                    </div>
                @endforeach

                {{-- Card Kas (Special Style) --}}
                <div
                    class="bg-indigo-600 rounded-[2rem] p-7 shadow-2xl shadow-indigo-900/40 border border-indigo-400/30 text-white flex flex-col justify-between group hover:bg-indigo-500 transition-all duration-300 relative overflow-hidden">
                    <div class="relative z-10 flex justify-between items-start">
                        <div>
                            <p class="text-[10px] font-black text-indigo-100 uppercase tracking-widest mb-2">Total Kas
                            </p>
                            <h3 class="text-3xl font-black tracking-tight truncate">Rp
                                {{ number_format($stats['total_revenue'], 0, ',', '.') }}</h3>
                        </div>
                        <div class="p-4 bg-white/10 rounded-2xl text-white border border-white/20">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div
                        class="mt-6 flex items-center text-[10px] font-bold text-indigo-100 uppercase tracking-widest relative z-10">
                        <span class="flex h-2 w-2 rounded-full bg-white animate-pulse mr-3"></span> Iuran Masuk
                        (Terbayar)
                    </div>
                    {{-- Decorative Circle --}}
                    <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-white/5 rounded-full"></div>
                </div>
            </div>

            {{-- MAIN TABLE SECTION --}}
            <div
                class="bg-slate-900 shadow-2xl sm:rounded-[3rem] border border-slate-800 overflow-hidden transition-all">
                <div class="p-6 md:p-10">
                    {{-- Table Header --}}
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-10">
                        <div>
                            <h3 class="font-black text-2xl text-white tracking-tight flex items-center uppercase">
                                Daftar Anggota
                                <span
                                    class="ml-4 px-4 py-1.5 bg-slate-950 text-indigo-400 text-[10px] font-black rounded-full border border-slate-800 shadow-inner">
                                    {{ $members->total() }} PERSONEL
                                </span>
                            </h3>
                            <p class="text-slate-500 font-medium mt-1">Manajemen database & lisensi sabuk nasional</p>
                        </div>

                        @if (in_array($role, ['admin_dojo', 'pb']))
                            @php $canAddMember = ($role === 'admin_dojo' && Auth::user()->dojo_id) || $role === 'pb'; @endphp
                            <a href="{{ $canAddMember ? route('admin.members.create') : '#' }}"
                                @if (!$canAddMember) onclick="alert('Pilih/Hubungkan Dojo terlebih dahulu.'); return false;" @endif
                                class="w-full md:w-auto inline-flex justify-center items-center px-8 py-4 {{ $canAddMember ? 'bg-indigo-600 hover:bg-indigo-500 shadow-indigo-900/20' : 'bg-slate-800 text-slate-600 cursor-not-allowed' }} rounded-2xl font-black text-xs text-white uppercase tracking-[0.15em] transition-all active:scale-95 shadow-xl border border-indigo-400/20">
                                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                                Tambah Murid
                            </a>
                        @endif
                    </div>

                    {{-- Section Filter (Glass Style) --}}
                    <div class="bg-slate-950/50 rounded-[2rem] p-6 mb-10 border border-slate-800/60 backdrop-blur-xl">
                        <form method="GET" action="{{ route('admin.dashboard') }}"
                            class="grid grid-cols-1 md:grid-cols-12 gap-5 items-end">
                            <div class="md:col-span-5">
                                <label
                                    class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1 mb-3 block">Cari
                                    Personel</label>
                                <div class="relative group">
                                    <input type="text" name="search" value="{{ request('search') }}"
                                        placeholder="Ketik Nama atau Nomor ID..."
                                        class="w-full h-12 border-slate-800 bg-slate-900/80 text-white rounded-xl text-xs font-bold focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 pl-12 transition-all placeholder:text-slate-700">
                                    <svg class="w-5 h-5 text-slate-600 absolute left-4 top-3.5 group-focus-within:text-indigo-500 transition-colors"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                            </div>

                            <div class="md:col-span-3">
                                <label
                                    class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1 mb-3 block">Status
                                    Keaktifan</label>
                                <select name="status"
                                    class="w-full h-12 border-slate-800 bg-slate-900/80 text-white rounded-xl text-xs font-bold focus:ring-2 focus:ring-indigo-500/50 transition-all">
                                    <option value="">Semua Status</option>
                                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>AKTIF
                                    </option>
                                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>NON-AKTIF
                                    </option>
                                </select>
                            </div>

                            <div class="md:col-span-4 flex gap-3">
                                <button type="submit"
                                    class="flex-1 h-12 bg-slate-100 text-slate-900 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-white transition-all shadow-lg active:scale-95">
                                    Terapkan Filter
                                </button>
                                <a href="{{ route('admin.dashboard') }}"
                                    class="w-12 h-12 bg-slate-800 border border-slate-700 text-slate-400 rounded-xl hover:text-rose-400 hover:border-rose-500/30 transition-all flex items-center justify-center shadow-lg group">
                                    <svg class="w-5 h-5 group-hover:rotate-180 transition-transform duration-500"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                </a>
                            </div>
                        </form>
                    </div>

                    {{-- TABLE VIEW (Desktop) --}}
                    <div
                        class="hidden md:block overflow-hidden rounded-3xl border border-slate-800/50 bg-slate-950/20">
                        <table class="w-full text-sm text-left border-collapse">
                            <thead>
                                <tr
                                    class="text-[10px] text-slate-500 uppercase font-black tracking-[0.2em] bg-slate-950/80 border-b border-slate-800">
                                    <th class="px-8 py-6">Identitas Murid</th>
                                    <th class="px-8 py-6">Tingkatan Sabuk</th>
                                    @if ($role !== 'admin_dojo')
                                        <th class="px-8 py-6">Dojo / Wilayah</th>
                                    @endif
                                    <th class="px-8 py-6 text-right">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800/50">
                                @forelse($members as $member)
                                    <tr class="hover:bg-indigo-500/[0.03] transition-colors group">
                                        <td class="px-8 py-6">
                                            <div class="flex items-center gap-4">
                                                <div
                                                    class="h-12 w-12 rounded-2xl bg-slate-800 border border-slate-700 flex items-center justify-center font-black text-lg text-slate-400 group-hover:border-indigo-500/50 group-hover:text-indigo-400 transition-all shadow-inner uppercase">
                                                    {{ substr($member->name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <div
                                                        class="font-bold text-slate-200 text-base leading-none mb-1.5 group-hover:text-white transition-colors">
                                                        {{ $member->name }}
                                                    </div>
                                                    <div
                                                        class="text-[10px] text-slate-600 font-black uppercase tracking-widest flex items-center">
                                                        <span class="text-indigo-500/50 mr-1.5">ID</span>
                                                        #{{ str_pad($member->id, 5, '0', STR_PAD_LEFT) }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-8 py-6">
                                            <div class="flex flex-col">
                                                <span
                                                    class="text-xs font-black text-slate-300 uppercase tracking-tight">{{ $member->beltLevel->name ?? 'N/A' }}</span>
                                                <span
                                                    class="text-[10px] text-indigo-400 font-bold uppercase tracking-tighter italic">{{ $member->beltLevel->kyu_dan ?? '-' }}</span>
                                            </div>
                                        </td>
                                        @if ($role !== 'admin_dojo')
                                            <td class="px-8 py-6">
                                                <div class="text-xs font-bold text-slate-400">
                                                    {{ $member->dojo->name ?? '-' }}</div>
                                                <div
                                                    class="text-[9px] text-slate-600 uppercase font-black tracking-tighter mt-1">
                                                    {{ $member->city->name ?? '' }}</div>
                                            </td>
                                        @endif
                                        <td class="px-8 py-6 text-right">
                                            <span
                                                class="inline-flex items-center px-4 py-2 {{ $member->is_active ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' : 'bg-rose-500/10 text-rose-400 border-rose-500/20' }} text-[10px] font-black uppercase rounded-xl border">
                                                <span
                                                    class="h-1.5 w-1.5 rounded-full {{ $member->is_active ? 'bg-emerald-400 animate-pulse' : 'bg-rose-500' }} mr-2.5"></span>
                                                {{ $member->is_active ? 'Aktif' : 'Non-Aktif' }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="100%" class="px-8 py-24 text-center">
                                            <div class="flex flex-col items-center">
                                                <div
                                                    class="p-5 bg-slate-900 rounded-full mb-4 border border-slate-800">
                                                    <svg class="w-10 h-10 text-slate-700" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="1.5"
                                                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                                    </svg>
                                                </div>
                                                <p class="font-black text-slate-700 uppercase tracking-[0.4em]">Data
                                                    Tidak Ditemukan</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- MOBILE VIEW --}}
                    <div class="md:hidden space-y-4">
                        @foreach ($members as $member)
                            <div class="bg-slate-950/60 rounded-[2rem] p-6 border border-slate-800 shadow-lg">
                                <div class="flex justify-between items-start mb-5">
                                    <div class="flex items-center gap-4">
                                        <div
                                            class="h-12 w-12 rounded-2xl bg-indigo-600 text-white flex items-center justify-center font-black text-xl shadow-lg uppercase">
                                            {{ substr($member->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <h4 class="font-black text-slate-100 text-base leading-none mb-1.5">
                                                {{ $member->name }}</h4>
                                            <p
                                                class="text-[10px] font-black text-slate-600 uppercase tracking-[0.2em]">
                                                #{{ str_pad($member->id, 5, '0', STR_PAD_LEFT) }}</p>
                                        </div>
                                    </div>
                                    <span
                                        class="h-2.5 w-2.5 rounded-full {{ $member->is_active ? 'bg-emerald-500 shadow-[0_0_10px_rgba(16,185,129,0.5)]' : 'bg-rose-500' }}"></span>
                                </div>
                                <div class="grid grid-cols-2 gap-4 pt-5 border-t border-slate-800/80">
                                    <div>
                                        <p
                                            class="text-[9px] font-black text-slate-600 uppercase tracking-widest mb-1.5">
                                            Tingkatan</p>
                                        <p class="text-xs font-bold text-indigo-300">
                                            {{ $member->beltLevel->name ?? '-' }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p
                                            class="text-[9px] font-black text-slate-600 uppercase tracking-widest mb-1.5">
                                            Unit/Dojo</p>
                                        <p class="text-xs font-bold text-slate-400 truncate">
                                            {{ $member->dojo->name ?? '-' }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-10 pt-8 border-t border-slate-800/50">
                        {{ $members->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
