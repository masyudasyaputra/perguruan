<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-gray-800 leading-tight uppercase tracking-tight">
                {{ __('Data Wilayah: Provinsi') }}
            </h2>
            <a href="{{ route('admin.provinces.create') }}"
                class="inline-flex items-center bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition shadow-lg shadow-indigo-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path>
                </svg>
                Tambah Provinsi
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Flash Message Success --}}
            @if (session('success'))
                <div
                    class="mb-6 p-4 bg-emerald-100 border-l-4 border-emerald-500 text-emerald-700 text-xs font-bold uppercase tracking-widest rounded shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            {{-- NOTIFIKASI EXPIRED SK --}}
            @php
                $now = now()->startOfDay();
                $warningProvinces = $provinces->filter(function ($p) use ($now) {
                    if (!$p->sk_expiry_date) {
                        return false;
                    }
                    $expiry = \Carbon\Carbon::parse($p->sk_expiry_date)->startOfDay();
                    $diff = $now->diffInDays($expiry, false);
                    return $diff >= 0 && $diff <= 30;
                });
            @endphp

            @if ($warningProvinces->count() > 0)
                <div class="mb-8 bg-amber-50 border-l-8 border-amber-500 rounded-r-2xl p-5 shadow-sm animate-pulse">
                    <div class="flex items-center mb-2">
                        <svg class="h-5 w-5 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                clip-rule="evenodd" />
                        </svg>
                        <h3 class="ml-3 text-xs font-black text-amber-900 uppercase tracking-widest">
                            Perhatian: {{ $warningProvinces->count() }} Provinsi Memasuki Masa Akhir SK!
                        </h3>
                    </div>
                    <p class="ml-8 text-[11px] text-amber-800 italic">
                        Daftar: @foreach ($warningProvinces as $wp)
                            {{ $wp->name }}{{ !$loop->last ? ',' : '' }}
                        @endforeach
                    </p>
                </div>
            @endif

            {{-- TABEL PROVINSI --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
                <div class="p-6 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="font-black text-gray-700 uppercase text-xs tracking-widest flex items-center">
                        <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7">
                            </path>
                        </svg>
                        Manajemen Wilayah & Statistik
                    </h3>
                    <span
                        class="text-[10px] bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full font-black uppercase tracking-tighter">
                        Total: {{ $provinces->count() }} Provinsi
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50/50">
                            <tr>
                                <th
                                    class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                    NO</th>
                                <th
                                    class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                    Provinsi & Ketua</th>
                                {{-- KOLOM BARU --}}
                                <th
                                    class="px-6 py-4 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                    Statistik</th>
                                <th
                                    class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                    Administrasi SK</th>
                                <th
                                    class="px-6 py-4 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                    Status SK</th>
                                <th
                                    class="px-6 py-4 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @foreach ($provinces as $province)
                                <tr class="hover:bg-indigo-50/30 transition duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-400 font-mono font-bold">
                                        {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-gray-800 uppercase tracking-tight">
                                            {{ $province->name }}</div>
                                        <div class="text-[10px] text-indigo-500 font-medium italic mt-0.5">
                                            Ketua: {{ $province->leader_name ?? 'Belum Diisi' }}
                                        </div>
                                    </td>

                                    {{-- DATA JUMLAH PENGCAB & DOJO --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="flex justify-center items-center gap-2">
                                            {{-- Hitung Pengcab (Cities) --}}
                                            <div
                                                class="flex flex-col items-center bg-slate-50 border border-slate-100 rounded-lg px-2 py-1 min-w-[55px]">
                                                <span
                                                    class="text-[8px] font-black text-slate-400 uppercase">Pengcab</span>
                                                <span class="text-xs font-bold text-indigo-600">
                                                    {{ $province->pengcab_count ?? 0 }}
                                                </span>
                                            </div>

                                            {{-- Hitung Dojo --}}
                                            <div
                                                class="flex flex-col items-center bg-slate-50 border border-slate-100 rounded-lg px-2 py-1 min-w-[55px]">
                                                <span class="text-[8px] font-black text-slate-400 uppercase">Dojo</span>
                                                <span class="text-xs font-bold text-emerald-600">
                                                    {{ $province->dojos_count ?? 0 }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-[11px] font-bold text-gray-600">
                                            {{ $province->sk_number ?? 'No SK: -' }}</div>
                                        <div class="text-[10px] text-gray-400">Hingga:
                                            {{ $province->sk_expiry_date ? \Carbon\Carbon::parse($province->sk_expiry_date)->format('d M Y') : '-' }}
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @php
                                            $expiryDate = $province->sk_expiry_date
                                                ? \Carbon\Carbon::parse($province->sk_expiry_date)->startOfDay()
                                                : null;
                                            $isExpired = $expiryDate ? $expiryDate->isPast() : false;
                                            $daysRemaining = $expiryDate
                                                ? (int) $now->diffInDays($expiryDate, false)
                                                : null;
                                            $isWarning = !$isExpired && $daysRemaining !== null && $daysRemaining <= 30;
                                        @endphp

                                        @if (!$expiryDate)
                                            <span
                                                class="px-3 py-1 rounded-full text-[9px] font-black uppercase bg-gray-100 text-gray-500">Data
                                                Kosong</span>
                                        @elseif ($isExpired)
                                            <span
                                                class="px-3 py-1 rounded-full text-[9px] font-black uppercase bg-red-100 text-red-600">Expired</span>
                                        @elseif ($isWarning)
                                            <span
                                                class="px-3 py-1 rounded-full text-[9px] font-black uppercase bg-amber-100 text-amber-600">Warning
                                                ({{ $daysRemaining }} Hari)
                                            </span>
                                        @else
                                            <span
                                                class="px-3 py-1 rounded-full text-[9px] font-black uppercase bg-emerald-100 text-emerald-600">Aktif</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="flex justify-center items-center gap-2">
                                            <a href="{{ route('admin.provinces.edit', $province->id) }}"
                                                class="w-8 h-8 flex items-center justify-center bg-amber-50 text-amber-600 rounded-lg hover:bg-amber-500 hover:text-white transition shadow-sm">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2.5"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                    </path>
                                                </svg>
                                            </a>
                                            <form action="{{ route('admin.provinces.destroy', $province->id) }}"
                                                method="POST"
                                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus Provinsi {{ $province->name }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="w-8 h-8 flex items-center justify-center bg-red-50 text-red-600 rounded-lg hover:bg-red-600 hover:text-white transition shadow-sm">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2.5"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1-1v3M4 7h16">
                                                        </path>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- FOOTER INFO --}}
            <div class="mt-6 bg-amber-50 border-l-4 border-amber-500 p-4 rounded-r-xl shadow-sm">
                <div class="flex">
                    <svg class="h-5 w-5 text-amber-500" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                            clip-rule="evenodd" />
                    </svg>
                    <div class="ml-3">
                        <p class="text-[11px] text-amber-800 font-medium italic">
                            Berhati-hatilah saat mengelola data <strong class="uppercase font-black">Wilayah</strong>.
                            Penghapusan provinsi akan berdampak pada relasi data di bawahnya (Pengcab & Dojo).
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
