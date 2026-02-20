<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight uppercase tracking-tighter">
                {{ __('Manajemen Pengurus') }}
                {{-- Tambahkan Nama Provinsi jika dia Pengprov --}}
                @if (auth()->user()->role === 'pengprov')
                    - {{ auth()->user()->province->name ?? '' }}
                @endif
            </h2>
            <a href="{{ route('admin.officials.create') }}"
                class="inline-flex items-center bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-bold shadow-sm transition ease-in-out duration-150">
                <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Tambah Pengurus
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- LOGIKA NOTIFIKASI --}}
            @php
                // Ambil pengurus yang masa berlakunya <= 30 hari atau sudah lewat
                $warningOfficials = $officials->filter(function ($official) {
                    $expiry = \Carbon\Carbon::parse($official->sk_expiry_date)->startOfDay();
                    $days = (int) \Carbon\Carbon::now()->startOfDay()->diffInDays($expiry, false);
                    return $days <= 30;
                });
            @endphp

            {{-- NOTIFIKASI ALERT STYLE DOJO DENGAN WILAYAH --}}
            @if ($warningOfficials->count() > 0)
                <div class="mb-6 bg-amber-50 border-l-4 border-amber-500 rounded-lg p-4 shadow-sm">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-bold text-amber-800 uppercase tracking-tight">
                                Perhatian: {{ $warningOfficials->count() }} Pengurus Perlu Perpanjangan SK!
                            </h3>
                        </div>
                    </div>
                    <div class="mt-2 ml-8">
                        <p class="text-xs text-amber-700 leading-relaxed">
                            Pengurus berikut akan tamat tempo SK dalam masa kurang 30 hari:
                            <span class="font-bold italic">
                                @foreach ($warningOfficials as $wo)
                                    {{ $wo->name }}
                                    ({{ $wo->level === 'pengcab' ? $wo->city->name ?? '-' : $wo->province->name ?? '-' }})
                                    {{ !$loop->last ? ',' : '' }}
                                @endforeach
                            </span>.
                        </p>
                    </div>
                </div>
            @endif

            {{-- Table --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-indigo-500">
                <div class="p-6 text-gray-900">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th
                                        class="px-6 py-4 text-left text-[10px] font-black text-gray-500 uppercase tracking-widest">
                                        Pengurus & Jabatan</th>
                                    <th
                                        class="px-6 py-4 text-left text-[10px] font-black text-gray-500 uppercase tracking-widest">
                                        Wilayah Kerja</th>
                                    <th
                                        class="px-6 py-4 text-center text-[10px] font-black text-gray-500 uppercase tracking-widest">
                                        Jumlah Dojo</th>
                                    <th
                                        class="px-6 py-4 text-center text-[10px] font-black text-gray-500 uppercase tracking-widest">
                                        Status SK</th>
                                    <th
                                        class="px-6 py-4 text-center text-[10px] font-black text-gray-500 uppercase tracking-widest">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @forelse ($officials as $official)
                                    @php
                                        $now = \Carbon\Carbon::now()->startOfDay();
                                        $expiryDate = \Carbon\Carbon::parse($official->sk_expiry_date)->startOfDay();
                                        $isExpired = $expiryDate->isPast();
                                        $daysRemaining = (int) $now->diffInDays($expiryDate, false);
                                        $isExpiringSoon = !$isExpired && $daysRemaining <= 30;
                                    @endphp
                                    <tr
                                        class="hover:bg-gray-50 transition duration-150 {{ $isExpiringSoon || $isExpired ? 'bg-amber-50/20' : '' }}">
                                        {{-- Kolom Pengurus --}}
                                        <td class="px-6 py-4">
                                            <div class="font-black text-gray-900 text-sm uppercase leading-none">
                                                {{ $official->name }}</div>
                                            <div
                                                class="text-[10px] text-indigo-600 font-bold bg-indigo-50 px-2 py-0.5 rounded inline-block mt-2 border border-indigo-100 uppercase italic">
                                                {{ $official->position }}
                                            </div>
                                        </td>

                                        {{-- Kolom Wilayah --}}
                                        <td class="px-6 py-4 text-sm">
                                            @if ($official->level === 'pengcab')
                                                <div class="font-bold text-gray-700 leading-none uppercase">
                                                    {{ $official->city->name ?? '-' }}</div>
                                                <div
                                                    class="text-[10px] text-blue-500 uppercase font-bold mt-1 tracking-tighter italic">
                                                    PENGCAB</div>
                                            @else
                                                <div class="font-bold text-gray-700 leading-none uppercase">
                                                    {{ $official->province->name ?? '-' }}</div>
                                                <div
                                                    class="text-[10px] text-indigo-500 uppercase font-bold mt-1 tracking-tighter italic">
                                                    PENGPROV</div>
                                            @endif
                                        </td>

                                        {{-- KOLOM JUMLAH DOJO --}}
                                        <td class="px-6 py-4 text-center">
                                            @if ($official->level === 'pengcab' && $official->city)
                                                <div
                                                    class="inline-flex flex-col items-center justify-center bg-gray-100 px-3 py-1 rounded-md border border-gray-200">
                                                    <span
                                                        class="text-lg font-black text-gray-800 leading-none">{{ $official->city->dojos->count() }}</span>
                                                    <span
                                                        class="text-[8px] font-bold text-gray-400 uppercase tracking-tighter mt-1">Dojo</span>
                                                </div>
                                            @else
                                                <span class="text-gray-300 italic text-xs">-</span>
                                            @endif
                                        </td>

                                        {{-- Kolom Masa Bakti --}}
                                        <td class="px-6 py-4 text-center">
                                            @if ($isExpired)
                                                <span
                                                    class="px-3 py-1 text-[9px] font-black uppercase rounded-full bg-red-100 text-red-700 border border-red-200">Demisioner</span>
                                            @elseif ($isExpiringSoon)
                                                <span
                                                    class="px-3 py-1 text-[9px] font-black uppercase rounded-full bg-amber-100 text-amber-700 border border-amber-200 animate-pulse uppercase">Hampir
                                                    Habis</span>
                                                <div class="text-[9px] text-amber-600 mt-1 font-bold">
                                                    {{ $daysRemaining }} Hari Lagi</div>
                                            @else
                                                <span
                                                    class="px-3 py-1 text-[9px] font-black uppercase rounded-full bg-green-100 text-green-700 border border-green-200 uppercase">Aktif</span>
                                            @endif
                                            <div class="text-[9px] text-gray-400 mt-1 font-semibold italic">
                                                {{ $expiryDate->format('d/m/Y') }}</div>
                                        </td>

                                        <td class="px-6 py-4 text-center text-sm font-medium">
                                            <div class="flex justify-center items-center space-x-2">
                                                {{-- Tombol Edit --}}
                                                <a href="{{ route('admin.officials.edit', $official->id) }}"
                                                    class="inline-flex items-center justify-center bg-indigo-500 hover:bg-indigo-600 text-white w-8 h-8 rounded shadow-sm transition transform hover:scale-110"
                                                    title="Edit Pengurus">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                        </path>
                                                    </svg>
                                                </a>
                                                <form action="{{ route('admin.officials.destroy', $official->id) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('Hapus Pengurus {{ $official->name }}?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit"
                                                        class="bg-rose-500 hover:bg-rose-600 text-white p-1.5 rounded shadow-sm transition transform hover:scale-110">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                            </path>
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5"
                                            class="px-6 py-10 text-center text-gray-400 italic text-sm font-bold uppercase tracking-widest">
                                            Belum ada data pengurus.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
