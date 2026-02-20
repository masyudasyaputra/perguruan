<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-y-4">
            <div>
                <h2 class="font-black text-3xl text-slate-900 leading-tight tracking-tight">
                    {{ __('Dojo') }} <span class="text-indigo-600">Mastery</span>
                </h2>
                <p class="text-xs font-medium text-slate-500 uppercase tracking-[0.2em] mt-1">
                    @if (auth()->user()->role === 'pengprov')
                        Wilayah {{ auth()->user()->province->name ?? '' }}
                    @elseif (auth()->user()->role === 'pengcab')
                        Cabang {{ auth()->user()->city->name ?? '' }}
                    @else
                        Pusat Kendali Data Dojo
                    @endif
                </p>
            </div>

            <a href="{{ route('admin.dojos.create') }}"
                class="group relative inline-flex items-center justify-center bg-indigo-600 hover:bg-indigo-700 text-white px-7 py-3 rounded-2xl text-sm font-bold shadow-[0_10px_20px_rgba(79,70,229,0.2)] transition-all duration-300 hover:-translate-y-1 active:scale-95 w-full md:w-auto">
                <svg class="w-5 h-5 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                </svg>
                Tambah Dojo Baru
            </a>
        </div>
    </x-slot>

    <div class="py-8 bg-[#f8fafc]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- 1. NOTIFIKASI SK (Premium Alert) --}}
            @if ($warningDojos->count() > 0)
                <div
                    class="mb-10 relative overflow-hidden bg-white border border-amber-100 rounded-3xl p-5 shadow-[0_4px_20px_rgba(251,191,36,0.08)]">
                    <div class="absolute top-0 right-0 p-3">
                        <span class="flex h-3 w-3">
                            <span
                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-amber-500"></span>
                        </span>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="bg-amber-100 p-3 rounded-2xl">
                            <svg class="h-6 w-6 text-amber-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-slate-800 uppercase tracking-tight">Perlu Perpanjangan SK
                            </h3>
                            <p class="text-xs text-slate-500 mt-0.5">Dojo: <span
                                    class="font-bold text-amber-700">{{ $warningDojos->pluck('name')->take(3)->implode(', ') }}...</span>
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- 2. FORM FILTER (Glassmorphism Look) --}}
            <div class="mb-10 bg-white/70 backdrop-blur-md p-6 rounded-[2.5rem] shadow-sm border border-white">
                <form action="{{ route('admin.dojos.index') }}" method="GET"
                    class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="space-y-2">
                        <label
                            class="text-[11px] font-bold text-slate-400 uppercase tracking-widest ml-1">Pencarian</label>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Cari nama dojo..."
                            class="w-full bg-slate-50 border-none rounded-2xl text-sm font-semibold focus:ring-2 focus:ring-indigo-500/20 transition-all placeholder:text-slate-300">
                    </div>

                    @if (auth()->user()->role === 'admin')
                        <div class="space-y-2">
                            <label
                                class="text-[11px] font-bold text-slate-400 uppercase tracking-widest ml-1">Provinsi</label>
                            <select name="province_id" onchange="this.form.submit()"
                                class="w-full bg-slate-50 border-none rounded-2xl text-sm font-semibold focus:ring-2 focus:ring-indigo-500/20 cursor-pointer">
                                <option value="">Semua Provinsi</option>
                                @foreach ($provinces as $prov)
                                    <option value="{{ $prov->id }}"
                                        {{ request('province_id') == $prov->id ? 'selected' : '' }}>{{ $prov->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="space-y-2">
                        <label class="text-[11px] font-bold text-slate-400 uppercase tracking-widest ml-1">Kategori
                            Wilayah</label>
                        <select name="city_id" onchange="this.form.submit()"
                            class="w-full bg-slate-50 border-none rounded-2xl text-sm font-semibold focus:ring-2 focus:ring-indigo-500/20 cursor-pointer">
                            <option value="">Semua Kota/Kab</option>
                            @foreach ($cities as $city)
                                <option value="{{ $city->id }}"
                                    {{ request('city_id') == $city->id ? 'selected' : '' }}>{{ $city->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-end gap-3">
                        <button type="submit"
                            class="flex-1 bg-slate-900 hover:bg-black text-white py-3 rounded-2xl text-xs font-bold uppercase tracking-widest transition-all shadow-lg shadow-slate-200">
                            Terapkan
                        </button>
                        <a href="{{ route('admin.dojos.index') }}"
                            class="p-3 bg-slate-100 hover:bg-slate-200 text-slate-500 rounded-2xl transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                        </a>
                    </div>
                </form>
            </div>

            {{-- 3. VIEW DESKTOP (Modern Table with Dividers) --}}
            <div
                class="hidden md:block bg-white rounded-[2.5rem] shadow-[0_20px_50px_rgba(0,0,0,0.02)] border border-slate-100 overflow-hidden">
                <table class="min-w-full">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-100">
                            <th
                                class="px-8 py-6 text-left text-[11px] font-bold text-slate-400 uppercase tracking-[0.2em]">
                                Info Dojo</th>
                            <th
                                class="px-8 py-6 text-left text-[11px] font-bold text-slate-400 uppercase tracking-[0.2em]">
                                Lokasi</th>
                            <th
                                class="px-8 py-6 text-left text-[11px] font-bold text-slate-400 uppercase tracking-[0.2em]">
                                Sensei</th>
                            <th
                                class="px-8 py-6 text-center text-[11px] font-bold text-slate-400 uppercase tracking-[0.2em]">
                                Kapasitas</th>
                            <th
                                class="px-8 py-6 text-center text-[11px] font-bold text-slate-400 uppercase tracking-[0.2em]">
                                Status SK</th>
                            <th
                                class="px-8 py-6 text-right text-[11px] font-bold text-slate-400 uppercase tracking-[0.2em]">
                                Opsi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        @forelse ($dojos as $dojo)
                            @php
                                $expiryDate = \Carbon\Carbon::parse($dojo->sk_expiry_date);
                            @endphp
                            {{-- Border Bottom sebagai pembatas baris --}}
                            <tr
                                class="hover:bg-slate-50/50 transition-all duration-200 border-b border-slate-50 last:border-none group">
                                <td class="px-8 py-6">
                                    <div
                                        class="font-bold text-slate-800 text-base group-hover:text-indigo-600 transition-colors uppercase tracking-tight">
                                        {{ $dojo->name }}
                                    </div>
                                    <div class="text-[10px] font-bold text-slate-400 mt-1 uppercase tracking-tighter">
                                        SK: {{ $dojo->sk_number ?? '—' }}</div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="font-semibold text-slate-700 text-sm">{{ $dojo->city->name ?? '-' }}
                                    </div>
                                    <div class="text-[10px] text-slate-400 uppercase font-bold mt-0.5 tracking-tighter">
                                        {{ $dojo->city->province->name ?? '-' }}</div>
                                </td>
                                <td class="px-8 py-6 text-sm font-black text-slate-600 uppercase italic">
                                    {{ $dojo->sensei_name ?? '-' }}
                                </td>
                                <td class="px-8 py-6 text-center">
                                    <div
                                        class="inline-flex flex-col items-center justify-center bg-slate-50/50 border border-slate-100 px-4 py-2 rounded-2xl min-w-[60px]">
                                        <span
                                            class="text-sm font-black text-slate-800 leading-none">{{ number_format($dojo->members_count ?? 0) }}</span>
                                        <span class="text-[8px] font-bold text-slate-400 uppercase mt-1">Orang</span>
                                    </div>
                                </td>
                                <td class="px-8 py-6 text-center">
                                    @include('admin.dojos._badge_status', ['official' => $dojo]) {{-- Mengirim $dojo sebagai $official agar kompatibel dengan partial --}}
                                    <div class="text-[9px] text-slate-400 mt-2 font-bold uppercase tracking-tighter">
                                        {{ $expiryDate->format('M Y') }}</div>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <div class="flex justify-end gap-2">
                                        @include('admin.dojos._action_buttons')
                                    </div>
                                </td>
                            </tr>
                        @empty
                            {{-- Kosong --}}
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- 4. VIEW MOBILE (Modern Cards with Spacing) --}}
            <div class="md:hidden space-y-6"> {{-- space-y-6 untuk jarak antar card --}}
                @foreach ($dojos as $dojo)
                    <div
                        class="bg-white rounded-[2rem] p-6 shadow-[0_10px_30px_rgba(0,0,0,0.04)] border border-slate-100 relative overflow-hidden">
                        <div class="flex justify-between items-start mb-6">
                            <h4 class="font-black text-lg text-slate-900 uppercase leading-none tracking-tight">
                                {{ $dojo->name }}</h4>
                            <div
                                class="bg-slate-900 text-white text-[10px] px-3 py-1 rounded-full font-black uppercase italic shadow-lg shadow-slate-200">
                                {{ number_format($dojo->members_count ?? 0) }}
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div class="p-4 bg-slate-50/50 rounded-2xl border border-slate-100">
                                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1">Coach</p>
                                <p class="text-xs font-black text-slate-700 uppercase leading-tight">
                                    {{ Str::limit($dojo->sensei_name, 12) }}</p>
                            </div>
                            <div class="p-4 bg-slate-50/50 rounded-2xl border border-slate-100">
                                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1">Wilayah
                                </p>
                                <p class="text-xs font-black text-slate-700 uppercase leading-tight">
                                    {{ Str::limit($dojo->city->name, 12) }}</p>
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-5 border-t border-slate-100">
                            @include('admin.dojos._badge_status', ['official' => $dojo])
                            <div class="flex gap-3">
                                @include('admin.dojos._action_buttons')
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-10">
                {{ $dojos->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
