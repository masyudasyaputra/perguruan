<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div>
                <h2 class="font-black text-xl sm:text-3xl text-slate-900 leading-tight tracking-tighter uppercase">
                    {{ __('Manajemen') }} <span class="text-red-600">Pengurus</span>
                </h2>
                <p class="text-[8px] sm:text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mt-0.5">
                    @if (auth()->user()->role === 'pengprov')
                        Wilayah {{ auth()->user()->province->name ?? '' }}
                    @else
                        Struktur Organisasi Pusat
                    @endif
                </p>
            </div>
        </div>
    </x-slot>

    {{-- Floating Action Button for Mobile --}}
    <div class="fixed bottom-6 right-6 z-50 md:hidden">
        <a href="{{ route('admin.officials.create') }}"
            class="flex items-center justify-center w-14 h-14 bg-red-600 text-white rounded-2xl shadow-[0_10px_25px_rgba(220,38,38,0.4)] active:scale-90 transition-transform border-b-4 border-red-800">
            <svg class="w-7 h-7 stroke-[3]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="square" stroke-linejoin="square" d="M12 4v16m8-8H4"></path>
            </svg>
        </a>
    </div>

    <div class="py-6 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Toolbar Desktop --}}
            <div class="hidden md:flex justify-between items-end mb-6">
                <div>
                    <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest">Daftar Inventaris Pengurus
                    </h3>
                    <p class="text-[10px] text-slate-400 font-bold uppercase mt-1">Total: {{ $officials->count() }}
                        Personel Terdaftar</p>
                </div>

                <a href="{{ route('admin.officials.create') }}"
                    class="inline-flex items-center bg-slate-900 hover:bg-red-600 text-white px-5 py-3 rounded-xl text-[10px] font-black transition-all duration-300 hover:-translate-y-1 shadow-lg shadow-slate-200 uppercase tracking-[0.15em] border-b-4 border-slate-700 hover:border-red-800">
                    <svg class="w-4 h-4 me-2 stroke-[3]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="square" stroke-linejoin="square" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Tambah Pengurus Baru
                </a>
            </div>

            {{-- 1. NOTIFIKASI SK --}}
            @php
                $warningOfficials = $officials->filter(function ($official) {
                    $expiry = \Carbon\Carbon::parse($official->sk_expiry_date)->startOfDay();
                    return \Carbon\Carbon::now()->startOfDay()->diffInDays($expiry, false) <= 30;
                });
            @endphp

            @if ($warningOfficials->count() > 0)
                <div
                    class="mb-6 relative overflow-hidden bg-white border-2 border-red-50 rounded-2xl p-4 sm:p-6 shadow-sm">
                    <div class="relative flex items-center gap-4 sm:gap-5">
                        <div class="bg-red-600 p-2.5 rounded-xl flex-shrink-0 shadow-lg shadow-red-200">
                            <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="square" stroke-linejoin="square" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xs sm:text-sm font-black text-slate-900 uppercase tracking-tight">Status SK:
                                Perlu Atensi</h3>
                            <p class="text-[10px] sm:text-xs text-slate-500 mt-0.5 font-bold">
                                <span class="text-red-600 underline decoration-2">{{ $warningOfficials->count() }}
                                    pengurus</span> akan segera berakhir masa baktinya.
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- 2. VIEW DESKTOP --}}
            <div
                class="hidden md:block bg-white rounded-[1.5rem] border-2 border-slate-100 shadow-xl shadow-slate-200/50 overflow-hidden">
                <table class="min-w-full">
                    <thead>
                        <tr class="bg-slate-900 text-white">
                            <th class="px-8 py-5 text-left text-[10px] font-black uppercase tracking-[0.2em]">Data
                                Profil</th>
                            <th class="px-8 py-5 text-left text-[10px] font-black uppercase tracking-[0.2em]">Wilayah
                                Kerja</th>
                            <th class="px-8 py-5 text-center text-[10px] font-black uppercase tracking-[0.2em]">Unit
                                Dojo</th>
                            <th class="px-8 py-5 text-center text-[10px] font-black uppercase tracking-[0.2em]">Masa
                                Aktif</th>
                            <th class="px-8 py-5 text-right text-[10px] font-black uppercase tracking-[0.2em]">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($officials as $official)
                            <tr class="hover:bg-slate-50 transition-all group">
                                <td class="px-8 py-6">
                                    <div class="flex flex-col">
                                        <span
                                            class="font-black text-slate-900 text-sm uppercase group-hover:text-red-600 transition-colors">{{ $official->name }}</span>
                                        <div class="inline-flex mt-1">
                                            <span
                                                class="text-[9px] font-black bg-red-50 text-red-600 px-2 py-0.5 rounded uppercase tracking-widest border border-red-100">{{ $official->position }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex flex-col">
                                        <span
                                            class="text-xs font-bold text-slate-700 italic">{{ $official->level === 'pengcab' ? $official->city->name ?? '-' : $official->province->name ?? '-' }}</span>
                                        <span
                                            class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter">Level:
                                            {{ $official->level }}</span>
                                    </div>
                                </td>
                                <td class="px-8 py-6 text-center">
                                    <div class="inline-block px-4 py-2 bg-slate-50 rounded-lg border border-slate-100">
                                        <span
                                            class="text-sm font-black text-slate-900 block leading-none">{{ $official->level === 'pengcab' && $official->city ? $official->city->dojos->count() : 0 }}</span>
                                        <span class="text-[8px] font-black text-slate-400 uppercase">Dojo</span>
                                    </div>
                                </td>
                                <td class="px-8 py-6 text-center">
                                    <div class="flex flex-col items-center gap-1">
                                        @include('admin.officials._badge_status')
                                        <span
                                            class="text-[9px] font-black text-slate-400 uppercase">{{ \Carbon\Carbon::parse($official->sk_expiry_date)->format('d/m/Y') }}</span>
                                    </div>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <div class="flex justify-end gap-2">
                                        @include('admin.officials._action_buttons')
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5"
                                    class="py-20 text-center italic text-slate-400 font-bold uppercase tracking-widest text-sm">
                                    Belum ada data pengurus</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- 3. VIEW MOBILE (RE-DESIGNED) --}}
            <div class="md:hidden space-y-5">
                @foreach ($officials as $official)
                    <div
                        class="relative bg-white rounded-[2rem] border border-slate-200 shadow-sm overflow-hidden active:scale-[0.98] transition-transform">
                        {{-- Top Section --}}
                        <div class="p-5 pb-4">
                            <div class="flex justify-between items-start gap-4 mb-3">
                                <div class="flex-1">
                                    <h4
                                        class="font-black text-slate-900 uppercase text-base leading-tight tracking-tight">
                                        {{ $official->name }}
                                    </h4>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-600"></span>
                                        <span
                                            class="text-[9px] font-black text-slate-400 uppercase tracking-widest italic">
                                            {{ $official->position }}
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-shrink-0 flex flex-col items-end">
                                    @include('admin.officials._badge_status')
                                    <span
                                        class="text-[8px] font-black text-slate-400 mt-1 uppercase tracking-tighter italic">
                                        s/d {{ \Carbon\Carbon::parse($official->sk_expiry_date)->format('d/m/y') }}
                                    </span>
                                </div>
                            </div>

                            {{-- Info Grid --}}
                            <div class="grid grid-cols-2 gap-4">
                                <div
                                    class="bg-slate-50 p-3 rounded-2xl border border-slate-100 flex items-center gap-3">
                                    <div class="p-2 bg-white rounded-lg text-slate-400 shadow-sm">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </div>
                                    <div class="min-w-0">
                                        <p
                                            class="text-[7px] font-black text-slate-400 uppercase tracking-widest mb-0.5">
                                            Wilayah</p>
                                        <p
                                            class="text-[10px] font-black text-slate-700 truncate uppercase italic leading-tight">
                                            {{ $official->level === 'pengcab' ? $official->city->name ?? '-' : $official->province->name ?? '-' }}
                                        </p>
                                    </div>
                                </div>
                                <div
                                    class="bg-slate-50 p-3 rounded-2xl border border-slate-100 flex items-center gap-3">
                                    <div class="p-2 bg-white rounded-lg text-red-600 shadow-sm">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                    </div>
                                    <div class="min-w-0">
                                        <p
                                            class="text-[7px] font-black text-slate-400 uppercase tracking-widest mb-0.5">
                                            Cakupan</p>
                                        <p class="text-[10px] font-black text-slate-900 uppercase tracking-tighter">
                                            {{ $official->level === 'pengcab' && $official->city ? $official->city->dojos->count() : 0 }}
                                            Dojo
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Footer Action Area --}}
                        <div class="px-5 py-3 bg-slate-900 flex justify-between items-center">
                            <div class="flex items-center gap-2">
                                <div class="w-1 h-4 bg-red-600 rounded-full"></div>
                                <div>
                                    <p
                                        class="text-[7px] font-black text-slate-500 uppercase tracking-widest leading-none">
                                        Level Jabatan</p>
                                    <p class="text-[9px] font-bold text-white uppercase mt-0.5">{{ $official->level }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                @include('admin.officials._action_buttons')
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="h-20 md:hidden"></div>
        </div>
    </div>
</x-app-layout>
