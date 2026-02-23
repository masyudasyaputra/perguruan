<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center px-2">
            <div>
                <h2 class="font-black text-lg md:text-xl text-slate-800 uppercase tracking-tight">Manajemen Sesi Ujian</h2>
                <p class="text-[9px] md:text-[10px] font-bold text-slate-400 uppercase tracking-widest">Detail & Pendaftaran Peserta</p>
            </div>
            <a href="{{ route('admin.exams.index') }}" class="group flex items-center gap-2 text-xs font-black text-slate-400 hover:text-indigo-600 transition-all">
                <span class="p-2 bg-white rounded-lg shadow-sm group-hover:shadow-md transition-all">←</span>
                <span class="hidden md:block">KEMBALI</span>
            </a>
        </div>
    </x-slot>

    @php
        function getBeltColor($beltName) {
            $name = strtolower($beltName);
            if (str_contains($name, 'putih')) return 'bg-slate-100 text-slate-500 border-slate-200';
            if (str_contains($name, 'kuning muda')) return 'bg-yellow-100 text-yellow-600 border-yellow-200';
            if (str_contains($name, 'kuning tua')) return 'bg-yellow-400 text-yellow-900 border-yellow-500';
            if (str_contains($name, 'orange')) return 'bg-orange-500 text-white border-orange-600';
            if (str_contains($name, 'hijau')) return 'bg-emerald-600 text-white border-emerald-700';
            if (str_contains($name, 'biru')) return 'bg-blue-600 text-white border-blue-700';
            if (str_contains($name, 'ungu')) return 'bg-purple-600 text-white border-purple-700';
            if (str_contains($name, 'cokelat') || str_contains($name, 'coklat')) return 'bg-amber-800 text-white border-amber-900';
            if (str_contains($name, 'hitam')) return 'bg-slate-900 text-white border-slate-950';
            return 'bg-indigo-50 text-indigo-600 border-indigo-100';
        }

        $participants = $exam->participants;
        $rekapPengcab = $participants->groupBy(fn($p) => $p->dojo->city->name ?? 'TANPA WILAYAH');
        $rekapDojo = $participants->groupBy(fn($p) => $p->dojo->name ?? 'TANPA DOJO');
    @endphp

    <div class="py-4 md:py-6 bg-slate-50 min-h-screen font-sans" x-data="{ tab: 'operasional' }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4 md:space-y-6">
            
            {{-- NAVIGATION TABS (Mobile Optimized) --}}
            <div class="flex p-1 bg-slate-200/60 rounded-2xl w-full md:w-fit mx-auto mb-4 border border-slate-200 shadow-inner">
                <button @click="tab = 'operasional'" :class="tab === 'operasional' ? 'bg-white shadow-sm text-indigo-600' : 'text-slate-500'" class="flex-1 md:flex-none px-4 md:px-8 py-2.5 rounded-xl text-[10px] md:text-[11px] font-black uppercase tracking-wider transition-all">
                    Pendaftaran
                </button>
                <button @click="tab = 'rekap'" :class="tab === 'rekap' ? 'bg-white shadow-sm text-indigo-600' : 'text-slate-500'" class="flex-1 md:flex-none px-4 md:px-8 py-2.5 rounded-xl text-[10px] md:text-[11px] font-black uppercase tracking-wider transition-all">
                    Rekap Detail
                </button>
            </div>

            {{-- TAB 1: OPERASIONAL --}}
            <div x-show="tab === 'operasional'" x-transition class="space-y-4">
                {{-- Form Pendaftaran & Info Mini --}}
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
                    <div class="lg:col-span-3 bg-slate-900 p-5 md:p-6 rounded-[1.5rem] md:rounded-[2rem] shadow-lg text-white relative overflow-hidden">
                        <div class="relative z-10">
                            <h4 class="text-xs md:text-sm font-black uppercase tracking-tight truncate">{{ $exam->name }}</h4>
                            <p class="text-[9px] md:text-[10px] font-bold text-slate-400 uppercase mt-1">{{ $exam->location }}</p>
                        </div>
                        <div class="relative z-10 flex justify-between items-end mt-4">
                            <div class="text-lg font-black leading-none">{{ $participants->count() }} <span class="text-[8px] text-slate-400 block uppercase tracking-widest">Peserta</span></div>
                            <span class="px-2 py-0.5 bg-indigo-500 rounded text-[8px] font-black uppercase">{{ $exam->status }}</span>
                        </div>
                    </div>

                    <div class="lg:col-span-9 bg-white p-5 md:p-6 rounded-[1.5rem] md:rounded-[2rem] shadow-sm border border-slate-100">
                        <form action="{{ route('admin.exams.register-member', $exam->id) }}" method="POST" class="flex flex-col sm:flex-row gap-3">
                            @csrf
                            <div class="flex-1">
                                <select name="user_id" required class="w-full bg-slate-50 border-slate-200 border rounded-xl px-4 py-3 text-xs font-bold text-slate-700 focus:ring-2 focus:ring-indigo-500/20">
                                    <option value="">Cari Anggota...</option>
                                    @foreach($members->groupBy(fn($m) => $m->dojo->name ?? 'TANPA DOJO') as $dojoName => $group)
                                        <optgroup label="DOJO: {{ strtoupper($dojoName) }}">
                                            @foreach($group as $m)
                                                <option value="{{ $m->id }}">{{ $m->name }} ({{ $m->beltLevel->name ?? 'Putih' }})</option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="w-full sm:w-auto px-8 py-3 bg-indigo-600 text-white rounded-xl font-black text-[10px] md:text-[11px] uppercase tracking-widest hover:bg-slate-900 transition-all shadow-md">
                                DAFTARKAN
                            </button>
                        </form>
                    </div>
                </div>

                {{-- RESPONSIVE TABLE/LIST --}}
                <div class="bg-white md:rounded-[2rem] rounded-[1.5rem] shadow-sm border border-slate-100 overflow-hidden">
                    <div class="hidden md:block">
                        {{-- Desktop View Table --}}
                        <table class="w-full text-left">
                            <thead class="bg-slate-50 border-b border-slate-100">
                                <tr class="text-[9px] font-black text-slate-400 uppercase tracking-widest">
                                    <th class="px-6 py-4">Peserta / Dojo</th>
                                    <th class="px-4 py-4 text-center">Ujian Sabuk</th>
                                    <th class="px-4 py-4 text-center">Status</th>
                                    <th class="px-6 py-4 text-right">Biaya</th>
                                    <th class="px-6 py-4 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50 text-xs">
                                @forelse($participants as $p)
                                    <tr class="hover:bg-slate-50/50 transition-all">
                                        <td class="px-6 py-3">
                                            <p class="font-black text-slate-700 uppercase">{{ $p->user->name }}</p>
                                            <p class="text-[8px] font-bold text-slate-400 uppercase">{{ $p->dojo->name ?? 'N/A' }}</p>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <div class="inline-flex items-center gap-2 px-2 py-1 bg-white border border-slate-100 rounded-lg shadow-sm">
                                                <span class="px-1.5 py-0.5 rounded text-[8px] font-black {{ getBeltColor($p->currentBelt->name ?? 'Putih') }}">{{ $p->currentBelt->name ?? 'Putih' }}</span>
                                                <span class="text-slate-300">→</span>
                                                <span class="px-1.5 py-0.5 rounded text-[8px] font-black {{ getBeltColor($p->targetBelt->name) }}">{{ $p->targetBelt->name }}</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="px-2 py-0.5 text-[8px] font-black rounded {{ $p->payment_status === 'paid' ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }}">
                                                {{ $p->payment_status === 'paid' ? 'LUNAS' : 'PENDING' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-3 text-right font-black text-slate-700 uppercase">Rp{{ number_format($p->fee_amount, 0, ',', '.') }}</td>
                                        <td class="px-6 py-3 text-center">
                                            <form action="{{ route('admin.exams.remove-member', $p->id) }}" method="POST">
                                                @csrf @method('DELETE')
                                                <button class="text-slate-300 hover:text-rose-500"><svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="py-10 text-center text-[9px] font-black text-slate-300 uppercase italic">Belum ada data</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Mobile View Cards (Hidden on Desktop) --}}
                    <div class="md:hidden divide-y divide-slate-100">
                        @forelse($participants as $p)
                            <div class="p-4 flex flex-col gap-3">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="font-black text-slate-700 text-xs uppercase">{{ $p->user->name }}</p>
                                        <p class="text-[9px] font-bold text-slate-400 uppercase">{{ $p->dojo->name ?? 'N/A' }}</p>
                                    </div>
                                    <form action="{{ route('admin.exams.remove-member', $p->id) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button class="p-1.5 bg-rose-50 text-rose-400 rounded-lg"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                                    </form>
                                </div>
                                <div class="flex justify-between items-center bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                                    <div class="flex items-center gap-1.5">
                                        <span class="px-1.5 py-0.5 rounded text-[8px] font-black {{ getBeltColor($p->currentBelt->name ?? 'Putih') }}">{{ $p->currentBelt->name ?? 'Putih' }}</span>
                                        <span class="text-slate-300 text-[8px]">→</span>
                                        <span class="px-1.5 py-0.5 rounded text-[8px] font-black {{ getBeltColor($p->targetBelt->name) }}">{{ $p->targetBelt->name }}</span>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-[10px] font-black text-slate-700">Rp{{ number_format($p->fee_amount, 0, ',', '.') }}</p>
                                        <p class="text-[7px] font-bold {{ $p->payment_status === 'paid' ? 'text-emerald-500' : 'text-rose-500' }} uppercase">{{ $p->payment_status === 'paid' ? 'Lunas' : 'Menunggu' }}</p>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="py-10 text-center text-[10px] font-black text-slate-300 uppercase italic">Belum ada data</div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- TAB 2: REKAP DETAIL (COMPACT MOBILE) --}}
            <div x-show="tab === 'rekap'" x-transition class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                {{-- Wilayah --}}
                <div class="bg-white p-5 md:p-6 rounded-[1.5rem] md:rounded-[2rem] shadow-sm border border-slate-100">
                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                        <span class="w-1 h-3 bg-indigo-500 rounded-full"></span> Wilayah
                    </h3>
                    <div class="space-y-3">
                        @foreach($rekapPengcab as $pengcab => $items)
                            <div class="p-3 bg-slate-50/50 rounded-xl border border-slate-100">
                                <p class="text-[10px] font-black text-slate-700 uppercase mb-2">{{ $pengcab }} ({{ $items->count() }})</p>
                                <div class="flex flex-wrap gap-1">
                                    @foreach($items->groupBy(fn($p) => $p->targetBelt->name)->sortKeys() as $beltName => $group)
                                        <span class="text-[8px] font-black px-1.5 py-0.5 bg-white border border-slate-100 rounded text-slate-500">
                                            {{ $beltName }}: {{ $group->count() }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Dojo --}}
                <div class="bg-white p-5 md:p-6 rounded-[1.5rem] md:rounded-[2rem] shadow-sm border border-slate-100">
                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                        <span class="w-1 h-3 bg-emerald-500 rounded-full"></span> Dojo
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach($rekapDojo as $dojo => $items)
                            <div class="p-3 border border-slate-100 rounded-xl">
                                <p class="text-[9px] font-black text-slate-700 uppercase truncate mb-1">{{ $dojo }}</p>
                                <p class="text-[10px] font-black text-emerald-600 mb-2">Rp{{ number_format($items->sum('fee_amount'), 0, ',', '.') }}</p>
                                <div class="flex flex-wrap gap-1">
                                    @foreach($items->groupBy(fn($p) => $p->targetBelt->name)->sortKeys() as $beltName => $group)
                                        <span class="text-[7px] font-bold px-1 py-0.5 bg-slate-50 rounded text-slate-400 border border-slate-100">
                                            {{ $group->count() }} {{ substr($beltName, 0, 1) }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>