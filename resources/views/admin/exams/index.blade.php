<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center px-2">
            <div>
                <h2 class="font-black text-2xl text-slate-800 tracking-tight uppercase">Jadwal Ujian Sabuk</h2>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Manajemen sesi ujian dan pendaftaran peserta</p>
            </div>
            {{-- Tombol Jadwal Baru: Hanya untuk PB, Pengprov, dan Pengcab --}}
            @if(auth()->user()->hasRole(['pb', 'pengprov', 'pengcab']))
                <button onclick="document.getElementById('modalAddExam').classList.remove('hidden')" class="px-6 py-3 bg-slate-900 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-indigo-600 transition-all shadow-lg shadow-slate-200">
                    + Jadwal Baru
                </button>
            @endif
        </div>
    </x-slot>

    <div class="py-12 bg-slate-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-100 text-emerald-700 rounded-[2rem] font-bold text-sm flex items-center gap-4 shadow-sm animate-fade-in">
                    <div class="p-2 bg-emerald-500 text-white rounded-full">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error') || $errors->any())
                <div class="mb-6 p-4 bg-rose-50 border border-rose-100 text-rose-700 rounded-[2rem] font-bold text-sm shadow-sm animate-fade-in">
                    <div class="flex items-center gap-4">
                        <div class="p-2 bg-rose-500 text-white rounded-full">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
                        </div>
                        <span>{{ session('error') ?? 'Terdapat kesalahan pada input Anda.' }}</span>
                    </div>
                    @if($errors->any())
                        <ul class="mt-2 ml-12 list-disc list-inside text-xs opacity-80">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($exams as $exam)
                <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 group">
                    <div class="p-8">
                        <div class="flex justify-between items-start mb-6">
                            <span class="px-4 py-1.5 {{ $exam->status === 'open' ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-100 text-slate-400' }} rounded-full text-[10px] font-black uppercase tracking-widest">
                                {{ $exam->status }}
                            </span>
                            <div class="text-right">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Tanggal Ujian</p>
                                <p class="text-xs font-bold text-slate-600">{{ is_string($exam->execution_date) ? \Carbon\Carbon::parse($exam->execution_date)->format('d M Y') : $exam->execution_date->format('d M Y') }}</p>
                            </div>
                        </div>

                        <h3 class="text-xl font-black text-slate-800 mb-2 group-hover:text-indigo-600 transition-colors leading-tight uppercase">{{ $exam->name }}</h3>
                        
                        <div class="flex items-center gap-2 text-slate-400 mb-8">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /></svg>
                            <span class="text-[10px] font-bold uppercase tracking-wide">{{ $exam->location }}</span>
                        </div>
                        
                        <div class="flex items-center justify-between p-5 bg-slate-50 rounded-[2rem] border border-slate-100/50">
                            <div>
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Peserta</p>
                                <p class="text-lg font-black text-slate-800">{{ $exam->participants->count() }} <span class="text-[10px] text-slate-400">JIWA</span></p>
                            </div>
                            <a href="{{ route('admin.exams.show', $exam->id) }}" class="flex items-center gap-2 px-6 py-3 bg-white border border-slate-200 shadow-sm rounded-2xl text-[10px] font-black uppercase text-slate-700 hover:bg-slate-900 hover:text-white hover:border-slate-900 transition-all duration-300">
                                KELOLA
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-full py-20 bg-white rounded-[3rem] border border-dashed border-slate-200 text-center">
                    <div class="p-4 bg-slate-50 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <p class="text-sm font-black text-slate-400 uppercase tracking-[0.2em]">Belum Ada Jadwal Ujian Tersedia</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>