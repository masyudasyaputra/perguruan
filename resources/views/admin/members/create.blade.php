<x-app-layout>
    {{-- deps --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    {{-- Header mengikuti style admin (dark slate + red accent) --}}
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <div class="min-w-0">
                <h2 class="font-black text-xl sm:text-3xl text-slate-900 leading-tight tracking-tighter uppercase">
                    Pendaftaran <span class="text-red-600">Kolektif</span>
                    <span class="text-red-600">•</span>
                </h2>
                <p class="text-[8px] sm:text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mt-0.5">
                    Dojo: <span class="text-slate-600">{{ auth()->user()->dojo->name ?? 'DOJO' }}</span>
                </p>
            </div>

        </div>
    </x-slot>

    {{-- Floating Action Button (mobile) like dojo/pengurus index --}}
    <div class="fixed bottom-6 right-6 z-50 md:hidden" x-data>
        <button type="button" @click="$dispatch('add-member')"
            class="flex items-center justify-center w-14 h-14 bg-red-600 text-white rounded-2xl shadow-[0_10px_25px_rgba(220,38,38,0.4)] active:scale-90 transition-transform border-b-4 border-red-800">
            <svg class="w-7 h-7 stroke-[3]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="square" stroke-linejoin="square" d="M12 4v16m8-8H4"></path>
            </svg>
        </button>
    </div>

    <div class="py-6 md:py-8 bg-slate-50 min-h-screen" x-data="{
        members: [],
        showPass: false,
        rawDojoName: '{{ auth()->user()->dojo->name ?? 'DOJO' }}',
    
        get defaultPass() {
            let nameParts = (this.rawDojoName || 'DOJO').trim().split(' ');
            return (nameParts[0] || 'DOJO').toUpperCase() + '123';
        },
    
        async checkWa(index) {
            let wa = this.members[index].whatsapp;
    
            if (!wa || wa.length < 9) {
                this.members[index].errorWa = '';
                return;
            }
    
            let isDuplicateLocal = this.members.some((m, idx) => m.whatsapp === wa && idx !== index);
            if (isDuplicateLocal) {
                this.members[index].errorWa = 'Nomor ini sudah ditulis di baris lain';
                return;
            }
    
            try {
                let response = await fetch(`/api/check-whatsapp?number=${wa}`);
                let data = await response.json();
                this.members[index].errorWa = data.exists ? 'Nomor sudah terdaftar di sistem' : '';
            } catch (e) {
                console.error('WA check failed');
            }
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
    
            this.$nextTick(() => window.initBeltSelects && window.initBeltSelects());
        },
    
        removeMember(id) {
            if (this.members.length > 1) {
                this.members = this.members.filter(m => m.id !== id);
                this.$nextTick(() => window.initBeltSelects && window.initBeltSelects());
            }
        },
    
        init() {
            const savedData = localStorage.getItem('dojo_collective_2026');
            if (savedData) {
                try { this.members = JSON.parse(savedData); } catch (e) { this.members = []; }
            }
    
            if (this.members.length === 0) this.addMember();
    
            this.$watch('members', (val) => {
                localStorage.setItem('dojo_collective_2026', JSON.stringify(val));
            }, { deep: true });
    
            this.$nextTick(() => window.initBeltSelects && window.initBeltSelects());
    
            // listen FAB event
            this.$root.addEventListener('add-member', () => this.addMember());
        }
    }">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- INFO LOGIN SECTION (dark slate + red accent) --}}
            <div
                class="mb-6 bg-white rounded-[2rem] border-2 border-slate-100 shadow-xl shadow-slate-200/50 overflow-hidden">
                <button type="button"
                    class="w-full text-left px-5 sm:px-7 py-5 flex items-center justify-between gap-4 bg-slate-900"
                    @click="showPass = !showPass">
                    <div class="flex items-center gap-3 sm:gap-4 min-w-0">
                        <div class="p-2.5 rounded-2xl bg-white/10 border border-white/10 shrink-0">
                            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="square" stroke-linejoin="square" stroke-width="2.5"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-300">
                                Akses Login Murid
                            </p>
                            <p class="mt-1 text-[10px] font-black uppercase tracking-widest text-slate-200">
                                Username: No. WhatsApp • Pass: <span class="text-white" x-text="defaultPass"></span>
                            </p>
                        </div>
                    </div>

                    <span
                        class="shrink-0 inline-flex items-center gap-2 px-4 py-2 rounded-2xl text-[9px] font-black uppercase tracking-widest bg-white/10 border border-white/10 text-white">
                        <span class="w-2 h-2 rounded-full bg-red-600"></span>
                        <span x-text="showPass ? 'TUTUP' : 'DETAIL'"></span>
                    </span>
                </button>

                <div x-show="showPass" x-transition
                    class="px-5 sm:px-7 py-5 bg-white border-t-2 border-slate-100 grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4">
                    <div class="bg-slate-50 p-4 rounded-2xl border-2 border-slate-100">
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1">
                            Cara Login
                        </span>
                        <p class="text-xs font-bold text-slate-700">
                            Murid login menggunakan nomor WhatsApp yang didaftarkan di bawah ini.
                        </p>
                    </div>
                    <div class="bg-slate-50 p-4 rounded-2xl border-2 border-slate-100">
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1">
                            Password Default
                        </span>
                        <p class="text-sm font-black text-red-600 uppercase" x-text="defaultPass"></p>
                    </div>
                </div>
            </div>

            <form action="{{ route('admin.members.review') }}" method="POST"
                @submit="localStorage.removeItem('dojo_collective_2026')">
                @csrf

                {{-- Toolbar --}}
                <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-5 gap-3">
                    <div class="min-w-0">
                        <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                            Form Input Kolektif
                        </h3>
                        <p class="mt-1 text-[11px] font-bold text-slate-600">
                            Isi data murid, nomor WA (login), dan sabuk saat ini.
                        </p>
                    </div>

                    {{-- mobile hint only --}}
                    <div
                        class="md:hidden inline-flex items-center gap-2 text-[9px] font-black uppercase tracking-widest text-slate-500">
                        <span class="w-2 h-2 rounded-full bg-red-600"></span>
                        tambah murid via tombol +
                    </div>

                    {{-- Desktop add button --}}
                    <button type="button" @click="addMember()"
                        class="hidden md:inline-flex items-center bg-slate-900 hover:bg-red-600 text-white px-5 py-3 rounded-xl text-[10px] font-black transition-all duration-300 hover:-translate-y-1 shadow-lg shadow-slate-200 uppercase tracking-[0.15em] border-b-4 border-slate-700 hover:border-red-800 active:translate-y-0">
                        <svg class="w-4 h-4 me-2 stroke-[3]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="square" stroke-linejoin="square" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Tambah Murid
                    </button>
                </div>

                <div class="space-y-4">
                    <template x-for="(member, index) in members" :key="member.id">
                        <div
                            class="relative bg-white rounded-[2rem] border-2 border-slate-100 shadow-xl shadow-slate-200/50 overflow-hidden">

                            <input type="hidden" :name="'members[' + index + '][password]'" :value="defaultPass">

                            <button type="button" x-show="members.length > 1" @click="removeMember(member.id)"
                                class="absolute top-4 right-4 z-20 inline-flex items-center justify-center w-9 h-9 rounded-2xl bg-rose-50 text-rose-600 border-2 border-rose-100 hover:bg-rose-600 hover:text-white transition-all">
                                <svg class="w-4 h-4 stroke-[3]" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="square" stroke-linejoin="square" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>

                            <div class="px-5 sm:px-7 py-4 bg-slate-900 flex items-center justify-between gap-4">
                                <div class="flex items-center gap-3 min-w-0">
                                    <span
                                        class="w-9 h-9 rounded-2xl bg-white/10 border border-white/10 flex items-center justify-center text-[10px] font-black uppercase tracking-widest text-white shrink-0"
                                        x-text="index + 1"></span>

                                    <div class="min-w-0">
                                        <p class="text-[9px] font-black text-slate-300 uppercase tracking-[0.2em]">
                                            Data Murid
                                        </p>
                                        <p class="text-[10px] font-black uppercase tracking-widest text-white truncate"
                                            x-text="member.name ? member.name : 'BELUM DIISI'"></p>
                                    </div>
                                </div>

                                <span
                                    class="hidden sm:inline-flex items-center gap-2 text-[9px] font-black uppercase tracking-widest bg-white/10 border border-white/10 text-slate-200 px-3 py-2 rounded-2xl shrink-0">
                                    <span class="w-2 h-2 rounded-full"
                                        :class="member.errorWa ? 'bg-rose-500' : (member.whatsapp ? 'bg-emerald-500' :
                                            'bg-slate-500')"></span>
                                    <span x-text="member.errorWa ? 'ERROR' : (member.whatsapp ? 'OK' : 'DRAFT')"></span>
                                </span>
                            </div>

                            <div class="px-5 sm:px-7 py-5">
                                <div x-show="member.errorWa" x-cloak
                                    class="mb-4 p-4 bg-white border-l-4 border-rose-600 rounded-2xl shadow-sm">
                                    <div class="flex gap-3">
                                        <div class="bg-rose-100 p-2 rounded-xl">
                                            <svg class="h-5 w-5 text-rose-600" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="square" stroke-linejoin="square"
                                                    stroke-width="3" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </div>
                                        <div class="min-w-0">
                                            <h4 class="text-[10px] font-black text-rose-800 uppercase tracking-widest">
                                                Validasi WhatsApp
                                            </h4>
                                            <p class="mt-1 text-[11px] font-bold text-rose-600/80 uppercase tracking-tight"
                                                x-text="member.errorWa"></p>
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                                    <div class="md:col-span-3">
                                        <label
                                            class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                                            Nama Murid <span class="text-red-600">*</span>
                                        </label>
                                        <input type="text" x-model="member.name"
                                            :name="'members[' + index + '][name]'"
                                            class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl py-3 px-4 text-sm font-bold focus:ring-0 focus:border-slate-900 transition-all uppercase"
                                            required placeholder="Nama Lengkap">
                                    </div>

                                    <div class="md:col-span-3">
                                        <label
                                            class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                                            Nama Ortu <span class="text-red-600">*</span>
                                        </label>
                                        <input type="text" x-model="member.parent_name"
                                            :name="'members[' + index + '][parent_name]'"
                                            class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl py-3 px-4 text-sm font-bold focus:ring-0 focus:border-slate-900 transition-all uppercase"
                                            required placeholder="Ayah/Ibu">
                                    </div>

                                    <div class="md:col-span-3">
                                        <label
                                            class="block text-[10px] font-black uppercase tracking-widest mb-2 text-slate-400">
                                            WhatsApp (ID Login) <span class="text-red-600">*</span>
                                        </label>
                                        <input type="tel" inputmode="numeric" x-model="member.whatsapp"
                                            @input.debounce.500ms="checkWa(index)"
                                            :name="'members[' + index + '][whatsapp]'"
                                            class="w-full border-2 rounded-2xl py-3 px-4 text-sm font-black focus:ring-0 transition-all"
                                            :class="member.errorWa ? 'border-rose-300 bg-rose-50 focus:border-rose-600' :
                                                'border-slate-100 bg-slate-50 focus:border-slate-900'"
                                            required placeholder="0812...">
                                        <p class="mt-1 text-[10px] text-slate-400 font-bold uppercase tracking-tight">
                                            * Dipakai untuk username login.
                                        </p>
                                    </div>

                                    <div class="md:col-span-3">
                                        <label
                                            class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                                            Email (Opsional)
                                        </label>
                                        <input type="email" x-model="member.email"
                                            :name="'members[' + index + '][email]'"
                                            class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl py-3 px-4 text-sm font-bold focus:ring-0 focus:border-slate-900 transition-all"
                                            placeholder="opsional@email.com">
                                    </div>

                                    <div class="md:col-span-4">
                                        <label
                                            class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                                            Sabuk Saat Ini <span class="text-red-600">*</span>
                                        </label>

                                        <div wire:ignore>
                                            <select class="belt-select w-full" :id="'belt_select_' + member.id"
                                                :name="'members[' + index + '][belt_level_id]'"
                                                x-init="$nextTick(() => {
                                                    if (window.initOneBeltSelect) window.initOneBeltSelect($el, member, members);
                                                })" required>
                                                <option value=""></option>
                                                @foreach ($beltLevels as $belt)
                                                    <option value="{{ $belt->id }}">
                                                        {{ strtoupper($belt->name) }} ({{ $belt->kyu_dan }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <p class="mt-1 text-[10px] text-slate-400 font-bold uppercase tracking-tight">
                                            * Pilih tingkatan sabuk saat ini.
                                        </p>
                                    </div>

                                    <div class="md:col-span-8">
                                        <div class="bg-slate-50 border-2 border-slate-100 rounded-2xl p-4 h-full">
                                            <div class="flex items-start justify-between gap-3">
                                                <div class="min-w-0">
                                                    <p
                                                        class="text-[9px] font-black text-slate-400 uppercase tracking-widest">
                                                        Password Default Otomatis
                                                    </p>
                                                    <p class="mt-1 text-sm font-black text-slate-900 uppercase tracking-tight"
                                                        x-text="defaultPass"></p>
                                                    <p class="mt-1 text-[10px] font-bold text-slate-500">
                                                        Login = WA + password default (bisa diubah nanti).
                                                    </p>
                                                </div>
                                                <div
                                                    class="shrink-0 p-2 rounded-2xl bg-white border-2 border-slate-100">
                                                    <svg class="w-5 h-5 text-red-600" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="square" stroke-linejoin="square"
                                                            stroke-width="2.5"
                                                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>{{-- grid --}}
                            </div>{{-- body --}}
                        </div>
                    </template>
                </div>

                {{-- FOOTER / CTA --}}
                <div class="mt-8 mb-24 md:mb-10">
                    <div class="bg-slate-900 rounded-[2rem] p-6 sm:p-8 shadow-2xl border border-slate-800">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                            <div class="min-w-0">
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em]">Ringkasan
                                </p>
                                <h3 class="mt-1 text-2xl sm:text-3xl font-black uppercase tracking-tight text-white">
                                    <span x-text="members.length"></span> <span class="text-red-600">Calon
                                        Anggota</span>
                                </h3>
                                <p class="mt-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                                    Pastikan WhatsApp aktif & unik.
                                </p>
                            </div>

                            <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                                <button type="button"
                                    @click="if(confirm('Hapus semua inputan?')){ members = []; addMember(); localStorage.removeItem('dojo_collective_2026'); }"
                                    class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-4 rounded-2xl bg-white/5 border border-white/10 text-slate-200 hover:text-white hover:bg-white/10 transition-all text-[10px] font-black uppercase tracking-widest">
                                    Reset Form
                                </button>

                                <button type="submit"
                                    :disabled="members.some(m => !m.name || !m.parent_name || !m.belt_level_id || m.errorWa || !m
                                        .whatsapp)"
                                    class="w-full sm:w-auto inline-flex items-center justify-center bg-red-600 hover:bg-red-500 text-white px-10 py-4 rounded-2xl font-black uppercase tracking-[0.2em] transition-all shadow-xl border-b-4 border-red-800 active:translate-y-1 disabled:opacity-25 disabled:grayscale disabled:cursor-not-allowed">
                                    Lanjut Ke Pembayaran
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <div class="h-10 md:h-0"></div>
        </div>
    </div>

    <style>
        .select2-container {
            width: 100% !important;
        }

        .select2-container--default .select2-selection--single {
            border-radius: 1rem !important;
            border: 2px solid #e2e8f0 !important;
            height: 48px !important;
            background-color: #f8fafc !important;
            display: flex !important;
            align-items: center !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            font-weight: 800 !important;
            font-size: 12px !important;
            color: #0f172a !important;
            text-transform: uppercase !important;
            letter-spacing: 0.08em !important;
            padding-left: 14px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__placeholder {
            color: #94a3b8 !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 46px !important;
            right: 12px !important;
        }

        .custom-dropdown .select2-results__option {
            font-weight: 800 !important;
            font-size: 12px !important;
            padding: 10px 14px !important;
            text-transform: uppercase !important;
            letter-spacing: 0.08em !important;
        }

        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #dc2626 !important;
        }

        .select2-container--default .select2-results__option[aria-selected="true"] {
            background-color: #fee2e2 !important;
            color: #991b1b !important;
        }

        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
    </style>

    <script>
        // init per-element (lebih aman untuk dynamic rows)
        window.initOneBeltSelect = (el, member, membersRef) => {
            if (!window.jQuery) return;

            const $el = window.jQuery(el);

            if ($el.hasClass('select2-hidden-accessible')) {
                $el.off('change.select2sync');
                $el.select2('destroy');
            }

            $el.select2({
                width: '100%',
                placeholder: 'PILIH',
                dropdownCssClass: 'custom-dropdown'
            });

            if (member.belt_level_id) {
                $el.val(member.belt_level_id).trigger('change.select2');
            } else {
                $el.val(null).trigger('change.select2');
            }

            $el.on('change.select2sync', (e) => {
                member.belt_level_id = e.target.value;
                membersRef = Array.from(membersRef);
            });
        };

        window.initBeltSelects = () => {
            document.querySelectorAll('select.belt-select').forEach((el) => {
                if (!window.jQuery(el).hasClass('select2-hidden-accessible')) {
                    window.jQuery(el).select2({
                        width: '100%',
                        placeholder: 'PILIH',
                        dropdownCssClass: 'custom-dropdown'
                    });
                }
            });
        };
    </script>
</x-app-layout>
