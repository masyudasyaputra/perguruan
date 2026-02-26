<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div>
                <h2 class="font-black text-xl sm:text-3xl text-slate-900 leading-tight tracking-tighter uppercase">
                    {{ __('Manajemen') }} <span class="text-red-600">User</span>
                </h2>
                <p class="text-[8px] sm:text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mt-0.5">
                    Kelola akses akun sistem
                </p>
            </div>
        </div>
    </x-slot>

    {{-- Floating Action Button for Mobile --}}
    <div class="fixed bottom-6 right-6 z-50 md:hidden">
        <a href="{{ route('admin.users.create') }}"
            class="flex items-center justify-center w-14 h-14 bg-red-600 text-white rounded-2xl shadow-[0_10px_25px_rgba(220,38,38,0.4)] active:scale-90 transition-transform border-b-4 border-red-800">
            <svg class="w-7 h-7 stroke-[3]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="square" stroke-linejoin="square" d="M12 4v16m8-8H4"></path>
            </svg>
        </a>
    </div>

    <div class="py-6 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5">

            {{-- Toolbar Desktop (ikuti pola Dojo/Pengurus) --}}
            <div class="hidden md:flex justify-between items-end">
                <div>
                    <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest">
                        Daftar Akun Sistem
                    </h3>
                    <p class="text-[10px] text-slate-400 font-bold uppercase mt-1">
                        Total: {{ $users->total() }} Akun Terdaftar
                    </p>
                </div>

                <a href="{{ route('admin.users.create') }}"
                    class="inline-flex items-center bg-slate-900 hover:bg-red-600 text-white px-5 py-3 rounded-xl text-[10px] font-black transition-all duration-300 hover:-translate-y-1 shadow-lg shadow-slate-200 uppercase tracking-[0.15em] border-b-4 border-slate-700 hover:border-red-800">
                    <svg class="w-4 h-4 me-2 stroke-[3]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="square" stroke-linejoin="square" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Tambah User Baru
                </a>
            </div>

            {{-- FILTER / SEARCH (AUTO SUBMIT) --}}
            <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm p-4 sm:p-5">
                <form id="filterForm" method="GET" action="{{ route('admin.users.index') }}" class="space-y-3">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
                        {{-- Search --}}
                        <div class="md:col-span-6">
                            <label class="text-[8px] font-black text-slate-400 uppercase tracking-widest block mb-1">
                                Cari Nama / Email
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-4 flex items-center text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </span>
                                <input id="searchInput" type="text" name="search" value="{{ request('search') }}"
                                    class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl py-3 pl-12 pr-4 text-sm font-bold text-slate-700 focus:ring-0 focus:border-slate-900 transition-all"
                                    placeholder="Ketik nama atau email...">
                            </div>
                            <p class="mt-2 text-[10px] text-slate-400 font-bold uppercase tracking-widest">
                                * otomatis mencari saat berhenti mengetik
                            </p>
                        </div>

                        {{-- Role --}}
                        <div class="md:col-span-3">
                            <label class="text-[8px] font-black text-slate-400 uppercase tracking-widest block mb-1">
                                Role
                            </label>
                            <select id="roleSelect" name="role"
                                class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl py-3 px-4 text-sm font-bold text-slate-700 focus:ring-0 focus:border-slate-900 transition-all uppercase">
                                <option value="">Semua</option>
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

                        {{-- Province --}}
                        <div class="md:col-span-3">
                            <label class="text-[8px] font-black text-slate-400 uppercase tracking-widest block mb-1">
                                Provinsi
                            </label>
                            <select id="provinceSelect" name="province_id"
                                class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl py-3 px-4 text-sm font-bold text-slate-700 focus:ring-0 focus:border-slate-900 transition-all uppercase">
                                <option value="">Semua</option>
                                @foreach (\App\Models\Province::orderBy('name')->get() as $prov)
                                    <option value="{{ $prov->id }}"
                                        {{ request('province_id') == $prov->id ? 'selected' : '' }}>
                                        {{ $prov->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- footer kecil --}}
                    <div class="flex items-center justify-between gap-3">
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">
                            Total: <span class="text-slate-700">{{ $users->total() }}</span>
                        </p>

                        @if (request('search') || request('role') || request('province_id'))
                            <a href="{{ route('admin.users.index') }}"
                                class="inline-flex items-center justify-center bg-white border-2 border-slate-200 text-slate-500 px-4 py-2.5 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-50 transition-all">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            {{-- DESKTOP TABLE --}}
            <div
                class="hidden md:block bg-white rounded-[2rem] border-2 border-slate-100 shadow-xl shadow-slate-200/50 overflow-hidden">
                <table class="min-w-full">
                    <thead>
                        <tr class="bg-slate-900 text-white">
                            <th class="px-8 py-5 text-left text-[10px] font-black uppercase tracking-[0.2em]">Pengguna
                            </th>
                            <th class="px-8 py-5 text-left text-[10px] font-black uppercase tracking-[0.2em]">Role &
                                Akses</th>
                            <th class="px-8 py-5 text-left text-[10px] font-black uppercase tracking-[0.2em]">Wilayah /
                                Unit</th>
                            <th class="px-8 py-5 text-center text-[10px] font-black uppercase tracking-[0.2em]">Status
                            </th>
                            <th class="px-8 py-5 text-right text-[10px] font-black uppercase tracking-[0.2em]">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @forelse ($users as $user)
                            @php
                                $roleColors = [
                                    'pb' => 'bg-purple-100 text-purple-700 border-purple-200',
                                    'pengprov' => 'bg-blue-100 text-blue-700 border-blue-200',
                                    'pengcab' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                    'admin_dojo' => 'bg-amber-100 text-amber-700 border-amber-200',
                                    'penguji' => 'bg-rose-100 text-rose-700 border-rose-200',
                                    'member' => 'bg-slate-100 text-slate-700 border-slate-200',
                                ];

                                $allRoles = collect($user->roles ?? [])
                                    ->push($user->role)
                                    ->filter()
                                    ->unique()
                                    ->values();
                            @endphp

                            <tr class="hover:bg-slate-50 transition-all group">
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-4">
                                        <div
                                            class="h-11 w-11 shrink-0 rounded-2xl bg-slate-900 text-white flex items-center justify-center text-sm font-black shadow-sm">
                                            {{ mb_substr($user->name, 0, 1) }}
                                        </div>
                                        <div class="min-w-0">
                                            <div
                                                class="text-sm font-black text-slate-900 uppercase tracking-tight truncate">
                                                {{ $user->name }}
                                            </div>
                                            <div class="text-[10px] text-slate-400 font-bold tracking-tight truncate">
                                                {{ $user->email }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-8 py-6">
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach ($allRoles as $r)
                                            <span
                                                class="inline-flex items-center px-2 py-1 rounded-xl border text-[9px] font-black uppercase tracking-widest {{ $roleColors[$r] ?? 'bg-slate-100 text-slate-700 border-slate-200' }}">
                                                {{ str_replace('_', ' ', $r) }}
                                            </span>
                                        @endforeach
                                    </div>
                                </td>

                                <td class="px-8 py-6">
                                    <div class="text-xs font-black text-slate-700 uppercase leading-tight">
                                        {{ $user->province->name ?? 'NASIONAL' }}
                                    </div>
                                    <div class="text-[10px] text-slate-500 mt-1 font-bold uppercase tracking-tight">
                                        @if ($user->role === 'pengcab')
                                            Cabang: {{ $user->city->name ?? 'Semua Cabang' }}
                                        @elseif(in_array($user->role, ['admin_dojo', 'member', 'penguji']))
                                            {{ $user->dojo->name ?? ($user->city->name ?? 'Unit Belum Set') }}
                                        @else
                                            Akses Wilayah Utama
                                        @endif
                                    </div>
                                </td>

                                <td class="px-8 py-6 text-center">
                                    <span
                                        class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest border
                                        {{ $user->is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-rose-50 text-rose-700 border-rose-100' }}">
                                        <span
                                            class="w-1.5 h-1.5 rounded-full {{ $user->is_active ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                                        {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>

                                <td class="px-8 py-6">
                                    <div class="flex justify-end items-center gap-2">
                                        <a href="{{ route('admin.users.edit', $user->id) }}"
                                            class="inline-flex items-center justify-center px-4 py-2 rounded-2xl bg-white border-2 border-slate-200 text-slate-700 text-[10px] font-black uppercase tracking-widest hover:bg-slate-900 hover:text-white hover:border-slate-900 transition-all">
                                            Edit
                                        </a>

                                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST"
                                            onsubmit="return confirm('Hapus user?')">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="inline-flex items-center justify-center px-4 py-2 rounded-2xl bg-white border-2 border-slate-200 text-rose-600 text-[10px] font-black uppercase tracking-widest hover:bg-rose-600 hover:text-white hover:border-rose-700 transition-all">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5"
                                    class="py-16 text-center italic text-slate-400 font-bold uppercase tracking-widest text-sm">
                                    Data tidak ditemukan
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- MOBILE CARD --}}
            <div class="md:hidden space-y-5">
                @forelse ($users as $user)
                    @php
                        $allRoles = collect($user->roles ?? [])
                            ->push($user->role)
                            ->filter()
                            ->unique()
                            ->values();
                    @endphp

                    <div
                        class="relative bg-white rounded-[2rem] border border-slate-200 shadow-sm overflow-hidden active:scale-[0.98] transition-transform">
                        <div class="p-5 pb-4">
                            <div class="flex justify-between items-start gap-4 mb-3">
                                <div class="flex-1 min-w-0">
                                    <h4
                                        class="font-black text-slate-900 uppercase text-base leading-tight tracking-tight">
                                        {{ $user->name }}
                                    </h4>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-600"></span>
                                        <span
                                            class="text-[9px] font-black text-slate-400 uppercase tracking-widest italic truncate">
                                            {{ $user->email }}
                                        </span>
                                    </div>
                                </div>

                                <div class="flex-shrink-0 flex flex-col items-end">
                                    <span
                                        class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest border
                                        {{ $user->is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-rose-50 text-rose-700 border-rose-100' }}">
                                        {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-slate-50 p-3 rounded-2xl border border-slate-100">
                                    <p class="text-[7px] font-black text-slate-400 uppercase tracking-widest mb-0.5">
                                        Roles</p>
                                    <p class="text-[10px] font-black text-slate-700 uppercase italic leading-tight">
                                        {{ $allRoles->map(fn($r) => str_replace('_', ' ', $r))->implode(', ') }}
                                    </p>
                                </div>

                                <div class="bg-slate-50 p-3 rounded-2xl border border-slate-100">
                                    <p class="text-[7px] font-black text-slate-400 uppercase tracking-widest mb-0.5">
                                        Wilayah</p>
                                    <p
                                        class="text-[10px] font-black text-slate-700 uppercase italic leading-tight truncate">
                                        {{ $user->province->name ?? 'Nasional' }}
                                    </p>
                                </div>
                            </div>

                            <div class="mt-4 bg-slate-50 p-4 rounded-2xl border border-slate-100">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p
                                            class="text-[7px] font-black text-slate-400 uppercase tracking-widest leading-none">
                                            Unit</p>
                                        <p class="text-[9px] font-bold text-slate-700 uppercase mt-0.5">
                                            {{ $user->dojo->name ?? ($user->city->name ?? 'Unit Belum Set') }}
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <p
                                            class="text-[7px] font-black text-slate-400 uppercase tracking-widest leading-none">
                                            Status</p>
                                        <p class="text-[9px] font-bold text-slate-900 uppercase mt-0.5">
                                            {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="px-5 py-3 bg-slate-900 flex justify-between items-center">
                            <div class="flex items-center gap-2">
                                <div class="w-1 h-4 bg-red-600 rounded-full"></div>
                                <div>
                                    <p
                                        class="text-[7px] font-black text-slate-500 uppercase tracking-widest leading-none">
                                        Aksi</p>
                                    <p class="text-[9px] font-bold text-white uppercase mt-0.5">Kelola User</p>
                                </div>
                            </div>

                            <div class="flex gap-2">
                                <a href="{{ route('admin.users.edit', $user->id) }}"
                                    class="px-4 py-2 bg-white text-slate-900 rounded-xl text-[9px] font-black uppercase tracking-widest hover:bg-red-600 hover:text-white transition-all">
                                    Edit
                                </a>
                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST"
                                    onsubmit="return confirm('Hapus user?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="px-4 py-2 bg-rose-50 text-rose-700 rounded-xl text-[9px] font-black uppercase tracking-widest hover:bg-rose-600 hover:text-white transition-all">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div
                        class="py-12 text-center text-slate-300 font-black uppercase text-[10px] italic bg-white rounded-[2rem] border border-dashed">
                        Data tidak ditemukan
                    </div>
                @endforelse
            </div>

            <div class="pb-20 md:pb-0">
                {{ $users->links() }}
            </div>
        </div>
    </div>

    {{-- AUTO SUBMIT SCRIPT --}}
    <script>
        (function() {
            const form = document.getElementById('filterForm');
            const search = document.getElementById('searchInput');
            const role = document.getElementById('roleSelect');
            const prov = document.getElementById('provinceSelect');
            if (!form) return;

            const clearPageParam = () => {
                const url = new URL(window.location.href);
                if (url.searchParams.has('page')) url.searchParams.delete('page');
                window.history.replaceState({}, '', url.toString());
            };

            const debounce = (fn, delay = 400) => {
                let t;
                return (...args) => {
                    clearTimeout(t);
                    t = setTimeout(() => fn(...args), delay);
                };
            };

            const submitNow = () => {
                clearPageParam();
                form.submit();
            };

            if (search) {
                const debouncedSubmit = debounce(submitNow, 400);

                search.addEventListener('input', () => debouncedSubmit());
                search.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        submitNow();
                    }
                });
            }

            if (role) role.addEventListener('change', submitNow);
            if (prov) prov.addEventListener('change', submitNow);
        })();
    </script>
</x-app-layout>
