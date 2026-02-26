<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-3 px-4 sm:px-0">
            <div class="min-w-0">
                <h2 class="font-black text-xl sm:text-3xl text-slate-900 leading-tight tracking-tighter uppercase">
                    {{ $title }}
                </h2>
                <p class="text-[8px] sm:text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mt-1">
                    Dashboard Overview • {{ now()->format('d M Y') }}
                </p>
            </div>

            {{-- <div
                class="hidden sm:inline-flex items-center gap-2 bg-white border-2 border-slate-200 px-4 py-2 rounded-2xl shadow-sm">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                <span class="text-[9px] font-black uppercase tracking-widest text-slate-700">
                    {{ str_replace('_', ' ', $role ?? auth()->user()->role) }}
                </span>
            </div> --}}
        </div>
    </x-slot>

    @php
        // ==========================================================
        // Helpers
        // ==========================================================
        if (!function_exists('beltBadgeClass')) {
            function beltBadgeClass($beltName)
            {
                $n = strtolower($beltName ?? '');
                if (str_contains($n, 'putih')) {
                    return 'bg-white text-slate-700 border-slate-200';
                }
                if (str_contains($n, 'kuning muda')) {
                    return 'bg-yellow-50 text-yellow-800 border-yellow-200';
                }
                if (str_contains($n, 'kuning')) {
                    return 'bg-yellow-400 text-yellow-950 border-yellow-500';
                }
                if (str_contains($n, 'orange')) {
                    return 'bg-orange-500 text-white border-orange-600';
                }
                if (str_contains($n, 'hijau')) {
                    return 'bg-emerald-600 text-white border-emerald-700';
                }
                if (str_contains($n, 'biru')) {
                    return 'bg-blue-600 text-white border-blue-700';
                }
                if (str_contains($n, 'ungu')) {
                    return 'bg-purple-600 text-white border-purple-700';
                }
                if (str_contains($n, 'cokelat') || str_contains($n, 'coklat')) {
                    return 'bg-amber-800 text-white border-amber-900';
                }
                if (str_contains($n, 'hitam')) {
                    return 'bg-slate-900 text-white border-slate-950';
                }
                return 'bg-slate-50 text-slate-700 border-slate-200';
            }
        }

        if (!function_exists('beltHexColor')) {
            function beltHexColor($beltName)
            {
                $n = strtolower($beltName ?? '');
                if (str_contains($n, 'putih')) {
                    return '#E5E7EB';
                }
                if (str_contains($n, 'kuning muda')) {
                    return '#FDE68A';
                }
                if (str_contains($n, 'kuning')) {
                    return '#F59E0B';
                }
                if (str_contains($n, 'orange')) {
                    return '#F97316';
                }
                if (str_contains($n, 'hijau')) {
                    return '#10B981';
                }
                if (str_contains($n, 'biru')) {
                    return '#2563EB';
                }
                if (str_contains($n, 'ungu')) {
                    return '#7C3AED';
                }
                if (str_contains($n, 'cokelat') || str_contains($n, 'coklat')) {
                    return '#92400E';
                }
                if (str_contains($n, 'hitam')) {
                    return '#0F172A';
                }
                return '#64748B';
            }
        }

        // ==========================================================
        // Role & Access
        // (Controller sudah kirim $role sesuai hierarki akses)
        // ==========================================================
        $currentRole = $role ?? auth()->user()->role;
        $isPB = $currentRole === 'pb';
        $isPengprov = $currentRole === 'pengprov';
        $isPengcab = $currentRole === 'pengcab';
        $isAdminDojo = $currentRole === 'admin_dojo';

        // ==========================================================
        // Filter options (SUDAH discope oleh controller)
        // - $provinces: PB -> semua, selain PB -> hanya prov user
        // - $cities: PB -> berdasarkan province_id request (atau kosong), pengprov -> hanya prov user
        // - $dojos: pengprov/pengcab -> sesuai scope, pb -> bisa berdasarkan city_id request (atau kosong)
        // - $beltLevels: dikirim controller
        // ==========================================================
        $beltOptions = $beltLevels ?? collect();
        $dojoOptions = $dojos ?? collect();
        $provinceOptions = $provinces ?? collect();
        $cityOptions = $cities ?? collect();

        // ==========================================================
        // Donut data (beltCounts)
        // Ideal: controller kirim $beltCounts untuk semua hasil filter (tanpa pagination).
        // Fallback: pakai collection halaman ini (pagination)
        // ==========================================================
        $beltCounts =
            $beltCounts ??
            $members
                ->getCollection()
                ->groupBy(fn($m) => $m->beltLevel->name ?? 'Putih')
                ->map(fn($g) => $g->count())
                ->toArray();

        if ($beltOptions && $beltOptions->count()) {
            $orderMap = $beltOptions->pluck('order', 'name')->toArray();
            uksort($beltCounts, function ($a, $b) use ($orderMap) {
                $oa = $orderMap[$a] ?? 999;
                $ob = $orderMap[$b] ?? 999;
                return $oa <=> $ob;
            });
        }

        $beltLabels = array_keys($beltCounts);
        $beltValues = array_values($beltCounts);
        $beltColors = array_map(fn($name) => beltHexColor($name), $beltLabels);
        $totalBeltSlice = array_sum($beltValues) ?: 0;

        // ==========================================================
        // Stats cards
        // ==========================================================
        $statCards = [
            [
                'label' => 'Total Anggota',
                'value' => number_format($stats['total_members'] ?? 0),
                'dot' => 'bg-slate-500',
                'footer' => 'Database Terdaftar',
                'icon' =>
                    'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
            ],
            [
                'label' => 'Status Aktif',
                'value' => number_format($stats['active_members'] ?? 0),
                'dot' => 'bg-emerald-500',
                'footer' => 'Terverifikasi',
                'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
            ],
            [
                'label' => 'Konfirmasi',
                'value' => number_format($stats['pending_payments'] ?? 0),
                'dot' => 'bg-amber-500',
                'footer' => 'Menunggu Review',
                'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
            ],
            [
                'label' => 'Total Kas',
                'value' => 'Rp ' . number_format($stats['total_revenue'] ?? 0, 0, ',', '.'),
                'dot' => 'bg-[#bf953f]',
                'footer' => 'Iuran Terbayar',
                'icon' =>
                    'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                'dark' => true,
            ],
        ];

        // ==========================================================
        // Current request values (for UI)
        // ==========================================================
        $rqProvince = request('province_id');
        $rqCity = request('city_id');
        $rqDojo = request('dojo_id');
        $rqBelt = request('belt_level_id');
        $rqStatus = request('status');
        $rqSearch = request('search');
    @endphp

    <div class="py-4 md:py-7 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            {{-- NOTIFIKASI STATUS DOJO --}}
            @if ($isAdminDojo)
                @if (!Auth::user()->dojo_id)
                    <div
                        class="bg-white rounded-[2rem] p-5 sm:p-6 border-2 border-red-50 shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <div class="flex items-start gap-4">
                            <div class="bg-red-600 p-3 rounded-2xl shadow-lg shadow-red-200 shrink-0">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="square" stroke-linejoin="square" stroke-width="2.5"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm sm:text-base font-black uppercase tracking-tight text-slate-900">
                                    Akun Belum Terhubung
                                </h4>
                                <p class="text-[11px] sm:text-sm font-bold text-slate-600 mt-1">
                                    Anda belum memiliki otoritas pada Dojo manapun. Hubungi Admin Pusat.
                                </p>
                            </div>
                        </div>
                        <a href="#"
                            class="w-full sm:w-auto text-center px-6 py-3 bg-slate-900 hover:bg-red-600 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest transition-all border-b-4 border-slate-700 hover:border-red-800 active:translate-y-1">
                            Hubungi Bantuan
                        </a>
                    </div>
                @else
                    <div
                        class="bg-white rounded-[2rem] p-4 sm:p-5 border border-slate-200 shadow-sm flex items-center justify-between gap-4">
                        <div class="flex items-center gap-4 min-w-0">
                            <div
                                class="h-12 w-12 bg-slate-100 rounded-2xl flex items-center justify-center text-[#bf953f] border border-slate-200 shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="square" stroke-linejoin="square" stroke-width="2.5"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[8px] font-black text-slate-400 uppercase tracking-[0.2em] mb-0.5">
                                    Administrator Dojo</p>
                                <h4 class="text-sm sm:text-lg font-black text-slate-900 uppercase truncate">
                                    {{ Auth::user()->dojo->name ?? 'Dojo Terdaftar' }}
                                </h4>
                            </div>
                        </div>
                        <span
                            class="hidden sm:inline-flex items-center gap-2 text-[10px] font-black text-emerald-600 uppercase bg-emerald-50 px-4 py-2 rounded-full border border-emerald-200 shrink-0">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Aktif
                        </span>
                    </div>
                @endif
            @endif

            {{-- TOP GRID: stats + donut --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
                <div class="lg:col-span-7">
                    <div class="grid grid-cols-2 gap-3 sm:gap-4">
                        @foreach ($statCards as $card)
                            <div
                                class="{{ !empty($card['dark']) ? 'bg-slate-900 border-[#bf953f]/40 text-white' : 'bg-white border-slate-200 text-slate-900' }} rounded-[1.75rem] p-4 sm:p-5 border shadow-sm overflow-hidden">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <p
                                            class="{{ !empty($card['dark']) ? 'text-slate-300' : 'text-slate-400' }} text-[8px] sm:text-[9px] font-black uppercase tracking-widest">
                                            {{ $card['label'] }}
                                        </p>
                                        <h3
                                            class="{{ !empty($card['dark']) ? 'text-white' : 'text-slate-900' }} mt-1 text-xl sm:text-2xl font-black tracking-tight truncate">
                                            {{ $card['value'] }}
                                        </h3>
                                    </div>

                                    <div
                                        class="{{ !empty($card['dark']) ? 'bg-white/10 text-[#bf953f] border-white/10' : 'bg-slate-50 text-slate-400 border-slate-100' }} p-2.5 sm:p-3 rounded-2xl border shrink-0">
                                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="{{ $card['icon'] }}" />
                                        </svg>
                                    </div>
                                </div>

                                <div
                                    class="mt-3 flex items-center text-[8px] sm:text-[9px] font-bold uppercase tracking-widest {{ !empty($card['dark']) ? 'text-slate-300' : 'text-slate-400' }}">
                                    <span class="h-2 w-2 rounded-full {{ $card['dot'] }} mr-2"></span>
                                    {{ $card['footer'] }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="lg:col-span-5">
                    <div class="bg-white rounded-[2rem] border border-slate-200 shadow-sm overflow-hidden h-full">
                        <div class="p-5 sm:p-6">
                            <div class="flex items-start justify-between gap-4">
                                <div class="min-w-0">
                                    <h3
                                        class="text-[11px] sm:text-xs font-black text-slate-900 uppercase tracking-[0.2em]">
                                        Distribusi Sabuk
                                    </h3>
                                    <p class="mt-1 text-[10px] font-bold text-slate-500">
                                        Jumlah anggota per tingkatan
                                    </p>
                                </div>
                                <span
                                    class="shrink-0 inline-flex items-center gap-2 px-4 py-2 rounded-2xl border border-slate-200 bg-slate-50 text-[10px] font-black uppercase tracking-widest text-slate-700">
                                    Total: {{ number_format($totalBeltSlice) }}
                                </span>
                            </div>

                            <div class="mt-4 grid grid-cols-1 sm:grid-cols-12 gap-4 items-center">
                                <div class="sm:col-span-5">
                                    <div class="relative w-full max-w-[320px] mx-auto">
                                        <canvas id="beltDonut" class="w-full h-auto"></canvas>
                                        <div
                                            class="pointer-events-none absolute inset-0 flex items-center justify-center">
                                            <div class="text-center">
                                                <p
                                                    class="text-[9px] font-black text-slate-400 uppercase tracking-widest">
                                                    Total</p>
                                                <p class="text-2xl font-black text-slate-900 tracking-tight">
                                                    {{ number_format($totalBeltSlice) }}
                                                </p>
                                                <p
                                                    class="text-[9px] font-bold text-slate-500 uppercase tracking-widest">
                                                    Member</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="sm:col-span-7">
                                    <div class="grid grid-cols-2 gap-2">
                                        @forelse ($beltCounts as $beltName => $count)
                                            <div class="bg-slate-50 border border-slate-100 rounded-2xl p-3">
                                                <div class="flex items-center justify-between gap-2">
                                                    <div class="min-w-0 flex items-center gap-2">
                                                        <span class="w-3 h-3 rounded-full shrink-0"
                                                            style="background: {{ beltHexColor($beltName) }}"></span>
                                                        <span
                                                            class="inline-flex items-center justify-center px-3 py-1 rounded-2xl text-[9px] font-black uppercase tracking-widest border leading-none {{ beltBadgeClass($beltName) }} truncate">
                                                            {{ strtoupper($beltName) }}
                                                        </span>
                                                    </div>
                                                    <span class="text-[11px] font-black text-slate-900 tabular-nums">
                                                        {{ number_format($count) }}
                                                    </span>
                                                </div>
                                            </div>
                                        @empty
                                            <div
                                                class="col-span-2 py-10 text-center border-2 border-dashed border-slate-100 rounded-2xl">
                                                <p class="text-xs font-black text-slate-300 uppercase tracking-widest">
                                                    Belum ada data sabuk</p>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>

                            <p class="mt-3 text-[10px] text-slate-500 italic">
                                * Donut mengikuti data yang tersedia. Jika ingin full akurat untuk semua hasil filter,
                                kirimkan <span class="font-black">beltCounts</span> dari controller (tanpa pagination).
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- MAIN: LIST + FILTER + TABLE/CARDS --}}
            <div class="bg-white shadow-sm rounded-[2rem] border border-slate-200 overflow-hidden">
                <div class="p-5 sm:p-8">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-5">
                        <div class="min-w-0">
                            <h3
                                class="font-black text-lg sm:text-xl text-slate-900 uppercase tracking-tight flex items-center gap-3">
                                Daftar Anggota
                                <span
                                    class="px-3 py-1 bg-slate-100 text-slate-900 text-[9px] font-black rounded-full border border-slate-200">
                                    {{ $members->total() }} PERSONEL
                                </span>
                            </h3>
                            <p class="text-[11px] font-bold text-slate-500 mt-1">Manajemen database & lisensi sabuk
                                nasional</p>
                        </div>

                        @if (in_array($currentRole, ['admin_dojo', 'pb'], true))
                            @php
                                $canAddMember =
                                    ($currentRole === 'admin_dojo' && Auth::user()->dojo_id) || $currentRole === 'pb';
                            @endphp
                            <a href="{{ $canAddMember ? route('admin.members.create') : '#' }}"
                                @if (!$canAddMember) onclick="alert('Pilih/Hubungkan Dojo terlebih dahulu.'); return false;" @endif
                                class="w-full sm:w-auto inline-flex justify-center items-center px-6 py-3 bg-slate-900 hover:bg-black text-white rounded-2xl font-black text-[10px] uppercase tracking-widest transition-all shadow-lg shadow-slate-200 border-b-4 border-slate-700 active:translate-y-1">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="square" stroke-linejoin="square" stroke-width="3"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                                Tambah Murid
                            </a>
                        @endif
                    </div>

                    {{-- FILTER (menyesuaikan akses role login) --}}
                    <div class="bg-slate-50 rounded-[1.5rem] p-4 border border-slate-200 mb-6">
                        <form id="filterForm" method="GET" action="{{ route('admin.dashboard') }}"
                            class="flex flex-col md:flex-row md:items-center gap-3 md:gap-2">

                            {{-- Search --}}
                            <div class="w-full md:flex-1">
                                <div class="relative group">
                                    <input id="searchInput" type="text" name="search"
                                        value="{{ $rqSearch }}" placeholder="Cari nama / ID..."
                                        class="w-full h-11 bg-white border-2 border-slate-200 rounded-2xl pl-11 pr-4 text-xs font-black text-slate-900 focus:ring-0 focus:border-slate-900 transition-all">
                                    <svg class="w-4 h-4 text-slate-300 absolute left-4 top-3.5 group-focus-within:text-slate-600 transition-colors"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                            </div>

                            {{-- Status --}}
                            <div class="w-full md:w-[150px]">
                                <select id="statusSelect" name="status"
                                    class="w-full h-11 bg-white border-2 border-slate-200 rounded-2xl text-xs font-black uppercase tracking-widest text-slate-900 focus:ring-0 focus:border-slate-900">
                                    <option value="">Status</option>
                                    <option value="1" {{ $rqStatus === '1' ? 'selected' : '' }}>AKTIF</option>
                                    <option value="0" {{ $rqStatus === '0' ? 'selected' : '' }}>NON</option>
                                </select>
                            </div>

                            {{-- Belt --}}
                            <div class="w-full md:w-[220px]">
                                <select id="beltSelect" name="belt_level_id"
                                    class="w-full h-11 bg-white border-2 border-slate-200 rounded-2xl text-xs font-black uppercase tracking-widest text-slate-900 focus:ring-0 focus:border-slate-900">
                                    <option value="">Sabuk</option>
                                    @foreach ($beltOptions as $b)
                                        <option value="{{ $b->id }}"
                                            {{ (string) $rqBelt === (string) $b->id ? 'selected' : '' }}>
                                            {{ $b->name }} {{ $b->kyu_dan }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Province (HANYA PB) --}}
                            @if ($isPB)
                                <div class="w-full md:w-[220px]">
                                    <select id="provinceSelect" name="province_id"
                                        class="w-full h-11 bg-white border-2 border-slate-200 rounded-2xl text-xs font-black uppercase tracking-widest text-slate-900 focus:ring-0 focus:border-slate-900">
                                        <option value="">Provinsi</option>
                                        @foreach ($provinceOptions as $p)
                                            <option value="{{ $p->id }}"
                                                {{ (string) $rqProvince === (string) $p->id ? 'selected' : '' }}>
                                                {{ $p->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- City (PB / Pengprov) --}}
                                <div class="w-full md:w-[220px]">
                                    <select id="citySelect" name="city_id"
                                        class="w-full h-11 bg-white border-2 border-slate-200 rounded-2xl text-xs font-black uppercase tracking-widest text-slate-900 focus:ring-0 focus:border-slate-900"
                                        {{ empty($rqProvince) ? 'disabled' : '' }}>
                                        <option value="">Kota/Cabang</option>
                                        @foreach ($cityOptions as $c)
                                            <option value="{{ $c->id }}"
                                                {{ (string) $rqCity === (string) $c->id ? 'selected' : '' }}>
                                                {{ $c->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Dojo (PB) --}}
                                <div class="w-full md:w-[240px]">
                                    <select id="dojoSelect" name="dojo_id"
                                        class="w-full h-11 bg-white border-2 border-slate-200 rounded-2xl text-xs font-black uppercase tracking-widest text-slate-900 focus:ring-0 focus:border-slate-900"
                                        {{ empty($rqCity) ? 'disabled' : '' }}>
                                        <option value="">Dojo</option>
                                        @foreach ($dojoOptions as $d)
                                            <option value="{{ $d->id }}"
                                                {{ (string) $rqDojo === (string) $d->id ? 'selected' : '' }}>
                                                {{ $d->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @elseif ($isPengprov)
                                {{-- City (Pengprov) --}}
                                <div class="w-full md:w-[220px]">
                                    <select id="citySelect" name="city_id"
                                        class="w-full h-11 bg-white border-2 border-slate-200 rounded-2xl text-xs font-black uppercase tracking-widest text-slate-900 focus:ring-0 focus:border-slate-900">
                                        <option value="">Kota/Cabang</option>
                                        @foreach ($cityOptions as $c)
                                            <option value="{{ $c->id }}"
                                                {{ (string) $rqCity === (string) $c->id ? 'selected' : '' }}>
                                                {{ $c->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Dojo (Pengprov - berdasarkan city pilihan atau semua yang disediakan controller) --}}
                                <div class="w-full md:w-[240px]">
                                    <select id="dojoSelect" name="dojo_id"
                                        class="w-full h-11 bg-white border-2 border-slate-200 rounded-2xl text-xs font-black uppercase tracking-widest text-slate-900 focus:ring-0 focus:border-slate-900"
                                        {{ empty($rqCity) ? '' : '' }}>
                                        <option value="">Dojo</option>
                                        @foreach ($dojoOptions as $d)
                                            <option value="{{ $d->id }}"
                                                {{ (string) $rqDojo === (string) $d->id ? 'selected' : '' }}>
                                                {{ $d->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @elseif ($isPengcab)
                                {{-- Dojo (Pengcab) --}}
                                <div class="w-full md:w-[240px]">
                                    <select id="dojoSelect" name="dojo_id"
                                        class="w-full h-11 bg-white border-2 border-slate-200 rounded-2xl text-xs font-black uppercase tracking-widest text-slate-900 focus:ring-0 focus:border-slate-900">
                                        <option value="">Dojo</option>
                                        @foreach ($dojoOptions as $d)
                                            <option value="{{ $d->id }}"
                                                {{ (string) $rqDojo === (string) $d->id ? 'selected' : '' }}>
                                                {{ $d->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            {{-- Reset --}}
                            <div class="w-full md:w-[120px]">
                                <a href="{{ route('admin.dashboard') }}"
                                    class="w-full h-11 inline-flex items-center justify-center bg-white border-2 border-slate-200 text-slate-600 rounded-2xl hover:bg-slate-50 transition-all text-[10px] font-black uppercase tracking-widest">
                                    Reset
                                </a>
                            </div>
                        </form>

                        <p class="mt-3 text-[10px] text-slate-500 italic">
                            * Filter otomatis berjalan saat kamu berhenti mengetik / memilih opsi. Opsi filter mengikuti
                            hak akses akun.
                        </p>
                    </div>

                    {{-- Desktop Table --}}
                    <div class="hidden md:block overflow-hidden rounded-[2rem] border border-slate-200 bg-white">
                        <table class="w-full text-sm text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-900 text-white">
                                    <th class="px-8 py-5 text-[10px] font-black uppercase tracking-[0.2em]">Identitas
                                    </th>
                                    <th class="px-8 py-5 text-[10px] font-black uppercase tracking-[0.2em]">Sabuk</th>
                                    @if (!$isAdminDojo)
                                        <th class="px-8 py-5 text-[10px] font-black uppercase tracking-[0.2em]">Dojo /
                                            Wilayah</th>
                                    @endif
                                    <th class="px-8 py-5 text-right text-[10px] font-black uppercase tracking-[0.2em]">
                                        Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($members as $member)
                                    <tr class="hover:bg-slate-50 transition-colors">
                                        <td class="px-8 py-5">
                                            <div class="flex items-center gap-4">
                                                <div
                                                    class="h-12 w-12 rounded-2xl bg-slate-100 border border-slate-200 flex items-center justify-center font-black text-lg text-slate-900 shadow-sm uppercase shrink-0">
                                                    {{ substr($member->name, 0, 1) }}
                                                </div>
                                                <div class="min-w-0">
                                                    <div
                                                        class="font-bold text-slate-900 text-base leading-none mb-1 truncate">
                                                        {{ $member->name }}
                                                    </div>
                                                    <div
                                                        class="text-[10px] text-slate-400 font-black uppercase tracking-widest">
                                                        #{{ str_pad($member->id, 5, '0', STR_PAD_LEFT) }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="px-8 py-5">
                                            <div class="flex items-center gap-3">
                                                <span
                                                    class="inline-flex items-center justify-center px-3 py-1 rounded-2xl text-[9px] font-black uppercase tracking-widest border leading-none {{ beltBadgeClass($member->beltLevel->name ?? 'Putih') }}">
                                                    {{ $member->beltLevel->name ?? 'PUTIH' }}
                                                </span>
                                                <span
                                                    class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                                    {{ $member->beltLevel->kyu_dan ?? '-' }}
                                                </span>
                                            </div>
                                        </td>

                                        @if (!$isAdminDojo)
                                            <td class="px-8 py-5">
                                                <div class="text-xs font-bold text-slate-700 truncate">
                                                    {{ $member->dojo->name ?? '-' }}
                                                </div>
                                                <div
                                                    class="text-[9px] text-slate-400 uppercase font-black tracking-tighter mt-1">
                                                    {{ $member->province->name ?? '' }}{{ $member->city?->name ? ' • ' . $member->city->name : '' }}
                                                </div>
                                            </td>
                                        @endif

                                        <td class="px-8 py-5 text-right">
                                            <span
                                                class="inline-flex items-center px-4 py-2 {{ $member->is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-red-50 text-red-700 border-red-200' }} text-[10px] font-black uppercase rounded-xl border">
                                                <span
                                                    class="h-1.5 w-1.5 rounded-full {{ $member->is_active ? 'bg-emerald-500' : 'bg-red-500' }} mr-2.5"></span>
                                                {{ $member->is_active ? 'Aktif' : 'Non-Aktif' }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="100%" class="px-8 py-20 text-center">
                                            <p class="font-black text-slate-300 uppercase tracking-[0.4em]">Data Kosong
                                            </p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Mobile Cards --}}
                    <div class="md:hidden space-y-4">
                        @forelse ($members as $member)
                            <div
                                class="bg-white rounded-[2rem] border border-slate-200 shadow-sm overflow-hidden active:scale-[0.98] transition-transform">
                                <div class="p-5">
                                    <div class="flex items-start justify-between gap-4">
                                        <div class="flex items-start gap-3 min-w-0">
                                            <div
                                                class="h-11 w-11 rounded-2xl bg-slate-900 text-white flex items-center justify-center font-black text-base uppercase shrink-0">
                                                {{ substr($member->name, 0, 1) }}
                                            </div>
                                            <div class="min-w-0">
                                                <h4
                                                    class="font-black text-slate-900 uppercase text-[14px] leading-tight tracking-tight truncate">
                                                    {{ $member->name }}
                                                </h4>
                                                <p
                                                    class="mt-1 text-[9px] font-black text-slate-400 uppercase tracking-widest italic truncate">
                                                    #{{ str_pad($member->id, 5, '0', STR_PAD_LEFT) }}
                                                </p>
                                            </div>
                                        </div>

                                        <span
                                            class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest border {{ $member->is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-red-50 text-red-700 border-red-200' }} shrink-0">
                                            <span
                                                class="w-2 h-2 rounded-full {{ $member->is_active ? 'bg-emerald-500' : 'bg-red-500' }}"></span>
                                            {{ $member->is_active ? 'AKTIF' : 'NON' }}
                                        </span>
                                    </div>

                                    <div class="grid grid-cols-2 gap-3 mt-4">
                                        <div class="bg-slate-50 p-3 rounded-2xl border border-slate-100">
                                            <p class="text-[7px] font-black text-slate-400 uppercase tracking-widest">
                                                Sabuk</p>
                                            <div class="mt-2 flex items-center gap-2 min-w-0">
                                                <span
                                                    class="inline-flex items-center justify-center px-3 py-1 rounded-2xl text-[9px] font-black uppercase tracking-widest border leading-none {{ beltBadgeClass($member->beltLevel->name ?? 'Putih') }} truncate">
                                                    {{ $member->beltLevel->name ?? 'PUTIH' }}
                                                </span>
                                                <span
                                                    class="text-[9px] font-black text-slate-400 uppercase tracking-widest shrink-0">
                                                    {{ $member->beltLevel->kyu_dan ?? '-' }}
                                                </span>
                                            </div>
                                        </div>

                                        <div class="bg-slate-50 p-3 rounded-2xl border border-slate-100 text-right">
                                            <p class="text-[7px] font-black text-slate-400 uppercase tracking-widest">
                                                Dojo</p>
                                            <p
                                                class="mt-2 text-[10px] font-black text-slate-900 uppercase tracking-tight truncate">
                                                {{ $member->dojo->name ?? '-' }}
                                            </p>
                                            @if (!empty($member->province?->name) || !empty($member->city?->name))
                                                <p
                                                    class="text-[9px] font-bold text-slate-400 uppercase italic mt-1 truncate">
                                                    {{ $member->province->name ?? '' }}{{ $member->city?->name ? ' • ' . $member->city->name : '' }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="px-5 py-3 bg-slate-900 flex items-center justify-between">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <div class="w-1 h-4 bg-red-600 rounded-full"></div>
                                        <div class="min-w-0">
                                            <p
                                                class="text-[7px] font-black text-slate-500 uppercase tracking-widest leading-none">
                                                Member</p>
                                            <p class="text-[9px] font-bold text-white uppercase mt-0.5 truncate">
                                                Database</p>
                                        </div>
                                    </div>
                                    <span
                                        class="text-[9px] font-black text-slate-300 uppercase tracking-widest">OK</span>
                                </div>
                            </div>
                        @empty
                            <div class="py-10 text-center border-2 border-dashed border-slate-100 rounded-2xl">
                                <p class="text-xs font-black text-slate-300 uppercase tracking-widest">Tidak ada data
                                </p>
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-8 pt-6 border-t border-slate-100">
                        {{ $members->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    <script>
        // ==========================================================
        // Donut render
        // ==========================================================
        (function() {
            const labels = @json($beltLabels);
            const values = @json($beltValues);
            const colors = @json($beltColors);

            const el = document.getElementById('beltDonut');
            if (!el || !labels.length) return;

            new Chart(el, {
                type: 'doughnut',
                data: {
                    labels,
                    datasets: [{
                        data: values,
                        backgroundColor: colors,
                        borderWidth: 0,
                        hoverOffset: 8,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    cutout: '72%',
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const v = context.parsed ?? 0;
                                    const name = context.label || '';
                                    return `${name}: ${v.toLocaleString('id-ID')}`;
                                }
                            }
                        }
                    }
                }
            });
        })();

        // ==========================================================
        // Auto-submit filter
        // - Sabuk pasti ikut karena name="belt_level_id"
        // - Opsi yang tidak relevan (disabled/hidden) tidak akan ikut submit
        // ==========================================================
        (function() {
            const form = document.getElementById('filterForm');
            if (!form) return;

            const search = document.getElementById('searchInput');
            const status = document.getElementById('statusSelect');
            const belt = document.getElementById('beltSelect');
            const province = document.getElementById('provinceSelect');
            const city = document.getElementById('citySelect');
            const dojo = document.getElementById('dojoSelect');

            const debounce = (fn, delay = 550) => {
                let t;
                return (...args) => {
                    clearTimeout(t);
                    t = setTimeout(() => fn(...args), delay);
                };
            };

            const submitNow = () => form.submit();
            const submitDebounced = debounce(submitNow, 550);

            // search debounce
            if (search) {
                search.addEventListener('input', submitDebounced);
                search.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        submitNow();
                    }
                });
            }

            // selects immediate
            status?.addEventListener('change', submitNow);
            belt?.addEventListener('change', submitNow);

            // chain reset logic (agar sesuai akses & tidak nyangkut param)
            province?.addEventListener('change', () => {
                // ketika provinsi berubah, reset city & dojo agar tidak invalid
                if (city) city.value = '';
                if (dojo) dojo.value = '';
                submitNow();
            });

            city?.addEventListener('change', () => {
                // ketika kota berubah, reset dojo
                if (dojo) dojo.value = '';
                submitNow();
            });

            dojo?.addEventListener('change', submitNow);
        })();
    </script>
</x-app-layout>
