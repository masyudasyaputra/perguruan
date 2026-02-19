<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
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
            {{-- Alert Success --}}
            @if (session('success'))
                <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 shadow-sm" role="alert">
                    <p class="font-bold text-sm">Berhasil!</p>
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
                                        class="px-6 py-4 text-left text-xs font-black text-gray-600 uppercase tracking-widest">
                                        Nama Dojo & SK
                                    </th>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-black text-gray-600 uppercase tracking-widest">
                                        Wilayah (Kota/Prov)
                                    </th>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-black text-gray-600 uppercase tracking-widest">
                                        Sensei & Kontak
                                    </th>
                                    <th
                                        class="px-6 py-4 text-center text-xs font-black text-gray-600 uppercase tracking-widest">
                                        Status SK
                                    </th>
                                    <th
                                        class="px-6 py-4 text-center text-xs font-black text-gray-600 uppercase tracking-widest">
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @forelse ($dojos as $dojo)
                                    <tr class="hover:bg-gray-50 transition duration-150">
                                        {{-- Nama & SK --}}
                                        <td class="px-6 py-4">
                                            <div class="font-bold text-gray-900 text-sm uppercase">{{ $dojo->name }}
                                            </div>
                                            <div
                                                class="text-[10px] text-indigo-600 font-bold bg-indigo-50 px-2 py-0.5 rounded inline-block mt-1">
                                                SK: {{ $dojo->sk_number ?? 'Batal/Belum Ada' }}
                                            </div>
                                        </td>

                                        {{-- Wilayah --}}
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <div class="font-semibold text-gray-700">{{ $dojo->city->name }}</div>
                                            <div class="text-[10px] text-gray-400 uppercase font-bold">
                                                {{ $dojo->city->province->name }}
                                            </div>
                                        </td>

                                        {{-- Sensei & Alamat --}}
                                        <td class="px-6 py-4 text-sm">
                                            <div class="text-gray-900 font-bold">Sensei: {{ $dojo->sensei_name ?? '-' }}
                                            </div>
                                            <div class="text-[11px] text-gray-500 truncate w-48 leading-tight mt-1"
                                                title="{{ $dojo->address }}">
                                                {{ $dojo->address }}
                                            </div>
                                        </td>

                                        {{-- Status Otomatis Berdasarkan SK --}}
                                        <td class="px-6 py-4 text-center">
                                            @if ($dojo->is_active)
                                                <span
                                                    class="inline-flex px-3 py-1 text-[10px] font-black uppercase rounded-full bg-green-100 text-green-700 border border-green-200">
                                                    Aktif
                                                </span>
                                                <div class="text-[9px] text-gray-400 mt-1 font-semibold">
                                                    Hingga:
                                                    {{ \Carbon\Carbon::parse($dojo->sk_expiry_date)->format('d M Y') }}
                                                </div>
                                            @else
                                                <span
                                                    class="inline-flex px-3 py-1 text-[10px] font-black uppercase rounded-full bg-red-100 text-red-700 border border-red-200 shadow-sm">
                                                    Expired
                                                </span>
                                                <div class="text-[9px] text-red-500 mt-1 font-black italic">
                                                    Masa SK Habis!
                                                </div>
                                            @endif
                                        </td>

                                        {{-- Tombol Aksi --}}
                                        <td class="px-6 py-4 text-center text-sm font-medium">
                                            <div class="flex justify-center items-center space-x-2">
                                                <a href="{{ route('admin.dojos.edit', $dojo->id) }}"
                                                    class="bg-yellow-400 hover:bg-yellow-500 text-white p-1.5 rounded shadow-sm transition"
                                                    title="Edit Dojo">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                        </path>
                                                    </svg>
                                                </a>

                                                <form action="{{ route('admin.dojos.destroy', $dojo->id) }}"
                                                    method="POST" onsubmit="return confirm('Hapus data dojo ini?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit"
                                                        class="bg-red-500 hover:bg-red-600 text-white p-1.5 rounded shadow-sm transition"
                                                        title="Hapus Dojo">
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
                                        <td colspan="5" class="px-6 py-12 text-center">
                                            <div class="flex flex-col items-center">
                                                <svg class="w-12 h-12 text-gray-200 mb-3" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                                    </path>
                                                </svg>
                                                <span class="text-gray-400 italic">Belum ada data dojo yang
                                                    terdaftar.</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if ($dojos->hasPages())
                        <div class="mt-6">
                            {{ $dojos->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
