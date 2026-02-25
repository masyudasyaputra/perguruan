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

                <button type="button" onclick="window.location.reload()"
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

    <div class="py-3 bg-slate-50/50 min-h-screen" x-data="{
        selectedBelts: [],
        gridCols: 'lg:grid-cols-4',
        gridMobile: 'grid-cols-1',
        showModal: false,
        openResume() {
            window.updateResume();
            this.showModal = true;
        }
    }">

        <div class="max-w-[1600px] mx-auto px-2">
            {{-- Section Daftar Penguji --}}
            <div class="mb-3 flex items-center gap-2 overflow-x-auto no-scrollbar py-1">
                <span class="text-[8px] md:text-[10px] font-black text-slate-400 uppercase shrink-0">Penguji
                    Aktif:</span>
                @forelse ($assignedExaminers as $ex)
                    <div
                        class="flex items-center gap-1.5 bg-white border border-slate-200 px-2.5 py-1 rounded-full shadow-sm shrink-0">
                        <div class="w-1.5 h-1.5 rounded-full bg-emerald-500"></div>
                        <span
                            class="text-[9px] font-black text-slate-700 uppercase tracking-tighter">{{ $ex->name }}</span>
                    </div>
                @empty
                    <span class="text-[9px] font-bold text-rose-400 italic">Belum ada penguji terdaftar</span>
                @endforelse
            </div>

            <div
                class="flex flex-wrap md:flex-nowrap items-center gap-2 mb-3 bg-white p-2 rounded-xl border border-slate-100 shadow-sm">
                <div class="flex-1 flex items-center gap-1 overflow-x-auto no-scrollbar px-1 py-1">
                    <span class="text-[8px] md:text-[10px] font-black text-slate-400 uppercase pr-1 shrink-0">Filter
                        Sabuk:</span>
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
                        @for ($i = 1; $i <= 5; $i++)
                            <button
                                @click="window.innerWidth < 768 ? gridMobile = 'grid-cols-{{ $i }}' : gridCols = 'lg:grid-cols-{{ $i }}'"
                                :class="(window.innerWidth < 768 ? gridMobile : gridCols).includes('{{ $i }}') ?
                                    'bg-indigo-100 text-indigo-600' : 'text-slate-300'"
                                class="w-8 h-8 flex items-center justify-center rounded-lg transition-all font-black text-xs">
                                {{ $i }}
                            </button>
                        @endfor
                    </div>

                    <button type="button" @click="openResume()"
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
                            $score = $allScores[$targetUserId] ?? null;
                            $currentBelt = $p->currentBelt;
                            $beltClass = $beltColors[$currentBelt->name ?? 'Putih'] ?? $beltColors['Putih'];
                        @endphp

                        <div data-id="{{ $p->id }}" data-user-id="{{ $targetUserId }}"
                            data-name="{{ $p->user->name }}" x-data="participantCard('{{ $targetUserId }}', '{{ $score->result ?? 'Lulus' }}', '{{ $score->examiner_name ?? '' }}')"
                            x-show="selectedBelts.length === 0 || selectedBelts.includes('{{ $currentBelt->name ?? 'Putih' }}')"
                            class="participant-card bg-white rounded-2xl border border-slate-100 shadow-sm p-3 flex flex-col hover:border-indigo-300 transition-all group relative {{ $canScore ? 'cursor-move' : '' }}">

                            {{-- Indikator Nama Penguji --}}
                            <template x-if="examiner">
                                <div
                                    class="absolute -top-1 -right-1 z-20 flex items-center gap-1 bg-amber-100 border border-amber-200 px-2 py-0.5 rounded-full shadow-sm">
                                    <div class="w-1 h-1 rounded-full bg-amber-500 animate-pulse"></div>
                                    <span class="text-[7px] font-black text-amber-700 uppercase italic"
                                        x-text="'Oleh: ' + examiner.split(' ')[0]"></span>
                                </div>
                            </template>

                            <input type="hidden" name="scores[{{ $targetUserId }}][examiner_id]"
                                x-model="examinerIdInput">

                            @if (!$canScore)
                                <div
                                    class="absolute inset-0 bg-white/70 backdrop-blur-[2px] z-10 rounded-2xl flex flex-col items-center justify-center text-center p-6">
                                    <div
                                        class="w-10 h-10 bg-rose-100 text-rose-600 rounded-full flex items-center justify-center mb-2">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                        </svg>
                                    </div>
                                    <p class="text-[8px] font-bold text-slate-400 uppercase leading-tight mt-1">Akses
                                        Terbatas</p>
                                </div>
                            @endif

                            <div
                                class="absolute top-0 left-0 bg-slate-50 text-slate-300 text-[8px] px-1.5 py-0.5 rounded-br-lg group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                                ☰</div>

                            <div class="mb-3 mt-1 pl-2">
                                <h3
                                    class="text-[10px] md:text-[12px] font-black text-slate-800 uppercase truncate leading-tight">
                                    {{ $p->user->name }}</h3>
                                <div class="flex items-center gap-1.5 mt-1">
                                    <span
                                        class="px-1.5 py-0.5 rounded text-[7px] md:text-[8px] font-black uppercase {{ $beltClass }}">{{ $currentBelt->name ?? 'Putih' }}</span>
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
                                                    <label class="flex-1 {{ $canScore ? 'cursor-pointer' : '' }}">
                                                        <input type="radio"
                                                            name="scores[{{ $targetUserId }}][{{ $field }}]"
                                                            value="{{ $full }}" @click="markAsEdited()"
                                                            class="hidden peer {{ $canScore ? 'auto-save' : '' }}"
                                                            x-model="scores.{{ $field }}"
                                                            {{ !$canScore ? 'disabled' : '' }}>
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
                                        @change="markAsEdited()" {{ !$canScore ? 'disabled' : '' }}
                                        class="{{ $canScore ? 'auto-save' : '' }} w-full py-1.5 border-none bg-slate-100 rounded-lg text-[9px] md:text-[11px] font-black uppercase text-slate-700 focus:ring-1 focus:ring-indigo-500 cursor-pointer disabled:opacity-50">
                                        <option value="Lulus">Lulus</option>
                                        <option value="Percobaan">Percobaan</option>
                                        <option value="Tidak Lulus">Gagal</option>
                                    </select>

                                    <div x-show="result === 'Lulus'" x-transition
                                        class="p-1 bg-indigo-50 rounded-lg mt-1">
                                        <select name="scores[{{ $targetUserId }}][new_belt_level_id]"
                                            x-model="newBeltId" @change="markAsEdited()"
                                            {{ !$canScore ? 'disabled' : '' }}
                                            class="{{ $canScore ? 'auto-save' : '' }} w-full py-1 border-none bg-white rounded text-[8px] md:text-[10px] font-bold text-slate-600 shadow-sm cursor-pointer disabled:opacity-50">
                                            @foreach ($allBeltLevels as $bl)
                                                <option value="{{ $bl->id }}">{{ $bl->name }}
                                                    {{ $bl->kyu_dan }}</option>
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

        {{-- MODAL RESUME --}}
        <div x-show="showModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4" x-cloak>
            <div x-show="showModal" @click="showModal = false" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm">
            </div>
            <div x-show="showModal"
                class="relative bg-white rounded-3xl shadow-2xl max-w-2xl w-full max-h-[85vh] flex flex-col overflow-hidden">
                <div class="p-6 border-b border-slate-100">
                    <h3 class="text-xl font-black text-slate-800 uppercase italic">Resume Kelulusan</h3>
                </div>
                <div class="p-4 overflow-y-auto bg-slate-50/50 flex-1">
                    <div id="resume-container" class="space-y-2">
                        <p class="text-center text-[10px] text-slate-400 font-bold uppercase italic">Memuat data
                            terbaru...</p>
                    </div>
                </div>
                <div class="p-6 border-t border-slate-100 flex gap-3 bg-white">
                    <button @click="showModal = false"
                        class="flex-1 py-3 text-[10px] font-black uppercase tracking-widest text-slate-400">Kembali</button>
                    @if ($canScore)
                        <form action="{{ route('admin.exams.scoring.finalize', $exam) }}" method="POST"
                            class="flex-1">
                            @csrf
                            <button type="submit" onclick="return confirm('Finalisasi hasil sekarang?')"
                                class="w-full py-3 bg-indigo-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-indigo-700 shadow-lg shadow-indigo-100 transition-all">
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

            // Function to Generate Resume HTML
            window.updateResume = () => {
                let html = '<div class="grid grid-cols-1 gap-2">';
                document.querySelectorAll('.participant-card').forEach(card => {
                    const data = Alpine.$data(card);
                    const name = card.getAttribute('data-name');
                    const belt = card.querySelector('.px-1\\.5').innerText;

                    let resultColor = 'text-slate-400';
                    if (data.result === 'Lulus') resultColor = 'text-emerald-600';
                    if (data.result === 'Tidak Lulus') resultColor = 'text-rose-600';
                    if (data.result === 'Percobaan') resultColor = 'text-amber-600';

                    html += `
                        <div class="bg-white p-3 rounded-xl border border-slate-100 flex justify-between items-center shadow-sm">
                            <div>
                                <p class="text-[10px] font-black text-slate-800 uppercase">${name}</p>
                                <p class="text-[8px] font-bold text-slate-400 uppercase italic">${belt}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-[10px] font-black uppercase ${resultColor}">${data.result}</p>
                                ${data.result === 'Lulus' ? `<p class="text-[8px] font-bold text-indigo-500 uppercase italic">Naik ke: ${card.querySelector('select[name*="new_belt_level_id"] option[value="'+data.newBeltId+'"]')?.text || '-'}</p>` : ''}
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
                    }, 800);
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
                if (e.target.classList.contains('auto-save')) {
                    autoSave();
                }
            });

            if (document.getElementById('drag-container')) {
                new Sortable(document.getElementById('drag-container'), {
                    animation: 150,
                    handle: '.cursor-move',
                    ghostClass: 'opacity-20'
                });
            }
        });
    </script>
</x-app-layout>
