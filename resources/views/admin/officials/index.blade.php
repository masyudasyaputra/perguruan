<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-y-4">
            <div>
                <h2 class="font-black text-3xl text-slate-900 leading-tight tracking-tight">
                    {{ __('Manajemen') }} <span class="text-indigo-600">Pengurus</span>
                </h2>
                <p class="text-xs font-medium text-slate-500 uppercase tracking-[0.2em] mt-1">
                    @if (auth()->user()->role === 'pengprov')
                        Wilayah {{ auth()->user()->province->name ?? '' }}
                    @else
                        Kendali Struktur Organisasi
                    @endif
                </p>
            </div>

            <a href="{{ route('admin.officials.create') }}"
                class="group relative inline-flex items-center justify-center bg-indigo-600 hover:bg-indigo-700 text-white px-7 py-3 rounded-2xl text-sm font-bold shadow-[0_10px_20px_rgba(79,70,229,0.2)] transition-all duration-300 hover:-translate-y-1 active:scale-95 w-full md:w-auto">
                <svg class="w-5 h-5 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                </svg>
                Tambah Pengurus
            </a>
        </div>
    </x-slot>

    <div class="py-8 bg-[#f8fafc]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- 1. NOTIFIKASI SK (Premium Alert) --}}
            @php
                $warningOfficials = $officials->filter(function ($official) {
                    $expiry = \Carbon\Carbon::parse($official->sk_expiry_date)->startOfDay();
                    return \Carbon\Carbon::now()->startOfDay()->diffInDays($expiry, false) <= 30;
                });
            @endphp

            @if ($warningOfficials->count() > 0)
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
                            <h3 class="text-sm font-black text-slate-800 uppercase tracking-tight">Perhatian: Perlu
                                Perpanjangan SK</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Ada <span
                                    class="font-bold text-amber-700">{{ $warningOfficials->count() }} pengurus</span>
                                yang masa aktif SK-nya akan segera berakhir.</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- 2. VIEW DESKTOP (Modern Table) --}}
            <div
                class="hidden md:block bg-white rounded-[2.5rem] shadow-[0_20px_50px_rgba(0,0,0,0.02)] border border-slate-100 overflow-hidden">
                <table class="min-w-full">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-100">
                            <th
                                class="px-8 py-6 text-left text-[11px] font-bold text-slate-400 uppercase tracking-[0.2em]">
                                Data Pengurus</th>
                            <th
                                class="px-8 py-6 text-left text-[11px] font-bold text-slate-400 uppercase tracking-[0.2em]">
                                Wilayah Kerja</th>
                            <th
                                class="px-8 py-6 text-center text-[11px] font-bold text-slate-400 uppercase tracking-[0.2em]">
                                Cakupan Dojo</th>
                            <th
                                class="px-8 py-6 text-center text-[11px] font-bold text-slate-400 uppercase tracking-[0.2em]">
                                Status SK</th>
                            <th
                                class="px-8 py-6 text-right text-[11px] font-bold text-slate-400 uppercase tracking-[0.2em]">
                                Opsi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        @forelse ($officials as $official)
                            {{-- Tambahkan border-b border-slate-50 untuk pembatas antar baris --}}
                            <tr
                                class="hover:bg-slate-50/50 transition-all duration-200 border-b border-slate-50 last:border-none">
                                <td class="px-8 py-6">
                                    <div class="font-bold text-slate-800 text-base uppercase tracking-tight">
                                        {{ $official->name }}
                                    </div>
                                    <div
                                        class="inline-flex items-center text-[9px] font-black text-indigo-500 bg-indigo-50/50 px-2 py-0.5 rounded-lg mt-1 uppercase tracking-wider border border-indigo-100">
                                        {{ $official->position }}
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="font-semibold text-slate-700 text-sm italic">
                                        {{ $official->level === 'pengcab' ? $official->city->name ?? '-' : $official->province->name ?? '-' }}
                                    </div>
                                    <div class="text-[10px] text-slate-400 uppercase font-bold tracking-tighter mt-0.5">
                                        Level: {{ $official->level }}
                                    </div>
                                </td>
                                <td class="px-8 py-6 text-center">
                                    {{-- Logika Dojo Sama --}}
                                    <div
                                        class="inline-flex flex-col items-center justify-center bg-slate-50/50 border border-slate-100 px-4 py-2 rounded-2xl min-w-[60px]">
                                        <span
                                            class="text-sm font-black text-slate-800">{{ $official->level === 'pengcab' && $official->city ? $official->city->dojos->count() : 0 }}</span>
                                        <span class="text-[8px] font-bold text-slate-400 uppercase mt-1">Dojo</span>
                                    </div>
                                </td>
                                <td class="px-8 py-6 text-center">
                                    @include('admin.officials._badge_status')
                                    <div class="text-[9px] text-slate-400 mt-2 font-bold uppercase tracking-tighter">
                                        {{ \Carbon\Carbon::parse($official->sk_expiry_date)->format('d M Y') }}
                                    </div>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <div class="flex justify-end gap-2">
                                        @include('admin.officials._action_buttons')
                                    </div>
                                </td>
                            </tr>
                        @empty
                            {{-- Empty State --}}
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- 3. VIEW MOBILE (Modern Cards) --}}
            <div class="md:hidden space-y-6"> {{-- space-y-6 memberikan jarak antar card --}}
                @foreach ($officials as $official)
                    <div
                        class="bg-white rounded-[2rem] p-6 shadow-[0_10px_30px_rgba(0,0,0,0.04)] border border-slate-100 relative overflow-hidden">

                        {{-- Aksen Dekoratif Atas (Optional) --}}
                        <div
                            class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-indigo-500 to-transparent opacity-20">
                        </div>

                        <div class="flex justify-between items-start mb-6">
                            <div>
                                <h4 class="font-black text-lg text-slate-900 uppercase leading-none tracking-tight">
                                    {{ $official->name }}</h4>
                                <span
                                    class="inline-block mt-2 text-[10px] font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-lg border border-indigo-100 uppercase tracking-widest">
                                    {{ $official->position }}
                                </span>
                            </div>
                            @include('admin.officials._badge_status')
                        </div>

                        {{-- Isi Card --}}
                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div class="p-4 bg-slate-50/50 rounded-2xl border border-slate-100">
                                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1">Wilayah
                                </p>
                                <p class="text-xs font-black text-slate-700 uppercase leading-tight">
                                    {{ $official->level === 'pengcab' ? $official->city->name ?? '-' : $official->province->name ?? '-' }}
                                </p>
                            </div>
                            <div class="p-4 bg-slate-50/50 rounded-2xl border border-slate-100">
                                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1">SK Berlaku
                                </p>
                                <p class="text-xs font-black text-slate-700 uppercase leading-tight">
                                    {{ \Carbon\Carbon::parse($official->sk_expiry_date)->format('d/m/y') }}
                                </p>
                            </div>
                        </div>

                        {{-- Footer Card sebagai pembatas fungsional --}}
                        <div class="flex items-center justify-between pt-5 border-t border-slate-100">
                            <div class="text-[10px] font-black text-slate-400 uppercase tracking-tighter">
                                {{ $official->level === 'pengcab' ? ($official->city->dojos->count() ?? 0) . ' Dojo Terdata' : 'Level Pengprov' }}
                            </div>
                            <div class="flex gap-2">
                                @include('admin.officials._action_buttons')
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </div>
</x-app-layout>
