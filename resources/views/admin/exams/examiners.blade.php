<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 px-4 sm:px-0">
            <div>
                <h2 class="font-black text-2xl text-slate-800 leading-tight tracking-tight uppercase">
                    Assign Penguji
                </h2>
                <p class="text-sm text-slate-500 font-medium italic mt-1">
                    {{ $exam->name }} • {{ \Carbon\Carbon::parse($exam->execution_date)->format('d M Y') }}
                </p>
            </div>
            <div class="flex items-center gap-3 bg-white p-2 pr-4 rounded-2xl shadow-sm border border-slate-100">
                <div class="h-8 w-8 rounded-xl bg-indigo-600 flex items-center justify-center text-white shadow-lg shadow-indigo-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Otoritas Penunjukan</span>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-slate-50/50 min-h-screen">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- NOTIFIKASI BERHASIL --}}
            @if (session('success'))
                <div id="toast-success" class="mb-6 flex items-center w-full p-4 text-emerald-700 bg-emerald-50 border border-emerald-100 rounded-[2rem] shadow-sm transition-all duration-500">
                    <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-emerald-500 bg-emerald-100 rounded-xl">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                    </div>
                    <div class="ml-3 text-sm font-black uppercase tracking-wider">{{ session('success') }}</div>
                    <button type="button" onclick="this.parentElement.remove()" class="ml-auto bg-white text-slate-400 hover:text-slate-900 rounded-lg p-1.5">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"></path></svg>
                    </button>
                </div>
            @endif

            {{-- SECTION 1: TIM TERPILIH (HORIZONTAL SCROLL ON MOBILE) --}}
            <div class="mb-6">
                <div class="flex items-center justify-between px-4 mb-4">
                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Tim Penguji Saat Ini</h3>
                    <span class="text-[10px] font-bold text-indigo-600 bg-indigo-50 px-3 py-1 rounded-full uppercase">{{ count($selected) }} Terpilih</span>
                </div>
                
                <div class="flex gap-3 overflow-x-auto pb-2 px-2 no-scrollbar">
                    @forelse($exam->examiners as $assigned)
                        <div class="flex-none flex items-center gap-3 bg-white pl-2 pr-4 py-2 rounded-2xl border border-slate-200 shadow-sm">
                            <div class="h-8 w-8 rounded-xl bg-slate-900 flex items-center justify-center text-white font-black text-[10px]">
                                {{ substr($assigned->name, 0, 1) }}
                            </div>
                            <div class="whitespace-nowrap">
                                <p class="text-[11px] font-black text-slate-800 uppercase leading-none">{{ $assigned->name }}</p>
                                <p class="text-[9px] font-bold text-slate-400 uppercase mt-1">{{ $assigned->dojo->name ?? 'Independen' }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="w-full bg-slate-100/50 border-2 border-dashed border-slate-200 rounded-[1.5rem] py-4 text-center">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest italic text-center w-full">Belum ada penguji ditugaskan</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- SECTION 2: FORM DATABASE PENGUJI --}}
            <div class="bg-white shadow-xl shadow-slate-200/50 rounded-[2.5rem] border border-slate-100 overflow-hidden">
                <div class="p-6 md:p-10">
                    
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
                        <div>
                            <h3 class="font-black text-xl text-slate-800 tracking-tight">Database Penguji</h3>
                            <p class="text-xs text-slate-400 font-bold uppercase tracking-widest mt-1">Pilih dari seluruh daftar wilayah</p>
                        </div>
                        <div class="relative w-full md:w-72">
                            <input type="text" id="searchPenguji" placeholder="CARI NAMA / DOJO..." 
                                class="w-full pl-10 pr-4 py-3 bg-slate-50 border-none rounded-2xl text-[10px] font-black tracking-widest focus:ring-2 focus:ring-indigo-500 transition-all">
                            <svg class="w-4 h-4 text-slate-400 absolute left-4 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('admin.exams.examiners.update', $exam) }}" class="space-y-8">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-3" id="containerPenguji">
                            @foreach ($examiners as $u)
                                <label class="card-penguji relative flex items-center p-4 rounded-[1.8rem] border-2 border-slate-50 bg-slate-50/50 cursor-pointer hover:border-indigo-200 hover:bg-white transition-all group has-[:checked]:border-indigo-600 has-[:checked]:bg-indigo-50/50">
                                    <input type="checkbox" name="examiner_ids[]" value="{{ $u->id }}"
                                        @checked(in_array($u->id, $selected))
                                        class="w-5 h-5 rounded-lg border-slate-300 text-indigo-600 focus:ring-indigo-500 transition-all ml-1" />
                                    
                                    <div class="ml-4 flex-1 min-w-0">
                                        <p class="nama-penguji font-black text-slate-800 uppercase text-[13px] tracking-tight truncate group-hover:text-indigo-600 transition-colors">
                                            {{ $u->name }}
                                        </p>
                                        
                                        <div class="flex flex-wrap gap-x-3 gap-y-1 mt-1">
                                            <span class="text-[9px] font-bold text-slate-400 uppercase flex items-center gap-1">
                                                <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                                                {{ $u->province->name ?? 'N/A' }}
                                            </span>
                                            <span class="dojo-penguji text-[9px] font-black text-indigo-400 uppercase flex items-center gap-1">
                                                <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                                {{ $u->dojo->name ?? 'Independen' }}
                                            </span>
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>

                        <div class="pt-8 mt-4 border-t border-slate-100 flex flex-col sm:flex-row gap-3">
                            <button type="submit" 
                                class="flex-1 inline-flex justify-center items-center px-8 py-4 bg-slate-900 rounded-2xl font-black text-[10px] text-white uppercase tracking-[0.3em] hover:bg-indigo-600 shadow-xl shadow-slate-200 transition-all active:scale-95 group">
                                <svg class="w-4 h-4 mr-2 group-hover:animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                Simpan Perubahan
                            </button>
                            <a href="{{ route('admin.exams.show', $exam) }}" 
                                class="inline-flex justify-center items-center px-8 py-4 bg-white border-2 border-slate-100 rounded-2xl font-black text-[10px] text-slate-400 uppercase tracking-[0.3em] hover:bg-slate-50 transition-all">
                                Kembali ke Detail
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>

    <script>
        // Fitur Search Real-time
        document.getElementById('searchPenguji').addEventListener('input', function(e) {
            const keyword = e.target.value.toLowerCase();
            const cards = document.querySelectorAll('.card-penguji');
            
            cards.forEach(card => {
                const nama = card.querySelector('.nama-penguji').textContent.toLowerCase();
                const dojo = card.querySelector('.dojo-penguji').textContent.toLowerCase();
                
                if (nama.includes(keyword) || dojo.includes(keyword)) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        });

        // Auto-close Toast
        setTimeout(() => {
            const toast = document.getElementById('toast-success');
            if(toast) {
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 500);
            }
        }, 4000);
    </script>
</x-app-layout>