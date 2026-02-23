<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h2 class="font-black text-2xl text-slate-800 tracking-tight">Konfigurasi Iuran</h2>
                <p class="text-sm text-slate-500 font-medium italic">Biaya per tingkatan sabuk</p>
            </div>
            <div class="px-4 py-2 bg-white border border-slate-200 shadow-sm rounded-2xl flex items-center gap-3">
                <div class="relative flex h-3 w-3">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-indigo-500"></span>
                </div>
                <span class="text-[10px] font-black uppercase text-slate-600 tracking-widest">
                    Wilayah: {{ Auth::user()->province->name ?? 'Nasional' }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-6 md:py-10 bg-slate-50/50 min-h-screen">
        <div class="max-w-6xl mx-auto px-4">
            
            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-500 text-white rounded-2xl flex items-center gap-3 shadow-lg shadow-emerald-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                    <span class="text-xs font-black uppercase tracking-wider">{{ session('success') }}</span>
                </div>
            @endif

            <form action="{{ route('admin.fees.store') }}" method="POST" id="feeForm">
                @csrf
                
                <div class="hidden md:flex justify-between items-center mb-6 px-4">
                    <div class="flex items-center gap-2">
                        <span class="h-8 w-1 bg-indigo-600 rounded-full"></span>
                        <h3 class="font-black text-slate-800 uppercase text-sm tracking-widest">Pricing Matrix</h3>
                    </div>
                    <button type="submit" class="px-8 py-3 bg-indigo-600 text-white rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-slate-900 transition-all shadow-md">
                        Update Database
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($beltLevels as $belt)
                    <div class="bg-white rounded-3xl p-4 md:p-5 border border-slate-100 shadow-sm hover:shadow-md transition-all group">
                        <div class="flex items-center justify-between gap-4">
                            <div class="flex items-center gap-4 flex-1">
                                <div class="hidden sm:flex h-12 w-12 shrink-0 bg-slate-50 rounded-2xl items-center justify-center group-hover:bg-indigo-50 transition-colors">
                                    <span class="text-xs font-black text-slate-400 group-hover:text-indigo-600">{{ $belt->order }}</span>
                                </div>
                                <div class="min-w-0">
                                    <h4 class="text-sm md:text-base font-black text-slate-800 truncate leading-tight capitalize">{{ strtolower($belt->name) }}</h4>
                                    <p class="text-[9px] font-bold text-indigo-500 uppercase tracking-tighter">{{ $belt->kyu_dan }}</p>
                                </div>
                            </div>

                            <div class="w-36 md:w-52">
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <span class="text-slate-400 font-black text-[10px]">Rp</span>
                                    </div>
                                    <input type="text" 
                                           class="currency-input block w-full pl-10 pr-4 py-3 bg-slate-50 border-transparent rounded-xl text-sm font-black text-slate-700 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all"
                                           value="{{ number_format($fees[$belt->id]->amount ?? 0, 0, ',', '.') }}"
                                           placeholder="0">
                                    
                                    <input type="hidden" 
                                           name="amounts[{{ $belt->id }}]" 
                                           value="{{ $fees[$belt->id]->amount ?? 0 }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="mt-8 mb-20 md:mb-0">
                    <div class="bg-slate-900 rounded-[2rem] p-6 md:p-8 text-white shadow-2xl relative overflow-hidden group">
                        <div class="flex flex-col md:flex-row items-center justify-between gap-6 relative z-10">
                            <div class="flex items-center gap-5">
                                <div class="h-12 w-12 bg-white/10 backdrop-blur-md rounded-2xl flex items-center justify-center text-indigo-300 font-bold italic">Rp</div>
                                <div class="text-center md:text-left">
                                    <p class="font-black text-sm uppercase tracking-widest">Simpan Perubahan?</p>
                                    <p class="text-xs text-slate-400 font-medium">Data akan langsung diterapkan pada tagihan member.</p>
                                </div>
                            </div>
                            <button type="submit" class="w-full md:w-auto px-14 py-4 bg-white text-slate-900 rounded-2xl font-black text-xs uppercase tracking-[0.2em] hover:bg-indigo-400 hover:text-white transition-all active:scale-95">
                                Simpan Sekarang
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Script untuk Format Ribuan --}}
    <script>
        document.querySelectorAll('.currency-input').forEach(input => {
            input.addEventListener('input', function(e) {
                // Ambil angka murni
                let value = this.value.replace(/\D/g, "");
                
                // Update hidden input (angka murni untuk database)
                this.nextElementSibling.value = value;

                // Format tampilan dengan titik
                if (value === "") {
                    this.value = "";
                } else {
                    this.value = new Intl.NumberFormat('id-ID').format(value);
                }
            });
        });
    </script>
</x-app-layout>