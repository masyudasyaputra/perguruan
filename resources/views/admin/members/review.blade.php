<x-app-layout>
<div class="py-12 bg-slate-50 min-h-screen">
    <div class="max-w-3xl mx-auto px-4">
        
        <div class="text-center mb-10">
            <h2 class="text-3xl font-black text-slate-800 italic uppercase">Konfirmasi Pendaftaran</h2>
            <p class="text-slate-500 font-bold text-xs uppercase tracking-[0.2em]">Dojo {{ auth()->user()->dojo->name }}</p>
        </div>

        <div class="bg-white rounded-[3rem] shadow-xl overflow-hidden border border-slate-100">
            {{-- Daftar Member --}}
            <div class="p-8 border-b border-slate-50">
                <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-6">Daftar Calon Anggota</h4>
                <div class="space-y-6">
                    @foreach($member_data as $index => $item)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 bg-slate-900 text-white rounded-xl flex items-center justify-center font-black text-sm italic">
                                {{ $index + 1 }}
                            </div>
                            <div>
                                <p class="font-black text-slate-800 uppercase text-sm">{{ $item['name'] }}</p>
                                <p class="text-[10px] text-emerald-600 font-bold tracking-wider">{{ $item['whatsapp'] }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="px-3 py-1 bg-slate-100 rounded-full text-[9px] font-black text-slate-600 uppercase">
                                {{ $item['belt_name'] }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Perhitungan --}}
            <div class="p-8 bg-slate-50">
                <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Rincian Invoice</h4>
                <div class="space-y-3">
                    <div class="flex justify-between text-sm font-bold text-slate-600">
                        <span>Biaya Pendaftaran ({{ count($member_data) }}x)</span>
                        <span>Rp {{ number_format($price_per_person * count($member_data), 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-sm font-bold text-slate-600">
                        <span>Admin Sistem</span>
                        <span>Rp {{ number_format($admin_fee, 0, ',', '.') }}</span>
                    </div>
                    <hr class="border-slate-200 my-4">
                    <div class="flex justify-between items-center">
                        <span class="text-lg font-black text-slate-800 italic uppercase">Total Bayar</span>
                        <span class="text-2xl font-black text-emerald-600">Rp {{ number_format($total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            {{-- Tombol Final --}}
            <div class="p-8 bg-white">
                <form action="{{ route('admin.members.store') }}" method="POST">
                    @csrf
                    {{-- Hidden input untuk oper data --}}
                    @foreach($member_data as $index => $item)
                        <input type="hidden" name="members[{{$index}}][name]" value="{{ $item['name'] }}">
                        <input type="hidden" name="members[{{$index}}][whatsapp]" value="{{ $item['whatsapp'] }}">
                        <input type="hidden" name="members[{{$index}}][belt_level_id]" value="{{ $item['belt_level_id'] }}">
                        <input type="hidden" name="members[{{$index}}][parent_name]" value="{{ $item['parent_name'] }}">
                    @endforeach

                    <button type="submit" class="w-full bg-emerald-500 hover:bg-emerald-600 text-white py-6 rounded-2xl font-black uppercase tracking-[0.2em] transition-all shadow-lg shadow-emerald-100 flex items-center justify-center gap-3">
                        Konfirmasi & Bayar Sekarang
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </button>
                    <a href="{{ url()->previous() }}" class="block text-center mt-6 text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-red-500 transition-all">Kembali Edit Data</a>
                </form>
            </div>
        </div>

        <div class="mt-8 text-center">
            <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest px-10 leading-relaxed">
                Dengan menekan tombol bayar, Anda menyetujui bahwa data yang diinput akan didaftarkan ke sistem manajemen dojo secara permanen.
            </p>
        </div>
    </div>
</div>
</x-app-layout>