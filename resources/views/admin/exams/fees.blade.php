<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div>
                <h2 class="font-black text-xl sm:text-3xl text-slate-900 leading-tight tracking-tighter uppercase">
                    Master <span class="text-red-600">Biaya</span>
                </h2>
                <p class="text-[8px] sm:text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mt-0.5">
                    Konfigurasi Tarif Sabuk
                </p>
            </div>
        </div>
    </x-slot>

    {{-- Floating Back Button (Mobile) --}}
    <div class="fixed bottom-6 left-6 z-50 md:hidden">
        <a href="{{ route('admin.exams.index') }}"
            class="flex items-center justify-center w-14 h-14 bg-slate-900 text-white rounded-2xl shadow-[0_10px_25px_rgba(15,23,42,0.35)] active:scale-90 transition-transform border-b-4 border-slate-700">
            <svg class="w-7 h-7 stroke-[3]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="square" stroke-linejoin="square" d="M10 6l-6 6 6 6M4 12h16" />
            </svg>
        </a>
    </div>

    @php
        function getBeltColor($beltName)
        {
            $name = strtolower($beltName);
            if (str_contains($name, 'putih')) {
                return 'bg-white text-slate-400 border-slate-200';
            }
            if (str_contains($name, 'kuning muda')) {
                return 'bg-yellow-50 text-yellow-500 border-yellow-200';
            }
            if (str_contains($name, 'kuning')) {
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
            if (str_contains($name, 'coklat') || str_contains($name, 'cokelat')) {
                return 'bg-amber-800 text-white border-amber-900';
            }
            if (str_contains($name, 'hitam')) {
                return 'bg-slate-900 text-white border-slate-950';
            }
            return 'bg-slate-50 text-slate-600 border-slate-200';
        }
    @endphp

    <div class="py-6 sm:py-8 bg-slate-50 min-h-screen font-sans text-slate-700">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5">

            {{-- Toolbar Desktop --}}
            <div class="hidden md:flex justify-between items-end">
                <div>
                    <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest">
                        Pengaturan Tarif Ujian
                    </h3>
                    <p class="text-[10px] text-slate-400 font-bold uppercase mt-1">
                        Total: {{ $fees->count() }} Tingkatan Tarif
                    </p>
                </div>

                <a href="{{ route('admin.exams.index') }}"
                    class="inline-flex items-center bg-white hover:bg-slate-50 text-slate-600 px-5 py-3 rounded-xl text-[10px] font-black transition-all duration-300 hover:-translate-y-1 shadow-lg shadow-slate-200 uppercase tracking-[0.15em] border-2 border-slate-200">
                    <svg class="w-4 h-4 me-2 stroke-[3]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="square" stroke-linejoin="square" d="M10 6l-6 6 6 6M4 12h16" />
                    </svg>
                    Kembali
                </a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">

                {{-- FORM --}}
                <div class="lg:col-span-4 lg:sticky lg:top-24 space-y-4">
                    @if (session('success'))
                        <div
                            class="p-4 bg-emerald-500 text-white rounded-2xl shadow-lg shadow-emerald-500/20 flex items-center gap-3">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            <span
                                class="text-[10px] font-black uppercase tracking-widest">{{ session('success') }}</span>
                        </div>
                    @endif

                    <div
                        class="bg-white rounded-[2rem] shadow-xl shadow-slate-200/50 border-2 border-slate-100 p-5 sm:p-6 relative overflow-hidden">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em]">
                                Input Data Baru
                            </h3>
                            <span class="text-[9px] font-black text-slate-300 uppercase tracking-widest">
                                FORM
                            </span>
                        </div>

                        <form id="feeForm" action="{{ route('admin.exams.fees.store') }}" method="POST"
                            class="space-y-4">
                            @csrf

                            <div class="space-y-2">
                                <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">
                                    Kenaikan Sabuk <span class="text-red-600">*</span>
                                </label>
                                <select name="belt_level_id" id="belt_level_id" required
                                    class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl px-4 py-3 text-xs font-bold focus:ring-0 focus:border-slate-900 transition-all uppercase">
                                    <option value="">Pilih Progres...</option>
                                    @foreach ($belts as $belt)
                                        @php $prev = $belts->where('order', '<', $belt->order)->sortByDesc('order')->first(); @endphp
                                        <option value="{{ $belt->id }}">
                                            {{ $prev ? $prev->name . ' → ' : '' }}{{ $belt->name }}
                                            ({{ $belt->kyu_dan }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="space-y-2">
                                <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">
                                    Biaya (Rp) <span class="text-red-600">*</span>
                                </label>
                                <div class="relative group">
                                    <span
                                        class="absolute left-4 top-1/2 -translate-y-1/2 text-[10px] font-black text-slate-300 uppercase">
                                        Rp
                                    </span>
                                    <input type="text" id="amount_mask" required placeholder="0"
                                        class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl pl-10 pr-4 py-3 text-xs font-black focus:ring-0 focus:border-slate-900 transition-all">
                                </div>
                                <input type="hidden" name="amount" id="amount_real">
                                <p class="text-[10px] text-slate-500 italic">
                                    * Format otomatis. Contoh: 150.000
                                </p>
                            </div>

                            <button type="submit"
                                class="w-full bg-slate-900 text-white rounded-2xl py-3.5 font-black text-[10px] uppercase tracking-[0.2em] hover:bg-red-600 transition-all shadow-lg shadow-slate-900/10 border-b-4 border-slate-700 hover:border-red-800 active:translate-y-1">
                                Simpan Data
                            </button>
                        </form>
                    </div>
                </div>

                {{-- LIST --}}
                <div class="lg:col-span-8 space-y-3">
                    <div class="flex items-center justify-between px-1 sm:px-2">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em]">
                            Master List Biaya
                        </p>
                        <span
                            class="text-[9px] font-black text-slate-500 bg-white border border-slate-200 px-3 py-1 rounded-full uppercase">
                            {{ $fees->count() }} Tingkatan
                        </span>
                    </div>

                    <div class="grid gap-3">
                        @forelse($fees as $fee)
                            @php
                                $prevBelt = $belts
                                    ->where('order', '<', $fee->beltLevel->order)
                                    ->sortByDesc('order')
                                    ->first();
                            @endphp

                            {{-- MOBILE CARD (ikut style dojo/pengurus) --}}
                            <div
                                class="md:hidden relative bg-white rounded-[2rem] border border-slate-200 shadow-sm overflow-hidden active:scale-[0.98] transition-transform">
                                <div class="p-5 pb-4">
                                    <div class="flex justify-between items-start gap-4 mb-3">
                                        <div class="flex-1 min-w-0">
                                            <h4
                                                class="font-black text-slate-900 uppercase text-base leading-tight tracking-tight">
                                                {{ $fee->beltLevel->name }}
                                            </h4>

                                            <div class="flex items-center gap-2 mt-1">
                                                <span class="w-1.5 h-1.5 rounded-full bg-red-600"></span>
                                                <span
                                                    class="text-[9px] font-black text-slate-400 uppercase tracking-widest italic">
                                                    {{ $fee->beltLevel->kyu_dan }}
                                                </span>
                                            </div>
                                        </div>

                                        <div class="flex-shrink-0 flex flex-col items-end">
                                            <span
                                                class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest border bg-emerald-50 text-emerald-700 border-emerald-100">
                                                Rp{{ number_format($fee->amount, 0, ',', '.') }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="bg-slate-50 p-3 rounded-2xl border border-slate-100">
                                            <p
                                                class="text-[7px] font-black text-slate-400 uppercase tracking-widest mb-0.5">
                                                Progres
                                            </p>
                                            <p
                                                class="text-[10px] font-black text-slate-700 uppercase italic leading-tight">
                                                {{ $prevBelt ? $prevBelt->name . ' → ' . $fee->beltLevel->name : $fee->beltLevel->name }}
                                            </p>
                                        </div>

                                        <div class="bg-slate-50 p-3 rounded-2xl border border-slate-100">
                                            <p
                                                class="text-[7px] font-black text-slate-400 uppercase tracking-widest mb-0.5">
                                                Badge
                                            </p>
                                            <div class="mt-1">
                                                <span
                                                    class="inline-flex px-3 py-1 rounded-2xl text-[9px] font-black uppercase border {{ getBeltColor($fee->beltLevel->name) }}">
                                                    {{ $fee->beltLevel->name }}
                                                </span>
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
                                                Aksi
                                            </p>
                                            <p class="text-[9px] font-bold text-white uppercase mt-0.5">
                                                Kelola Tarif
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex gap-2">
                                        <button type="button"
                                            onclick="quickEdit('{{ $fee->belt_level_id }}', '{{ $fee->amount }}')"
                                            class="px-4 py-2 bg-white text-slate-900 rounded-xl text-[9px] font-black uppercase tracking-widest hover:bg-red-600 hover:text-white transition-all">
                                            Edit
                                        </button>

                                        <form action="{{ route('admin.exams.fees.destroy', $fee->id) }}"
                                            method="POST" onsubmit="return confirm('Hapus?')">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="px-4 py-2 bg-rose-50 text-rose-700 rounded-xl text-[9px] font-black uppercase tracking-widest hover:bg-rose-600 hover:text-white transition-all">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            {{-- DESKTOP ROW (tetap rapi) --}}
                            <div
                                class="hidden md:flex bg-white p-4 rounded-[2rem] shadow-sm border border-slate-100 items-center justify-between gap-3 group hover:border-slate-300 transition-all">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="flex items-center bg-slate-50 rounded-2xl p-2 pr-4 border border-slate-100">
                                        @if ($prevBelt)
                                            <div class="flex items-center gap-2 px-2 border-r border-slate-200 mr-2">
                                                <span
                                                    class="w-2 h-2 rounded-full {{ explode(' ', getBeltColor($prevBelt->name))[0] }}"></span>
                                                <span
                                                    class="text-[9px] font-bold text-slate-400 uppercase">{{ $prevBelt->name }}</span>
                                            </div>
                                        @endif
                                        <div class="flex items-center gap-2">
                                            <span
                                                class="px-3 py-1 rounded-2xl text-[9px] font-black uppercase border {{ getBeltColor($fee->beltLevel->name) }}">
                                                {{ $fee->beltLevel->name }}
                                            </span>
                                            <span
                                                class="text-[8px] font-bold text-slate-400 uppercase italic">{{ $fee->beltLevel->kyu_dan }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center gap-5">
                                    <div class="text-right">
                                        <p class="text-[7px] font-black text-slate-300 uppercase tracking-widest">Biaya
                                            Ujian</p>
                                        <span class="font-black text-slate-900 text-sm tabular-nums">
                                            Rp{{ number_format($fee->amount, 0, ',', '.') }}
                                        </span>
                                    </div>

                                    <div class="flex gap-2">
                                        <button type="button"
                                            onclick="quickEdit('{{ $fee->belt_level_id }}', '{{ $fee->amount }}')"
                                            class="inline-flex items-center justify-center px-4 py-2 rounded-2xl bg-white border-2 border-slate-200 text-slate-700 text-[10px] font-black uppercase tracking-widest hover:bg-slate-900 hover:text-white hover:border-slate-900 transition-all">
                                            Edit
                                        </button>

                                        <form action="{{ route('admin.exams.fees.destroy', $fee->id) }}"
                                            method="POST" onsubmit="return confirm('Hapus?')">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="inline-flex items-center justify-center px-4 py-2 rounded-2xl bg-white border-2 border-slate-200 text-rose-600 text-[10px] font-black uppercase tracking-widest hover:bg-rose-600 hover:text-white hover:border-rose-700 transition-all">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                        @empty
                            <div
                                class="py-12 text-center bg-white rounded-[2rem] border-2 border-dashed border-slate-100">
                                <p class="text-[10px] font-black text-slate-300 uppercase tracking-widest">Data Belum
                                    Tersedia</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="h-16 md:hidden"></div>
        </div>
    </div>

    <script>
        const maskInput = document.getElementById('amount_mask');
        const realInput = document.getElementById('amount_real');

        maskInput?.addEventListener('input', function() {
            let value = this.value.replace(/[^0-9]/g, '');
            realInput.value = value;
            this.value = value ? new Intl.NumberFormat('id-ID').format(value) : '';
        });

        function quickEdit(beltId, amount) {
            document.getElementById('belt_level_id').value = beltId;
            realInput.value = amount;
            maskInput.value = new Intl.NumberFormat('id-ID').format(amount);

            document.getElementById('feeForm').scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
            maskInput.focus();

            maskInput.parentElement.classList.add('ring-2', 'ring-slate-900/20');
            setTimeout(() => maskInput.parentElement.classList.remove('ring-2', 'ring-slate-900/20'), 900);
        }
    </script>
</x-app-layout>
