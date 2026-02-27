<x-app-layout>
    <x-slot name="header">
        <div class="flex items-start sm:items-center justify-between gap-3 px-1 sm:px-2">
            <div class="min-w-0">
                <h2 class="font-black text-xl sm:text-3xl text-slate-900 leading-tight tracking-tighter uppercase">
                    Scoring <span class="text-red-600">Ujian</span>
                </h2>
                <p class="text-[8px] sm:text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mt-1">
                    {{ $exam->name }}
                </p>
            </div>

            <div class="flex items-center gap-3 shrink-0">
                {{-- Status Indicator --}}
                <div id="save-status" class="hidden items-center gap-2 transition-all">
                    <span class="relative flex h-2 w-2">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-40"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-red-600"></span>
                    </span>
                    <span
                        class="text-[9px] font-black text-slate-400 uppercase tracking-widest italic">Menyimpan…</span>
                </div>
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
        $currentUser = auth()->user();
        $isStruktural = $currentUser->hasRole(['pb', 'pengprov', 'admin']);

        $assignedExaminers = \DB::table('exam_examiners')
            ->join('users', 'exam_examiners.user_id', '=', 'users.id')
            ->where('exam_examiners.exam_id', $exam->id)
            ->select('users.name')
            ->get();

        $isAuthorizedExaminer = \DB::table('exam_examiners')
            ->where('exam_id', $exam->id)
            ->where('user_id', $currentUser->id)
            ->exists();

        $canScore = $isStruktural || $isAuthorizedExaminer;

        $allScores = \App\Models\ExamScore::where('exam_id', $exam->id)
            ->leftJoin('users', 'exam_scores.examiner_id', '=', 'users.id')
            ->select('exam_scores.*', 'users.name as examiner_name')
            ->get()
            ->keyBy('member_id');

        $sortedParticipants = $participants->sortByDesc(fn($p) => $p->currentBelt->order ?? 0);
        $availableBelts = $participants->map(fn($p) => $p->currentBelt->name ?? 'Putih')->unique();
    @endphp

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

    <div class="py-5 sm:py-6 bg-slate-50 min-h-screen" x-data="{
        selectedBelts: [],
        // IMPORTANT: jangan pakai lg:grid-cols-*, karena itu bikin '3' dan '5' terasa tidak sesuai di beberapa breakpoint.
        // Kita pakai grid-cols responsif yang jelas.
        gridCols: 'xl:grid-cols-4',
        gridMobile: 'grid-cols-1',
        showModal: false,
        openResume() {
            window.updateResume();
            this.showModal = true;
        }
    }">

        <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 space-y-3">

            {{-- PENGUJI AKTIF (chips) --}}
            <div class="flex items-center gap-2 overflow-x-auto no-scrollbar py-1">
                <span class="text-[8px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest shrink-0">
                    Penguji Aktif:
                </span>

                @forelse ($assignedExaminers as $ex)
                    <div
                        class="flex items-center gap-2 bg-white border border-slate-200 px-3 py-1.5 rounded-full shadow-sm shrink-0">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        <span class="text-[9px] font-black text-slate-700 uppercase tracking-tighter">
                            {{ $ex->name }}
                        </span>
                    </div>
                @empty
                    <span class="text-[9px] font-bold text-rose-500 italic">Belum ada penguji terdaftar</span>
                @endforelse
            </div>

            {{-- TOOLBAR (rapat + efisien) --}}
            <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm p-3 sm:p-4">
                <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">

                    {{-- Filter sabuk --}}
                    <div class="flex items-center gap-2 overflow-x-auto no-scrollbar">
                        <span
                            class="text-[8px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest shrink-0">
                            Filter:
                        </span>

                        @foreach ($availableBelts as $beltName)
                            <button type="button"
                                @click="selectedBelts.includes('{{ $beltName }}') ? selectedBelts = selectedBelts.filter(b => b !== '{{ $beltName }}') : selectedBelts.push('{{ $beltName }}')"
                                :class="selectedBelts.includes('{{ $beltName }}') ?
                                    'bg-slate-900 text-white border-slate-900' :
                                    'bg-slate-50 text-slate-600 border-slate-200'"
                                class="px-3 py-2 rounded-2xl text-[9px] sm:text-[10px] font-black uppercase tracking-widest transition-all shrink-0 whitespace-nowrap border hover:border-slate-300">
                                {{ $beltName }}
                            </button>
                        @endforeach
                    </div>

                    {{-- Right controls --}}
                    <div class="flex items-center justify-between sm:justify-end gap-2">
                        {{-- Layout switcher (FIX: set class responsif lengkap, bukan lg:grid-cols-*) --}}
                        <div class="flex items-center gap-1 border border-slate-200 rounded-2xl bg-slate-50 p-1">
                            @for ($i = 1; $i <= 5; $i++)
                                <button type="button"
                                    @click="
                                        if (window.innerWidth < 768) {
                                            gridMobile = 'grid-cols-{{ $i }}';
                                        } else {
                                            // Desktop: pakai base md:grid-cols-2 biar tidak kepadatan di layar kecil,
                                            // dan tingkatkan hanya di xl agar 3/5 benar-benar terasa.
                                            gridCols =
                                                ({{ $i }} === 1) ? 'md:grid-cols-2 xl:grid-cols-1' :
                                                ({{ $i }} === 2) ? 'md:grid-cols-2 xl:grid-cols-2' :
                                                ({{ $i }} === 3) ? 'md:grid-cols-2 xl:grid-cols-3' :
                                                ({{ $i }} === 4) ? 'md:grid-cols-2 xl:grid-cols-4' :
                                                'md:grid-cols-2 xl:grid-cols-5';
                                        }
                                    "
                                    :class="(window.innerWidth < 768 ? gridMobile : gridCols).includes(
                                            'xl:grid-cols-{{ $i }}') || (window.innerWidth < 768 &&
                                            gridMobile === 'grid-cols-{{ $i }}') ?
                                        'bg-white text-slate-900 shadow-sm' :
                                        'text-slate-400 hover:text-slate-600'"
                                    class="w-9 h-9 flex items-center justify-center rounded-2xl transition-all font-black text-[10px]">
                                    {{ $i }}
                                </button>
                            @endfor
                        </div>

                        {{-- Reset di sebelah layout --}}
                        <button type="button" onclick="window.location.reload()"
                            class="inline-flex items-center justify-center bg-white border-2 border-slate-200 text-slate-500 px-4 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-50 transition-all">
                            Reset
                        </button>

                        {{-- Finalisasi --}}
                        <button type="button" @click="openResume()"
                            class="inline-flex items-center justify-center px-5 py-3 rounded-2xl bg-slate-900 text-white text-[10px] font-black uppercase tracking-widest hover:bg-red-600 transition-all border-b-4 border-slate-700 hover:border-red-800 active:translate-y-1">
                            Finalisasi
                        </button>
                    </div>
                </div>

                @if (!$canScore)
                    <div class="mt-3 p-3 bg-rose-50 border border-rose-100 rounded-2xl">
                        <p class="text-[10px] font-black text-rose-700 uppercase tracking-widest">
                            Akses Terbatas
                        </p>
                        <p class="text-[10px] font-bold text-slate-600 mt-1">
                            Anda tidak dapat melakukan penilaian pada sesi ini (bukan struktural & tidak ter-assign
                            sebagai penguji).
                        </p>
                    </div>
                @endif
            </div>

            {{-- GRID CARD --}}
            <form id="scoringForm">
                @csrf

                <div id="drag-container" class="grid gap-3 transition-all duration-300"
                    :class="[
                        gridMobile,
                        gridCols
                    ]">

                    @foreach ($sortedParticipants as $p)
                        @php
                            $targetUserId = $p->user_id;
                            $score = $allScores[$targetUserId] ?? null;
                            $currentBelt = $p->currentBelt;
                            $beltClass = $beltColors[$currentBelt->name ?? 'Putih'] ?? $beltColors['Putih'];
                        @endphp

                        <div data-id="{{ $p->id }}" data-user-id="{{ $targetUserId }}"
                            data-name="{{ $p->user->name }}" x-data="participantCard('{{ $targetUserId }}', '{{ $score->result ?? 'Lulus' }}', '{{ $score->examiner_name ?? '' }}')"
                            x-show="selectedBelts.length === 0 || selectedBelts.includes('{{ $currentBelt->name ?? 'Putih' }}')"
                            class="participant-card relative bg-white rounded-[1rem] border border-slate-200 shadow-sm overflow-hidden group {{ $canScore ? 'cursor-move' : '' }}">

                            {{-- handle icon --}}
                            <div
                                class="absolute top-0 left-0 bg-slate-50 text-slate-300 text-[8px] px-2 py-1 rounded-br-xl group-hover:bg-slate-900 group-hover:text-white transition-colors">
                                ☰
                            </div>

                            {{-- examiner badge --}}
                            <template x-if="examiner">
                                <div
                                    class="absolute top-3 right-3 z-20 flex items-center gap-2 bg-amber-100 border border-amber-200 px-3 py-1 rounded-full shadow-sm">
                                    <div class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></div>
                                    <span class="text-[8px] font-black text-amber-800 uppercase italic"
                                        x-text="'Oleh: ' + examiner.split(' ')[0]"></span>
                                </div>
                            </template>

                            <input type="hidden" name="scores[{{ $targetUserId }}][examiner_id]"
                                x-model="examinerIdInput">

                            <div class="p-4 sm:p-4">
                                <div class="mb-3">
                                    <h3
                                        class="text-[12px] sm:text-[13px] font-black text-slate-900 uppercase truncate leading-tight">
                                        {{ $p->user->name }}
                                    </h3>

                                    <div class="flex items-center gap-2 mt-2">
                                        <span
                                            class="px-2 py-1 rounded-xl text-[8px] font-black uppercase {{ $beltClass }}">
                                            {{ $currentBelt->name ?? 'Putih' }}
                                        </span>
                                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">
                                            ID: #{{ $p->id }}
                                        </span>
                                    </div>
                                </div>

                                {{-- compact score blocks --}}
                                <div class="space-y-2.5">
                                    @foreach (['kihon', 'kata', 'kumite'] as $field)
                                        <div class="bg-slate-50 border border-slate-100 rounded-2xl p-3">
                                            <div class="flex items-center justify-between mb-2">
                                                <span
                                                    class="text-[9px] font-black text-slate-400 uppercase tracking-widest">
                                                    {{ $field }}
                                                </span>
                                                <span
                                                    class="text-[9px] font-black text-slate-300 uppercase tracking-widest">
                                                    K / B / SB
                                                </span>
                                            </div>

                                            <div class="grid grid-cols-3 gap-2">
                                                @foreach (['Kurang' => 'K', 'Baik' => 'B', 'Sangat Baik' => 'SB'] as $full => $short)
                                                    <label
                                                        class="{{ $canScore ? 'cursor-pointer' : 'cursor-not-allowed' }}">
                                                        <input type="radio"
                                                            name="scores[{{ $targetUserId }}][{{ $field }}]"
                                                            value="{{ $full }}" @click="markAsEdited()"
                                                            class="hidden peer {{ $canScore ? 'auto-save' : '' }}"
                                                            x-model="scores.{{ $field }}"
                                                            {{ !$canScore ? 'disabled' : '' }}>
                                                        <span
                                                            class="block text-center py-2 rounded-2xl bg-white border-2 border-slate-200 text-[10px] font-black text-slate-500
                                                                   peer-checked:bg-slate-900 peer-checked:text-white peer-checked:border-slate-900 transition-all uppercase italic">
                                                            {{ $short }}
                                                        </span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                {{-- result --}}
                                <div class="mt-3 pt-3 border-t border-slate-100 space-y-2.5">
                                    <div>
                                        <label
                                            class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-2">
                                            Keputusan
                                        </label>
                                        <select name="scores[{{ $targetUserId }}][result]" x-model="result"
                                            @change="markAsEdited()" {{ !$canScore ? 'disabled' : '' }}
                                            class="{{ $canScore ? 'auto-save' : '' }} w-full py-3 px-4 border-2 border-slate-200 bg-white rounded-2xl text-[11px] font-black uppercase text-slate-700 focus:ring-0 focus:border-slate-900 cursor-pointer disabled:opacity-50">
                                            <option value="Lulus">Lulus</option>
                                            <option value="Percobaan">Percobaan</option>
                                            <option value="Tidak Lulus">Gagal</option>
                                        </select>
                                    </div>

                                    <div x-show="result === 'Lulus'" x-transition
                                        class="p-3 bg-slate-50 border border-slate-100 rounded-2xl">
                                        <label
                                            class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-2">
                                            Sabuk Baru
                                        </label>
                                        <select name="scores[{{ $targetUserId }}][new_belt_level_id]"
                                            x-model="newBeltId" @change="markAsEdited()"
                                            {{ !$canScore ? 'disabled' : '' }}
                                            class="{{ $canScore ? 'auto-save' : '' }} w-full py-3 px-4 border-2 border-slate-200 bg-white rounded-2xl text-[10px] font-black uppercase text-slate-700 focus:ring-0 focus:border-slate-900 cursor-pointer disabled:opacity-50">
                                            @foreach ($allBeltLevels as $bl)
                                                <option value="{{ $bl->id }}">{{ $bl->name }}
                                                    {{ $bl->kyu_dan }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            @if (!$canScore)
                                <div
                                    class="absolute inset-0 bg-white/70 backdrop-blur-[2px] z-10 rounded-[2rem] flex flex-col items-center justify-center text-center p-6">
                                    <div
                                        class="w-10 h-10 bg-rose-100 text-rose-600 rounded-full flex items-center justify-center mb-2">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                        </svg>
                                    </div>
                                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">
                                        Akses Terbatas
                                    </p>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </form>

            <div class="h-8"></div>
        </div>

        {{-- MODAL RESUME --}}
        <div x-show="showModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4" x-cloak>
            <div x-show="showModal" @click="showModal = false"
                class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"></div>

            <div x-show="showModal"
                class="relative bg-white rounded-[2rem] shadow-2xl max-w-2xl w-full max-h-[85vh] flex flex-col overflow-hidden border border-slate-100">
                <div class="p-5 border-b border-slate-100 bg-white">
                    <h3 class="text-lg sm:text-xl font-black text-slate-900 uppercase tracking-tight">
                        Resume Kelulusan
                    </h3>
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] mt-1">
                        Ringkasan hasil per peserta
                    </p>
                </div>

                <div class="p-4 overflow-y-auto bg-slate-50/50 flex-1">
                    <div id="resume-container" class="space-y-2">
                        <p class="text-center text-[10px] text-slate-400 font-bold uppercase italic">
                            Memuat data terbaru...
                        </p>
                    </div>
                </div>

                <div class="p-5 border-t border-slate-100 flex gap-3 bg-white">
                    <button @click="showModal = false"
                        class="flex-1 py-3 rounded-2xl bg-white border-2 border-slate-200 text-slate-500 text-[10px] font-black uppercase tracking-widest hover:bg-slate-50 transition-all">
                        Kembali
                    </button>

                    @if ($canScore)
                        <form action="{{ route('admin.exams.scoring.finalize', $exam) }}" method="POST"
                            class="flex-1">
                            @csrf
                            <button type="submit" onclick="return confirm('Finalisasi hasil sekarang?')"
                                class="w-full py-3 rounded-2xl bg-slate-900 text-white text-[10px] font-black uppercase tracking-widest hover:bg-red-600 transition-all border-b-4 border-slate-700 hover:border-red-800 active:translate-y-1">
                                Finalisasi & Simpan
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        const CURRENT_USER_ID = {{ $currentUser->id }};
        const CURRENT_USER_NAME = "{{ $currentUser->name }}";

        document.addEventListener('alpine:init', () => {
            Alpine.data('participantCard', (userId, initialResult, initialExaminer) => ({
                userId: userId,
                result: initialResult,
                examiner: initialExaminer,
                examinerIdInput: null,
                newBeltId: null,
                scores: {
                    kihon: '',
                    kata: '',
                    kumite: ''
                },
                isEditing: false,

                init() {
                    @foreach ($allScores as $mid => $s)
                        if (this.userId == "{{ $mid }}") {
                            this.scores.kihon = "{{ $s->kihon }}";
                            this.scores.kata = "{{ $s->kata }}";
                            this.scores.kumite = "{{ $s->kumite }}";
                            this.newBeltId = "{{ $s->new_belt_level_id }}";
                            this.examinerIdInput = "{{ $s->examiner_id }}";
                        }
                    @endforeach
                },

                markAsEdited() {
                    this.isEditing = true;
                    this.examiner = CURRENT_USER_NAME;
                    this.examinerIdInput = CURRENT_USER_ID;
                }
            }));
        });

        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('scoringForm');
            const statusIndicator = document.getElementById('save-status');
            const resumeContainer = document.getElementById('resume-container');

            window.updateResume = () => {
                let html = '<div class="grid grid-cols-1 gap-2">';
                document.querySelectorAll('.participant-card').forEach(card => {
                    const data = Alpine.$data(card);
                    const name = card.getAttribute('data-name');
                    const beltEl = card.querySelector('span.px-2');
                    const belt = beltEl ? beltEl.innerText : '-';

                    let resultColor = 'text-slate-400';
                    if (data.result === 'Lulus') resultColor = 'text-emerald-600';
                    if (data.result === 'Tidak Lulus') resultColor = 'text-rose-600';
                    if (data.result === 'Percobaan') resultColor = 'text-amber-600';

                    const nextBeltOpt = card.querySelector(
                        'select[name*="new_belt_level_id"] option[value="' + data.newBeltId + '"]');
                    const nextBeltText = nextBeltOpt ? nextBeltOpt.text : '-';

                    html += `
                        <div class="bg-white p-4 rounded-2xl border border-slate-100 flex justify-between items-center shadow-sm">
                            <div>
                                <p class="text-[10px] font-black text-slate-900 uppercase">${name}</p>
                                <p class="text-[8px] font-bold text-slate-400 uppercase tracking-widest italic">${belt}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-[10px] font-black uppercase ${resultColor}">${data.result}</p>
                                ${data.result === 'Lulus' ? `<p class="text-[8px] font-bold text-slate-600 uppercase italic">Naik: ${nextBeltText}</p>` : ''}
                            </div>
                        </div>
                    `;
                });
                html += '</div>';
                resumeContainer.innerHTML = html;
            };

            const autoSave = async () => {
                statusIndicator.classList.remove('hidden');
                statusIndicator.classList.add('flex');

                const formData = new FormData(form);
                try {
                    await fetch("{{ route('admin.exams.scoring.store', $exam) }}", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData
                    });

                    document.querySelectorAll('.participant-card').forEach(card => {
                        const alpineData = Alpine.$data(card);
                        if (alpineData) alpineData.isEditing = false;
                    });

                    setTimeout(() => {
                        statusIndicator.classList.add('hidden');
                    }, 700);
                } catch (e) {
                    console.error('Save failed', e);
                }
            };

            const fetchUpdates = async () => {
                try {
                    const response = await fetch("{{ route('admin.exams.scoring.show', $exam) }}", {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!response.ok) return;
                    const data = await response.json();

                    if (data.scores) {
                        document.querySelectorAll('.participant-card').forEach(card => {
                            const userId = card.getAttribute('data-user-id');
                            const alpineData = Alpine.$data(card);

                            if (data.scores[userId] && alpineData && !alpineData.isEditing) {
                                alpineData.result = data.scores[userId].result;
                                alpineData.examiner = data.scores[userId].examiner_name;
                                alpineData.scores.kihon = data.scores[userId].kihon;
                                alpineData.scores.kata = data.scores[userId].kata;
                                alpineData.scores.kumite = data.scores[userId].kumite;
                                alpineData.newBeltId = data.scores[userId].new_belt_level_id;
                            }
                        });
                    }
                } catch (e) {
                    console.error('Polling error:', e);
                }
            };

            setInterval(fetchUpdates, 3000);

            form.addEventListener('change', (e) => {
                if (e.target.classList.contains('auto-save')) autoSave();
            });

            const container = document.getElementById('drag-container');
            if (container) {
                new Sortable(container, {
                    animation: 150,
                    handle: '.cursor-move',
                    ghostClass: 'opacity-20'
                });
            }
        });
    </script>
</x-app-layout>
