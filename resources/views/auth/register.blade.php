{{-- File: resources/views/auth/register.blade.php --}}
<x-guest-layout>
    {{-- Menggunakan min-h-screen agar jika konten lebih panjang dari layar (di HP), tidak terpotong --}}
    <div class="w-full max-w-[98%] mx-auto px-2 min-h-screen flex flex-col justify-center" x-data="{
        formData: {
            province_id: '{{ old('province_id') }}',
            province_name: '',
            city_id: '{{ old('city_id') }}',
            city_name: '',
            dojo_id: '{{ old('dojo_id') }}',
            dojo_name: ''
        },
        searchProvince: '',
        searchCity: '',
        searchDojo: '',
        openProv: false,
        openCity: false,
        openDojo: false,
        provinces: @js($provinces),
        cities: [],
        dojos: [],
        loadingCities: false,
        loadingDojos: false,
    
        get filteredProvinces() {
            return this.provinces.filter(p => p.name.toLowerCase().includes(this.searchProvince.toLowerCase()))
        },
        get filteredCities() {
            return this.cities.filter(c => c.name.toLowerCase().includes(this.searchCity.toLowerCase()))
        },
        get filteredDojos() {
            return this.dojos.filter(d => d.name.toLowerCase().includes(this.searchDojo.toLowerCase()))
        },
    
        async selectProvince(id, name) {
            this.formData.province_id = id;
            this.formData.province_name = name;
            this.openProv = false;
            this.formData.city_id = '';
            this.formData.city_name = '';
            this.loadingCities = true;
            try {
                const res = await fetch(`/api/cities/${id}`);
                this.cities = await res.json();
            } finally { this.loadingCities = false; }
        },
        async selectCity(id, name) {
            this.formData.city_id = id;
            this.formData.city_name = name;
            this.openCity = false;
            this.formData.dojo_id = '';
            this.formData.dojo_name = '';
            this.loadingDojos = true;
            try {
                const res = await fetch(`/api/dojos/${id}`);
                this.dojos = await res.json();
            } finally { this.loadingDojos = false; }
        }
    }">

        {{-- Header Section: Ditambah margin top di mobile agar tidak mepet status bar --}}
        <div class="mb-6 lg:mb-8 text-center shrink-0 mt-8 lg:mt-0">
            <h2 class="text-2xl md:text-3xl lg:text-4xl font-black text-white tracking-tighter uppercase leading-tight">
                Registrasi <span class="gold-gradient-text">Anggota Baru</span>
            </h2>
            <div class="h-1 w-16 bg-gradient-to-r from-[#bf953f] via-red-600 to-transparent mx-auto mt-2 rounded-full">
            </div>
        </div>

        <form method="POST" action="{{ route('register') }}" class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-stretch">
            @csrf

            {{-- Kolom Kiri: Identitas --}}
            <div class="lg:col-span-8 flex">
                <div
                    class="w-full bg-white/5 p-5 md:p-6 lg:p-8 rounded-[2rem] border border-white/10 backdrop-blur-md flex flex-col">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="h-5 w-1.5 bg-red-600 rounded-full shadow-[0_0_8px_#dc2626]"></div>
                        <h3 class="text-[10px] lg:text-[11px] font-bold uppercase tracking-[0.2em] text-white/90">Data
                            Identitas Murid & Wali</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-5">
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold uppercase tracking-widest text-slate-400 ml-1">Nama
                                Lengkap Murid</label>
                            <x-text-input name="name"
                                class="block w-full !rounded-xl bg-white/5 border-white/10 text-white py-3 px-4 text-sm focus:bg-white/10 transition-all focus:border-red-500/50"
                                type="text" :value="old('name')" required />
                        </div>

                        <div class="space-y-1">
                            <label class="text-[10px] font-bold uppercase tracking-widest text-slate-400 ml-1">Orang Tua
                                / Wali</label>
                            <x-text-input name="parent_name"
                                class="block w-full !rounded-xl bg-white/5 border-white/10 text-white py-3 px-4 text-sm focus:bg-white/10 transition-all focus:border-red-500/50"
                                type="text" :value="old('parent_name')" required />
                        </div>

                        <div class="space-y-1">
                            <label
                                class="text-[10px] font-bold uppercase tracking-widest text-slate-400 ml-1">WhatsApp</label>
                            <div class="relative">
                                <span
                                    class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs font-bold border-r pr-2 border-white/10">08</span>
                                <x-text-input name="whatsapp"
                                    class="block w-full pl-12 !rounded-xl bg-white/5 border-white/10 text-white py-3 text-sm focus:border-red-500/50"
                                    type="number" required />
                            </div>
                        </div>

                        <div class="space-y-1">
                            <label class="text-[10px] font-bold uppercase tracking-widest text-slate-400 ml-1">Email
                                Aktif</label>
                            <x-text-input name="email"
                                class="block w-full !rounded-xl bg-white/5 border-white/10 text-white py-3 px-4 text-sm focus:border-red-500/50"
                                type="email" />
                        </div>

                        <div class="space-y-1">
                            <label class="text-[10px] font-bold uppercase tracking-widest text-slate-400 ml-1">Kata
                                Sandi</label>
                            <x-text-input type="password" name="password"
                                class="block w-full !rounded-xl bg-white/5 border-white/10 text-white py-3 px-4 text-sm focus:border-red-500/50"
                                required />
                        </div>

                        <div class="space-y-1">
                            <label
                                class="text-[10px] font-bold uppercase tracking-widest text-slate-400 ml-1">Konfirmasi
                                Sandi</label>
                            <x-text-input type="password" name="password_confirmation"
                                class="block w-full !rounded-xl bg-white/5 border-white/10 text-white py-3 px-4 text-sm focus:border-red-500/50"
                                required />
                        </div>
                    </div>
                </div>
            </div>

            {{-- Kolom Kanan --}}
            <div class="lg:col-span-4 flex flex-col gap-4">
                <div class="bg-white/5 p-5 md:p-6 rounded-[2rem] border border-white/10 backdrop-blur-md">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="h-5 w-1.5 bg-emerald-500 rounded-full shadow-[0_0_8px_#10b981]"></div>
                        <h3 class="text-[10px] lg:text-[11px] font-bold uppercase tracking-[0.2em] text-white/90">Lokasi
                            & Unit</h3>
                    </div>

                    <div class="space-y-4">
                        <div class="relative">
                            <label
                                class="text-[10px] font-bold uppercase tracking-widest text-slate-400 ml-1 mb-1 block">Provinsi</label>
                            <div @click="openProv = !openProv"
                                class="w-full bg-white/5 border border-white/10 rounded-xl py-3 px-4 flex justify-between items-center cursor-pointer text-xs font-semibold text-white">
                                <span class="truncate" x-text="formData.province_name || 'Pilih Provinsi'"></span>
                                <svg class="w-3 h-3 text-[#bf953f]" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path d="M19 9l-7 7-7-7" stroke-width="4" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>
                            </div>
                            <div x-show="openProv" @click.away="openProv = false" x-cloak
                                class="absolute z-[100] w-full mt-1 bg-slate-900 border border-white/20 rounded-xl shadow-2xl p-2">
                                <input type="text" x-model="searchProvince"
                                    class="w-full bg-white/10 border-none p-2 text-xs text-white rounded-lg mb-2 focus:ring-1 focus:ring-red-500"
                                    placeholder="Cari...">
                                <div class="max-h-40 overflow-y-auto custom-scroll">
                                    <template x-for="p in filteredProvinces" :key="p.id">
                                        <div @click="selectProvince(p.id, p.name)"
                                            class="p-2 hover:bg-[#bf953f]/20 rounded-lg cursor-pointer text-[10px] text-slate-300 uppercase transition-colors"
                                            x-text="p.name"></div>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <div class="relative">
                            <label
                                class="text-[10px] font-bold uppercase tracking-widest text-slate-400 ml-1 mb-1 block">Kota
                                / Kabupaten</label>
                            <div @click="if(formData.province_id) openCity = !openCity"
                                class="w-full bg-white/5 border border-white/10 rounded-xl py-3 px-4 flex justify-between items-center cursor-pointer text-xs font-semibold text-white"
                                :class="!formData.province_id && 'opacity-30'">
                                <span class="truncate" x-text="formData.city_name || 'Pilih Kota'"></span>
                                <svg class="w-3 h-3 text-[#bf953f]" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path d="M19 9l-7 7-7-7" stroke-width="4" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>
                            </div>
                            <div x-show="openCity" @click.away="openCity = false" x-cloak
                                class="absolute z-[90] w-full mt-1 bg-slate-900 border border-white/20 rounded-xl shadow-2xl p-2">
                                <div class="max-h-40 overflow-y-auto custom-scroll">
                                    <template x-for="c in filteredCities" :key="c.id">
                                        <div @click="selectCity(c.id, c.name)"
                                            class="p-2 hover:bg-emerald-500/20 rounded-lg cursor-pointer text-[10px] text-slate-300 uppercase transition-colors"
                                            x-text="c.name"></div>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <div class="relative">
                            <label
                                class="text-[10px] font-bold uppercase tracking-widest text-slate-400 ml-1 mb-1 block">Unit
                                Dojo</label>
                            <div @click="if(formData.city_id) openDojo = !openDojo"
                                class="w-full bg-white/5 border border-white/10 rounded-xl py-3 px-4 flex justify-between items-center cursor-pointer text-xs font-semibold text-white"
                                :class="!formData.city_id && 'opacity-30'">
                                <span class="truncate" x-text="formData.dojo_name || 'Pilih Unit'"></span>
                                <svg class="w-3 h-3 text-[#bf953f]" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path d="M19 9l-7 7-7-7" stroke-width="4" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>
                            </div>
                            <div x-show="openDojo" @click.away="openDojo = false" x-cloak
                                class="absolute z-[80] w-full mt-1 bg-slate-900 border border-white/20 rounded-xl shadow-2xl p-2">
                                <div class="max-h-40 overflow-y-auto custom-scroll">
                                    <template x-for="d in filteredDojos" :key="d.id">
                                        <div @click="formData.dojo_id=d.id; formData.dojo_name=d.name; openDojo=false"
                                            class="p-2 hover:bg-red-500/20 rounded-lg cursor-pointer text-[10px] text-slate-300 uppercase transition-colors"
                                            x-text="d.name"></div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-slate-900 to-red-950/30 p-5 rounded-[2rem] border border-white/10">
                    <label
                        class="text-[10px] font-bold uppercase tracking-widest text-slate-400 ml-1 mb-2 block">Tingkatan
                        Sabuk</label>
                    <select name="belt_level_id"
                        class="w-full border-none focus:ring-1 focus:ring-[#bf953f] rounded-xl bg-white/5 text-white font-black py-3 px-4 text-xs cursor-pointer appearance-none"
                        required>
                        <option value="" class="text-slate-900">PILIH TINGKATAN...</option>
                        @foreach ($beltLevels as $belt)
                            <option value="{{ $belt->id }}" class="text-slate-900 bg-white">
                                {{ strtoupper($belt->name) }}
                                {{ !empty($belt->kyu_dan) ? '- ' . strtoupper($belt->kyu_dan) : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Submit Area --}}
            <div
                class="lg:col-span-12 flex flex-col md:flex-row items-center justify-between bg-white/5 p-4 rounded-[2rem] border border-white/10 backdrop-blur-md gap-4 mb-8 lg:mb-0">
                <a href="{{ route('login') }}"
                    class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] hover:text-white transition-all md:ml-6 order-2 md:order-1">
                    Sudah Terdaftar? <span class="gold-gradient-text ml-1">Masuk Portal</span>
                </a>
                <button type="submit"
                    class="w-full md:max-w-md py-4 bg-gradient-to-r from-red-800 to-red-950 hover:from-red-700 hover:to-red-900 text-white rounded-full font-black uppercase tracking-[0.3em] text-[10px] shadow-xl border border-white/5 transition-all flex items-center justify-center gap-4 group order-1 md:order-2">
                    <span>Selesaikan Registrasi</span>
                    <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                            d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </button>
                <div class="w-32 hidden lg:block order-3"></div>
            </div>
        </form>
    </div>

    <style>
        [x-cloak] {
            display: none !important;
        }

        .custom-scroll::-webkit-scrollbar {
            width: 3px;
        }

        .custom-scroll::-webkit-scrollbar-thumb {
            background: #bf953f;
            border-radius: 10px;
        }

        {{-- Perbaikan: Hanya desktop yang dikunci tingginya, mobile harus bebas scroll --}} @media (min-width: 1024px) {
            body {
                overflow: hidden;
            }
        }

        @media (max-width: 1023px) {
            body {
                overflow-y: auto !important;
                height: auto !important;
            }
        }

        .gold-gradient-text {
            background: linear-gradient(to right, #bf953f, #fcf6ba, #b38728, #fbf5b7, #aa771c);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</x-guest-layout>
