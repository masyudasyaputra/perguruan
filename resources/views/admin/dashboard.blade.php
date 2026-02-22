<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 px-4 sm:px-0">
            <div>
                <h2 class="font-black text-2xl text-slate-800 leading-tight tracking-tight">
                    {{ $title }}
                </h2>
                <p class="text-sm text-slate-500 font-medium italic">Dashboard Overview • {{ now()->format('d M Y') }}</p>
            </div>
            <div class="flex items-center gap-3 bg-white p-2 pr-4 rounded-2xl shadow-sm border border-slate-100">
                <div class="relative flex h-3 w-3 ml-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-indigo-600"></span>
                </div>
                <div class="flex flex-col">
                    <span class="text-[9px] font-black uppercase tracking-[0.15em] text-slate-400 line-height-1">Role Akses</span>
                    <span class="text-xs font-bold text-indigo-700 uppercase">{{ str_replace('_', ' ', $role) }}</span>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-6 md:py-12 bg-slate-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Infografis Statistik - FULLY RESPONSIVE --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-10">
    
    {{-- Card: Total Anggota --}}
    <div class="bg-white rounded-[2rem] p-6 shadow-sm border border-slate-100 flex flex-col justify-between">
        <div class="flex justify-between items-start">
            <div class="p-3 bg-blue-50 rounded-2xl text-blue-600 order-2 md:order-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
            </div>
            <div class="order-1">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Anggota</p>
                <h3 class="text-3xl font-black text-slate-800 tracking-tight">{{ number_format($stats['total_members']) }}</h3>
            </div>
        </div>
        <div class="mt-4 flex items-center text-[10px] font-bold text-slate-400 uppercase tracking-tighter">
            <span class="text-blue-500 mr-1">●</span> Database Terdaftar
        </div>
    </div>

    {{-- Card: Status Aktif --}}
    <div class="bg-white rounded-[2rem] p-6 shadow-sm border border-slate-100 flex flex-col justify-between">
        <div class="flex justify-between items-start">
            <div class="p-3 bg-emerald-50 rounded-2xl text-emerald-600 order-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            <div class="order-1">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Status Aktif</p>
                <h3 class="text-3xl font-black text-slate-800 tracking-tight">{{ number_format($stats['active_members']) }}</h3>
            </div>
        </div>
        <div class="mt-4 flex items-center text-[10px] font-bold text-slate-400 uppercase tracking-tighter">
            <span class="text-emerald-500 mr-1">●</span> Anggota Terverifikasi
        </div>
    </div>

    {{-- Card: Perlu Konfirmasi --}}
    <div class="bg-white rounded-[2rem] p-6 shadow-sm border border-slate-100 flex flex-col justify-between">
        <div class="flex justify-between items-start">
            <div class="p-3 bg-amber-50 rounded-2xl text-amber-600 order-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            <div class="order-1">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Konfirmasi</p>
                <h3 class="text-3xl font-black text-slate-800 tracking-tight">{{ number_format($stats['pending_payments']) }}</h3>
            </div>
        </div>
        <div class="mt-4 flex items-center text-[10px] font-bold text-slate-400 uppercase tracking-tighter">
            <span class="text-amber-500 mr-1">●</span> Menunggu Review
        </div>
    </div>

    {{-- Card: Kas Iuran (Highlight) --}}
    <div class="bg-indigo-600 rounded-[2rem] p-6 shadow-lg shadow-indigo-100 border border-indigo-500 text-white flex flex-col justify-between">
        <div class="flex justify-between items-start">
            <div class="p-3 bg-white/10 rounded-2xl text-indigo-100 order-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            <div class="order-1">
                <p class="text-[10px] font-black text-indigo-200 uppercase tracking-widest mb-1">Total Kas</p>
                <h3 class="text-2xl font-black truncate">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</h3>
            </div>
        </div>
        <div class="mt-4 flex items-center text-[10px] font-bold text-indigo-200 uppercase tracking-tighter">
            Iuran Masuk (Paid)
        </div>
    </div>

