<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-2xl text-slate-800 tracking-tight">Jadwal Ujian Sabuk</h2>
            @if(auth()->user()->hasRole(['pb', 'pengprov']))
                <button onclick="document.getElementById('modalAddExam').classList.remove('hidden')" class="px-6 py-3 bg-slate-900 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-indigo-600 transition-all shadow-lg shadow-slate-200">
                    + Jadwal Baru
                </button>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4">
            {{-- Alert Success --}}
            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-100 text-emerald-600 rounded-2xl font-bold text-sm flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ session('success') }}
                </div>
            @endif

            {{-- 1. TAMPILAN DESKTOP: TABEL MODERN --}}
            <div class="hidden md:block overflow-visible bg-white border border-slate-100 shadow-sm rounded-[2.5rem]">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50">
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Sesi Ujian</th>
                            <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Tanggal</th>
                            <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Lokasi</th>
                            <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Peserta</th>
                            <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Status</th>
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($exams as $exam)
                        <tr class="hover:bg-slate-50/30 transition-colors group">
                            <td class="px-8 py-6 font-black text-slate-800">{{ $exam->name }}</td>
                            <td class="px-6 py-6 text-sm font-bold text-slate-600">{{ $exam->execution_date->format('d M Y') }}</td>
                            <td class="px-6 py-6 text-xs font-bold text-slate-500">{{ $exam->location }}</td>
                            <td class="px-6 py-6 text-center">
                                <span class="px-3 py-1 bg-slate-100 rounded-lg text-xs font-black text-slate-700">
                                    {{ $exam->participants->count() }}
                                </span>
                            </td>
                            <td class="px-6 py-6">
                                <span class="px-3 py-1 {{ $exam->status === 'open' ? 'bg-emerald-50 text-emerald-600' : 'bg-indigo-50 text-indigo-600' }} rounded-full text-[10px] font-black uppercase">
                                    {{ $exam->status }}
                                </span>
                            </td>
                            <td class="px-8 py-6 text-right">
                                <div class="flex justify-end gap-3">
                                    {{-- Tombol Detail --}}
                                    <a href="{{ route('admin.exams.show', $exam->id) }}" class="group relative inline-flex items-center justify-center p-2.5 bg-white text-indigo-600 rounded-xl shadow-[0_4px_12px_rgba(79,70,229,0.1)] border border-indigo-50/50 hover:bg-indigo-600 hover:text-white hover:shadow-[0_8px_20px_rgba(79,70,229,0.3)] hover:-translate-y-0.5 transition-all duration-300 active:scale-95" title="Detail Peserta">
                                        <span class="absolute -top-10 left-1/2 -translate-x-1/2 bg-gray-900 text-white text-[10px] px-2 py-1 rounded shadow-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none uppercase font-black tracking-widest z-10">Detail</span>
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>

                                    @if(auth()->user()->hasRole(['pb', 'pengprov']))
                                        {{-- Tombol Edit --}}
                                        <button onclick="openEditModal({{ $exam }})" class="group relative inline-flex items-center justify-center p-2.5 bg-white text-amber-500 rounded-xl shadow-[0_4px_12px_rgba(245,158,11,0.1)] border border-amber-50/50 hover:bg-amber-500 hover:text-white hover:shadow-[0_8px_20px_rgba(245,158,11,0.3)] hover:-translate-y-0.5 transition-all duration-300 active:scale-95">
                                            <span class="absolute -top-10 left-1/2 -translate-x-1/2 bg-gray-900 text-white text-[10px] px-2 py-1 rounded shadow-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none uppercase font-black tracking-widest z-10">Edit</span>
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>

                                        {{-- Tombol Hapus --}}
                                        <form action="{{ route('admin.exams.destroy', $exam->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus jadwal {{ $exam->name }}?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="group relative inline-flex items-center justify-center p-2.5 bg-white text-rose-500 rounded-xl shadow-[0_4px_12px_rgba(244,63,94,0.1)] border border-rose-50/50 hover:bg-rose-500 hover:text-white hover:shadow-[0_8px_20px_rgba(244,63,94,0.3)] hover:-translate-y-0.5 transition-all duration-300 active:scale-95">
                                                <span class="absolute -top-10 left-1/2 -translate-x-1/2 bg-rose-600 text-white text-[10px] px-2 py-1 rounded shadow-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none uppercase font-black tracking-widest z-10">Hapus</span>
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="px-8 py-20 text-center text-slate-400 font-bold uppercase text-[10px] tracking-widest">Belum ada jadwal tersedia</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- 2. TAMPILAN MOBILE: CARD GLASSMORP --}}
            <div class="grid grid-cols-1 gap-6 md:hidden">
                @foreach($exams as $exam)
                <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm p-8 group overflow-hidden relative">
                    <div class="flex justify-between items-start mb-6">
                        <span class="px-3 py-1 {{ $exam->status === 'open' ? 'bg-emerald-50 text-emerald-600' : 'bg-indigo-50 text-indigo-600' }} rounded-full text-[10px] font-black uppercase tracking-tighter">
                            {{ $exam->status }}
                        </span>
                        <p class="text-[10px] font-bold text-slate-400 uppercase">{{ $exam->execution_date->format('d M Y') }}</p>
                    </div>
                    <h3 class="text-xl font-black text-slate-800 mb-6 group-hover:text-indigo-600 transition-colors">{{ $exam->name }}</h3>
                    
                    <div class="flex items-center justify-between p-5 bg-slate-50 rounded-[2rem]">
                        <div>
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Peserta</p>
                            <p class="font-black text-slate-700">{{ $exam->participants->count() }} Jiwa</p>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('admin.exams.show', $exam->id) }}" class="p-3 bg-white text-indigo-600 rounded-xl shadow-sm border border-indigo-50/50">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- MODAL ADD --}}
    <div id="modalAddExam" class="hidden fixed inset-0 z-[60] overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 py-6">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="document.getElementById('modalAddExam').classList.add('hidden')"></div>
            <div class="bg-white rounded-[2.5rem] overflow-hidden shadow-2xl max-w-lg w-full z-10 transform transition-all border border-slate-100">
                <form action="{{ route('admin.exams.store') }}" method="POST" class="p-10">
                    @csrf
                    <div class="flex justify-between items-center mb-8">
                        <h3 class="text-2xl font-black text-slate-800 tracking-tight">JADWAL BARU</h3>
                        <button type="button" onclick="document.getElementById('modalAddExam').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <div class="space-y-5">
                        <div class="group">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-2 block">Nama Sesi Ujian</label>
                            <input type="text" name="name" required class="w-full bg-slate-50 border-none rounded-2xl px-5 py-4 text-sm font-bold focus:ring-2 focus:ring-indigo-500 transition-all">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-2 block">Tanggal</label>
                                <input type="date" name="execution_date" required class="w-full bg-slate-50 border-none rounded-2xl px-5 py-4 text-sm font-bold focus:ring-2 focus:ring-indigo-500 transition-all">
                            </div>
                            <div>
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-2 block">Status</label>
                                <select name="status" class="w-full bg-slate-50 border-none rounded-2xl px-5 py-4 text-sm font-bold focus:ring-2 focus:ring-indigo-500 transition-all">
                                    <option value="open">Open</option>
                                    <option value="draft">Draft</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-2 block">Lokasi GOR / Dojo</label>
                            <input type="text" name="location" required class="w-full bg-slate-50 border-none rounded-2xl px-5 py-4 text-sm font-bold focus:ring-2 focus:ring-indigo-500 transition-all">
                        </div>
                    </div>
                    <div class="mt-10 flex gap-4">
                        <button type="button" onclick="document.getElementById('modalAddExam').classList.add('hidden')" class="flex-1 py-4 bg-slate-100 text-slate-600 rounded-2xl font-black text-xs uppercase hover:bg-slate-200 transition-all">Batal</button>
                        <button type="submit" class="flex-1 py-4 bg-slate-900 text-white rounded-2xl font-black text-xs uppercase hover:bg-indigo-600 transition-all shadow-lg">Simpan Jadwal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL EDIT --}}
    <div id="modalEditExam" class="hidden fixed inset-0 z-[60] overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 py-6">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="closeEditModal()"></div>
            <div class="bg-white rounded-[2.5rem] overflow-hidden shadow-2xl max-w-lg w-full z-10 border border-slate-100">
                <form id="editForm" method="POST" class="p-10">
                    @csrf @method('PUT')
                    <div class="flex justify-between items-center mb-8">
                        <h3 class="text-2xl font-black text-slate-800 tracking-tight uppercase">Edit Sesi</h3>
                    </div>
                    <div class="space-y-5">
                        <input type="text" name="name" id="edit_name" required class="w-full bg-slate-50 border-none rounded-2xl px-5 py-4 text-sm font-bold focus:ring-2 focus:ring-amber-500 transition-all">
                        <div class="grid grid-cols-2 gap-4">
                            <input type="date" name="execution_date" id="edit_date" required class="w-full bg-slate-50 border-none rounded-2xl px-5 py-4 text-sm font-bold">
                            <select name="status" id="edit_status" class="w-full bg-slate-50 border-none rounded-2xl px-5 py-4 text-sm font-bold">
                                <option value="draft">Draft</option>
                                <option value="open">Open</option>
                                <option value="ongoing">Ongoing</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>
                        <input type="text" name="location" id="edit_location" required class="w-full bg-slate-50 border-none rounded-2xl px-5 py-4 text-sm font-bold">
                    </div>
                    <div class="mt-10 flex gap-4">
                        <button type="button" onclick="closeEditModal()" class="flex-1 py-4 bg-slate-100 text-slate-600 rounded-2xl font-black text-xs uppercase">Batal</button>
                        <button type="submit" class="flex-1 py-4 bg-amber-500 text-white rounded-2xl font-black text-xs uppercase hover:bg-amber-600 transition-all shadow-lg shadow-amber-100">Update Jadwal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openEditModal(exam) {
            const form = document.getElementById('editForm');
            form.action = `/admin/exams/${exam.id}`;
            document.getElementById('edit_name').value = exam.name;
            
            // Format Tanggal untuk Input Date
            const dateVal = exam.execution_date ? exam.execution_date.split('T')[0] : '';
            document.getElementById('edit_date').value = dateVal;
            
            document.getElementById('edit_location').value = exam.location;
            document.getElementById('edit_status').value = exam.status;
            document.getElementById('modalEditExam').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('modalEditExam').classList.add('hidden');
        }
    </script>
</x-app-layout>