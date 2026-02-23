<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center px-2">
            <div>
                <h2 class="font-black text-lg md:text-xl text-slate-800 uppercase tracking-tight">Master Biaya</h2>
                <p class="text-[9px] md:text-[10px] font-bold text-slate-400 uppercase tracking-widest">Konfigurasi Tarif Sabuk</p>
            </div>
            <a href="{{ route('admin.exams.index') }}" class="group flex items-center gap-2 text-[10px] font-black text-slate-400 hover:text-indigo-600 transition-all uppercase tracking-widest">
                <span class="hidden md:inline">KEMBALI</span>
                <span class="p-2 bg-white rounded-xl shadow-sm group-hover:shadow-md transition-all text-base">←</span>
            </a>
        </div>
    </x-slot>

    @php
        function getBeltColor($beltName) {
            $name = strtolower($beltName);
            if (str_contains($name, 'putih')) return 'bg-white text-slate-400 border-slate-200';
            if (str_contains($name, 'kuning muda')) return 'bg-yellow-50 text-yellow-500 border-yellow-200';
            if (str_contains($name, 'kuning')) return 'bg-yellow-400 text-yellow-900 border-yellow-500';
            if (str_contains($name, 'orange')) return 'bg-orange-500 text-white border-orange-600';
            if (str_contains($name, 'hijau')) return 'bg-emerald-600 text-white border-emerald-700';
            if (str_contains($name, 'biru')) return 'bg-blue-600 text-white border-blue-700';
            if (str_contains($name, 'ungu')) return 'bg-purple-600 text-white border-purple-700';
            if (str_contains($name, 'coklat')) return 'bg-amber-800 text-white border-amber-900';
            if (str_contains($name, 'hitam')) return 'bg-slate-900 text-white border-slate-950';
            return 'bg-indigo-50 text-indigo-600 border-indigo-100';
        }
    @endphp

    <div class="py-6 md:py-10 bg-slate-50 min-h-screen font-sans text-slate-700">
        <div class="max-w-6xl mx-auto px-4">
            
            {{-- GRID CONTAINER: Desktop 2 Kolom, Mobile 1 Kolom --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                
                {{-- KOLOM KIRI: FORM (Sticky di Desktop) --}}
                <div class="lg:col-span-4 lg:sticky lg:top-24 space-y-6">
                    @if(session('success'))
                        <div class="p-4 bg-emerald-500 text-white rounded-2xl shadow-lg shadow-emerald-500/20 flex items-center gap-3 animate-fade-in">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            <span class="text-[10px] font-black uppercase tracking-wider">{{ session('success') }}</span>
                        </div>
                    @endif

                    <div class="bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-slate-100 p-6 md:p-8 relative overflow-hidden">
                        <h3 class="text-[10px] font-black text-slate-300 uppercase tracking-[0.3em] mb-6">Input Data Baru</h3>
                        <form id="feeForm" action="{{ route('admin.exams.fees.store') }}" method="POST" class="space-y-5">
                            @csrf
                            <div class="space-y-2">
                                <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">Kenaikan Sabuk</label>
                                <select name="belt_level_id" id="belt_level_id" required class="w-full bg-slate-50 border-none rounded-xl px-4 py-3 text-xs font-bold focus:ring-2 focus:ring-indigo-500/20 transition-all">
                                    <option value="">Pilih Progres...</option>
                                    @foreach($belts as $belt)
                                        @php $prev = $belts->where('order', '<', $belt->order)->sortByDesc('order')->first(); @endphp
                                        <option value="{{ $belt->id }}">
                                            {{ $prev ? $prev->name . ' → ' : '' }}{{ $belt->name }} ({{ $belt->kyu_dan }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="space-y-2">
                                <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">Biaya (Rp)</label>
                                <div class="relative group">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[10px] font-black text-slate-300">Rp</span>
                                    <input type="text" id="amount_mask" required placeholder="0" 
                                        class="w-full bg-slate-50 border-none rounded-xl pl-10 pr-4 py-3 text-xs font-black focus:ring-2 focus:ring-indigo-500/20 transition-all">
                                </div>
                                <input type="hidden" name="amount" id="amount_real">
                            </div>

                            <button type="submit" class="w-full bg-slate-900 text-white rounded-xl py-4 font-black text-[10px] uppercase tracking-[0.2em] hover:bg-indigo-600 transition-all shadow-lg shadow-slate-900/10">
                                Simpan Data
                            </button>
                        </form>
                    </div>
                </div>

                {{-- KOLOM KANAN: LIST DATA --}}
                <div class="lg:col-span-8 space-y-4">
                    <div class="flex items-center justify-between px-4">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em]">Master List Biaya</p>
                        <span class="text-[9px] font-bold text-slate-300 bg-slate-100 px-2 py-1 rounded-md uppercase">{{ $fees->count() }} Tingkatan</span>
                    </div>

                    <div class="grid gap-3">
                        @forelse($fees as $fee)
                        <div class="bg-white p-3 md:p-4 rounded-[1.5rem] md:rounded-[2rem] shadow-sm border border-slate-100 flex flex-wrap md:flex-nowrap items-center justify-between group hover:border-indigo-200 transition-all">
                            
                            {{-- Info Sabuk --}}
                            <div class="flex items-center gap-3 w-full md:w-auto">
                                <div class="flex items-center bg-slate-50 rounded-xl p-1.5 pr-4 border border-slate-100 flex-1 md:flex-none">
                                    @php $prevBelt = $belts->where('order', '<', $fee->beltLevel->order)->sortByDesc('order')->first(); @endphp
                                    
                                    @if($prevBelt)
                                        <div class="flex items-center gap-2 px-2 border-r border-slate-200 mr-2">
                                            <span class="w-2 h-2 rounded-full {{ explode(' ', getBeltColor($prevBelt->name))[0] }}"></span>
                                            <span class="text-[9px] font-bold text-slate-400 uppercase hidden md:inline">{{ $prevBelt->name }}</span>
                                        </div>
                                    @endif

                                    <div class="flex items-center gap-2">
                                        <span class="px-3 py-1 rounded-lg text-[9px] font-black uppercase border {{ getBeltColor($fee->beltLevel->name) }}">
                                            {{ $fee->beltLevel->name }}
                                        </span>
                                        <span class="text-[8px] font-bold text-slate-400 uppercase italic">{{ $fee->beltLevel->kyu_dan }}</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Info Biaya & Action --}}
                            <div class="flex items-center justify-between md:justify-end gap-6 w-full md:w-auto mt-3 md:mt-0 pl-2 md:pl-0 border-t md:border-t-0 pt-3 md:pt-0 border-slate-50">
                                <div class="text-left md:text-right">
                                    <p class="text-[7px] font-black text-slate-300 uppercase">Biaya Ujian</p>
                                    <span class="font-black text-slate-800 text-xs md:text-sm tabular-nums">Rp{{ number_format($fee->amount, 0, ',', '.') }}</span>
                                </div>
                                
                                <div class="flex gap-1">
                                    <button onclick="quickEdit('{{ $fee->belt_level_id }}', '{{ $fee->amount }}')" class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all">
                                        <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5M18.364 5.364l1.273 1.273m-1.273-1.273L17.5 6.5M18.364 5.364l-6.364 6.364l-1.414 1.414l-1.414 1.414L9 15l.707-.707l1.414-1.414l1.414-1.414l6.364-6.364z"/></svg>
                                    </button>
                                    <form action="{{ route('admin.exams.fees.destroy', $fee->id) }}" method="POST" onsubmit="return confirm('Hapus?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-all">
                                            <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="py-12 text-center bg-white rounded-[2rem] border-2 border-dashed border-slate-100">
                            <p class="text-[10px] font-black text-slate-300 uppercase tracking-widest">Data Belum Tersedia</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const maskInput = document.getElementById('amount_mask');
        const realInput = document.getElementById('amount_real');

        maskInput.addEventListener('input', function(e) {
            let value = this.value.replace(/[^0-9]/g, '');
            realInput.value = value;
            this.value = value ? new Intl.NumberFormat('id-ID').format(value) : '';
        });

        function quickEdit(beltId, amount) {
            document.getElementById('belt_level_id').value = beltId;
            realInput.value = amount;
            maskInput.value = new Intl.NumberFormat('id-ID').format(amount);
            
            // Fokus ke form
            document.getElementById('feeForm').scrollIntoView({ behavior: 'smooth', block: 'center' });
            maskInput.focus();
            
            // Visual feedback
            maskInput.parentElement.classList.add('ring-2', 'ring-indigo-500');
            setTimeout(() => maskInput.parentElement.classList.remove('ring-2', 'ring-indigo-500'), 1000);
        }
    </script>
</x-app-layout>