</div>

            {{-- Main Table Section --}}
            <div class="bg-white shadow-xl shadow-slate-200/50 sm:rounded-[2.5rem] border border-slate-100 overflow-hidden">
                <div class="p-4 md:p-8">
                    
                    {{-- Table Header --}}
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
                        <div>
                            <h3 class="font-black text-xl text-slate-800 tracking-tight flex items-center">
                                Daftar Anggota
                                <span class="ml-3 px-2.5 py-0.5 bg-slate-100 text-slate-500 text-[10px] font-bold rounded-full">{{ $members->total() }} Murid</span>
                            </h3>
                            <p class="text-sm text-slate-500 font-medium mt-1">Manajemen database & lisensi sabuk</p>
                        </div>
                        @if (in_array($role, ['admin_dojo', 'pb']))
                            <a href="{{ route('admin.members.create') }}" class="w-full md:w-auto inline-flex justify-center items-center px-6 py-3.5 bg-indigo-600 rounded-2xl font-black text-xs text-white uppercase tracking-widest hover:bg-indigo-700 shadow-lg shadow-indigo-200 transition-all active:scale-95">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" /></svg>
                                Tambah Murid
                            </a>
                        @endif
                    </div>

                    {{-- Section Filter - Responsive Grid --}}
                    <div class="bg-slate-50/80 rounded-3xl p-4 md:p-6 mb-8 border border-slate-100">
                        <form method="GET" action="{{ route('admin.dashboard') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-10 gap-4 items-end">
                            
                            <div class="lg:col-span-3">
                                <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-2 block">Cari Nama / ID</label>
                                <div class="relative">
                                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Contoh: Budi Santoso" class="w-full border-slate-200 bg-white rounded-xl text-xs font-bold focus:ring-indigo-500 focus:border-indigo-500 pl-10 transition-all">
                                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                </div>
                            </div>

                            @if($role === 'pb')
                            <div class="lg:col-span-2">
                                <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-2 block">Provinsi</label>
                                <select name="province_id" class="w-full border-slate-200 bg-white rounded-xl text-xs font-bold focus:ring-indigo-500">
                                    <option value="">Semua Wilayah</option>
                                    @foreach($provinces as $prov)
                                        <option value="{{ $prov->id }}" {{ request('province_id') == $prov->id ? 'selected' : '' }}>{{ $prov->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif

                            @if(in_array($role, ['pb', 'pengprov', 'admin_pengprov']))
                            <div class="lg:col-span-2">
                                <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-2 block">Pengcab</label>
                                <select name="city_id" class="w-full border-slate-200 bg-white rounded-xl text-xs font-bold focus:ring-indigo-500">
                                    <option value="">Semua Kota</option>
                                    @foreach($cities as $city)
                                        <option value="{{ $city->id }}" {{ request('city_id') == $city->id ? 'selected' : '' }}>{{ $city->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif

                            @if($role !== 'admin_dojo')
                            <div class="lg:col-span-2">
                                <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-2 block">Dojo</label>
                                <select name="dojo_id" class="w-full border-slate-200 bg-white rounded-xl text-xs font-bold focus:ring-indigo-500">
                                    <option value="">Semua Dojo</option>
                                    @foreach($dojos as $dojo)
                                        <option value="{{ $dojo->id }}" {{ request('dojo_id') == $dojo->id ? 'selected' : '' }}>{{ $dojo->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif

                            <div class="lg:col-span-1">
                                <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-2 block">Status</label>
                                <select name="status" class="w-full border-slate-200 bg-white rounded-xl text-xs font-bold focus:ring-indigo-500">
                                    <option value="">Semua</option>
                                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Aktif</option>
                                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Non-Aktif</option>
                                </select>
                            </div>

                            <div class="lg:col-span-2 flex gap-2">
                                <button type="submit" class="flex-1 bg-slate-800 text-white px-4 py-2.5 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-indigo-600 transition-all shadow-sm">
                                    Filter
                                </button>
                                <a href="{{ route('admin.dashboard') }}" class="bg-white border border-slate-200 text-slate-400 px-3 py-2.5 rounded-xl hover:text-red-500 transition-all flex items-center justify-center shadow-sm" title="Reset">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                                </a>
                            </div>
                        </form>
                    </div>

                    {{-- TABLE VIEW (Desktop) --}}
                    <div class="hidden md:block overflow-x-auto rounded-2xl border border-slate-100">
                        <table class="w-full text-sm text-left">
                            <thead class="text-[10px] text-slate-400 uppercase font-black tracking-[0.2em] bg-slate-50/50">
                                <tr>
                                    <th class="px-6 py-5">Identitas Murid</th>
                                    <th class="px-6 py-5">Tingkatan Sabuk</th>
                                    @if (in_array($role, ['pb', 'pengprov', 'admin_pengprov']))
                                        <th class="px-6 py-5 text-center">Wilayah</th>
                                    @endif
                                    @if ($role !== 'admin_dojo')
                                        <th class="px-6 py-5">Dojo</th>
                                    @endif
                                    <th class="px-6 py-5 text-right">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @forelse($members as $member)
                                    <tr class="hover:bg-indigo-50/30 transition-colors group">
                                        <td class="px-6 py-5">
                                            <div class="flex items-center gap-3">
                                                <div class="h-10 w-10 rounded-full bg-slate-100 flex items-center justify-center font-black text-slate-400 text-xs group-hover:bg-indigo-100 group-hover:text-indigo-600 transition-colors">
                                                    {{ substr($member->name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <div class="font-bold text-slate-700 leading-none mb-1">{{ $member->name }}</div>
                                                    <div class="text-[10px] text-slate-400 font-bold uppercase tracking-tighter">ID: #{{ str_pad($member->id, 5, '0', STR_PAD_LEFT) }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-5">
                                            <div class="flex flex-col">
                                                <span class="text-xs font-black text-slate-700 uppercase tracking-tight">{{ $member->beltLevel->name ?? 'N/A' }}</span>
                                                <span class="text-[10px] text-indigo-500 font-bold uppercase">{{ $member->beltLevel->kyu_dan ?? '-' }}</span>
                                            </div>
                                        </td>
                                        @if (in_array($role, ['pb', 'pengprov', 'admin_pengprov']))
                                            <td class="px-6 py-5 text-center">
                                                <div class="text-xs font-bold text-slate-600">{{ $member->city->name ?? '-' }}</div>
                                                <div class="text-[9px] text-slate-400 uppercase font-bold">{{ $member->province->name ?? '' }}</div>
                                            </td>
                                        @endif
                                        @if ($role !== 'admin_dojo')
                                            <td class="px-6 py-5">
                                                <span class="px-3 py-1 bg-slate-100 rounded-lg text-[10px] font-bold text-slate-600 uppercase">{{ $member->dojo->name ?? '-' }}</span>
                                            </td>
                                        @endif
                                        <td class="px-6 py-5 text-right">
                                            <span class="inline-flex items-center px-3 py-1.5 {{ $member->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-600' }} text-[10px] font-black uppercase rounded-xl">
                                                <span class="h-1.5 w-1.5 rounded-full {{ $member->is_active ? 'bg-emerald-500' : 'bg-red-500' }} mr-2"></span>
                                                {{ $member->is_active ? 'Aktif' : 'Non-Aktif' }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="100%" class="px-6 py-20 text-center font-bold text-slate-300 uppercase tracking-[0.3em]">Data Kosong</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- MOBILE LIST VIEW (Hanya muncul di HP) --}}
                    <div class="md:hidden space-y-4">
                        @forelse($members as $member)
                            <div class="bg-slate-50 rounded-2xl p-5 border border-slate-100">
                                <div class="flex justify-between items-start mb-4">
                                    <div class="flex items-center gap-3">
                                        <div class="h-10 w-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-black">
                                            {{ substr($member->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <h4 class="font-black text-slate-800">{{ $member->name }}</h4>
                                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">ID: #{{ str_pad($member->id, 5, '0', STR_PAD_LEFT) }}</p>
                                        </div>
                                    </div>
                                    <span class="px-2 py-1 {{ $member->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }} text-[9px] font-black uppercase rounded-lg">
                                        {{ $member->is_active ? 'Aktif' : 'Non-Aktif' }}
                                    </span>
                                </div>
                                <div class="grid grid-cols-2 gap-4 pt-4 border-t border-slate-200/50">
                                    <div>
                                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Sabuk</p>
                                        <p class="text-xs font-bold text-slate-700 italic">{{ $member->beltLevel->name ?? '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Dojo</p>
                                        <p class="text-xs font-bold text-slate-700">{{ $member->dojo->name ?? '-' }}</p>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="py-10 text-center text-slate-400 font-bold uppercase text-xs">Data Tidak Ditemukan</div>
                        @endforelse
                    </div>

                    <div class="mt-8 pt-6 border-t border-slate-100">
                        {{ $members->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Style Tambahan untuk No-Scrollbar --}}
    <style>
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</x-app-layout>