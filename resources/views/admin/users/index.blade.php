<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    {{ __('Manajemen User') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1 italic md:not-italic">Kelola akses akun sistem</p>
            </div>
            <a href="{{ route('admin.users.create') }}"
                class="w-full md:w-auto inline-flex justify-center items-center px-6 py-3 bg-indigo-600 rounded-2xl font-bold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition shadow-lg shadow-indigo-200 active:scale-95">
                <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                </svg>
                Tambah User
            </a>
        </div>
    </x-slot>

    <div class="py-6 md:py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Search Bar --}}
            <div class="mb-6">
                <form method="GET" action="{{ route('admin.users.index') }}" class="space-y-4">
                    <div class="flex flex-col md:flex-row gap-4">
                        {{-- Search Input --}}
                        <div class="flex-1 relative group">
                            <span class="absolute inset-y-0 left-0 flex items-center ps-4 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400 group-focus-within:text-indigo-500 transition-colors"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </span>
                            <input type="text" name="search" value="{{ request('search') }}"
                                class="bg-white border-none shadow-sm text-gray-900 text-sm rounded-2xl focus:ring-2 focus:ring-indigo-500 block w-full ps-12 p-4 transition-all"
                                placeholder="Cari nama atau email...">
                        </div>

                        {{-- Filter Role --}}
                        <div class="w-full md:w-48">
                            <select name="role" onchange="this.form.submit()"
                                class="bg-white border-none shadow-sm text-gray-700 text-sm rounded-2xl focus:ring-2 focus:ring-indigo-500 block w-full p-4 transition-all">
                                <option value="">Semua Role</option>
                                <option value="pb" {{ request('role') == 'pb' ? 'selected' : '' }}>PB (Pusat)
                                </option>
                                <option value="pengprov" {{ request('role') == 'pengprov' ? 'selected' : '' }}>Pengprov
                                </option>
                                <option value="pengcab" {{ request('role') == 'pengcab' ? 'selected' : '' }}>Pengcab
                                </option>
                                <option value="admin_dojo" {{ request('role') == 'admin_dojo' ? 'selected' : '' }}>Admin
                                    Dojo</option>
                                <option value="penguji" {{ request('role') == 'penguji' ? 'selected' : '' }}>Penguji
                                </option>
                                <option value="member" {{ request('role') == 'member' ? 'selected' : '' }}>Member
                                </option>
                            </select>
                        </div>

                        {{-- Filter Wilayah --}}
                        <div class="w-full md:w-64">
                            <select name="province_id" onchange="this.form.submit()"
                                class="bg-white border-none shadow-sm text-gray-700 text-sm rounded-2xl focus:ring-2 focus:ring-indigo-500 block w-full p-4 transition-all">
                                <option value="">Semua Provinsi</option>
                                @foreach (\App\Models\Province::orderBy('name')->get() as $prov)
                                    <option value="{{ $prov->id }}"
                                        {{ request('province_id') == $prov->id ? 'selected' : '' }}>
                                        {{ $prov->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        @if (request('search') || request('role') || request('province_id'))
                            <a href="{{ route('admin.users.index') }}"
                                class="inline-flex items-center justify-center px-6 py-4 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-2xl transition-all shadow-sm group">
                                <svg class="w-5 h-5 group-hover:rotate-180 transition-transform duration-500"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                    </path>
                                </svg>
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Desktop Table View --}}
            <div class="hidden md:block bg-white shadow-sm rounded-[2rem] border border-gray-100 overflow-hidden">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50/50 border-b border-gray-50">
                        <tr class="text-xs text-gray-400 uppercase tracking-widest">
                            <th class="px-6 py-5 font-bold">Pengguna</th>
                            <th class="px-6 py-5 font-bold">Role & Akses</th>
                            <th class="px-6 py-5 font-bold">Wilayah / Unit</th>
                            <th class="px-6 py-5 font-bold text-center">Status</th>
                            <th class="px-6 py-5 font-bold text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse ($users as $user)
                            <tr class="hover:bg-indigo-50/30 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div
                                            class="h-10 w-10 shrink-0 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-sm font-bold shadow-sm">
                                            {{ substr($user->name, 0, 1) }}
                                        </div>
                                        <div class="ms-4">
                                            <div class="text-sm font-bold text-gray-900">{{ $user->name }}</div>
                                            <div class="text-xs text-gray-400 font-medium">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1">
                                        @php
                                            $roleColors = [
                                                'pb' => 'bg-purple-100 text-purple-700 border-purple-200',
                                                'pengprov' => 'bg-blue-100 text-blue-700 border-blue-200',
                                                'pengcab' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                                'admin_dojo' => 'bg-amber-100 text-amber-700 border-amber-200',
                                                'penguji' => 'bg-rose-100 text-rose-700 border-rose-200',
                                                'member' => 'bg-slate-100 text-slate-700 border-slate-200',
                                            ];

                                            // Gabungkan role utama dan roles tambahan agar unik
                                            $allRoles = collect($user->roles ?? [])
                                                ->push($user->role)
                                                ->unique();
                                        @endphp

                                        @foreach ($allRoles as $r)
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded-md border text-[9px] font-black uppercase tracking-tight {{ $roleColors[$r] ?? 'bg-gray-100 text-gray-700 border-gray-200' }}">
                                                {{ str_replace('_', ' ', $r) }}
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-xs font-bold text-gray-700 uppercase leading-tight">
                                        {{ $user->province->name ?? 'NASIONAL' }}
                                    </div>
                                    <div class="text-[10px] text-indigo-500 mt-1 font-medium italic">
                                        @if ($user->role === 'pengcab')
                                            Cabang: {{ $user->city->name ?? 'Semua Cabang' }}
                                        @elseif(in_array($user->role, ['admin_dojo', 'member', 'penguji']))
                                            {{ $user->dojo->name ?? ($user->city->name ?? 'Unit Belum Set') }}
                                        @else
                                            Akses Wilayah Utama
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="h-2.5 w-2.5 rounded-full {{ $user->is_active ? 'bg-green-500 ring-green-100' : 'bg-red-500 ring-red-100' }} inline-block ring-4"></span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex justify-center items-center gap-3">
                                        <a href="{{ route('admin.users.edit', $user->id) }}"
                                            class="group relative inline-flex items-center justify-center p-2.5 bg-white text-indigo-600 rounded-xl shadow-sm border border-indigo-50 hover:bg-indigo-600 hover:text-white transition-all duration-300">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                </path>
                                            </svg>
                                        </a>
                                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST"
                                            class="inline" onsubmit="return confirm('Hapus user?')">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="group relative inline-flex items-center justify-center p-2.5 bg-white text-rose-500 rounded-xl shadow-sm border border-rose-50 hover:bg-rose-500 hover:text-white transition-all duration-300">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="1.8"
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
                                    class="px-6 py-12 text-center text-gray-400 italic font-medium tracking-wide">Data
                                    tidak ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mobile Card View --}}
            <div class="grid grid-cols-1 gap-4 md:hidden">
                @forelse ($users as $user)
                    <div
                        class="bg-white p-5 rounded-[2rem] shadow-sm border border-gray-100 relative overflow-hidden transition-all">
                        <div class="flex items-center mb-4">
                            <div
                                class="h-12 w-12 shrink-0 rounded-2xl bg-indigo-600 flex items-center justify-center text-white text-lg font-bold">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                            <div class="ms-4">
                                <div class="text-base font-bold text-gray-900 leading-tight">{{ $user->name }}</div>
                                <div class="text-xs text-gray-400 font-medium truncate w-40">{{ $user->email }}</div>
                            </div>
                            <div class="absolute top-6 right-6">
                                <span
                                    class="h-3 w-3 rounded-full {{ $user->is_active ? 'bg-green-500 ring-green-100' : 'bg-red-500 ring-red-100' }} inline-block ring-4"></span>
                            </div>
                        </div>

                        <div class="flex flex-col gap-2 bg-gray-50 rounded-2xl p-4 mb-4">
                            <div class="flex justify-between items-start">
                                <p class="text-[10px] text-gray-400 uppercase font-bold tracking-widest">Akses System
                                </p>
                                <div class="flex flex-col items-end gap-1">
                                    @php
                                        $allRoles = collect($user->roles ?? [])
                                            ->push($user->role)
                                            ->unique();
                                    @endphp
                                    @foreach ($allRoles as $r)
                                        <span
                                            class="text-[10px] font-black text-indigo-600 uppercase">{{ str_replace('_', ' ', $r) }}</span>
                                    @endforeach
                                </div>
                            </div>
                            <div class="border-t border-gray-200/50 my-1"></div>
                            <div class="flex justify-between">
                                <p class="text-[10px] text-gray-400 uppercase font-bold tracking-widest">Wilayah</p>
                                <div class="text-right">
                                    <p class="text-xs font-bold text-gray-800 uppercase leading-none">
                                        {{ $user->province->name ?? 'Nasional' }}</p>
                                    <p class="text-[9px] text-indigo-500 italic font-medium mt-1">
                                        {{ $user->dojo->name ?? ($user->city->name ?? '-') }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex gap-3">
                            <a href="{{ route('admin.users.edit', $user->id) }}"
                                class="flex-1 inline-flex items-center justify-center py-3 bg-white text-indigo-600 rounded-xl shadow-sm border border-indigo-50 font-bold text-xs uppercase transition-all active:scale-95">
                                Edit
                            </a>
                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST"
                                class="flex-1" onsubmit="return confirm('Hapus user?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="w-full inline-flex items-center justify-center py-3 bg-white text-rose-500 rounded-xl shadow-sm border border-rose-50 font-bold text-xs uppercase transition-all active:scale-95">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-10 bg-white rounded-[2rem] text-gray-400 italic">Data tidak ditemukan.
                    </div>
                @endforelse
            </div>

            <div class="mt-6">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
