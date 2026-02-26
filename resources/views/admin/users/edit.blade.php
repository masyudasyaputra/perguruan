<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <div class="min-w-0">
                <h2 class="font-black text-xl sm:text-3xl text-slate-900 leading-tight tracking-tighter uppercase">
                    Edit <span class="text-red-600">User</span>
                </h2>
                <p class="text-[8px] sm:text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mt-1">
                    Modifikasi Data: <span class="text-slate-600">{{ $user->name }}</span>
                </p>
            </div>

            <a href="{{ route('admin.users.index') }}"
                class="hidden sm:inline-flex items-center justify-center bg-white border-2 border-slate-200 text-slate-500 px-5 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-50 transition-all">
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-8 sm:py-10 bg-slate-50 min-h-screen">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Pesan Error Validasi --}}
            @if ($errors->any())
                <div class="mb-6 sm:mb-8 p-4 sm:p-5 bg-white border-l-4 border-red-600 rounded-2xl shadow-sm">
                    <div class="flex gap-3 sm:gap-4">
                        <div class="bg-red-100 p-2 rounded-xl">
                            <svg class="h-5 w-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="square" stroke-linejoin="square" stroke-width="3"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-xs font-black text-red-800 uppercase tracking-widest">Validasi Gagal</h3>
                            <ul
                                class="mt-1 list-disc list-inside text-[11px] font-bold text-red-600/80 uppercase tracking-tight">
                                @foreach ($errors->all() as $error)
                                    <li class="break-words">{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <div
                class="bg-white rounded-[2rem] border-2 border-slate-100 shadow-xl shadow-slate-200/50 overflow-hidden">

                {{-- Header Form --}}
                <div class="bg-slate-900 px-5 sm:px-7 md:px-8 py-5">
                    <div class="flex items-start sm:items-center justify-between gap-4">
                        <div class="min-w-0">
                            <h3 class="text-white text-[10px] sm:text-xs font-black uppercase tracking-[0.2em]">
                                Formulir Pembaruan Akun
                            </h3>
                            <p class="mt-1 text-[9px] font-black uppercase tracking-widest text-slate-400">
                                Role • Profil • Wilayah • Status
                            </p>
                        </div>

                        <span
                            class="inline-flex items-center gap-2 bg-white/5 border border-white/10 text-slate-200 px-3 sm:px-4 py-2 rounded-2xl text-[9px] font-black uppercase tracking-widest shrink-0">
                            <span class="w-2 h-2 rounded-full bg-red-600"></span>
                            ID: #{{ $user->id }}
                        </span>
                    </div>
                </div>
                <div class="h-1 bg-gradient-to-r from-slate-900 via-slate-700 to-red-600/60"></div>

                <form action="{{ route('admin.users.update', $user->id) }}" method="POST"
                    class="px-5 sm:px-7 md:px-8 pt-5 sm:pt-6 pb-6 sm:pb-8 space-y-6 sm:space-y-8">
                    @csrf
                    @method('PUT')

                    {{-- SECTION 1: ROLE --}}
                    <section class="rounded-[1.5rem] border-2 border-slate-100 bg-white p-4 sm:p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h4
                                class="text-[10px] font-black uppercase tracking-[0.15em] text-slate-900 flex items-center gap-3">
                                <span
                                    class="w-8 h-8 bg-slate-100 flex items-center justify-center rounded-xl border-2 border-slate-200">
                                    01
                                </span>
                                Hak Akses
                            </h4>
                            <span
                                class="hidden sm:inline-flex items-center gap-2 text-[9px] font-black uppercase tracking-widest text-slate-400">
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span> Multi Role
                            </span>
                        </div>

                        <div x-data="{ selectedRoles: @js(old('roles', is_array($user->roles) ? $user->roles : [$user->role])) }">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">
                                Pilih Role (Bisa Lebih Dari 1)
                            </label>

                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                                @foreach ($availableRoles as $key => $label)
                                    <label
                                        class="relative flex flex-col p-4 rounded-2xl border-2 cursor-pointer transition-all duration-200 group"
                                        :class="selectedRoles.includes('{{ $key }}') ?
                                            'border-slate-900 bg-slate-50' :
                                            'border-slate-100 bg-white hover:border-slate-300'">

                                        <input type="checkbox" name="roles[]" value="{{ $key }}" class="hidden"
                                            x-model="selectedRoles">

                                        <div class="flex justify-between items-start gap-3">
                                            <span
                                                class="font-black text-[11px] uppercase tracking-widest transition-colors"
                                                :class="selectedRoles.includes('{{ $key }}') ? 'text-slate-900' :
                                                    'text-slate-700'">
                                                {{ $label }}
                                            </span>

                                            <template x-if="selectedRoles.includes('{{ $key }}')">
                                                <div class="shrink-0 bg-slate-900 text-white rounded-xl p-1.5">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                </div>
                                            </template>
                                        </div>

                                        <span
                                            class="text-[10px] mt-2 leading-tight text-slate-400 font-bold uppercase tracking-tight"
                                            :class="selectedRoles.includes('{{ $key }}') ? 'text-slate-500' : ''">
                                            Akses as {{ $label }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>

                            <p class="mt-3 text-[10px] text-slate-500 italic">
                                * Role utama sistem tetap mengikuti field <b>role</b>. Roles[] adalah tambahan akses.
                            </p>
                        </div>
                    </section>

                    {{-- SECTION 2: PROFIL --}}
                    <section class="rounded-[1.5rem] border-2 border-slate-100 bg-white p-4 sm:p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h4
                                class="text-[10px] font-black uppercase tracking-[0.15em] text-slate-900 flex items-center gap-3">
                                <span
                                    class="w-8 h-8 bg-slate-100 flex items-center justify-center rounded-xl border-2 border-slate-200">
                                    02
                                </span>
                                Data Akun
                            </h4>
                            <span
                                class="hidden sm:inline-flex items-center gap-2 text-[9px] font-black uppercase tracking-widest text-slate-400">
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span> Profil
                            </span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-5">
                            <div class="md:col-span-2">
                                <label
                                    class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                                    Nama Lengkap <span class="text-red-600">*</span>
                                </label>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}"
                                    class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl py-3 px-4 text-sm font-bold text-slate-700 focus:ring-0 focus:border-slate-900 transition-all uppercase"
                                    required>
                            </div>

                            <div>
                                <label
                                    class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                                    Email Login <span class="text-red-600">*</span>
                                </label>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                    class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl py-3 px-4 text-sm font-bold text-slate-700 focus:ring-0 focus:border-slate-900 transition-all"
                                    required>
                            </div>

                            <div>
                                <label
                                    class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                                    Status Akun <span class="text-red-600">*</span>
                                </label>
                                <select name="is_active" required
                                    class="w-full bg-white border-2 border-slate-200 rounded-2xl py-3 px-4 text-sm font-bold text-slate-700 focus:ring-0 focus:border-slate-900 transition-all uppercase">
                                    <option value="1"
                                        {{ old('is_active', (string) $user->is_active) === '1' ? 'selected' : '' }}>
                                        Aktif</option>
                                    <option value="0"
                                        {{ old('is_active', (string) $user->is_active) === '0' ? 'selected' : '' }}>
                                        Nonaktif</option>
                                </select>
                            </div>

                            <div class="md:col-span-2">
                                <label
                                    class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                                    Password (Kosongkan jika tidak diganti)
                                </label>
                                <input type="password" name="password"
                                    class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl py-3 px-4 text-sm font-bold text-slate-700 focus:ring-0 focus:border-slate-900 transition-all"
                                    placeholder="••••••••">
                            </div>
                        </div>
                    </section>

                    {{-- SECTION 3: WILAYAH --}}
                    <section class="rounded-[1.5rem] border-2 border-slate-100 bg-slate-50 p-4 sm:p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h4
                                class="text-[10px] font-black uppercase tracking-[0.15em] text-slate-900 flex items-center gap-3">
                                <span
                                    class="w-8 h-8 bg-white flex items-center justify-center rounded-xl border-2 border-slate-200 shadow-sm">
                                    03
                                </span>
                                Wilayah Wewenang
                            </h4>
                            <span
                                class="hidden sm:inline-flex items-center gap-2 text-[9px] font-black uppercase tracking-widest text-slate-400">
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span> Scope
                            </span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 sm:gap-4">
                            <div>
                                <label
                                    class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                                    Provinsi <span class="text-red-600">*</span>
                                </label>
                                <select name="province_id" id="province_id" required
                                    class="w-full bg-white border-2 border-slate-200 rounded-2xl py-3 px-4 text-sm font-bold text-slate-700 focus:ring-0 focus:border-slate-900 transition-all uppercase">
                                    <option value="">Pilih Provinsi</option>
                                    @foreach ($provinces as $p)
                                        <option value="{{ $p->id }}"
                                            {{ old('province_id', $user->province_id) == $p->id ? 'selected' : '' }}>
                                            {{ $p->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label
                                    class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                                    Kota/Cabang
                                </label>
                                <select name="city_id" id="city_id"
                                    data-selected="{{ old('city_id', $user->city_id) }}"
                                    class="w-full bg-white border-2 border-slate-200 rounded-2xl py-3 px-4 text-sm font-bold text-slate-700 focus:ring-0 focus:border-slate-900 transition-all uppercase">
                                    <option value="">Pilih Kota</option>
                                </select>
                            </div>

                            <div>
                                <label
                                    class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                                    Dojo
                                </label>
                                <select name="dojo_id" id="dojo_id"
                                    data-selected="{{ old('dojo_id', $user->dojo_id) }}"
                                    class="w-full bg-white border-2 border-slate-200 rounded-2xl py-3 px-4 text-sm font-bold text-slate-700 focus:ring-0 focus:border-slate-900 transition-all uppercase">
                                    <option value="">Pilih Dojo</option>
                                </select>
                            </div>
                        </div>

                        <p class="mt-3 text-[10px] text-slate-500 italic">
                            * Kosongkan Dojo jika Admin Pengcab. Kosongkan Kota jika Admin Pengprov.
                        </p>
                    </section>

                    {{-- ACTIONS --}}
                    <div
                        class="sm:hidden -mx-5 px-5 pt-4 pb-5 sticky bottom-0 bg-slate-50/95 backdrop-blur border-t border-slate-100">
                        <div class="grid grid-cols-2 gap-3">
                            <a href="{{ route('admin.users.index') }}"
                                class="inline-flex justify-center items-center bg-white border-2 border-slate-200 text-slate-500 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-50 transition-all">
                                Batal
                            </a>
                            <button type="submit"
                                class="inline-flex justify-center items-center bg-slate-900 text-white py-3 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] hover:bg-red-600 transition-all border-b-4 border-slate-700 hover:border-red-800 active:translate-y-1">
                                Simpan
                            </button>
                        </div>
                    </div>

                    <div class="hidden sm:flex justify-end gap-3 pt-6 border-t-2 border-slate-50">
                        <a href="{{ route('admin.users.index') }}"
                            class="inline-flex justify-center items-center bg-white border-2 border-slate-200 text-slate-500 px-8 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-50 transition-all">
                            Batal
                        </a>
                        <button type="submit"
                            class="inline-flex justify-center items-center bg-slate-900 text-white px-10 py-3 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] hover:bg-red-600 shadow-lg shadow-slate-200 transition-all border-b-4 border-slate-700 hover:border-red-800 active:translate-y-1">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Script AJAX Dropdown Dinamis --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const provinceSelect = document.getElementById('province_id');
            const citySelect = document.getElementById('city_id');
            const dojoSelect = document.getElementById('dojo_id');

            const selectedCity = citySelect?.dataset?.selected || null;
            const selectedDojo = dojoSelect?.dataset?.selected || null;

            const fillSelect = (select, placeholder, items, selectedId = null) => {
                select.innerHTML = `<option value="">${placeholder}</option>`;
                (items || []).forEach(item => {
                    const opt = document.createElement('option');
                    opt.value = item.id;
                    opt.textContent = item.name;
                    if (selectedId && String(selectedId) === String(item.id)) opt.selected = true;
                    select.appendChild(opt);
                });
            };

            const fetchCities = async (provinceId, selectedId = null) => {
                if (!provinceId) {
                    fillSelect(citySelect, 'Pilih Kota', []);
                    fillSelect(dojoSelect, 'Pilih Dojo', []);
                    return;
                }
                citySelect.innerHTML = `<option value="">Memuat...</option>`;
                fillSelect(dojoSelect, 'Pilih Dojo', []);

                try {
                    const res = await fetch(`/api/cities/${provinceId}`);
                    const data = await res.json();
                    fillSelect(citySelect, 'Pilih Kota', data, selectedId);

                    // jika ada terpilih, fetch dojo
                    if (citySelect.value) {
                        await fetchDojos(citySelect.value, selectedDojo);
                    }
                } catch (e) {
                    fillSelect(citySelect, 'Gagal memuat', []);
                }
            };

            const fetchDojos = async (cityId, selectedId = null) => {
                if (!cityId) {
                    fillSelect(dojoSelect, 'Pilih Dojo', []);
                    return;
                }
                dojoSelect.innerHTML = `<option value="">Memuat...</option>`;
                try {
                    const res = await fetch(`/api/dojos/${cityId}`);
                    const data = await res.json();
                    fillSelect(dojoSelect, 'Pilih Dojo', data, selectedId);
                } catch (e) {
                    fillSelect(dojoSelect, 'Gagal memuat', []);
                }
            };

            provinceSelect?.addEventListener('change', function() {
                fetchCities(this.value, null);
            });

            citySelect?.addEventListener('change', function() {
                fetchDojos(this.value, null);
            });

            // init existing
            if (provinceSelect && provinceSelect.value) {
                fetchCities(provinceSelect.value, selectedCity);
            }
        });
    </script>
</x-app-layout>
