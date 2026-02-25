<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center px-4">
            <div>
                <h2 class="font-black text-lg text-slate-800 uppercase tracking-tight">Scoring</h2>
                <p class="text-[10px] text-slate-500 font-medium italic">{{ $exam->name }}</p>
            </div>
            <div class="flex items-center gap-3">
                {{-- Status Indicator --}}
                <div id="save-status" class="hidden items-center gap-1.5 transition-all">
                    <span class="relative flex h-2 w-2">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-indigo-500"></span>
                    </span>
                    <span class="text-[10px] font-bold text-slate-400 uppercase italic">Menyimpan...</span>
                </div>

                <button type="button" onclick="resetOrder()"
                    class="bg-slate-200 text-slate-600 px-3 py-2 rounded-lg font-black text-[10px] uppercase tracking-widest hover:bg-slate-300 transition-all">
                    Reset Urutan
                </button>
            </div>
        </div>
    </x-slot>

    @php
        $beltColors = [
            'Hitam' => 'bg-slate-900 text-white',
            'Cokelat' => 'bg-amber-800 text-white',
            'Ungu' => 'bg-purple-600 text-white',
            'Biru' => 'bg-blue-600 text-white',
            'Hijau' => 'bg-emerald-600 text-white',
            'Orange' => 'bg-orange-500 text-white',
            'Kuning Tua' => 'bg-yellow-500 text-white',
            'Kuning Muda' => 'bg-yellow-300 text-yellow-900',
            'Putih' => 'bg-slate-100 text-slate-600 border border-slate-200',
        ];
        $allBeltLevels = \App\Models\BeltLevel::orderBy('order')->get();
        $sortedParticipants = $participants->sortByDesc(fn($p) => $p->currentBelt->order ?? 0);
        $availableBelts = $participants->map(fn($p) => $p->currentBelt->name ?? 'Putih')->unique();
    @endphp

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

    <div class="py-3 bg-slate-50/50 min-h-screen" x-data="{
        selectedBelts: [],
        gridCols: 'lg:grid-cols-4',
        gridMobile: 'grid-cols-1',
        showModal: false
    }" @open-modal.window="showModal = true">

        <div class="max-w-[1600px] mx-auto px-2">
            {{-- Toolbar & Layout Switcher --}}
            <div
                class="flex flex-wrap md:flex-nowrap items-center gap-2 mb-3 bg-white p-2 rounded-xl border border-slate-100 shadow-sm">
                <div class="flex-1 flex items-center gap-1 overflow-x-auto no-scrollbar px-1 py-1">
                    <span
                        class="text-[8px] md:text-[10px] font-black text-slate-400 uppercase pr-1 shrink-0">Filter:</span>
                    @foreach ($availableBelts as $beltName)
                        <button type="button"
                            @click="selectedBelts.includes('{{ $beltName }}') ? selectedBelts = selectedBelts.filter(b => b !== '{{ $beltName }}') : selectedBelts.push('{{ $beltName }}')"
                            :class="selectedBelts.includes('{{ $beltName }}') ? 'bg-indigo-600 text-white' :
                                'bg-slate-50 text-slate-400'"
                            class="px-2 py-1.5 rounded-lg text-[8px] md:text-[10px] font-black uppercase transition-all shrink-0 whitespace-nowrap">
                            {{ $beltName }}
                        </button>
                    @endforeach
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    <div class="flex items-center gap-0.5 border-r border-slate-100 pr-2 mr-2">
                        <div class="flex md:hidden gap-0.5">
                            @for ($i = 1; $i <= 2; $i++)
                                <button @click="gridMobile = 'grid-cols-{{ $i }}'"
                                    :class="gridMobile === 'grid-cols-{{ $i }}' ? 'bg-indigo-600 text-white' :
                                        'text-slate-300'"
                                    class="w-7 h-7 flex items-center justify-center rounded-lg transition-all font-black text-[10px]">
                                    {{ $i }}
                                </button>
                            @endfor
                        </div>
                        <div class="hidden md:flex gap-0.5">
                            @for ($i = 1; $i <= 5; $i++)
                                <button @click="gridCols = 'lg:grid-cols-{{ $i }}'"
                                    :class="gridCols === 'lg:grid-cols-{{ $i }}' ? 'bg-indigo-100 text-indigo-600' :
                                        'text-slate-300'"
                                    class="w-8 h-8 flex items-center justify-center rounded-lg transition-all font-black text-xs">
                                    {{ $i }}
                                </button>
                            @endfor
                        </div>
                    </div>

                    <button type="button" @click="window.dispatchEvent(new CustomEvent('open-modal'))"
                        class="bg-emerald-600 text-white px-4 py-2.5 rounded-lg font-black text-[10px] uppercase tracking-widest hover:bg-emerald-700 transition-all shadow-sm shadow-emerald-200 shrink-0">
                        Finalisasi Hasil
                    </button>
                </div>
            </div>

            <form id="scoringForm">
                @csrf
                <div id="drag-container" class="grid gap-2 transition-all duration-300" :class="[gridMobile, gridCols]">
                    @foreach ($sortedParticipants as $p)
                        @php
                            $targetUserId = $p->user_id;
                            $score = $existingScores[$targetUserId] ?? null;
                            $currentBelt = $p->currentBelt;
                            $beltClass = $beltColors[$currentBelt->name ?? 'Putih'] ?? $beltColors['Putih'];
                            $dbResult = $score->result ?? 'Lulus';
                            $dbNewBeltId = $score->new_belt_level_id ?? $p->target_belt_level_id;
                        @endphp

                        <div data-id="{{ $p->id }}" x-data="{ result: '{{ $dbResult }}' }"
                            x-show="selectedBelts.length === 0 || selectedBelts.includes('{{ $currentBelt->name ?? 'Putih' }}')"
                            class="participant-card bg-white rounded-2xl border border-slate-100 shadow-sm p-3 flex flex-col hover:border-indigo-300 transition-all group relative cursor-move">

                            <div
                                class="absolute top-0 left-0 bg-slate-50 text-slate-300 text-[8px] px-1.5 py-0.5 rounded-br-lg group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                                ☰
                            </div>

                            <div class="mb-3 mt-1 pl-2">
                                <h3
                                    class="text-[10px] md:text-[12px] font-black text-slate-800 uppercase truncate leading-tight">
                                    {{ $p->user->name }}
                                </h3>
                                <div class="flex items-center gap-1.5 mt-1">
                                    <span
                                        class="px-1.5 py-0.5 rounded text-[7px] md:text-[8px] font-black uppercase {{ $beltClass }}">
                                        {{ $currentBelt->name ?? 'Putih' }}
                                    </span>
                                    <span
                                        class="text-[8px] md:text-[10px] font-bold text-slate-400 italic">{{ $currentBelt->kyu_dan ?? '' }}</span>
                                </div>
                            </div>

                            <div class="space-y-2.5 mt-auto">
                                <div class="grid grid-cols-1 gap-1.5">
                                    @foreach (['kihon', 'kata', 'kumite'] as $field)
                                        <div class="flex flex-col gap-1">
                                            <span
                                                class="text-[7px] md:text-[9px] font-black text-slate-400 uppercase px-1 tracking-tighter">{{ $field }}</span>
                                            <div class="flex gap-1">
                                                @foreach (['Kurang' => 'K', 'Baik' => 'B', 'Sangat Baik' => 'SB'] as $full => $short)
                                                    <label class="flex-1 cursor-pointer">
                                                        <input type="radio"
                                                            name="scores[{{ $targetUserId }}][{{ $field }}]"
                                                            value="{{ $full }}" class="hidden peer auto-save"
                                                            {{ $score && $score->$field == $full ? 'checked' : '' }}>
                                                        <span
                                                            class="block text-center py-1.5 rounded-lg bg-slate-50 text-[9px] md:text-[11px] font-black text-slate-400 border border-slate-50 peer-checked:bg-indigo-600 peer-checked:text-white transition-all uppercase italic">
                                                            {{ $short }}
                                                        </span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="pt-2 border-t border-slate-50 space-y-1">
                                    <select name="scores[{{ $targetUserId }}][result]" x-model="result"
                                        class="auto-save w-full py-1.5 border-none bg-slate-100 rounded-lg text-[9px] md:text-[11px] font-black uppercase text-slate-700 focus:ring-1 focus:ring-indigo-500 cursor-pointer">
                                        <option value="Lulus">Lulus</option>
                                        <option value="Percobaan">Percobaan</option>
                                        <option value="Tidak Lulus">Gagal</option>
                                    </select>

                                    <div x-show="result === 'Lulus'" x-transition
                                        class="p-1 bg-indigo-50 rounded-lg mt-1">
                                        <select name="scores[{{ $targetUserId }}][new_belt_level_id]"
                                            class="auto-save w-full py-1 border-none bg-white rounded text-[8px] md:text-[10px] font-bold text-slate-600 shadow-sm cursor-pointer">
                                            @foreach ($allBeltLevels as $bl)
                                                <option value="{{ $bl->id }}"
                                                    {{ $dbNewBeltId == $bl->id ? 'selected' : '' }}>
                                                    {{ $bl->name }} {{ $bl->kyu_dan }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </form>
        </div>

        {{-- MODAL RESUME KELULUSAN --}}
        <div x-show="showModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4" x-cloak>
            <div x-show="showModal" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" @click="showModal = false"
                class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"></div>

            <div x-show="showModal" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                class="relative bg-white rounded-3xl shadow-2xl max-w-2xl w-full max-h-[85vh] flex flex-col overflow-hidden">

                <div class="p-6 border-b border-slate-100">
                    <h3 class="text-xl font-black text-slate-800 uppercase italic">Resume Kelulusan</h3>
                    <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wider">Tinjau data sebelum masuk
                        ke Belt History</p>
                </div>

                <div class="p-4 overflow-y-auto bg-slate-50/50 flex-1">
                    <div class="space-y-2">
                        @foreach ($sortedParticipants as $p)
                            @php
                                $s = $existingScores[$p->user_id] ?? null;
                                $res = $s->result ?? 'Lulus';
                                $targetId = $s->new_belt_level_id ?? $p->target_belt_level_id;
                                $nb = $allBeltLevels->firstWhere('id', $targetId);
                            @endphp
                            <div
                                class="bg-white p-3 rounded-xl border border-slate-100 flex items-center justify-between">
                                <div>
                                    <p class="text-xs font-black text-slate-800 uppercase">{{ $p->user->name }}</p>
                                    <p class="text-[9px] font-bold text-slate-400">
                                        {{ $p->currentBelt->name ?? 'Putih' }} ({{ $p->currentBelt->kyu_dan ?? '-' }})
                                        →
                                        <span class="text-indigo-600 font-black">
                                            @if ($res == 'Lulus')
                                                {{ $nb->name ?? 'Belum Dipilih' }} ({{ $nb->kyu_dan ?? '-' }})
                                            @else
                                                Tetap ({{ $p->currentBelt->name ?? 'Putih' }})
                                            @endif
                                        </span>
                                    </p>
                                </div>
                                <div class="text-right">
                                    <span
                                        class="px-2 py-1 rounded text-[8px] font-black uppercase {{ $res == 'Lulus' ? 'bg-emerald-100 text-emerald-600' : ($res == 'Percobaan' ? 'bg-amber-100 text-amber-600' : 'bg-rose-100 text-rose-600') }}">
                                        {{ $res }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="p-6 border-t border-slate-100 flex gap-3 bg-white">
                    <button @click="showModal = false"
                        class="flex-1 py-3 text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-slate-600 transition-all">
                        Kembali Edit
                    </button>
                    <form action="{{ route('admin.exams.scoring.finalize', $exam) }}" method="POST" class="flex-1">
                        @csrf
                        <button type="submit"
                            onclick="return confirm('Data akan dipindahkan ke Belt History & Mengubah Sabuk Member. Lanjutkan?')"
                            class="w-full py-3 bg-indigo-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-indigo-700 shadow-lg shadow-indigo-100 transition-all">
                            Konfirmasi & Simpan Ke Riwayat
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        [x-cloak] {
            display: none !important;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('scoringForm');
            const statusIndicator = document.getElementById('save-status');

            const autoSave = async () => {
                statusIndicator.classList.remove('hidden');
                statusIndicator.classList.add('flex');
                const formData = new FormData(form);
                try {
                    const response = await fetch("{{ route('admin.exams.scoring.store', $exam) }}", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData
                    });
                    if (response.ok) {
                        setTimeout(() => {
                            statusIndicator.classList.add('hidden');
                            statusIndicator.classList.remove('flex');
                        }, 500);
                    }
                } catch (error) {
                    console.error('Gagal menyimpan:', error);
                }
            };

            form.addEventListener('change', (e) => {
                if (e.target.classList.contains('auto-save') || e.target.type === 'radio' || e.target
                    .tagName === 'SELECT') {
                    autoSave();
                }
            });

            const el = document.getElementById('drag-container');
            const savedOrder = localStorage.getItem('scoring-order-{{ $exam->id }}');
            if (savedOrder) {
                const orderArr = JSON.parse(savedOrder);
                orderArr.forEach(id => {
                    const item = el.querySelector(`[data-id="${id}"]`);
                    if (item) el.appendChild(item);
                });
            }

            new Sortable(el, {
                animation: 150,
                ghostClass: 'opacity-20',
                handle: '.participant-card',
                onEnd: function() {
                    const order = Array.from(el.querySelectorAll('[data-id]')).map(item => item
                        .getAttribute('data-id'));
                    localStorage.setItem('scoring-order-{{ $exam->id }}', JSON.stringify(order));
                }
            });
        });

        function resetOrder() {
            if (confirm('Reset urutan?')) {
                localStorage.removeItem('scoring-order-{{ $exam->id }}');
                window.location.reload();
            }
        }
    </script>
</x-app-layout>
