<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center px-2">
            <div>
                <h2 class="font-black text-xl text-slate-800 uppercase tracking-tight">Detail Sesi Ujian</h2>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Manajemen Peserta & Pendaftaran</p>
            </div>
            <a href="{{ route('admin.exams.index') }}" class="group flex items-center gap-2 text-xs font-black text-slate-400 hover:text-indigo-600 transition-all">
                <span class="p-2 bg-white rounded-lg shadow-sm group-hover:shadow-md transition-all">←</span>
                KEMBALI
            </a>
        </div>
    </x-slot>

    @php
        function getBeltColor($beltName) {
            $name = strtolower($beltName);
            if (str_contains($name, 'putih')) return 'bg-slate-100 text-slate-500 border border-slate-200';
            if (str_contains($name, 'kuning')) return 'bg-yellow-100 text-yellow-700 border border-yellow-200';
            if (str_contains($name, 'hijau')) return 'bg-emerald-100 text-emerald-700 border border-emerald-200';
            if (str_contains($name, 'biru')) return 'bg-blue-100 text-blue-700 border border-blue-200';
            if (str_contains($name, 'coklat')) return 'bg-amber-800 text-amber-50 border border-amber-900';
            if (str_contains($name, 'merah')) return 'bg-rose-100 text-rose-700 border border-rose-200';
            if (str_contains($name, 'hitam')) return 'bg-slate-900 text-slate-50 border border-slate-800';
            return 'bg-indigo-50 text-indigo-600 border border-indigo-100';
        }
    @endphp

    <div class="py-6 bg-slate-50 min-h-screen font-sans">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
            
            {{-- ALERT NOTIFIKASI --}}
            @if(session('success'))
                <div class="p-4 bg-emerald-500 text-white rounded-[1.5rem] shadow-lg shadow-emerald-100 flex items-center gap-3 animate-fade-in">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-xs font-black uppercase tracking-wider">{{ session('success') }}</span>
                </div>
            @endif

            {{-- 1. HEADER RINGKAS (INFO & DAFTAR) --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
                {{-- Info Sesi (Kiri) --}}
                <div class="lg:col-span-4 bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 flex flex-col justify-center">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-black text-slate-800 uppercase text-[10px] tracking-widest flex items-center gap-2">
                            <span class="w-1.5 h-3 bg-indigo-600 rounded-full"></span>
                            Info Sesi
                        </h3>
                        <span class="px-2 py-0.5 bg-indigo-50 text-indigo-600 rounded-md text-[9px] font-black uppercase tracking-tighter">{{ $exam->status }}</span>
                    </div>
                    <div>
                        <p class="font-black text-slate-700 text-base leading-tight">"{{ $exam->name }}"</p>
                        <p class="text-[10px] font-bold text-slate-400 mt-1 uppercase">{{ $exam->location }} • {{ $exam->execution_date->format('d M Y') }}</p>
                    </div>
                </div>

                {{-- Form Daftar Cepat (Kanan) --}}
                <div class="lg:col-span-8 bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 flex flex-col justify-center">
                    <div class="flex items-center gap-2 mb-4">
                         <span class="w-1.5 h-3 bg-emerald-500 rounded-full"></span>
                         <h3 class="font-black text-slate-800 uppercase text-[10px] tracking-widest">Pendaftaran Cepat</h3>
                    </div>
                    <form action="{{ route('admin.exams.register-member', $exam->id) }}" method="POST" class="flex flex-col sm:flex-row gap-2">
                        @csrf
                        <div class="flex-1 relative">
                            <select name="user_id" required class="w-full bg-slate-50 border-slate-100 border rounded-xl px-4 py-2.5 text-xs font-bold text-slate-700 focus:ring-2 focus:ring-indigo-500/20 transition-all appearance-none cursor-pointer">
                                <option value="">Pilih Member...</option>
                                @php $groupedMembers = $members->groupBy(fn($m) => $m->dojo->name ?? 'TANPA DOJO'); @endphp
                                @foreach($groupedMembers as $dojoName => $group)
                                    <optgroup label="DOJO: {{ strtoupper($dojoName) }}">
                                        @foreach($group as $m)
                                            <option value="{{ $m->id }}">{{ $m->name }} ({{ $m->beltLevel->name ?? 'Putih' }})</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="px-6 py-2.5 bg-slate-900 text-white rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-indigo-600 transition-all shadow-md active:scale-95 flex items-center justify-center gap-2">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
                            TAMBAHKAN
                        </button>
                    </form>
                </div>
            </div>

            {{-- 2. TABEL DATA & FILTER --}}
            <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
                
                {{-- Panel Filter --}}
                <div class="p-6 border-b border-slate-50 bg-slate-50/30">
                    <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-4">
                        <div class="hidden xl:block">
                            <h3 class="font-black text-slate-800 uppercase text-xs">Daftar Peserta</h3>
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">{{ $exam->participants->count() }} Orang Terdaftar</p>
                        </div>

                        <form action="{{ route('admin.exams.show', $exam->id) }}" method="GET" class="flex flex-wrap md:flex-nowrap items-center gap-2">
                            <div class="relative w-full md:w-48">
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama..." class="w-full pl-3 pr-3 py-2 bg-white border border-slate-100 rounded-lg text-[11px] font-bold shadow-sm">
                            </div>

                            <select name="dojo_id" class="w-full md:w-40 px-3 py-2 bg-white border border-slate-100 rounded-lg text-[11px] font-bold shadow-sm">
                                <option value="">Semua Dojo</option>
                                @foreach($dojos as $d)
                                    <option value="{{ $d->id }}" {{ request('dojo_id') == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                                @endforeach
                            </select>

                            <select name="payment_status" class="w-full md:w-36 px-3 py-2 bg-white border border-slate-100 rounded-lg text-[11px] font-bold shadow-sm">
                                <option value="">Pembayaran</option>
                                <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Lunas</option>
                                <option value="unpaid" {{ request('payment_status') == 'unpaid' ? 'selected' : '' }}>Belum</option>
                            </select>

                            <select name="target_belt_id" class="w-full md:w-40 px-3 py-2 bg-white border border-slate-100 rounded-lg text-[11px] font-bold shadow-sm">
                                <option value="">Sabuk</option>
                                @foreach($belts as $b)
                                    <option value="{{ $b->id }}" {{ request('target_belt_id') == $b->id ? 'selected' : '' }}>Ke {{ $b->name }}</option>
                                @endforeach
                            </select>

                            <div class="flex gap-1 w-full md:w-auto">
                                <button type="submit" class="flex-1 md:flex-none px-4 py-2 bg-indigo-600 text-white rounded-lg text-[10px] font-black uppercase tracking-widest hover:bg-indigo-700 transition-all shadow-sm">FILTER</button>
                                <a href="{{ route('admin.exams.show', $exam->id) }}" class="p-2 bg-white text-slate-300 rounded-lg border border-slate-100 hover:text-rose-500 transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Desktop Table View --}}
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] bg-slate-50/50">
                                <th class="px-8 py-4 border-b border-slate-50">Peserta</th>
                                <th class="px-6 py-4 border-b border-slate-50 text-center">Progresi Sabuk</th>
                                <th class="px-6 py-4 border-b border-slate-50 text-center">Status Bayar</th>
                                <th class="px-6 py-4 border-b border-slate-50 text-right">Biaya</th>
                                <th class="px-8 py-4 border-b border-slate-50 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @forelse($exam->participants as $p)
                            <tr class="hover:bg-slate-50/30 transition-colors">
                                <td class="px-8 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center font-black text-[10px] border border-slate-200 uppercase">
                                            {{ substr($p->user->name, 0, 2) }}
                                        </div>
                                        <div>
                                            <p class="font-black text-slate-700 text-sm leading-tight">{{ $p->user->name }}</p>
                                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter mt-0.5">{{ $p->dojo->name ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-slate-50/50 rounded-xl border border-slate-100">
                                        <span class="px-2 py-0.5 rounded text-[8px] font-black uppercase {{ getBeltColor($p->currentBelt->name ?? 'Putih') }}">
                                            {{ $p->currentBelt->name ?? 'Putih' }}
                                        </span>
                                        <svg class="w-3 h-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="3" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                                        <span class="px-2 py-0.5 rounded text-[8px] font-black uppercase shadow-sm {{ getBeltColor($p->targetBelt->name) }}">
                                            {{ $p->targetBelt->name }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-3 py-1 {{ $p->payment_status === 'paid' ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }} rounded-lg text-[9px] font-black uppercase border border-current opacity-70">
                                        {{ $p->payment_status === 'paid' ? 'LUNAS' : 'BELUM' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right font-black text-slate-700 text-xs">
                                    Rp{{ number_format($p->fee_amount, 0, ',', '.') }}
                                </td>
                                <td class="px-8 py-4 text-center">
                                    <form action="{{ route('admin.exams.remove-member', $p->id) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button class="p-2 text-slate-300 hover:text-rose-600 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="py-20 text-center text-[10px] font-black text-slate-300 uppercase tracking-widest">Database Kosong</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Mobile View (Card Style - TETAP SEPERTI BARUSAN) --}}
                <div class="md:hidden p-4 space-y-3">
                    @forelse($exam->participants as $p)
                    <div class="bg-white rounded-[1.5rem] border border-slate-100 p-4 shadow-sm">
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-slate-50 text-indigo-600 flex items-center justify-center font-black text-xs border border-slate-100">
                                    {{ substr($p->user->name, 0, 2) }}
                                </div>
                                <div>
                                    <p class="font-black text-slate-700 text-sm leading-none">{{ $p->user->name }}</p>
                                    <p class="text-[9px] font-bold text-slate-400 uppercase italic mt-1">{{ $p->dojo->name ?? 'N/A' }}</p>
                                </div>
                            </div>
                            <form action="{{ route('admin.exams.remove-member', $p->id) }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 text-rose-300"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                            </form>
                        </div>

                        <div class="bg-slate-50/50 rounded-2xl p-4 border border-slate-50 mb-3">
                            <p class="text-[8px] font-black text-slate-300 uppercase mb-2 text-center">Progresi Sabuk</p>
                            <div class="flex items-center justify-center gap-3">
                                <span class="px-2 py-0.5 rounded-lg text-[8px] font-black uppercase {{ getBeltColor($p->currentBelt->name ?? 'Putih') }}">
                                    {{ $p->currentBelt->name ?? 'Putih' }}
                                </span>
                                <svg class="w-3 h-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="3" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                                <span class="px-2 py-0.5 rounded-lg text-[8px] font-black uppercase {{ getBeltColor($p->targetBelt->name) }}">
                                    {{ $p->targetBelt->name }}
                                </span>
                            </div>
                        </div>

                        <div class="flex justify-between items-center px-2">
                            <div>
                                <p class="text-[8px] font-black text-slate-300 uppercase">Status</p>
                                <p class="text-[10px] font-black {{ $p->payment_status === 'paid' ? 'text-emerald-500' : 'text-rose-500' }} uppercase">
                                    {{ $p->payment_status === 'paid' ? 'LUNAS' : 'BELUM' }}
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-[8px] font-black text-slate-300 uppercase">Biaya</p>
                                <p class="text-xs font-black text-slate-700 mt-0.5">Rp{{ number_format($p->fee_amount, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="py-10 text-center text-[10px] font-black text-slate-300 uppercase italic">Belum Ada Peserta</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>