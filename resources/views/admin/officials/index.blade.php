<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-gray-800 leading-tight">
                {{ __('Manajemen Pengurus Wilayah') }}
            </h2>
            <a href="{{ route('admin.officials.create') }}"
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-xl text-xs font-black uppercase tracking-widest transition shadow-lg shadow-indigo-200">
                + Tambah Pengurus
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Statistik Ringkas --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <p class="text-xs font-black text-gray-400 uppercase tracking-widest">Total Personil</p>
                    <h3 class="text-2xl font-black text-gray-800">{{ $officials->count() }}</h3>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <p class="text-xs font-black text-blue-400 uppercase tracking-widest">Tingkat Pengcab</p>
                    <h3 class="text-2xl font-black text-gray-800">{{ $officials->where('level', 'pengcab')->count() }}
                    </h3>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <p class="text-xs font-black text-indigo-400 uppercase tracking-widest">Tingkat Pengprov</p>
                    <h3 class="text-2xl font-black text-gray-800">{{ $officials->where('level', 'provinsi')->count() }}
                    </h3>
                </div>
            </div>

            {{-- Table --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                    Pengurus</th>
                                <th
                                    class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                    Level & Wilayah</th>
                                <th
                                    class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                    SK & Masa Bakti</th>
                                <th
                                    class="px-6 py-4 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @foreach ($officials as $official)
                                <tr class="hover:bg-gray-50/50 transition">
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-bold text-gray-800">{{ $official->name }}</div>
                                        <div class="text-[10px] font-bold text-indigo-600 uppercase">
                                            {{ $official->position }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($official->level === 'pengcab')
                                            <span
                                                class="px-2 py-1 text-[10px] font-black bg-blue-100 text-blue-700 rounded-md uppercase">PENGCAB</span>
                                            <div class="text-xs mt-1 text-gray-600 font-medium">
                                                {{ $official->city->name ?? '-' }}</div>
                                        @else
                                            <span
                                                class="px-2 py-1 text-[10px] font-black bg-indigo-100 text-indigo-700 rounded-md uppercase">PENGPROV</span>
                                            <div class="text-xs mt-1 text-gray-600 font-medium">
                                                {{ $official->province->name ?? '-' }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-xs text-gray-700 font-mono">
                                            {{ $official->sk_number ?? 'No SK: -' }}</div>
                                        <div
                                            class="text-[10px] {{ \Carbon\Carbon::parse($official->sk_expiry_date)->isPast() ? 'text-red-500' : 'text-gray-400' }}">
                                            Berakhir:
                                            {{ \Carbon\Carbon::parse($official->sk_expiry_date)->format('d M Y') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex justify-center gap-2">
                                            <a href="{{ route('admin.officials.edit', $official->id) }}"
                                                class="p-2 bg-amber-50 text-amber-600 rounded-lg hover:bg-amber-100 transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                            </a>
                                            <form action="{{ route('admin.officials.destroy', $official->id) }}"
                                                method="POST" onsubmit="return confirm('Hapus data pengurus ini?')">
                                                @csrf @method('DELETE')
                                                <button
                                                    class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </button>
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
        </div>
    </div>
</x-app-layout>
