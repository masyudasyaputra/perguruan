<x-app-layout>
    <x-slot name="header">
        <div class="flex items-start sm:items-center justify-between gap-3">
            <div class="min-w-0">
                <h2 class="font-black text-xl sm:text-3xl text-slate-900 leading-tight tracking-tighter uppercase">
                    {{ __('Jadwal') }} <span class="text-red-600">Ujian Sabuk</span>
                </h2>
                <p class="text-[8px] sm:text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mt-1">
                    Manajemen sesi ujian • pendaftaran peserta • penilaian
                </p>
            </div>
        </div>
    </x-slot>

    {{-- Floating Action Button for Mobile (PB/PENGPROV saja) --}}
    @if (auth()->user()->hasRole(['pb', 'pengprov']))
        <div class="fixed bottom-6 right-6 z-50 md:hidden">
            <button onclick="toggleModal('modalAddExam')"
                class="flex items-center justify-center w-14 h-14 bg-red-600 text-white rounded-2xl shadow-[0_10px_25px_rgba(220,38,38,0.4)] active:scale-90 transition-transform border-b-4 border-red-800">
                <svg class="w-7 h-7 stroke-[3]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="square" stroke-linejoin="square" d="M12 4v16m8-8H4"></path>
                </svg>
            </button>
        </div>
    @endif

    <div class="py-6 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Toolbar Desktop --}}
            <div class="hidden md:flex justify-between items-end mb-6">
                <div>
                    <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest">Daftar Jadwal Ujian</h3>
                    <p class="text-[10px] text-slate-400 font-bold uppercase mt-1">
                        Total: {{ $exams->count() }} Sesi Terdaftar
                    </p>
                </div>

                @if (auth()->user()->hasRole(['pb', 'pengprov']))
                    <button onclick="toggleModal('modalAddExam')"
                        class="inline-flex items-center bg-slate-900 hover:bg-red-600 text-white px-5 py-3 rounded-xl text-[10px] font-black transition-all duration-300 hover:-translate-y-1 shadow-lg shadow-slate-200 uppercase tracking-[0.15em] border-b-4 border-slate-700 hover:border-red-800">
                        <svg class="w-4 h-4 me-2 stroke-[3]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="square" stroke-linejoin="square" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Tambah Jadwal Baru
                    </button>
                @endif
            </div>

            {{-- Alert sukses --}}
            @if (session('success'))
                <div
                    class="mb-6 relative overflow-hidden bg-white border-2 border-emerald-50 rounded-2xl p-4 sm:p-6 shadow-sm alert-notif">
                    <div class="relative flex items-center gap-4 sm:gap-5">
                        <div class="bg-emerald-600 p-2.5 rounded-xl flex-shrink-0 shadow-lg shadow-emerald-200">
                            <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="square" stroke-linejoin="square" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xs sm:text-sm font-black text-slate-900 uppercase tracking-tight">Berhasil
                            </h3>
                            <p class="text-[10px] sm:text-xs text-slate-500 mt-0.5 font-bold uppercase tracking-tight">
                                {{ session('success') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- =========================
                DESKTOP TABLE (AKSI DIPERBAIKI)
            ========================== --}}
            <div
                class="hidden md:block bg-white rounded-[1.5rem] border-2 border-slate-100 shadow-xl shadow-slate-200/50 overflow-hidden">
                <table class="min-w-full">
                    <thead>
                        <tr class="bg-slate-900 text-white">
                            <th class="px-8 py-5 text-left text-[10px] font-black uppercase tracking-[0.2em]">Sesi</th>
                            <th class="px-8 py-5 text-left text-[10px] font-black uppercase tracking-[0.2em]">Tanggal
                            </th>
                            <th class="px-8 py-5 text-left text-[10px] font-black uppercase tracking-[0.2em]">Lokasi
                            </th>
                            <th class="px-8 py-5 text-center text-[10px] font-black uppercase tracking-[0.2em]">Peserta
                            </th>
                            <th class="px-8 py-5 text-center text-[10px] font-black uppercase tracking-[0.2em]">Status
                            </th>
                            <th class="px-8 py-5 text-right text-[10px] font-black uppercase tracking-[0.2em]">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @forelse($exams as $exam)
                            <tr class="hover:bg-slate-50 transition-all group">
                                <td class="px-8 py-6">
                                    <div class="flex flex-col">
                                        <span
                                            class="font-black text-slate-900 text-sm uppercase group-hover:text-red-600 transition-colors">
                                            {{ $exam->name }}
                                        </span>
                                        <span
                                            class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-1">
                                            ID: #{{ $exam->id }}
                                        </span>
                                    </div>
                                </td>

                                <td class="px-8 py-6">
                                    <span class="text-xs font-black text-slate-800 uppercase tracking-widest">
                                        {{ is_string($exam->execution_date) ? \Carbon\Carbon::parse($exam->execution_date)->format('d M Y') : $exam->execution_date->format('d M Y') }}
                                    </span>
                                </td>

                                <td class="px-8 py-6">
                                    <span class="text-[10px] font-bold text-slate-600 uppercase tracking-widest">
                                        {{ $exam->location }}
                                    </span>
                                </td>

                                <td class="px-8 py-6 text-center">
                                    <div class="inline-block px-4 py-2 bg-slate-50 rounded-lg border border-slate-100">
                                        <span class="text-sm font-black text-slate-900 block leading-none">
                                            {{ $exam->participants->count() }}
                                        </span>
                                        <span class="text-[8px] font-black text-slate-400 uppercase">Peserta</span>
                                    </div>
                                </td>

                                <td class="px-8 py-6 text-center">
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest border
                                        {{ $exam->status === 'open'
                                            ? 'bg-emerald-50 text-emerald-700 border-emerald-100'
                                            : 'bg-slate-100 text-slate-500 border-slate-200' }}">
                                        {{ $exam->status }}
                                    </span>
                                </td>

                                {{-- AKSI: dibuat lebih rapi, konsisten, dan eye-catching --}}
                                <td class="px-8 py-6">
                                    <div class="flex justify-end items-center gap-2">
                                        {{-- Kelola (selalu ada) --}}
                                        <a href="{{ route('admin.exams.show', $exam->id) }}"
                                            class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl bg-slate-900 text-white text-[10px] font-black uppercase tracking-widest
                                                   hover:bg-red-600 transition-all border-b-4 border-slate-700 hover:border-red-800 active:translate-y-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                            </svg>
                                            Kelola
                                        </a>

                                        {{-- Input Nilai --}}
                                        @if (auth()->user()->hasRole(['pb', 'pengprov', 'penguji']))
                                            <a href="{{ route('admin.exams.scoring', $exam->id) }}"
                                                class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl bg-white border-2 border-slate-200 text-slate-700 text-[10px] font-black uppercase tracking-widest
                                                       hover:bg-slate-900 hover:text-white transition-all">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9 12h6m-6 4h6M9 8h6m4 14H5a2 2 0 01-2-2V4a2 2 0 012-2h10l6 6v14a2 2 0 01-2 2z" />
                                                </svg>
                                                Nilai
                                            </a>
                                        @endif

                                        {{-- Icon group (PB/PENGPROV) --}}
                                        @if (auth()->user()->hasRole(['pb', 'pengprov']))
                                            {{-- Atur Penguji --}}
                                            <a href="{{ route('admin.exams.examiners.edit', $exam->id) }}"
                                                title="Atur Penguji"
                                                class="p-2 rounded-xl bg-slate-50 border border-slate-200 text-slate-700
                                                       hover:bg-slate-900 hover:text-white transition-all">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                                </svg>
                                            </a>

                                            {{-- Edit --}}
                                            <button type="button" onclick="openEditModal({{ json_encode($exam) }})"
                                                title="Edit Jadwal"
                                                class="p-2 rounded-xl bg-slate-50 border border-slate-200 text-slate-700
                                                       hover:bg-slate-900 hover:text-white transition-all">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>

                                            {{-- Delete (destruktif) --}}
                                            <form action="{{ route('admin.exams.destroy', $exam->id) }}"
                                                method="POST" onsubmit="return confirm('Hapus jadwal ini?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" title="Hapus Jadwal"
                                                    class="p-2 rounded-xl bg-rose-50 border border-rose-100 text-rose-700
                                                           hover:bg-rose-600 hover:text-white transition-all">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6"
                                    class="py-20 text-center italic text-slate-400 font-bold uppercase tracking-widest text-sm">
                                    Belum ada data jadwal ujian
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- =========================
                MOBILE (CARD) - TAMPILKAN SEMUA AKSI
            ========================== --}}
            <div class="md:hidden space-y-5">
                @forelse ($exams as $exam)
                    <div
                        class="relative bg-white rounded-[2rem] border border-slate-200 shadow-sm overflow-hidden active:scale-[0.98] transition-transform">
                        {{-- Top --}}
                        <div class="p-5 pb-4">
                            <div class="flex justify-between items-start gap-4 mb-3">
                                <div class="flex-1 min-w-0">
                                    <h4
                                        class="font-black text-slate-900 uppercase text-base leading-tight tracking-tight">
                                        {{ $exam->name }}
                                    </h4>

                                    <div class="flex items-center gap-2 mt-1">
                                        <span
                                            class="w-1.5 h-1.5 rounded-full {{ $exam->status === 'open' ? 'bg-emerald-600' : 'bg-slate-400' }}"></span>
                                        <span
                                            class="text-[9px] font-black text-slate-400 uppercase tracking-widest italic">
                                            {{ $exam->status }}
                                        </span>
                                    </div>
                                </div>

                                <div class="flex-shrink-0 flex flex-col items-end">
                                    <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest">
                                        {{ is_string($exam->execution_date) ? \Carbon\Carbon::parse($exam->execution_date)->format('d/m/y') : $exam->execution_date->format('d/m/y') }}
                                    </span>
                                    <span
                                        class="text-[8px] font-black text-slate-300 mt-1 uppercase tracking-tighter italic">
                                        {{ $exam->location }}
                                    </span>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div
                                    class="bg-slate-50 p-3 rounded-2xl border border-slate-100 flex items-center gap-3">
                                    <div class="p-2 bg-white rounded-lg text-slate-400 shadow-sm">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                d="M8 7V3m8 4V3M5 11h14M7 21h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <div class="min-w-0">
                                        <p
                                            class="text-[7px] font-black text-slate-400 uppercase tracking-widest mb-0.5">
                                            Tanggal</p>
                                        <p
                                            class="text-[10px] font-black text-slate-700 truncate uppercase italic leading-tight">
                                            {{ is_string($exam->execution_date) ? \Carbon\Carbon::parse($exam->execution_date)->format('d M Y') : $exam->execution_date->format('d M Y') }}
                                        </p>
                                    </div>
                                </div>

                                <div
                                    class="bg-slate-50 p-3 rounded-2xl border border-slate-100 flex items-center gap-3">
                                    <div class="p-2 bg-white rounded-lg text-red-600 shadow-sm">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0" />
                                        </svg>
                                    </div>
                                    <div class="min-w-0">
                                        <p
                                            class="text-[7px] font-black text-slate-400 uppercase tracking-widest mb-0.5">
                                            Peserta</p>
                                        <p class="text-[10px] font-black text-slate-900 uppercase tracking-tighter">
                                            {{ $exam->participants->count() }} Orang
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Actions (ALL) --}}
                        <div class="px-5 py-4 bg-slate-900">
                            {{-- Row 1: Primary buttons --}}
                            <div class="grid grid-cols-2 gap-3">
                                <a href="{{ route('admin.exams.show', $exam->id) }}"
                                    class="inline-flex items-center justify-center gap-2 py-3 rounded-2xl bg-white text-slate-900 text-[10px] font-black uppercase tracking-widest hover:bg-slate-100 transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                    </svg>
                                    Kelola
                                </a>

                                @if (auth()->user()->hasRole(['pb', 'pengprov', 'penguji']))
                                    <a href="{{ route('admin.exams.scoring', $exam->id) }}"
                                        class="inline-flex items-center justify-center gap-2 py-3 rounded-2xl bg-red-600 text-white text-[10px] font-black uppercase tracking-widest hover:bg-red-700 transition-all border-b-4 border-red-800 active:translate-y-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6M9 8h6m4 14H5a2 2 0 01-2-2V4a2 2 0 012-2h10l6 6v14a2 2 0 01-2 2z" />
                                        </svg>
                                        Nilai
                                    </a>
                                @else
                                    <div class="opacity-60">
                                        <div
                                            class="inline-flex w-full items-center justify-center py-3 rounded-2xl bg-white/10 text-white/80 text-[10px] font-black uppercase tracking-widest border border-white/10">
                                            Nilai
                                        </div>
                                    </div>
                                @endif
                            </div>

                            {{-- Row 2: Secondary icon actions --}}
                            <div class="mt-3 grid grid-cols-4 gap-2">
                                {{-- Atur Penguji --}}
                                @if (auth()->user()->hasRole(['pb', 'pengprov']))
                                    <a href="{{ route('admin.exams.examiners.edit', $exam->id) }}"
                                        title="Atur Penguji"
                                        class="flex items-center justify-center p-3 rounded-2xl bg-white/10 border border-white/10 text-white hover:bg-white hover:text-slate-900 transition-all">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                    </a>
                                @else
                                    <div
                                        class="flex items-center justify-center p-3 rounded-2xl bg-white/5 border border-white/10 text-white/40">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0" />
                                        </svg>
                                    </div>
                                @endif

                                {{-- Edit --}}
                                @if (auth()->user()->hasRole(['pb', 'pengprov']))
                                    <button type="button" onclick="openEditModal({{ json_encode($exam) }})"
                                        title="Edit"
                                        class="flex items-center justify-center p-3 rounded-2xl bg-white/10 border border-white/10 text-white hover:bg-white hover:text-slate-900 transition-all">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                @else
                                    <div
                                        class="flex items-center justify-center p-3 rounded-2xl bg-white/5 border border-white/10 text-white/40">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </div>
                                @endif

                                {{-- Detail (redundan karena ada Kelola, tapi tetap sesuai permintaan "semua aksi") --}}
                                <a href="{{ route('admin.exams.show', $exam->id) }}" title="Detail"
                                    class="flex items-center justify-center p-3 rounded-2xl bg-white/10 border border-white/10 text-white hover:bg-white hover:text-slate-900 transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>

                                {{-- Delete --}}
                                @if (auth()->user()->hasRole(['pb', 'pengprov']))
                                    <form action="{{ route('admin.exams.destroy', $exam->id) }}" method="POST"
                                        onsubmit="return confirm('Hapus jadwal ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" title="Hapus"
                                            class="flex w-full items-center justify-center p-3 rounded-2xl bg-rose-50 text-rose-700 border border-rose-100 hover:bg-rose-600 hover:text-white transition-all">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                @else
                                    <div
                                        class="flex items-center justify-center p-3 rounded-2xl bg-white/5 border border-white/10 text-white/40">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white p-6 rounded-[2rem] border border-slate-200 text-center">
                        <p class="text-xs font-black uppercase tracking-widest text-slate-400">Belum ada data</p>
                    </div>
                @endforelse
            </div>

            <div class="h-20 md:hidden"></div>
        </div>
    </div>

    {{-- MODAL TAMBAH --}}
    <div id="modalAddExam"
        class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
        <div class="bg-white rounded-[2rem] w-full max-w-lg p-6 sm:p-8 shadow-2xl border border-slate-100">
            <form action="{{ route('admin.exams.store') }}" method="POST">
                @csrf

                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-lg sm:text-xl font-black text-slate-900 uppercase tracking-tight">Jadwal Baru</h3>
                    <button type="button" onclick="toggleModal('modalAddExam')"
                        class="p-2 rounded-xl bg-slate-50 border border-slate-200 hover:bg-slate-900 hover:text-white transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="square" stroke-linejoin="square" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="space-y-3">
                    <input type="text" name="name" placeholder="Nama Sesi" required
                        class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl px-5 py-4 text-sm font-bold focus:ring-0 focus:border-slate-900">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <input type="date" name="execution_date" required
                            class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl px-5 py-4 text-sm font-bold focus:ring-0 focus:border-slate-900">
                        <select name="status"
                            class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl px-5 py-4 text-sm font-bold focus:ring-0 focus:border-slate-900 uppercase">
                            <option value="open">Open</option>
                            <option value="draft">Draft</option>
                        </select>
                    </div>
                    <input type="text" name="location" placeholder="Lokasi" required
                        class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl px-5 py-4 text-sm font-bold focus:ring-0 focus:border-slate-900">
                </div>

                <div class="mt-6 grid grid-cols-2 gap-3">
                    <button type="button" onclick="toggleModal('modalAddExam')"
                        class="py-3 bg-white border-2 border-slate-200 text-slate-600 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-slate-50 transition-all">
                        Batal
                    </button>
                    <button type="submit"
                        class="py-3 bg-slate-900 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-red-600 transition-all border-b-4 border-slate-700 hover:border-red-800 active:translate-y-1">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL EDIT --}}
    <div id="modalEditExam"
        class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
        <div class="bg-white rounded-[2rem] w-full max-w-lg p-6 sm:p-8 shadow-2xl border border-slate-100">
            <form id="editForm" method="POST">
                @csrf @method('PUT')

                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-lg sm:text-xl font-black text-slate-900 uppercase tracking-tight">Edit Jadwal</h3>
                    <button type="button" onclick="toggleModal('modalEditExam')"
                        class="p-2 rounded-xl bg-slate-50 border border-slate-200 hover:bg-slate-900 hover:text-white transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="square" stroke-linejoin="square" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="space-y-3">
                    <input type="text" name="name" id="edit_name" required
                        class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl px-5 py-4 text-sm font-bold focus:ring-0 focus:border-slate-900">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <input type="date" name="execution_date" id="edit_date" required
                            class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl px-5 py-4 text-sm font-bold focus:ring-0 focus:border-slate-900">
                        <select name="status" id="edit_status"
                            class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl px-5 py-4 text-sm font-bold focus:ring-0 focus:border-slate-900 uppercase">
                            <option value="open">Open</option>
                            <option value="draft">Draft</option>
                        </select>
                    </div>
                    <input type="text" name="location" id="edit_location" required
                        class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl px-5 py-4 text-sm font-bold focus:ring-0 focus:border-slate-900">
                </div>

                <div class="mt-6 grid grid-cols-2 gap-3">
                    <button type="button" onclick="toggleModal('modalEditExam')"
                        class="py-3 bg-white border-2 border-slate-200 text-slate-600 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-slate-50 transition-all">
                        Batal
                    </button>
                    <button type="submit"
                        class="py-3 bg-slate-900 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-red-600 transition-all border-b-4 border-slate-700 hover:border-red-800 active:translate-y-1">
                        Update Jadwal
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleModal(id) {
            const modal = document.getElementById(id);
            if (modal) modal.classList.toggle('hidden');
        }

        function openEditModal(exam) {
            document.getElementById('editForm').action = `/admin/exams/${exam.id}`;
            document.getElementById('edit_name').value = exam.name;
            document.getElementById('edit_location').value = exam.location;
            document.getElementById('edit_status').value = exam.status;

            const date = new Date(exam.execution_date);
            document.getElementById('edit_date').value = date.toISOString().split('T')[0];

            toggleModal('modalEditExam');
        }

        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert-notif');
            alerts.forEach(a => a.remove());
        }, 4000);
    </script>
</x-app-layout>
