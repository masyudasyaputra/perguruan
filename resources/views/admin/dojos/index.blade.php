<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight uppercase tracking-tighter">
                {{ __('Manajemen Data Dojo') }}
            </h2>
            <a href="{{ route('admin.dojos.create') }}"
                class="inline-flex items-center bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-bold shadow-sm transition ease-in-out duration-150">
                <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Tambah Dojo
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- NOTIFIKASI MASA BERLAKU SK (30 HARI LAGI) --}}
            @if ($warningDojos->count() > 0)
                <div class="mb-6 bg-amber-50 border-l-4 border-amber-500 p-4 shadow-sm rounded-r-lg">
                    <div class="flex items-center">
                        <svg class="h-5 w-5 text-amber-500 me-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                clip-rule="evenodd" />
                        </svg>
                        <h3 class="text-sm font-bold text-amber-800">Perhatian: {{ $warningDojos->count() }} Dojo Perlu
                            Perpanjangan SK!</h3>
                    </div>
                    <div class="mt-2 text-xs text-amber-700">
                        Dojo berikut akan tamat tempo SK dalam masa kurang 30 hari:
                        <span class="font-bold italic">
                            {{ $warningDojos->pluck('name')->implode(', ') }}
                        </span>.
                    </div>
                </div>
            @endif

            {{-- Flash Success/Error --}}
            @if (session('success'))
                <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 shadow-sm" role="alert">
                    <p class="font-bold text-sm text-green-800">Berhasil!</p>
                    <p class="text-xs">{{ session('success') }}</p>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-indigo-500">
                <div class="p-6 text-gray-900">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th
                                        class="px-6 py-4 text-left text-[10px] font-black text-gray-500 uppercase tracking-widest">
                                        Dojo & SK</th>
                                    <th
                                        class="px-6 py-4 text-left text-[10px] font-black text-gray-500 uppercase tracking-widest">
                                        Wilayah</th>
                                    <th
                                        class="px-6 py-4 text-left text-[10px] font-black text-gray-500 uppercase tracking-widest">
                                        Sensei</th>
                                    <th
                                        class="px-6 py-4 text-center text-[10px] font-black text-gray-500 uppercase tracking-widest">
                                        Status SK</th>
                                    <th
                                        class="px-6 py-4 text-center text-[10px] font-black text-gray-500 uppercase tracking-widest">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @forelse ($dojos as $dojo)
                                    @php
                                        // Set ke startOfDay agar hitungan harinya bulat (integer)
                                        $now = \Carbon\Carbon::now()->startOfDay();
                                        $expiryDate = \Carbon\Carbon::parse($dojo->sk_expiry_date)->startOfDay();

                                        $isExpired = $expiryDate->isPast();
                                        // Hitung selisih hari secara absolut
                                        $daysRemaining = (int) $now->diffInDays($expiryDate, false);
                                        $isExpiringSoon = !$isExpired && $daysRemaining <= 30;
                                    @endphp
                                    <tr
                                        class="hover:bg-gray-50 transition duration-150 {{ $isExpiringSoon ? 'bg-amber-50/30' : '' }}">
                                        <td class="px-6 py-4">
                                            <div class="font-black text-gray-900 text-sm uppercase leading-none">
                                                {{ $dojo->name }}</div>
                                            <div
                                                class="text-[10px] text-indigo-600 font-bold bg-indigo-50 px-2 py-0.5 rounded inline-block mt-2 border border-indigo-100">
                                                SK: {{ $dojo->sk_number ?? 'Batal' }}
                                            </div>
                                        </td>

                                        <td class="px-6 py-4 text-sm">
                                            <div class="font-bold text-gray-700 leading-none">
                                                {{ $dojo->city->name ?? '-' }}</div>
                                            <div
                                                class="text-[10px] text-gray-400 uppercase font-bold mt-1 tracking-tighter">
                                                {{ $dojo->city->province->name ?? '-' }}</div>
                                        </td>

                                        <td class="px-6 py-4 text-sm">
                                            <div class="text-gray-900 font-bold leading-none">
                                                {{ $dojo->sensei_name ?? '-' }}</div>
                                            <div class="text-[10px] text-gray-400 mt-1 italic">
                                                {{ $dojo->phone_number ?? 'Tiada No. Tel' }}</div>
                                        </td>

                                        <td class="px-6 py-4 text-center">
                                            @if ($isExpired)
                                                <span
                                                    class="px-3 py-1 text-[9px] font-black uppercase rounded-full bg-red-100 text-red-700 border border-red-200">Expired</span>
                                            @elseif ($isExpiringSoon)
                                                <span
                                                    class="px-3 py-1 text-[9px] font-black uppercase rounded-full bg-amber-100 text-amber-700 border border-amber-200 animate-pulse">Hampir
                                                    Habis</span>
                                                <div class="text-[9px] text-amber-600 mt-1 font-bold">
                                                    {{ $daysRemaining }} Hari Lagi</div>
                                            @else
                                                <span
                                                    class="px-3 py-1 text-[9px] font-black uppercase rounded-full bg-green-100 text-green-700 border border-green-200">Aktif</span>
                                            @endif
                                            <div class="text-[9px] text-gray-400 mt-1 font-semibold italic">
                                                {{ $expiryDate->format('d/m/Y') }}</div>
                                        </td>

                                        <td class="px-6 py-4 text-center text-sm font-medium">
                                            <div class="flex justify-center items-center space-x-2">
                                                <a href="{{ route('admin.dojos.edit', $dojo->id) }}"
                                                    class="bg-indigo-500 hover:bg-indigo-600 text-white p-1.5 rounded shadow-sm transition transform hover:scale-110">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                        </path>
                                                    </svg>
                                                </a>
                                                <form action="{{ route('admin.dojos.destroy', $dojo->id) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('Hapus Dojo {{ $dojo->name }}?')">
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
                                        <td colspan="5" class="px-6 py-10 text-center text-gray-400 italic text-sm">
                                            Belum ada data dojo.</td>
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
