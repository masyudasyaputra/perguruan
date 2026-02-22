<x-app-layout>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<div class="py-6 md:py-12 bg-slate-50 min-h-screen" 
    x-data="{ 
        members: [], 
        showPass: false,
        rawDojoName: '{{ auth()->user()->dojo->name ?? 'DOJO' }}',

        get defaultPass() {
            let nameParts = this.rawDojoName.trim().split(' ');
            return (nameParts[0] || 'DOJO').toUpperCase() + '123';
        },

        async checkWa(index) {
            let wa = this.members[index].whatsapp;
            if (!wa || wa.length < 9) {
                this.members[index].errorWa = '';
                return;
            }
            try {
                let response = await fetch(`/api/check-whatsapp?number=${wa}`);
                let data = await response.json();
                this.members[index].errorWa = data.exists ? 'Sudah terdaftar' : '';
            } catch (e) { console.error('WA check failed'); }
        },

        addMember() {
            this.members.push({ 
                id: Date.now() + Math.random(), 
                name: '', 
                parent_name: '', 
                whatsapp: '', 
                email: '', 
                belt_level_id: '', 
                errorWa: '' 
            });
        },

        removeMember(id) {
            if(this.members.length > 1) {
                this.members = this.members.filter(m => m.id !== id);
            }
        },

        init() {
            // FITUR SIMPAN INPUTAN: Ambil dari localStorage saat load
            const savedData = localStorage.getItem('dojo_collective_2026');
            if (savedData) {
                try { 
                    this.members = JSON.parse(savedData); 
                } catch (e) { 
                    this.members = []; 
                }
            }
            
            if (this.members.length === 0) this.addMember();

            // FITUR SIMPAN INPUTAN: Watch setiap perubahan dan simpan otomatis
            this.$watch('members', (val) => {
                localStorage.setItem('dojo_collective_2026', JSON.stringify(val));
            }, { deep: true });
        }
    }">
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- INFO LOGIN SECTION --}}
        <div class="mb-8 bg-white border-l-8 border-emerald-500 rounded-[2rem] shadow-sm overflow-hidden">
            <div class="p-6 flex justify-between items-center cursor-pointer" @click="showPass = !showPass">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-emerald-50 rounded-2xl">
                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002-2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </div>
                    <div>
                        <h4 class="font-black text-slate-800 text-sm uppercase italic tracking-tight">Akses Login Otomatis Murid</h4>
                        <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest">Klik untuk detail username & password</p>
                    </div>
                </div>
                <button type="button" class="text-emerald-600 font-black text-[10px] uppercase bg-emerald-50 px-4 py-2 rounded-xl" x-text="showPass ? 'TUTUP' : 'LIHAT DETAIL'"></button>
            </div>
            
            <div x-show="showPass" x-transition class="p-6 bg-slate-50 border-t border-slate-100 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-white p-4 rounded-2xl border border-slate-200">
                    <span class="text-[9px] font-black text-slate-400 uppercase block mb-1">Username Login</span>
                    <p class="text-sm font-bold text-slate-700">Nomor WhatsApp Masing-masing</p>
                </div>
                <div class="bg-white p-4 rounded-2xl border border-slate-200">
                    <span class="text-[9px] font-black text-slate-400 uppercase block mb-1">Password Default</span>
                    <p class="text-sm font-black text-emerald-600 uppercase" x-text="defaultPass"></p>
                </div>
            </div>
        </div>

        <form action="{{ route('admin.members.review') }}" method="POST" @submit="localStorage.removeItem('dojo_collective_2026')">
            @csrf
            
            <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
                <div class="text-center md:text-left">
                    <h2 class="text-2xl md:text-3xl font-black text-slate-800 uppercase italic leading-none">Pendaftaran Kolektif</h2>
                    <p class="text-slate-500 text-[10px] font-bold uppercase tracking-widest mt-1">Dojo <span class="text-emerald-600" x-text="rawDojoName"></span></p>
                </div>
                <button type="button" @click="addMember()" class="bg-emerald-600 text-white px-8 py-4 rounded-2xl text-sm font-black hover:bg-emerald-700 shadow-lg uppercase tracking-widest flex items-center gap-2 transition-transform active:scale-95">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg> Tambah Murid
                </button>
            </div>

            <div class="space-y-4">
                <template x-for="(member, index) in members" :key="member.id">
                    <div class="bg-white p-5 md:p-6 rounded-[2.5rem] md:rounded-3xl shadow-sm border border-slate-200 relative transition-all hover:border-emerald-300">
                        
                        <input type="hidden" :name="'members['+index+'][password]'" :value="defaultPass">

                        <button type="button" x-show="members.length > 1" @click="removeMember(member.id)" class="absolute -top-2 -right-2 bg-red-500 text-white p-1.5 rounded-full shadow-md hover:bg-red-600 z-20 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>

                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-start">
                            <div class="hidden md:flex md:col-span-1 justify-center pt-4">
                                <span class="bg-slate-100 text-slate-500 text-[10px] font-black w-8 h-8 flex items-center justify-center rounded-lg" x-text="index + 1"></span>
                            </div>

                            <div class="md:col-span-3">
                                <label class="text-[9px] font-black text-slate-400 uppercase mb-1 block tracking-wider">Nama Murid</label>
                                <input type="text" x-model="member.name" :name="'members['+index+'][name]'" class="w-full rounded-xl border-slate-200 font-bold p-3 text-sm focus:ring-emerald-500 focus:border-emerald-500" required placeholder="Nama Lengkap">
                            </div>

                            <div class="md:col-span-2">
                                <label class="text-[9px] font-black text-slate-400 uppercase mb-1 block tracking-wider">Nama Ortu</label>
                                <input type="text" x-model="member.parent_name" :name="'members['+index+'][parent_name]'" class="w-full rounded-xl border-slate-200 p-3 text-sm focus:ring-emerald-500" required placeholder="Ayah/Ibu">
                            </div>

                            <div class="md:col-span-2">
                                <label class="text-[9px] font-black text-emerald-600 uppercase mb-1 block tracking-wider">WhatsApp</label>
                                <input type="number" x-model="member.whatsapp" @input.debounce.500ms="checkWa(index)" :name="'members['+index+'][whatsapp]'" 
                                    class="w-full rounded-xl p-3 text-sm font-black border-slate-200 bg-emerald-50/20 focus:ring-emerald-500" required placeholder="0812...">
                                <span x-show="member.errorWa" class="text-[8px] text-red-600 font-bold mt-1 block italic" x-text="member.errorWa"></span>
                            </div>

                            <div class="md:col-span-2">
                                <label class="text-[9px] font-black text-slate-400 uppercase mb-1 block tracking-wider">Email</label>
                                <input type="email" x-model="member.email" :name="'members['+index+'][email]'" class="w-full rounded-xl border-slate-200 p-3 text-sm" placeholder="Opsional">
                            </div>

                            <div class="md:col-span-2">
                                <label class="text-[9px] font-black text-slate-400 uppercase mb-1 block tracking-wider">Sabuk & Tingkat</label>
                                <div wire:ignore>
                                    <select 
                                        class="belt-select w-full"
                                        :name="'members['+index+'][belt_level_id]'"
                                        x-init="
                                            $nextTick(() => {
                                                if (window.jQuery) {
                                                    const selectEl = $($el);
                                                    selectEl.select2({ 
                                                        width: '100%',
                                                        placeholder: 'PILIH',
                                                        dropdownCssClass: 'custom-dropdown' 
                                                    });
                                                    
                                                    if (member.belt_level_id) {
                                                        selectEl.val(member.belt_level_id).trigger('change');
                                                    }

                                                    selectEl.on('change', (e) => {
                                                        member.belt_level_id = e.target.value;
                                                        members = Array.from(members);
                                                    });
                                                }
                                            });
                                        "
                                        required>
                                        <option value=""></option>
                                        @foreach($beltLevels as $belt)
                                            <option value="{{ $belt->id }}">
                                                {{ strtoupper($belt->name) }} ({{ $belt->kyu_dan }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            {{-- FOOTER SECTION --}}
            <div class="mt-10 mb-20 bg-slate-900 p-8 md:p-10 rounded-[3rem] shadow-2xl flex flex-col md:flex-row items-center justify-between text-white gap-6">
                <div class="text-center md:text-left">
                    <h3 class="text-2xl md:text-3xl font-black italic text-emerald-400 leading-none">
                        <span x-text="members.length" class="text-white"></span> Calon Anggota
                    </h3>
                    <p class="text-slate-400 text-[10px] font-bold uppercase tracking-widest mt-2">Data Anda tersimpan otomatis di browser ini</p>
                </div>
                <div class="flex flex-col md:flex-row gap-4 w-full md:w-auto">
                    <button type="button" @click="if(confirm('Hapus semua inputan?')){ members = []; addMember(); localStorage.removeItem('dojo_collective_2026'); }" class="px-6 py-4 rounded-2xl text-[10px] font-black uppercase text-slate-400 hover:text-white transition-colors">
                        Reset Form
                    </button>
                    <button type="submit" 
                        :disabled="members.some(m => !m.name || !m.belt_level_id || m.errorWa)"
                        class="bg-emerald-500 text-white px-12 py-5 rounded-2xl font-black uppercase tracking-widest hover:bg-emerald-600 transition-all shadow-xl disabled:opacity-20 disabled:cursor-not-allowed text-sm">
                        Lanjut Ke Pembayaran
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
    /* Styling Dasar Select2 agar menyatu dengan Tema */
    .select2-container--default .select2-selection--single {
        border-radius: 0.75rem !important; 
        border-color: #e2e8f0 !important; 
        height: 48px !important; 
        background-color: #f8fafc !important;
        display: flex !important;
        align-items: center !important;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        font-weight: 800 !important;
        font-size: 11px !important;
        color: #1e293b !important;
        text-transform: uppercase !important;
        padding-left: 12px !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 46px !important;
        right: 10px !important;
    }

    /* Styling Dropdown Pilihan */
    .custom-dropdown .select2-results__option {
        font-weight: 700 !important;
        font-size: 12px !important;
        padding: 10px 15px !important;
        text-transform: uppercase !important;
    }

    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #10b981 !important; /* Emerald 500 */
    }

    /* Animasi Input */
    input:focus {
        transform: translateY(-1px);
        transition: all 0.2s;
    }
</style>
</x-app-layout>