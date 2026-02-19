<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $title }}
            </h2>
            <span class="px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full text-xs font-bold uppercase">
                Role: {{ str_replace('_', ' ', $role) }}
            </span>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-b-4 border-blue-500">
                    <div class="text-gray-500 text-sm font-medium">Total Anggota</div>
                    <div class="text-2xl font-bold">{{ number_format($stats['total_members']) }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-b-4 border-green-500">
                    <div class="text-gray-500 text-sm font-medium">Anggota Aktif</div>
                    <div class="text-2xl font-bold">{{ number_format($stats['active_members']) }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-b-4 border-yellow-500">
                    <div class="text-gray-500 text-sm font-medium">Tagihan Pending</div>
                    <div class="text-2xl font-bold">{{ number_format($stats['pending_payments']) }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-b-4 border-indigo-500">
                    <div class="text-gray-500 text-sm font-medium">Total Kas Iuran</div>
                    <div class="text-2xl font-bold">Rp {{ number_format($stats['total_revenue']) }}</div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-bold text-lg">Daftar Anggota Murid</h3>
                        {{-- Tombol tambah hanya muncul untuk Admin Dojo atau level yang diizinkan --}}
                        @if ($role === 'admin_dojo' || $role === 'pb')
                            <a href="#"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Tambah Anggota
                            </a>
                        @endif
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3">Nama</th>
                                    <th class="px-6 py-3">Sabuk</th>
                                    {{-- Kolom dinamis berdasarkan Role --}}
                                    @if ($role === 'pb')
                                        <th class="px-6 py-3">Provinsi</th>
                                    @endif
                                    @if ($role === 'pb' || $role === 'pengprov')
                                        <th class="px-6 py-3">Kota/Cabang</th>
                                    @endif
                                    @if ($role !== 'admin_dojo')
                                        <th class="px-6 py-3">Dojo</th>
                                    @endif
                                    <th class="px-6 py-3">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse($members as $member)
                                    <tr class="bg-white hover:bg-gray-50">
                                        <td class="px-6 py-4 font-medium text-gray-900">{{ $member->name }}</td>
                                        <td class="px-6 py-4">
                                            <span
                                                class="px-2 py-1 rounded text-xs font-semibold bg-gray-200 text-gray-800">
                                                {{ $member->beltLevel->name ?? '-' }}
                                            </span>
                                        </td>

                                        @if ($role === 'pb')
                                            <td class="px-6 py-4">{{ $member->province->name ?? '-' }}</td>
                                        @endif
                                        @if ($role === 'pb' || $role === 'pengprov')
                                            <td class="px-6 py-4">{{ $member->city->name ?? '-' }}</td>
                                        @endif
                                        @if ($role !== 'admin_dojo')
                                            <td class="px-6 py-4">{{ $member->dojo->name ?? '-' }}</td>
                                        @endif

                                        <td class="px-6 py-4">
                                            @if ($member->is_active)
                                                <span
                                                    class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Aktif</span>
                                            @else
                                                <span
                                                    class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Non-Aktif</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center">Belum ada data anggota.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $members->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
