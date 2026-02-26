<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div>
                <h2 class="font-black text-xl sm:text-3xl text-slate-900 leading-tight tracking-tighter uppercase">
                    {{ __('Manajemen') }} <span class="text-red-600">Dojo</span>
                </h2>
                <p class="text-[8px] sm:text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mt-0.5">
                    @if (auth()->user()->role === 'pengprov')
                        Wilayah {{ auth()->user()->province->name ?? '' }}
                    @elseif (auth()->user()->role === 'pengcab')
                        Cabang {{ auth()->user()->city->name ?? '' }}
                    @else
                        Pusat Kendali Data Dojo
                    @endif
                </p>
            </div>
        </div>
    </x-slot>

    {{-- Floating Action Button for Mobile --}}
    <div class="fixed bottom-6 right-6 z-50 md:hidden">
        <a href="{{ route('admin.dojos.create') }}"
            class="flex items-center justify-center w-14 h-14 bg-red-600 text-white rounded-2xl shadow-[0_10px_25px_rgba(220,38,38,0.4)] active:scale-90 transition-transform border-b-4 border-red-800">
            <svg class="w-7 h-7 stroke-[3]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="square" stroke-linejoin="square" d="M12 4v16m8-8H4"></path>
            </svg>
        </a>
    </div>

    <div class="py-6 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Toolbar Desktop --}}
            <div class="hidden md:flex justify-between items-end mb-6">
                <div>
                    <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest">Inventaris Dojo Aktif</h3>
                    <p class="text-[10px] text-slate-400 font-bold uppercase mt-1">Total: {{ $dojos->count() }} Dojo
                        Terverifikasi</p>
                </div>

                <a href="{{ route('admin.dojos.create') }}"
                    class="inline-flex items-center bg-slate-900 hover:bg-red-600 text-white px-5 py-3 rounded-xl text-[10px] font-black transition-all duration-300 hover:-translate-y-1 shadow-lg shadow-slate-200 uppercase tracking-[0.15em] border-b-4 border-slate-700 hover:border-red-800">
                    <svg class="w-4 h-4 me-2 stroke-[3]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="square" stroke-linejoin="square" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Tambah Dojo Baru
                </a>
            </div>

            {{-- 1. NOTIFIKASI SK --}}
            @if ($warningDojos->count() > 0)
                <div
                    class="mb-6 relative overflow-hidden bg-white border-2 border-red-50 rounded-2xl p-4 sm:p-6 shadow-sm">
                    <div class="relative flex items-center gap-4 sm:gap-5">
                        <div class="bg-red-600 p-2.5 rounded-xl flex-shrink-0 shadow-lg shadow-red-200">
                            <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="square" stroke-linejoin="square" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xs sm:text-sm font-black text-slate-900 uppercase tracking-tight">Status SK:
                                Perlu Atensi</h3>
                            <p class="text-[10px] sm:text-xs text-slate-500 mt-0.5 font-bold">
                                <span class="text-red-600 underline decoration-2">{{ $warningDojos->count() }}
                                    Dojo</span> masa berlaku SK akan segera berakhir.
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- 2. SECTION FILTER --}}
            <div class="mb-8 bg-white p-4 sm:p-6 rounded-2xl border-2 border-slate-100 shadow-sm">
                <form action="{{ route('admin.dojos.index') }}" method="GET" class="flex flex-col gap-4">
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Cari Nama Dojo..."
                            class="w-full bg-slate-50 border-none rounded-xl py-3 px-4 text-xs font-bold uppercase tracking-widest focus:ring-2 focus:ring-red-600/20 placeholder:text-slate-400">
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        <select name="city_id" onchange="this.form.submit()"
                            class="bg-slate-50 border-none rounded-xl text-[10px] font-black uppercase tracking-widest focus:ring-2 focus:ring-red-600/20 cursor-pointer text-slate-700">
                            <option value="">Semua Wilayah</option>
                            @foreach ($cities as $city)
                                <option value="{{ $city->id }}"
                                    {{ request('city_id') == $city->id ? 'selected' : '' }}>{{ $city->name }}
                                </option>
                            @endforeach
                        </select>
                        <button type="submit"
                            class="bg-slate-900 text-white rounded-xl text-[10px] font-black uppercase tracking-[0.1em] hover:bg-black active:scale-95 transition-all py-3">
                            Terapkan
                        </button>
                        <a href="{{ route('admin.dojos.index') }}"
                            class="col-span-2 md:col-span-1 flex items-center justify-center bg-slate-100 text-slate-500 rounded-xl text-[10px] font-black uppercase hover:bg-slate-200 py-3 transition-colors">
                            Reset Filter
                        </a>
                    </div>
                </form>
            </div>

            {{-- 3. VIEW DESKTOP --}}
            <div
                class="hidden md:block bg-white rounded-[1.5rem] border-2 border-slate-100 shadow-xl shadow-slate-200/50 overflow-hidden">
                <table class="min-w-full">
                    <thead>
                        <tr class="bg-slate-900 text-white">
                            <th class="px-8 py-5 text-left text-[10px] font-black uppercase tracking-[0.2em]">Info Dojo
                            </th>
                            <th class="px-8 py-5 text-left text-[10px] font-black uppercase tracking-[0.2em]">Wilayah
                            </th>
                            <th class="px-8 py-5 text-left text-[10px] font-black uppercase tracking-[0.2em]">Sensei
                            </th>
                            <th class="px-8 py-5 text-center text-[10px] font-black uppercase tracking-[0.2em]">Anggota
                            </th>
                            <th class="px-8 py-5 text-center text-[10px] font-black uppercase tracking-[0.2em]">Status &
                                Periode SK</th>
                            <th class="px-8 py-5 text-right text-[10px] font-black uppercase tracking-[0.2em]">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($dojos as $dojo)
                            <tr class="hover:bg-slate-50 transition-all group">
                                <td class="px-8 py-6">
                                    <div class="flex flex-col">
                                        <span
                                            class="font-black text-slate-900 text-sm uppercase group-hover:text-red-600 transition-colors">{{ $dojo->name }}</span>
                                        <span
                                            class="text-[9px] font-black text-slate-400 mt-1 uppercase tracking-tighter">ID:
                                            #{{ $dojo->id }}</span>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex flex-col">
                                        <span
                                            class="text-xs font-bold text-slate-700 italic">{{ $dojo->city->name ?? '-' }}</span>
                                        <span
                                            class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter">{{ $dojo->city->province->name ?? '' }}</span>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <span
                                        class="text-xs font-black text-slate-600 uppercase italic">{{ $dojo->sensei_name ?? '-' }}</span>
                                </td>
                                <td class="px-8 py-6 text-center">
                                    <div
                                        class="inline-flex flex-col items-center justify-center bg-slate-50 border border-slate-100 px-3 py-1.5 rounded-xl min-w-[60px]">
                                        <span
                                            class="text-sm font-black text-slate-900 leading-none">{{ number_format($dojo->members_count ?? 0) }}</span>
                                        <span
                                            class="text-[7px] font-black text-slate-400 uppercase tracking-widest mt-0.5">Personel</span>
                                    </div>
                                </td>
                                <td class="px-8 py-6 text-center">
                                    <div class="flex flex-col items-center gap-1.5">
                                        @include('admin.dojos._badge_status')
                                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">
                                            s/d
                                            {{ $dojo->sk_expiry_date ? \Carbon\Carbon::parse($dojo->sk_expiry_date)->format('d/m/Y') : '-' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <div class="flex justify-end gap-2">
                                        @include('admin.dojos._action_buttons')
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6"
                                    class="py-20 text-center italic text-slate-400 font-bold uppercase tracking-widest text-sm">
                                    Belum ada data dojo</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- 4. VIEW MOBILE --}}
            <div class="md:hidden space-y-5">
                @foreach ($dojos as $dojo)
                    <div
                        class="relative bg-white rounded-[2rem] border border-slate-200 shadow-sm overflow-hidden active:scale-[0.98] transition-transform">
                        {{-- Top Section --}}
                        <div class="p-5 pb-4">
                            <div class="flex justify-between items-start gap-4 mb-3">
                                <div class="flex-1">
                                    <h4
                                        class="font-black text-slate-900 uppercase text-base leading-tight tracking-tight">
                                        {{ $dojo->name }}
                                    </h4>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-600"></span>
                                        <span
                                            class="text-[9px] font-black text-slate-400 uppercase tracking-widest italic">
                                            {{ $dojo->city->name ?? 'Wilayah Tidak Terdata' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-shrink-0 flex flex-col items-end">
                                    @include('admin.dojos._badge_status')
                                    <span
                                        class="text-[8px] font-black text-slate-400 mt-1 uppercase tracking-tighter italic">
                                        s/d
                                        {{ $dojo->sk_expiry_date ? \Carbon\Carbon::parse($dojo->sk_expiry_date)->format('d/m/y') : '-' }}
                                    </span>
                                </div>
                            </div>

                            {{-- Info Grid --}}
                            <div class="grid grid-cols-2 gap-4">
                                <div
                                    class="bg-slate-50 p-3 rounded-2xl border border-slate-100 flex items-center gap-3">
                                    <div class="p-2 bg-white rounded-lg text-slate-400 shadow-sm">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                    <div class="min-w-0">
                                        <p
                                            class="text-[7px] font-black text-slate-400 uppercase tracking-widest mb-0.5">
                                            Sensei</p>
                                        <p
                                            class="text-[10px] font-black text-slate-700 truncate uppercase italic leading-tight">
                                            {{ $dojo->sensei_name ?? '-' }}</p>
                                    </div>
                                </div>
                                <div
                                    class="bg-slate-50 p-3 rounded-2xl border border-slate-100 flex items-center gap-3">
                                    <div class="p-2 bg-white rounded-lg text-red-600 shadow-sm">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                    </div>
                                    <div class="min-w-0">
                                        <p
                                            class="text-[7px] font-black text-slate-400 uppercase tracking-widest mb-0.5">
                                            Anggota</p>
                                        <p class="text-[10px] font-black text-slate-900 uppercase tracking-tighter">
                                            {{ number_format($dojo->members_count ?? 0) }} Personel</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Footer Action Area --}}
                        <div class="px-5 py-3 bg-slate-900 flex justify-between items-center">
                            <div class="flex items-center gap-2">
                                <div class="w-1 h-4 bg-red-600 rounded-full"></div>
                                <div>
                                    <p
                                        class="text-[7px] font-black text-slate-500 uppercase tracking-widest leading-none">
                                        Nomor SK</p>
                                    <p class="text-[9px] font-bold text-white uppercase mt-0.5">
                                        {{ $dojo->sk_number ?? 'Belum Terbit' }}</p>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                @include('admin.dojos._action_buttons')
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="h-20 md:hidden"></div>

            <div class="mt-6">
                {{ $dojos->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
