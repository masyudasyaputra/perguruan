<x-guest-layout>
    <div class="min-h-screen py-8 px-4" 
        x-data="{ 
            formData: {
                province_id: '{{ old('province_id') }}',
                province_name: '',
                city_id: '{{ old('city_id') }}',
                city_name: '',
                dojo_id: '{{ old('dojo_id') }}',
                dojo_name: ''
            },
            searchProvince: '', searchCity: '', searchDojo: '',
            openProv: false, openCity: false, openDojo: false,
            provinces: @js($provinces), cities: [], dojos: [],
            loadingCities: false, loadingDojos: false,

            get filteredProvinces() { return this.provinces.filter(i => i.name.toLowerCase().includes(this.searchProvince.toLowerCase())) },
            get filteredCities() { return this.cities.filter(i => i.name.toLowerCase().includes(this.searchCity.toLowerCase())) },
            get filteredDojos() { return this.dojos.filter(i => i.name.toLowerCase().includes(this.searchDojo.toLowerCase())) },

            async selectProvince(id, name) {
                this.formData.province_id = id; this.formData.province_name = name; this.openProv = false;
                this.formData.city_id = ''; this.formData.city_name = ''; this.formData.dojo_id = ''; this.formData.dojo_name = '';
                this.loadingCities = true;
                try {
                    const res = await fetch(`/api/cities/${id}`);
                    this.cities = await res.json();
                } finally { this.loadingCities = false; }
            },
            async selectCity(id, name) {
                this.formData.city_id = id; this.formData.city_name = name; this.openCity = false;
                this.formData.dojo_id = ''; this.formData.dojo_name = '';
                this.loadingDojos = true;
                try {
                    const res = await fetch(`/api/dojos/${id}`);
                    this.dojos = await res.json();
                } finally { this.loadingDojos = false; }
            }
        }">
        
        <div class="max-w-4xl mx-auto">
            {{-- HEADER DENGAN LOGO --}}
            <div class="mb-10 text-center">
                <div class="flex justify-center mb-4">
                    <a href="/">
                        <x-application-logo class="w-20 h-20 fill-current text-indigo-600 drop-shadow-sm" />
                    </a>
                </div>
                <h2 class="text-3xl font-bold text-slate-800 tracking-tight uppercase">Pendaftaran Anggota</h2>
                <div class="h-1 w-20 bg-indigo-600 mx-auto mt-2 rounded-full"></div>
            </div>

            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    
                    {{-- DATA PERSONAL --}}
                    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-indigo-600 mb-4 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" stroke-width="2"/></svg>
                            Informasi Murid
                        </h3>

                        <div class="space-y-3">
                            <div>
                                <x-input-label for="name" :value="__('Nama Murid')" class="text-[11px] font-bold" />
                                <x-text-input id="name" name="name" class="block w-full !rounded-xl border-slate-200 text-sm py-2 shadow-sm" type="text" :value="old('name')" required />
                            </div>
                            <div>
                                <x-input-label for="parent_name" :value="__('Orang Tua / Wali')" class="text-[11px] font-bold" />
                                <x-text-input id="parent_name" name="parent_name" class="block w-full !rounded-xl border-slate-200 text-sm py-2 shadow-sm" type="text" :value="old('parent_name')" required />
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <x-input-label for="whatsapp" :value="__('WhatsApp')" class="text-[11px] font-bold" />
                                    <div class="relative mt-1">
                                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 text-xs font-bold">08</span>
                                        <x-text-input id="whatsapp" name="whatsapp" class="block w-full pl-10 !rounded-xl border-slate-200 text-sm py-2 shadow-sm" type="number" :value="old('whatsapp')" required />
                                    </div>
                                </div>
                                <div>
                                    <x-input-label for="email" :value="__('Email')" class="text-[11px] font-bold" />
                                    <x-text-input id="email" name="email" class="block w-full !rounded-xl border-slate-200 text-sm py-2 shadow-sm" type="email" :value="old('email')" />
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-3 pt-2 border-t border-slate-100">
                                <div>
                                    <x-input-label for="password" :value="__('Sandi')" class="text-[11px] font-bold" />
                                    <x-text-input id="password" class="block w-full !rounded-xl border-slate-200 text-sm py-2" type="password" name="password" required />
                                </div>
                                <div>
                                    <x-input-label for="password_confirmation" :value="__('Konfirmasi')" class="text-[11px] font-bold" />
                                    <x-text-input id="password_confirmation" class="block w-full !rounded-xl border-slate-200 text-sm py-2" type="password" name="password_confirmation" required />
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- WILAYAH & DOJO --}}
                    <div class="space-y-4">
                        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-emerald-600 mb-4 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" stroke-width="2"/></svg>
                                Lokasi Latihan
                            </h3>

                            <div class="space-y-3">
                                {{-- Searchable Provinsi --}}
                                <div class="relative">
                                    <x-input-label :value="__('Provinsi')" class="text-[11px] font-bold" />
                                    <input type="hidden" name="province_id" x-model="formData.province_id">
                                    <div @click="openProv = !openProv" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 flex justify-between items-center cursor-pointer text-sm font-medium hover:bg-slate-100 transition-colors">
                                        <span x-text="formData.province_name || 'Cari Provinsi...'" :class="!formData.province_name && 'text-slate-400'"></span>
                                        <svg class="w-3 h-3 text-slate-400 transition-transform" :class="openProv && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </div>
                                    <div x-show="openProv" @click.away="openProv = false" x-cloak class="absolute z-50 w-full mt-1 bg-white border border-slate-200 rounded-xl shadow-xl">
                                        <input type="text" x-model="searchProvince" class="w-full border-none focus:ring-0 p-3 bg-slate-50 border-b border-slate-100 text-xs rounded-t-xl" placeholder="Ketik nama provinsi...">
                                        <div class="max-h-40 overflow-y-auto overflow-x-hidden">
                                            <template x-for="p in filteredProvinces" :key="p.id">
                                                <div @click="selectProvince(p.id, p.name)" class="p-3 hover:bg-indigo-50 cursor-pointer text-xs font-semibold text-slate-700 transition-colors" x-text="p.name"></div>
                                            </template>
                                        </div>
                                    </div>
                                </div>

                                {{-- Searchable Kota --}}
                                <div class="relative">
                                    <x-input-label :value="__('Kota/Kabupaten')" class="text-[11px] font-bold" />
                                    <input type="hidden" name="city_id" x-model="formData.city_id">
                                    <div @click="if(formData.province_id) openCity = !openCity" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 flex justify-between items-center cursor-pointer text-sm font-medium" :class="!formData.province_id && 'opacity-50 cursor-not-allowed'">
                                        <span x-text="loadingCities ? 'Memuat...' : (formData.city_name || 'Cari Kota...')" :class="!formData.city_name && 'text-slate-400'"></span>
                                        <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </div>
                                    <div x-show="openCity" @click.away="openCity = false" x-cloak class="absolute z-40 w-full mt-1 bg-white border border-slate-200 rounded-xl shadow-xl">
                                        <input type="text" x-model="searchCity" class="w-full border-none focus:ring-0 p-3 bg-slate-50 border-b border-slate-100 text-xs rounded-t-xl" placeholder="Ketik nama kota...">
                                        <div class="max-h-40 overflow-y-auto overflow-x-hidden">
                                            <template x-for="c in filteredCities" :key="c.id">
                                                <div @click="selectCity(c.id, c.name)" class="p-3 hover:bg-emerald-50 cursor-pointer text-xs font-semibold text-slate-700 transition-colors" x-text="c.name"></div>
                                            </template>
                                        </div>
                                    </div>
                                </div>

                                {{-- Searchable Dojo --}}
                                <div class="relative">
                                    <x-input-label :value="__('Dojo')" class="text-[11px] font-bold" />
                                    <input type="hidden" name="dojo_id" x-model="formData.dojo_id">
                                    <div @click="if(formData.city_id) openDojo = !openDojo" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 flex justify-between items-center cursor-pointer text-sm font-medium" :class="!formData.city_id && 'opacity-50 cursor-not-allowed'">
                                        <span x-text="loadingDojos ? 'Memuat...' : (formData.dojo_name || 'Cari Dojo...')" :class="!formData.dojo_name && 'text-slate-400'"></span>
                                        <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </div>
                                    <div x-show="openDojo" @click.away="openDojo = false" x-cloak class="absolute z-30 w-full mt-1 bg-white border border-slate-200 rounded-xl shadow-xl">
                                        <input type="text" x-model="searchDojo" class="w-full border-none focus:ring-0 p-3 bg-slate-50 border-b border-slate-100 text-xs rounded-t-xl" placeholder="Ketik nama dojo...">
                                        <div class="max-h-40 overflow-y-auto overflow-x-hidden">
                                            <template x-for="d in filteredDojos" :key="d.id">
                                                <div @click="formData.dojo_id=d.id; formData.dojo_name=d.name; openDojo=false" class="p-3 hover:bg-amber-50 cursor-pointer text-xs font-semibold text-slate-700 transition-colors" x-text="d.name"></div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- SABUK SELECTION (Perbaikan: Menampilkan Kyu/Dan dari Database) --}}
                        <div class="bg-indigo-900 p-5 rounded-2xl shadow-md border border-indigo-950">
                            <x-input-label :value="__('Tingkatan Sabuk')" class="text-[10px] font-bold text-indigo-200 uppercase mb-2" />
                            
                            <select id="belt_level_id" name="belt_level_id" class="block w-full border-none focus:ring-2 focus:ring-white/20 rounded-xl bg-white/10 text-white font-semibold p-2.5 text-xs cursor-pointer" required>
                                <option value="" class="text-slate-800">-- Pilih Sabuk & Tingkatan --</option>
                                
                                @foreach ($beltLevels as $belt)
                                    <option value="{{ $belt->id }}" {{ old('belt_level_id') == $belt->id ? 'selected' : '' }} class="text-slate-900 bg-white">
                                        {{-- Nama Sabuk (Uppercase) --}}
                                        {{ strtoupper($belt->name) }} 
                                        
                                        {{-- Menampilkan Kyu/Dan jika kolom kyu_dan tidak kosong --}}
                                        @if(!empty($belt->kyu_dan))
                                            - {{ strtoupper($belt->kyu_dan) }}
                                        @endif

                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="pt-6 text-center">
                    <button type="submit" class="w-full md:w-72 py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold uppercase tracking-widest text-sm shadow-xl shadow-indigo-200 transition-all transform active:scale-[0.98]">
                        Daftar Sekarang
                    </button>
                    <p class="mt-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                        Sudah Terdaftar? <a href="{{ route('login') }}" class="text-indigo-600 hover:underline">Masuk Di Sini</a>
                    </p>
                </div>
            </form>
        </div>
    </div>

    <style>
        [x-cloak] { display: none !important; }
        /* Custom scrollbar untuk dropdown wilayah agar tetap compact */
        .overflow-y-auto::-webkit-scrollbar { width: 4px; }
        .overflow-y-auto::-webkit-scrollbar-track { background: transparent; }
        .overflow-y-auto::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
        /* Menghilangkan arrow number input */
        input[type=number]::-webkit-inner-spin-button, input[type=number]::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
    </style>
</x-guest-layout>