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
                <div class="bg-white rounded-3xl p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100 transition-hover hover:shadow-lg duration-300">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs font-black text-slate-400 uppercase tracking-[0.1em] mb-1">Total Anggota</p>
                            <h3 class="text-3xl font-black text-slate-800">{{ number_format($stats['total_members']) }}</h3>
                        </div>
                        <div class="p-3 bg-blue-50 rounded-2xl text-blue-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center text-xs font-bold text-emerald-500">
                        <span class="bg-emerald-50 px-2 py-0.5 rounded-lg mr-2">Data Terkini</span>
                    </div>
                </div>

                {{-- Card 2: Anggota Aktif --}}
                <div class="bg-white rounded-3xl p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100 transition-hover hover:shadow-lg duration-300">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs font-black text-slate-400 uppercase tracking-[0.1em] mb-1">Status Aktif</p>
                            <h3 class="text-3xl font-black text-slate-800">{{ number_format($stats['active_members']) }}</h3>
                        </div>
                        <div class="p-3 bg-emerald-50 rounded-2xl text-emerald-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 text-xs font-bold text-slate-400">
                        Rasio: <span class="text-slate-700">{{ $stats['total_members'] > 0 ? round(($stats['active_members'] / $stats['total_members']) * 100) : 0 }}% Aktif</span>
                    </div>
                </div>

                {{-- Card 3: Tagihan Pending --}}
                <div class="bg-white rounded-3xl p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100 transition-hover hover:shadow-lg duration-300">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs font-black text-slate-400 uppercase tracking-[0.1em] mb-1">Perlu Konfirmasi</p>
                            <h3 class="text-3xl font-black text-slate-800">{{ number_format($stats['pending_payments']) }}</h3>
                        </div>
                        <div class="p-3 bg-amber-50 rounded-2xl text-amber-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 text-xs font-bold text-amber-600 uppercase tracking-tighter">Menunggu Tindakan</div>
                </div>

                {{-- Card 4: Total Kas --}}
                <div class="bg-indigo-600 rounded-3xl p-6 shadow-xl shadow-indigo-100 border border-indigo-500 transition-hover hover:scale-[1.02] duration-300 text-white">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-[10px] font-black text-indigo-200 uppercase tracking-[0.2em] mb-1">Kas Iuran</p>
                            <h3 class="text-2xl font-black">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</h3>
                        </div>
                        <div class="p-3 bg-white/10 rounded-2xl text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 text-[10px] font-bold text-indigo-200 uppercase">Akumulasi Periode Ini</div>
                </div>
            </div>

            {{-- Table Section --}}
            <div class="bg-white shadow-[0_8px_30px_rgb(0,0,0,0.04)] sm:rounded-[2rem] border border-slate-100 overflow-hidden">
                <div class="p-8">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
                        <div>
                            <h3 class="font-black text-xl text-slate-800 tracking-tight">Daftar Anggota</h3>
                            <p class="text-sm text-slate-500 font-medium mt-1">Mengelola database murid dan status sabuk</p>
                        </div>

                        @if ($role === 'admin_dojo' || $role === 'pb')
                            <a href="{{ route('admin.members.create') }}"
                                class="inline-flex items-center px-6 py-3 bg-indigo-600 rounded-2xl font-black text-xs text-white uppercase tracking-[0.1em] hover:bg-indigo-700 shadow-lg shadow-indigo-100 transition-all active:scale-95">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" />
                                </svg>
                                Tambah Anggota
                            </a>
                        @endif
                    </div>

                    <div class="overflow-x-auto rounded-2xl border border-slate-50">
                        <table class="w-full text-sm text-left">
                            <thead class="text-[10px] text-slate-400 uppercase font-black tracking-[0.2em] bg-slate-50/50">
                                <tr>
                                    <th class="px-6 py-5">Identitas Murid</th>
                                    <th class="px-6 py-5">Tingkatan</th>
                                    @if ($role === 'pb') <th class="px-6 py-5">Wilayah</th> @endif
                                    @if ($role === 'pb' || $role === 'pengprov') <th class="px-6 py-5">Cabang</th> @endif
                                    @if ($role !== 'admin_dojo') <th class="px-6 py-5">Dojo</th> @endif
                                    <th class="px-6 py-5">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @forelse($members as $member)
                                    <tr class="hover:bg-slate-50/50 transition-colors">
                                        {{-- IDENTITAS LENGKAP --}}
                                        <td class="px-6 py-5">
                                            <div class="flex flex-col">
                                                <div class="font-black text-slate-800 text-sm uppercase tracking-tight">{{ $member->name }}</div>
                                                <div class="flex items-center gap-2 mt-1">
                                                    <span class="text-[10px] text-indigo-600 font-bold bg-indigo-50 px-1.5 py-0.5 rounded">#{{ str_pad($member->id, 5, '0', STR_PAD_LEFT) }}</span>
                                                    <span class="text-[10px] text-slate-400 font-bold uppercase tracking-tighter italic">Ortu: {{ $member->parent_name ?? '-' }}</span>
                                                </div>
                                                <div class="mt-2 flex flex-col gap-1 text-[10px]">
                                                    <div class="flex items-center text-slate-500 font-bold tracking-wider">
                                                        <svg class="w-3 h-3 mr-1.5 text-emerald-500" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/></svg>
                                                        {{ $member->whatsapp }}
                                                    </div>
                                                    @if($member->email)
                                                    <div class="flex items-center text-slate-400">
                                                        <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                                        {{ $member->email }}
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>

                                        {{-- TINGKATAN (SABUK) --}}
                                        <td class="px-6 py-5">
                                            <div class="flex flex-col items-start gap-1">
                                                <span class="inline-flex items-center px-3 py-1 rounded-xl text-[10px] font-black uppercase tracking-wider bg-slate-900 text-white shadow-sm">
                                                    {{ $member->beltLevel->name ?? '-' }}
                                                </span>
                                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest ml-1">
                                                    {{ $member->beltLevel->kyu_dan ?? '' }}
                                                </span>
                                            </div>
                                        </td>

                                        {{-- DINAMIS WILAYAH --}}
                                        @if ($role === 'pb')
                                            <td class="px-6 py-5 font-bold text-[11px] text-slate-600 uppercase italic">{{ $member->province->name ?? '-' }}</td>
                                        @endif
                                        @if ($role === 'pb' || $role === 'pengprov')
                                            <td class="px-6 py-5 font-bold text-[11px] text-slate-600 uppercase italic">{{ $member->city->name ?? '-' }}</td>
                                        @endif
                                        @if ($role !== 'admin_dojo')
                                            <td class="px-6 py-5">
                                                <div class="text-[11px] font-black text-indigo-600 uppercase tracking-tight bg-indigo-50 px-2 py-1 rounded-lg inline-block">
                                                    {{ $member->dojo->name ?? '-' }}
                                                </div>
                                            </td>
                                        @endif

                                        {{-- STATUS --}}
                                        <td class="px-6 py-5">
                                            @if ($member->is_active)
                                                <span class="inline-flex items-center px-3 py-1 bg-emerald-50 text-emerald-700 text-[10px] font-black uppercase tracking-widest rounded-full border border-emerald-100">
                                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 mr-2 animate-pulse"></span>
                                                    Aktif
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-3 py-1 bg-rose-50 text-rose-700 text-[10px] font-black uppercase tracking-widest rounded-full border border-rose-100">
                                                    <span class="h-1.5 w-1.5 rounded-full bg-rose-500 mr-2"></span>
                                                    Pending
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="100%" class="px-6 py-20 text-center">
                                            <div class="flex flex-col items-center text-slate-300">
                                                <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                                </svg>
                                                <p class="text-sm font-black uppercase tracking-widest">Database Kosong</p>
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