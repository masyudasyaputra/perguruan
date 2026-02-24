<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center px-4">
            <div>
                <h2 class="font-black text-xl text-slate-800 uppercase tracking-tight">Scoring</h2>
                <p class="text-xs text-slate-500 font-medium italic">{{ $exam->name }}</p>
            </div>
            <button type="submit" form="scoringForm" class="bg-indigo-600 text-white px-6 py-2.5 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-slate-900 shadow-md transition-all active:scale-95">
                Simpan Nilai
            </button>
        </div>
    </x-slot>

    <div class="py-6 bg-slate-50/50 min-h-screen" x-data="{ 
        selectedBelts: [], 
        gridCols: 'lg:grid-cols-3',
        gridMobile: 'grid-cols-1' 
    }">
        <div class="max-w-7xl mx-auto px-4">

            @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" class="mb-6 p-4 bg-emerald-500 text-white rounded-2xl shadow-lg flex items-center justify-between">
                    <span class="text-xs font-black uppercase tracking-widest">{{ session('success') }}</span>
                    <button @click="show = false"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="6 18L18 6M6 6l12 12" stroke-width="2.5"></path></svg></button>
                </div>
            @endif

            <div class="flex flex-col md:flex-row gap-4 mb-6">
                <div class="flex-1 flex flex-wrap items-center gap-2 bg-white p-3 rounded-2xl border border-slate-100 shadow-sm">
                    <span class="text-[10px] font-black text-slate-400 uppercase px-1 tracking-wider">Filter Sabuk Sekarang:</span>
                    @foreach(['Putih', 'Kuning', 'Orange', 'Hijau', 'Biru', 'Ungu', 'Cokelat', 'Hitam'] as $belt)
                        <button type="button" @click="selectedBelts.includes('{{ $belt }}') ? selectedBelts = selectedBelts.filter(b => b !== '{{ $belt }}') : selectedBelts.push('{{ $belt }}')"
                            :class="selectedBelts.includes('{{ $belt }}') ? 'bg-indigo-600 text-white border-indigo-600 shadow-sm' : 'bg-slate-50 text-slate-400 border-slate-100'"
                            class="px-3 py-1.5 rounded-xl border text-[10px] font-black uppercase transition-all">
                            {{ $belt }}
                        </button>
                    @endforeach
                </div>

                <div class="flex items-center justify-between bg-white p-3 rounded-2xl border border-slate-100 shadow-sm min-w-[200px]">
                    <span class="text-[10px] font-black text-slate-400 uppercase px-1 tracking-wider md:block hidden">Kolom:</span>
                    <div class="hidden md:flex gap-1">
                        @foreach(['lg:grid-cols-2' => '2', 'lg:grid-cols-3' => '3', 'lg:grid-cols-4' => '4', 'lg:grid-cols-5' => '5'] as $class => $label)
                            <button type="button" @click="gridCols = '{{ $class }}'" :class="gridCols === '{{ $class }}' ? 'bg-slate-800 text-white border-slate-800' : 'bg-slate-50 text-slate-400 border-slate-100'" class="w-8 h-8 rounded-xl border flex items-center justify-center text-xs font-black transition-all">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            <form id="scoringForm" method="POST" action="{{ route('admin.exams.scoring.store', $exam) }}">
                @csrf
                <div class="grid gap-4 transition-all duration-300" :class="[gridMobile, gridCols]">
                    @foreach($participants as $p)
                        @php 
                            $targetUserId = $p->user_id; 
                            $score = $existingScores[$targetUserId] ?? null;
                            $currentBeltName = ucfirst(strtolower($p->currentBelt->name ?? 'Putih'));
                            $targetBeltName = ucfirst(strtolower($p->targetBelt->name ?? '-'));
                        @endphp
                        
                        <div x-show="selectedBelts.length === 0 || selectedBelts.includes('{{ $currentBeltName }}')"
                             class="bg-white rounded-3xl border border-slate-100 shadow-sm p-5 flex flex-col hover:border-indigo-300 transition-all group relative overflow-hidden">
                            
                            <div class="absolute -top-1 -left-1 bg-slate-100 text-slate-400 font-black text-[10px] px-3 py-1 rounded-br-2xl group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                                {{ $loop->iteration }}
                            </div>

                            <div class="flex justify-between items-start mb-4 mt-2">
                                <div class="overflow-hidden pl-1">
                                    <h3 class="text-sm font-black text-slate-800 uppercase truncate leading-tight group-hover:text-indigo-600 transition-colors">
                                        {{ $p->user->name }}
                                    </h3>
                                    <p class="text-[9px] text-slate-400 uppercase font-bold tracking-tighter">
                                        {{ $currentBeltName }} → <span class="text-indigo-600">{{ $targetBeltName }}</span>
                                    </p>
                                </div>
                                
                                <span class="text-[8px] font-black px-2 py-1 rounded-lg border border-slate-100 bg-slate-50 uppercase text-slate-400 shrink-0">
                                    {{ $p->dojo->name ?? 'Dojo' }}
                                </span>
                            </div>

                            <div class="space-y-4 mt-auto">
                                @foreach(['kihon', 'kata', 'kumite'] as $field)
                                <div class="flex flex-col gap-1.5">
                                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest pl-1">{{ $field }}</span>
                                    <div class="flex gap-1.5">
                                        @foreach(['Kurang' => 'K', 'Baik' => 'B', 'Sangat Baik' => 'SB'] as $full => $short)
                                        <label class="flex-1 cursor-pointer">
                                            <input type="radio" name="scores[{{ $targetUserId }}][{{ $field }}]" value="{{ $full }}" class="hidden peer" {{ ($score && $score->$field == $full) ? 'checked' : '' }}>
                                            <span class="block text-center py-2.5 rounded-xl bg-slate-50 text-[10px] font-black text-slate-400 border border-slate-50 peer-checked:bg-indigo-600 peer-checked:text-white peer-checked:border-indigo-600 transition-all uppercase shadow-sm italic">
                                                {{ $short }}
                                            </span>
                                        </label>
                                        @endforeach
                                    </div>
                                </div>
                                @endforeach

                                <div class="pt-3 mt-2 border-t border-slate-100">
                                    <select name="scores[{{ $targetUserId }}][result]" class="w-full py-3 border-none bg-slate-50 rounded-xl text-[10px] font-black uppercase text-slate-600 focus:ring-2 focus:ring-indigo-500 cursor-pointer shadow-sm">
                                        <option value="Lulus" {{ ($score && $score->result == 'Lulus') ? 'selected' : '' }}>
                                            Lulus ke {{ $targetBeltName }}
                                        </option>
                                        <option value="Percobaan" {{ ($score && $score->result == 'Percobaan') ? 'selected' : '' }}>
                                            Tetap di {{ $currentBeltName }} (Percobaan)
                                        </option>
                                        <option value="Tidak Lulus" {{ ($score && $score->result == 'Tidak Lulus') ? 'selected' : '' }}>
                                            Gagal
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </form>
        </div>
    </div>
</x-app-layout>