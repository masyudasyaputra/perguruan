<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center px-2">
            <div>
                <h2 class="font-black text-lg md:text-xl text-slate-800 uppercase tracking-tight">Manajemen Sesi Ujian
                </h2>
                <p class="text-[9px] md:text-[10px] font-bold text-slate-400 uppercase tracking-widest">Detail &
                    Pendaftaran Peserta</p>
            </div>
            <a href="{{ route('admin.exams.index') }}"
                class="group flex items-center gap-2 text-xs font-black text-slate-400 hover:text-indigo-600 transition-all">
                <span class="p-2 bg-white rounded-lg shadow-sm group-hover:shadow-md transition-all">←</span>
                <span class="hidden md:block">KEMBALI</span>
            </a>
        </div>
    </x-slot>

    @php
        if (!function_exists('getBeltColor')) {
            function getBeltColor($beltName)
            {
                $name = strtolower($beltName);
                if (str_contains($name, 'putih')) {
                    return 'bg-slate-100 text-slate-500 border-slate-200';
                }
                if (str_contains($name, 'kuning muda')) {
                    return 'bg-yellow-100 text-yellow-600 border-yellow-200';
                }
                if (str_contains($name, 'kuning tua')) {
                    return 'bg-yellow-400 text-yellow-900 border-yellow-500';
                }
                if (str_contains($name, 'orange')) {
                    return 'bg-orange-500 text-white border-orange-600';
                }
                if (str_contains($name, 'hijau')) {
                    return 'bg-emerald-600 text-white border-emerald-700';
                }
                if (str_contains($name, 'biru')) {
                    return 'bg-blue-600 text-white border-blue-700';
                }
                if (str_contains($name, 'ungu')) {
                    return 'bg-purple-600 text-white border-purple-700';
                }
                if (str_contains($name, 'cokelat') || str_contains($name, 'coklat')) {
                    return 'bg-amber-800 text-white border-amber-900';
                }
                if (str_contains($name, 'hitam')) {
                    return 'bg-slate-900 text-white border-slate-950';
                }
                return 'bg-indigo-50 text-indigo-600 border-indigo-100';
            }
        }

        $user = auth()->user();
        $isStruktural = $user->hasRole(['pb', 'pengprov']);
        $allParticipants = $exam->participants;
        $myParticipants = $isStruktural ? $allParticipants : $allParticipants->where('dojo_id', $user->dojo_id);
        $registeredUserIds = $allParticipants->pluck('user_id')->toArray();
        $filteredMembers = $isStruktural ? $members : $members->where('dojo_id', $user->dojo_id);

        $totalBiaya = $myParticipants->sum('fee_amount');
        $lunasCount = $myParticipants->where('payment_status', 'paid')->count();
        $pendingCount = $myParticipants->where('payment_status', 'unpaid')->count();
        $totalTagihanPending = $myParticipants->where('payment_status', 'unpaid')->sum('fee_amount');

        $rekapPerSabuk = $myParticipants->groupBy('target_belt_id');
        $rekapPerWilayah = $myParticipants->groupBy(fn($p) => $p->dojo->city->name ?? 'TANPA WILAYAH');
    @endphp

    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

    <div class="py-4 md:py-6 bg-slate-50 min-h-screen font-sans" x-data="{
        tab: 'operasional',
        selectedIds: [],
        selectAll: false,
        search: '',
        filterDojo: '',
        filterSabuk: '',
        {{-- Filter ini sekarang akan merujuk ke Sabuk Sekarang --}}
        filterStatus: '',
    
        toggleAll() {
            if (this.selectAll) {
                this.selectedIds = [];
            } else {
                let visibleIds = [];
                document.querySelectorAll('.participant-item').forEach(el => {
                    if (el.style.display !== 'none') {
                        visibleIds.push(el.getAttribute('data-id'));
                    }
                });
                this.selectedIds = visibleIds;
            }
            this.selectAll = !this.selectAll;
        },
    
        isMatch(name, dojo, sabukSekarang, status) {
            const matchSearch = name.toLowerCase().includes(this.search.toLowerCase());
            const matchDojo = this.filterDojo === '' || dojo === this.filterDojo;
            const matchSabuk = this.filterSabuk === '' || sabukSekarang === this.filterSabuk;
            const matchStatus = this.filterStatus === '' || status === this.filterStatus;
            return matchSearch && matchDojo && matchSabuk && matchStatus;
        }
    }">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
            {{-- HEADER TABS --}}
            <div
                class="flex p-1 bg-slate-200/60 rounded-2xl w-full md:w-fit mx-auto border border-slate-200 shadow-inner">
                <button @click="tab = 'operasional'"
                    :class="tab === 'operasional' ? 'bg-white shadow-sm text-indigo-600' : 'text-slate-500'"
                    class="px-6 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">Pendaftaran</button>
                <button @click="tab = 'rekap'"
                    :class="tab === 'rekap' ? 'bg-white shadow-sm text-indigo-600' : 'text-slate-500'"
                    class="px-6 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">Rekap
                    Keuangan</button>
            </div>

            {{-- TAB 1: OPERASIONAL --}}
            <div x-show="tab === 'operasional'" x-transition class="space-y-4">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
                    {{-- Info Card --}}
                    <div class="lg:col-span-3">
                        <div
                            class="bg-slate-900 p-6 rounded-[2rem] shadow-lg text-white relative overflow-hidden h-full">
                            <div class="relative z-10">
                                <h4 class="text-xs font-black uppercase tracking-tight opacity-60 mb-1">Nama Sesi</h4>
                                <h3 class="text-sm font-black uppercase leading-tight">{{ $exam->name }}</h3>
                            </div>
                            <div class="mt-6 flex justify-between items-end relative z-10">
                                <div>
                                    <span class="text-2xl font-black block">{{ $myParticipants->count() }}</span>
                                    <span class="text-[8px] font-bold uppercase tracking-widest text-slate-400">Total
                                        Peserta</span>
                                </div>
                                <span
                                    class="px-2 py-1 bg-indigo-500 rounded text-[8px] font-black uppercase">{{ $exam->status }}</span>
                            </div>
                            <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-indigo-500/10 rounded-full"></div>
                        </div>
                    </div>

                    {{-- Registration Form --}}
                    <div class="lg:col-span-9 bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100">
                        <form action="{{ route('admin.exams.register-member', $exam->id) }}" method="POST">
                            @csrf
                            <label
                                class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Daftarkan
                                Anggota Baru</label>
                            <div class="flex flex-col md:flex-row gap-3">
                                <div class="flex-1">
                                    <select id="user-select" name="user_ids[]" multiple
                                        placeholder="Ketik nama anggota..." class="w-full">
                                        @foreach ($filteredMembers->groupBy(fn($m) => $m->dojo->name ?? 'TANPA DOJO') as $dojoName => $group)
                                            <optgroup label="DOJO: {{ strtoupper($dojoName) }}">
                                                @foreach ($group as $m)
                                                    @php $isAlreadyRegistered = in_array($m->id, $registeredUserIds); @endphp
                                                    <option value="{{ $m->id }}"
                                                        {{ $isAlreadyRegistered ? 'disabled' : '' }}>
                                                        {{ $m->name }} ({{ $m->beltLevel->name ?? 'Putih' }})
                                                        {{ $isAlreadyRegistered ? '[TERDAFTAR]' : '' }}
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit"
                                    class="px-8 py-3 bg-indigo-600 text-white rounded-xl font-black text-[11px] uppercase tracking-widest hover:bg-slate-900 transition-all shadow-md">Daftarkan</button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Bulk Payment --}}
                @if (!$isStruktural && $pendingCount > 0)
                    <div
                        class="bg-gradient-to-br from-indigo-600 to-indigo-700 p-6 rounded-[2.5rem] shadow-xl text-white overflow-hidden relative group">
                        <div class="relative z-10 flex flex-col md:flex-row justify-between items-center gap-6">
                            <div class="text-center md:text-left">
                                <h3 class="text-xs font-black uppercase tracking-[0.2em] text-indigo-200 mb-1">Tagihan
                                    Belum Dibayar</h3>
                                <h2 class="text-2xl font-black">
                                    Rp{{ number_format($totalTagihanPending, 0, ',', '.') }}</h2>
                                <p
                                    class="text-[9px] font-bold text-indigo-100 uppercase tracking-widest opacity-80 mt-1">
                                    Total untuk {{ $pendingCount }} Peserta</p>
                            </div>
                            <form action="{{ route('admin.exams.bulk-payment', $exam->id) }}" method="POST"
                                class="w-full md:w-auto">
                                @csrf
                                <button type="submit"
                                    class="w-full md:w-auto px-8 py-4 bg-white text-indigo-600 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-slate-900 hover:text-white transition-all shadow-lg flex items-center justify-center gap-3">
                                    <span>Lanjutkan Pembayaran</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                            d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                @endif

                {{-- FILTER PANEL --}}
                <div
                    class="bg-white p-4 rounded-3xl shadow-sm border border-slate-100 grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="col-span-2 md:col-span-1">
                        <label class="text-[8px] font-black text-slate-400 uppercase mb-1 block">Cari Nama</label>
                        <input type="text" x-model="search" placeholder="Cari..."
                            class="w-full bg-slate-50 border-none rounded-xl text-xs font-bold focus:ring-2 focus:ring-indigo-500">
                    </div>
                    @if ($isStruktural)
                        <div>
                            <label class="text-[8px] font-black text-slate-400 uppercase mb-1 block">Dojo</label>
                            <select x-model="filterDojo"
                                class="w-full bg-slate-50 border-none rounded-xl text-xs font-bold focus:ring-2 focus:ring-indigo-500">
                                <option value="">Semua</option>
                                @foreach ($myParticipants->pluck('dojo.name')->filter()->unique() as $dojoName)
                                    <option value="{{ $dojoName }}">{{ $dojoName }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    <div>
                        <label class="text-[8px] font-black text-slate-400 uppercase mb-1 block">Filter Sabuk
                            Sekarang</label>
                        <select x-model="filterSabuk"
                            class="w-full bg-slate-50 border-none rounded-xl text-xs font-bold focus:ring-2 focus:ring-indigo-500">
                            <option value="">Semua Sabuk</option>
                            @foreach ($myParticipants->pluck('currentBelt.name')->filter()->unique() as $beltName)
                                <option value="{{ $beltName }}">{{ $beltName }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-[8px] font-black text-slate-400 uppercase mb-1 block">Status</label>
                        <select x-model="filterStatus"
                            class="w-full bg-slate-50 border-none rounded-xl text-xs font-bold focus:ring-2 focus:ring-indigo-500">
                            <option value="">Semua</option>
                            <option value="paid">LUNAS</option>
                            <option value="unpaid">PENDING</option>
                        </select>
                    </div>
                </div>

                {{-- PARTICIPANT LIST --}}
                <div class="space-y-3">
                    <div x-show="selectedIds.length > 0" x-cloak
                        class="bg-rose-600 px-6 py-3 rounded-2xl flex justify-between items-center shadow-lg sticky top-4 z-30 transition-all">
                        <span class="text-[10px] font-black text-white uppercase tracking-widest italic">Terpilih: <span
                                x-text="selectedIds.length"></span> Peserta</span>
                        <form action="{{ route('admin.exams.bulk-remove-member', $exam->id) }}" method="POST"
                            onsubmit="return confirm('Hapus semua peserta yang dipilih?')">
                            @csrf @method('DELETE')
                            <template x-for="id in selectedIds" :key="id">
                                <input type="hidden" name="participant_ids[]" :value="id">
                            </template>
                            <button type="submit"
                                class="px-4 py-2 bg-white text-rose-600 rounded-lg font-black text-[9px] uppercase tracking-widest shadow-md">Hapus
                                Masal</button>
                        </form>
                    </div>

                    <div
                        class="hidden md:grid grid-cols-12 gap-4 px-8 py-3 text-[9px] font-black text-slate-400 uppercase tracking-widest">
                        <div class="col-span-1"><input type="checkbox" @click="toggleAll()" :checked="selectAll"
                                class="rounded border-slate-300 text-indigo-600"></div>
                        <div class="col-span-4">Peserta / Dojo</div>
                        <div class="col-span-2 text-center">Sabuk (Sekarang → Target)</div>
                        <div class="col-span-2 text-center">Status Bayar</div>
                        <div class="col-span-2 text-right">Biaya</div>
                        <div class="col-span-1 text-center">Aksi</div>
                    </div>

                    @forelse($myParticipants as $p)
                        <div class="participant-item bg-white p-4 md:p-3 md:px-8 rounded-[1.5rem] md:rounded-2xl shadow-sm border border-slate-100 md:grid md:grid-cols-12 md:gap-4 md:items-center transition-all hover:border-indigo-200"
                            data-id="{{ $p->id }}"
                            x-show="isMatch('{{ addslashes($p->user->name) }}', '{{ $p->dojo->name ?? '' }}', '{{ $p->currentBelt->name ?? 'Putih' }}', '{{ $p->payment_status }}')"
                            :class="selectedIds.includes('{{ $p->id }}') ? 'ring-2 ring-indigo-500 bg-indigo-50/30' : ''">

                            <div class="flex justify-between items-start md:col-span-1">
                                <input type="checkbox" value="{{ $p->id }}" x-model="selectedIds"
                                    class="rounded border-slate-300 text-indigo-600 w-5 h-5 md:w-4 md:h-4">
                            </div>

                            <div class="mt-2 md:mt-0 col-span-4">
                                <p class="font-black text-slate-700 uppercase text-xs md:text-sm">{{ $p->user->name }}
                                </p>
                                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-tight">
                                    {{ $p->dojo->name ?? 'N/A' }}</p>
                            </div>

                            <div class="flex items-center justify-between md:justify-center mt-4 md:mt-0 col-span-2">
                                <div class="flex items-center gap-1.5">
                                    <span
                                        class="px-2 py-0.5 rounded text-[8px] font-bold border {{ getBeltColor($p->currentBelt->name ?? 'Putih') }} uppercase opacity-70">
                                        {{ $p->currentBelt->name ?? 'Putih' }}
                                    </span>
                                    <span class="text-slate-400 text-[10px]">→</span>
                                    <span
                                        class="px-2.5 py-1 rounded text-[9px] font-black border {{ getBeltColor($p->targetBelt->name) }} uppercase shadow-sm">
                                        {{ $p->targetBelt->name }}
                                    </span>
                                </div>
                            </div>

                            <div class="flex items-center justify-between md:justify-center mt-2 md:mt-0 col-span-2">
                                <span
                                    class="px-3 py-1 text-[8px] font-black rounded-full {{ $p->payment_status === 'paid' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                    {{ $p->payment_status === 'paid' ? 'LUNAS' : 'PENDING' }}
                                </span>
                            </div>

                            <div class="flex items-center justify-between md:justify-end mt-2 md:mt-0 col-span-2">
                                <span
                                    class="font-black text-slate-700 text-xs md:text-sm tracking-tighter">Rp{{ number_format($p->fee_amount, 0, ',', '.') }}</span>
                            </div>

                            <div class="hidden md:block col-span-1 text-center">
                                <form action="{{ route('admin.exams.remove-member', $p->id) }}" method="POST"
                                    onsubmit="return confirm('Hapus peserta ini?')">
                                    @csrf @method('DELETE')
                                    <button class="text-slate-300 hover:text-rose-500 transition-colors">
                                        <svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div
                            class="py-12 text-center text-slate-300 font-black uppercase text-[10px] italic bg-white rounded-[2rem] border border-dashed">
                            Belum ada peserta terdaftar</div>
                    @endforelse
                </div>
            </div>

            {{-- TAB 2: REKAP DETAIL (Kembali ke kode asli Anda) --}}
            <div x-show="tab === 'rekap'" x-transition class="space-y-4" x-cloak>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-indigo-600 p-6 rounded-[2rem] text-white shadow-lg">
                        <span class="text-[9px] font-black uppercase opacity-60 tracking-widest">Estimasi Biaya</span>
                        <h2 class="text-xl font-black mt-1">Rp{{ number_format($totalBiaya, 0, ',', '.') }}</h2>
                    </div>
                    <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm">
                        <span class="text-[9px] font-black uppercase text-slate-400 tracking-widest">Lunas</span>
                        <h2 class="text-xl font-black mt-1 text-emerald-600">{{ $lunasCount }} <span
                                class="text-xs text-slate-400">Peserta</span></h2>
                    </div>
                    <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm">
                        <span class="text-[9px] font-black uppercase text-slate-400 tracking-widest">Pending</span>
                        <h2 class="text-xl font-black mt-1 text-rose-600">{{ $pendingCount }} <span
                                class="text-xs text-slate-400">Peserta</span></h2>
                    </div>
                    <div class="bg-slate-900 p-6 rounded-[2rem] text-white shadow-lg">
                        <span class="text-[9px] font-black uppercase opacity-60 tracking-widest">Total Sabuk</span>
                        <h2 class="text-xl font-black mt-1">{{ $rekapPerSabuk->count() }} <span
                                class="text-xs text-slate-400">Level</span></h2>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
                    <div class="lg:col-span-4 bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm">
                        <h3
                            class="text-[10px] font-black text-slate-800 uppercase tracking-widest mb-6 flex items-center gap-2">
                            <span class="w-2 h-2 bg-indigo-500 rounded-full"></span> Rincian Sabuk
                        </h3>
                        <div class="space-y-3">
                            @foreach ($rekapPerSabuk->sortBy(fn($val, $key) => $key) as $beltId => $items)
                                @php $beltInfo = $items->first()->targetBelt; @endphp
                                <div
                                    class="flex items-center justify-between p-3 bg-slate-50 rounded-2xl border border-slate-100">
                                    <div class="flex items-center gap-3">
                                        <span
                                            class="w-3 h-3 rounded-full border {{ getBeltColor($beltInfo->name) }}"></span>
                                        <span
                                            class="text-[10px] font-black text-slate-700 uppercase">{{ $beltInfo->name }}</span>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-[10px] font-black text-slate-900">{{ $items->count() }} Org</p>
                                        <p class="text-[8px] font-bold text-slate-400">
                                            Rp{{ number_format($items->sum('fee_amount'), 0, ',', '.') }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="lg:col-span-8">
                        @if ($isStruktural)
                            <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm">
                                <h3
                                    class="text-[10px] font-black text-slate-800 uppercase tracking-widest mb-6 flex items-center gap-2">
                                    <span class="w-2 h-2 bg-emerald-500 rounded-full"></span> Statistik Wilayah
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    @foreach ($rekapPerWilayah as $wilayah => $items)
                                        <div class="p-4 rounded-3xl border border-slate-100 bg-white shadow-sm">
                                            <div class="flex justify-between items-start mb-4">
                                                <div>
                                                    <h4 class="text-[11px] font-black text-slate-800 uppercase">
                                                        {{ $wilayah }}</h4>
                                                    <p
                                                        class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">
                                                        {{ $items->groupBy('dojo_id')->count() }} Dojo</p>
                                                </div>
                                                <span
                                                    class="text-[10px] font-black bg-indigo-50 text-indigo-600 px-2 py-1 rounded-lg">{{ $items->count() }}
                                                    Orang</span>
                                            </div>
                                            <div class="space-y-2">
                                                @php $percent = $items->count() > 0 ? ($items->where('payment_status', 'paid')->count() / $items->count()) * 100 : 0; @endphp
                                                <div
                                                    class="flex justify-between text-[9px] font-bold uppercase text-slate-400">
                                                    <span>Omzet</span>
                                                    <span
                                                        class="text-slate-700">Rp{{ number_format($items->sum('fee_amount'), 0, ',', '.') }}</span>
                                                </div>
                                                <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden">
                                                    <div class="bg-emerald-500 h-full"
                                                        style="width: {{ $percent }}%"></div>
                                                </div>
                                                <p class="text-[8px] font-bold text-slate-400 text-right italic">
                                                    {{ round($percent) }}% Lunas</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm">
                                <h3
                                    class="text-[10px] font-black text-slate-800 uppercase tracking-widest mb-6 flex items-center gap-2">
                                    <span class="w-2 h-2 bg-orange-500 rounded-full"></span> Status Tagihan Anggota
                                </h3>
                                <div class="space-y-2">
                                    @foreach ($myParticipants as $p)
                                        <div
                                            class="p-3 bg-slate-50 rounded-xl border border-slate-100 flex justify-between items-center">
                                            <div>
                                                <p class="text-[10px] font-black text-slate-700 uppercase">
                                                    {{ $p->user->name }}</p>
                                                <p class="text-[8px] text-slate-400 uppercase font-bold">
                                                    {{ $p->currentBelt->name ?? 'Putih' }} →
                                                    {{ $p->targetBelt->name }}</p>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-[10px] font-black text-slate-700">
                                                    Rp{{ number_format($p->fee_amount, 0, ',', '.') }}</p>
                                                <span
                                                    class="text-[7px] font-black uppercase {{ $p->payment_status === 'paid' ? 'text-emerald-600' : 'text-rose-600' }}">{{ $p->payment_status }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            new TomSelect('#user-select', {
                plugins: ['remove_button'],
                maxOptions: null,
            });
        });
    </script>

    <style>
        [x-cloak] {
            display: none !important;
        }

        .ts-control {
            border: none !important;
            padding: 12px !important;
            border-radius: 12px !important;
            background: #f8fafc !important;
            font-size: 12px !important;
            font-weight: 700 !important;
        }
    </style>
</x-app-layout>
