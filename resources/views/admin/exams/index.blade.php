<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center px-2">
            <div>
                <h2 class="font-black text-2xl text-slate-800 tracking-tight uppercase">Jadwal Ujian Sabuk</h2>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Manajemen sesi ujian dan
                    pendaftaran peserta</p>
            </div>
            {{-- HANYA ADMIN PB DAN PENGPROV YANG BISA TAMBAH JADWAL --}}
            @if (auth()->user()->hasRole(['pb', 'pengprov']))
                <button onclick="toggleModal('modalAddExam')"
                    class="px-6 py-3 bg-slate-900 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-indigo-600 transition-all shadow-lg shadow-slate-200">
                    + Jadwal Baru
                </button>
            @endif
        </div>
    </x-slot>

    <div class="py-12 bg-slate-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- ALERT NOTIFIKASI --}}
            @if (session('success'))
                <div
                    class="mb-6 p-4 bg-emerald-50 border border-emerald-100 text-emerald-700 rounded-[2rem] font-bold text-sm flex items-center gap-4 alert-notif">
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            {{-- TAMPILAN DESKTOP (TABLE) --}}
            <div class="hidden lg:block bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
                <table class="w-full text-left">
                    <thead>
                        <tr
                            class="bg-slate-50/50 border-b border-slate-100 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                            <th class="px-8 py-5">Status</th>
                            <th class="px-6 py-5">Nama Sesi</th>
                            <th class="px-6 py-5">Tanggal</th>
                            <th class="px-6 py-5">Lokasi</th>
                            <th class="px-6 py-5 text-center">Peserta</th>
                            <th class="px-8 py-5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($exams as $exam)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-8 py-6">
                                    <span
                                        class="px-3 py-1 {{ $exam->status === 'open' ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-100 text-slate-400' }} rounded-full text-[9px] font-black uppercase">
                                        {{ $exam->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-6 font-black text-slate-800 uppercase text-sm">{{ $exam->name }}
                                </td>
                                <td class="px-6 py-6 text-xs font-bold text-slate-500">
                                    {{ is_string($exam->execution_date) ? \Carbon\Carbon::parse($exam->execution_date)->format('d M Y') : $exam->execution_date->format('d M Y') }}
                                </td>
                                <td class="px-6 py-6 text-[10px] font-bold text-slate-400 uppercase">
                                    {{ $exam->location }}</td>
                                <td class="px-6 py-6 text-center font-black text-slate-700">
                                    {{ $exam->participants->count() }}</td>
                                <td class="px-8 py-6">
                                    <div class="flex justify-end items-center gap-2">

                                        {{-- TOMBOL INPUT NILAI (UNTUK PENGUJI / ADMIN) --}}
                                        @if (auth()->user()->hasRole(['pb', 'pengprov', 'penguji']))
                                            <a href="{{ route('admin.exams.scoring', $exam->id) }}"
                                                title="Input Nilai Ujian"
                                                class="px-4 py-2 bg-indigo-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-900 transition-all shadow-md">
                                                Input Nilai
                                            </a>
                                        @endif

                                        {{-- TOMBOL ASSIGN PENGUJI --}}
                                        @if (auth()->user()->hasRole(['pb', 'pengprov']))
                                            <a href="{{ route('admin.exams.examiners.edit', $exam->id) }}"
                                                title="Atur Penguji"
                                                class="p-2 bg-amber-50 text-amber-600 rounded-xl hover:bg-amber-500 hover:text-white transition-all">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                                </svg>
                                            </a>
                                        @endif

                                        <a href="{{ route('admin.exams.show', $exam->id) }}" title="Lihat Detail"
                                            class="p-2 bg-slate-100 text-slate-600 rounded-xl hover:bg-slate-900 hover:text-white transition-all">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                            </svg>
                                        </a>

                                        @if (auth()->user()->hasRole(['pb', 'pengprov']))
                                            <button onclick="openEditModal({{ json_encode($exam) }})"
                                                class="p-2 bg-indigo-50 text-indigo-600 rounded-xl hover:bg-indigo-600 hover:text-white transition-all">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>
                                            <form action="{{ route('admin.exams.destroy', $exam->id) }}" method="POST"
                                                onsubmit="return confirm('Hapus jadwal ini?')">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                    class="p-2 bg-rose-50 text-rose-600 rounded-xl hover:bg-rose-600 hover:text-white transition-all">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6"
                                    class="py-20 text-center font-bold text-slate-400 uppercase tracking-widest text-xs">
                                    Belum ada data</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- TAMPILAN MOBILE (CARD) --}}
            <div class="lg:hidden grid gap-6">
                @foreach ($exams as $exam)
                    <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm">
                        <div class="flex justify-between mb-4">
                            <span
                                class="px-3 py-1 bg-emerald-50 text-emerald-600 rounded-full text-[9px] font-black uppercase">{{ $exam->status }}</span>
                            <span class="text-[10px] font-bold text-slate-400 uppercase">
                                {{ is_string($exam->execution_date) ? \Carbon\Carbon::parse($exam->execution_date)->format('d M Y') : $exam->execution_date->format('d M Y') }}
                            </span>
                        </div>
                        <h3 class="font-black text-slate-800 uppercase mb-4">{{ $exam->name }}</h3>

                        {{-- TOMBOL INPUT NILAI MOBILE (UTAMA UNTUK PENGUJI) --}}
                        @if (auth()->user()->hasRole(['pb', 'pengprov', 'pengcab', 'penguji']))
                            <div class="mb-3">
                                <a href="{{ route('admin.exams.scoring', $exam->id) }}"
                                    class="flex items-center justify-center gap-2 w-full py-3 bg-indigo-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-indigo-100">
                                    Mulai Penilaian
                                </a>
                            </div>
                        @endif

                        {{-- TOMBOL PENGUJI MOBILE --}}
                        @if (auth()->user()->hasRole(['pb', 'pengprov']))
                            <div class="mb-4">
                                <a href="{{ route('admin.exams.examiners.edit', $exam->id) }}"
                                    class="flex items-center justify-center gap-2 w-full py-3 bg-amber-50 text-amber-600 border border-amber-100 rounded-2xl text-[10px] font-black uppercase tracking-widest">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    Atur Tim Penguji
                                </a>
                            </div>
                        @endif

                        <div class="flex justify-between items-center bg-slate-50 p-4 rounded-2xl">
                            <span
                                class="text-[10px] font-black text-slate-400 uppercase">{{ $exam->participants->count() }}
                                Peserta</span>
                            <div class="flex gap-2">
                                @if (auth()->user()->hasRole(['pb', 'pengprov']))
                                    <button onclick="openEditModal({{ json_encode($exam) }})"
                                        class="p-2 bg-indigo-100 text-indigo-600 rounded-lg text-[10px] font-black px-4 uppercase">Edit</button>
                                @endif
                                <a href="{{ route('admin.exams.show', $exam->id) }}"
                                    class="p-2 bg-slate-900 text-white rounded-lg px-4 text-[10px] font-black uppercase">KELOLA</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- MODAL TAMBAH --}}
    <div id="modalAddExam"
        class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
        <div class="bg-white rounded-[2.5rem] w-full max-w-lg p-10 shadow-2xl border border-slate-100">
            <form action="{{ route('admin.exams.store') }}" method="POST">
                @csrf
                <h3 class="text-2xl font-black text-slate-800 uppercase mb-6">Jadwal Baru</h3>
                <div class="space-y-4">
                    <input type="text" name="name" placeholder="Nama Sesi" required
                        class="w-full bg-slate-50 border-none rounded-2xl px-5 py-4 text-sm font-bold">
                    <div class="grid grid-cols-2 gap-4">
                        <input type="date" name="execution_date" required
                            class="w-full bg-slate-50 border-none rounded-2xl px-5 py-4 text-sm font-bold">
                        <select name="status"
                            class="w-full bg-slate-50 border-none rounded-2xl px-5 py-4 text-sm font-bold">
                            <option value="open">Open</option>
                            <option value="draft">Draft</option>
                        </select>
                    </div>
                    <input type="text" name="location" placeholder="Lokasi" required
                        class="w-full bg-slate-50 border-none rounded-2xl px-5 py-4 text-sm font-bold">
                </div>
                <div class="mt-8 flex gap-3">
                    <button type="button" onclick="toggleModal('modalAddExam')"
                        class="flex-1 py-4 bg-slate-100 text-slate-600 rounded-2xl font-black text-[10px] uppercase">Batal</button>
                    <button type="submit"
                        class="flex-1 py-4 bg-slate-900 text-white rounded-2xl font-black text-[10px] uppercase shadow-lg">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL EDIT --}}
    <div id="modalEditExam"
        class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
        <div class="bg-white rounded-[2.5rem] w-full max-w-lg p-10 shadow-2xl border border-slate-100">
            <form id="editForm" method="POST">
                @csrf @method('PUT')
                <h3 class="text-2xl font-black text-slate-800 uppercase mb-6">Edit Jadwal</h3>
                <div class="space-y-4">
                    <input type="text" name="name" id="edit_name" required
                        class="w-full bg-slate-50 border-none rounded-2xl px-5 py-4 text-sm font-bold">
                    <div class="grid grid-cols-2 gap-4">
                        <input type="date" name="execution_date" id="edit_date" required
                            class="w-full bg-slate-50 border-none rounded-2xl px-5 py-4 text-sm font-bold">
                        <select name="status" id="edit_status"
                            class="w-full bg-slate-50 border-none rounded-2xl px-5 py-4 text-sm font-bold">
                            <option value="open">Open</option>
                            <option value="draft">Draft</option>
                        </select>
                    </div>
                    <input type="text" name="location" id="edit_location" required
                        class="w-full bg-slate-50 border-none rounded-2xl px-5 py-4 text-sm font-bold">
                </div>
                <div class="mt-8 flex gap-3">
                    <button type="button" onclick="toggleModal('modalEditExam')"
                        class="flex-1 py-4 bg-slate-100 text-slate-600 rounded-2xl font-black text-[10px] uppercase">Batal</button>
                    <button type="submit"
                        class="flex-1 py-4 bg-indigo-600 text-white rounded-2xl font-black text-[10px] uppercase shadow-lg">Update
                        Jadwal</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleModal(id) {
            const modal = document.getElementById(id);
            if (modal) modal.classList.toggle('hidden');
        }

        function openEditModal(exam) {
            document.getElementById('editForm').action = `/admin/exams/${exam.id}`;
            document.getElementById('edit_name').value = exam.name;
            document.getElementById('edit_location').value = exam.location;
            document.getElementById('edit_status').value = exam.status;

            const date = new Date(exam.execution_date);
            const formattedDate = date.toISOString().split('T')[0];
            document.getElementById('edit_date').value = formattedDate;

            toggleModal('modalEditExam');
        }

        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert-notif');
            alerts.forEach(a => a.remove());
        }, 4000);
    </script>
</x-app-layout>
