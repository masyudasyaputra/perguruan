<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <div class="min-w-0">
                <h2 class="font-black text-xl sm:text-3xl text-slate-900 leading-tight tracking-tighter uppercase">
                    {{ __('Edit') }} <span class="text-slate-900">Pengurus</span>
                    <span class="text-red-600">•</span>
                </h2>
                <p class="text-[8px] sm:text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mt-1">
                    Modifikasi Data: <span class="text-slate-600">{{ $official->name }}</span>
                </p>
            </div>

            {{-- Chip mobile --}}
            <div
                class="sm:hidden shrink-0 inline-flex items-center gap-2 bg-slate-900 text-white px-3 py-2 rounded-2xl text-[9px] font-black uppercase tracking-widest">
                <span class="w-2 h-2 rounded-full bg-red-600"></span>
                EDIT
            </div>
        </div>
    </x-slot>

    <div class="py-8 sm:py-10 bg-slate-50 min-h-screen">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Pesan Error Validasi (merah hanya untuk error) --}}
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
                {{-- Header Form (slate dominan; merah aksen tipis) --}}
                <div class="relative">
                    <div class="bg-gradient-to-r from-slate-950 via-slate-900 to-slate-900 px-5 sm:px-7 md:px-8 py-5">
                        <div class="flex items-start sm:items-center justify-between gap-4">
                            <div class="min-w-0">
                                <h3 class="text-white text-[10px] sm:text-xs font-black uppercase tracking-[0.2em]">
                                    Formulir Pembaruan Data
                                </h3>
                                <p class="mt-1 text-[9px] font-black uppercase tracking-widest text-slate-400">
                                    Profil • Wilayah • Legalitas
                                </p>
                            </div>

                            <div class="text-right shrink-0">
                                <span
                                    class="inline-flex items-center gap-2 bg-white/5 border border-white/10 text-slate-200 px-3 sm:px-4 py-2 rounded-2xl text-[9px] font-black uppercase tracking-widest">
                                    <span class="w-2 h-2 rounded-full bg-red-600"></span>
                                    ID: #{{ $official->id }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="h-1 bg-gradient-to-r from-slate-900 via-slate-700 to-red-600/60"></div>

                    <div class="pointer-events-none absolute inset-0 opacity-10"
                        style="background: radial-gradient(520px 180px at 15% 30%, rgba(220,38,38,0.22), transparent 60%);">
                    </div>
                </div>

                <form action="{{ route('admin.officials.update', $official->id) }}" method="POST"
                    class="px-5 sm:px-7 md:px-8 pt-5 sm:pt-6 pb-6 sm:pb-8 space-y-6 sm:space-y-8">
                    @csrf
                    @method('PUT')

                    {{-- Section 1: Profil & Jabatan --}}
                    <section class="rounded-[1.5rem] border-2 border-slate-100 bg-white p-4 sm:p-6">
                        <div class="flex items-center justify-between mb-4 sm:mb-5">
                            <h4
                                class="text-[10px] font-black uppercase tracking-[0.15em] text-slate-900 flex items-center gap-3">
                                <span
                                    class="w-8 h-8 bg-slate-100 flex items-center justify-center rounded-xl border-2 border-slate-200">
                                    01
                                </span>
                                Profil Pengurus
                            </h4>

                            <span
                                class="hidden sm:inline-flex items-center gap-2 text-[9px] font-black uppercase tracking-widest text-slate-400">
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span> Data Identitas
                            </span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-5 md:gap-6">
                            <div class="md:col-span-2">
                                <label
                                    class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                                    Nama Lengkap & Gelar
                                </label>
                                <input type="text" name="name" value="{{ old('name', $official->name) }}"
                                    class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl py-3 px-4 text-sm font-bold focus:ring-0 focus:border-slate-900 transition-all uppercase"
                                    required placeholder="Contoh: Dr. H. Ahmad Fauzi, M.Pd">
                            </div>

                            <div>
                                <label
                                    class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                                    Jabatan
                                </label>
                                <select name="position"
                                    class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl py-3 px-4 text-sm font-bold focus:ring-0 focus:border-slate-900 transition-all uppercase"
                                    required>
                                    @php
                                        $jabatan = [
                                            'Ketua Umum',
                                            'Sekretaris',
                                            'Bendahara',
                                            'Ketua Bidang Prestasi',
                                            'Anggota',
                                        ];
                                    @endphp
                                    @foreach ($jabatan as $j)
                                        <option value="{{ $j }}"
                                            {{ old('position', $official->position) == $j ? 'selected' : '' }}>
                                            {{ $j }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label
                                    class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                                    Nomor WhatsApp
                                </label>
                                <div class="relative">
                                    <span
                                        class="absolute left-4 top-1/2 -translate-y-1/2 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                        +62
                                    </span>
                                    <input type="text" name="phone_number"
                                        value="{{ old('phone_number', $official->phone_number) }}"
                                        class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl py-3 pl-14 pr-4 text-sm font-bold focus:ring-0 focus:border-slate-900 transition-all"
                                        placeholder="812xxxx">
                                </div>
                                <p class="mt-2 text-[10px] text-slate-500 italic">
                                    * Isi tanpa 0 di depan. Contoh: 812xxxx
                                </p>
                            </div>
                        </div>
                    </section>

                    {{-- Section 2: Wilayah Tugas --}}
                    <section class="rounded-[1.5rem] border-2 border-slate-100 bg-slate-50 p-4 sm:p-6">
                        <div class="flex items-center justify-between mb-4 sm:mb-5">
                            <h4
                                class="text-[10px] font-black uppercase tracking-[0.15em] text-slate-900 flex items-center gap-3">
                                <span
                                    class="w-8 h-8 bg-white flex items-center justify-center rounded-xl border-2 border-slate-200 shadow-sm">
                                    02
                                </span>
                                Cakupan Wilayah Tugas
                            </h4>

                            <span
                                class="hidden sm:inline-flex items-center gap-2 text-[9px] font-black uppercase tracking-widest text-slate-400">
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span> Scope
                            </span>
                        </div>

                        <div class="mb-5 sm:mb-6">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">
                                Tingkat Kepengurusan
                            </label>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                @if (auth()->user()->role === 'pb')
                                    <label class="cursor-pointer">
                                        <input type="radio" name="level" value="provinsi" class="peer sr-only"
                                            {{ old('level', $official->level) === 'provinsi' ? 'checked' : '' }}
                                            onchange="toggleWilayah(this.value)">
                                        <div
                                            class="px-5 py-4 rounded-2xl border-2 border-slate-200 bg-white text-[10px] font-black uppercase tracking-widest text-slate-700
                                                   peer-checked:border-slate-900 peer-checked:bg-slate-50 transition-all">
                                            Provinsi (Pengprov)
                                            <p
                                                class="mt-1 text-[9px] font-black uppercase tracking-widest text-slate-400">
                                                Yurisdiksi provinsi</p>
                                        </div>
                                    </label>
                                @endif

                                <label class="cursor-pointer">
                                    <input type="radio" name="level" value="pengcab" class="peer sr-only"
                                        {{ old('level', $official->level) === 'pengcab' ? 'checked' : '' }}
                                        onchange="toggleWilayah(this.value)">
                                    <div
                                        class="px-5 py-4 rounded-2xl border-2 border-slate-200 bg-white text-[10px] font-black uppercase tracking-widest text-slate-700
                                               peer-checked:border-slate-900 peer-checked:bg-slate-50 transition-all">
                                        Kota/Kab (Pengcab)
                                        <p class="mt-1 text-[9px] font-black uppercase tracking-widest text-slate-400">
                                            Yurisdiksi kota/kab</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-5 md:gap-6">
                            <div>
                                <label
                                    class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                                    Provinsi
                                </label>

                                @if (auth()->user()->role === 'pengprov')
                                    <input type="hidden" name="province_id" value="{{ auth()->user()->province_id }}">
                                    <div
                                        class="w-full bg-white border-2 border-slate-200 rounded-2xl py-3 px-4 text-sm font-black text-slate-500 uppercase cursor-not-allowed">
                                        {{ auth()->user()->province->name }}
                                    </div>
                                @else
                                    <select name="province_id" id="province_id"
                                        class="w-full bg-white border-2 border-slate-200 rounded-2xl py-3 px-4 text-sm font-bold focus:ring-0 focus:border-slate-900 transition-all uppercase"
                                        required>
                                        <option value="">Pilih Provinsi</option>
                                        @foreach ($provinces as $p)
                                            <option value="{{ $p->id }}"
                                                {{ old('province_id', $official->province_id) == $p->id ? 'selected' : '' }}>
                                                {{ $p->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>

                            <div id="city_container"
                                class="{{ old('level', $official->level) === 'pengcab' ? '' : 'hidden' }}">
                                <label
                                    class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                                    Kota/Kabupaten
                                </label>

                                <select name="city_id" id="city_id"
                                    class="w-full bg-white border-2 border-slate-200 rounded-2xl py-3 px-4 text-sm font-bold focus:ring-0 focus:border-slate-900 transition-all uppercase"
                                    {{ old('level', $official->level) === 'pengcab' ? 'required' : '' }}>
                                    <option value="">Pilih Kota</option>
                                    @if ($official->level === 'pengcab')
                                        @foreach ($cities as $city)
                                            <option value="{{ $city->id }}"
                                                {{ old('city_id', $official->city_id) == $city->id ? 'selected' : '' }}>
                                                {{ $city->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>

                                <p class="mt-2 text-[10px] text-slate-500 italic">
                                    * Wajib diisi jika tingkat kepengurusan Kota/Kab (Pengcab).
                                </p>
                            </div>
                        </div>
                    </section>

                    {{-- Section 3: Legalitas SK --}}
                    <section class="rounded-[1.5rem] border-2 border-slate-100 bg-white p-4 sm:p-6">
                        <div class="flex items-center justify-between mb-4 sm:mb-5">
                            <h4
                                class="text-[10px] font-black uppercase tracking-[0.15em] text-slate-900 flex items-center gap-3">
                                <span
                                    class="w-8 h-8 bg-slate-100 flex items-center justify-center rounded-xl border-2 border-slate-200">
                                    03
                                </span>
                                Legalitas SK
                            </h4>

                            <span
                                class="hidden sm:inline-flex items-center gap-2 text-[9px] font-black uppercase tracking-widest text-slate-400">
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span> Masa Berlaku
                            </span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-5 md:gap-6">
                            <div>
                                <label
                                    class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                                    Nomor SK
                                </label>
                                <input type="text" name="sk_number"
                                    value="{{ old('sk_number', $official->sk_number) }}"
                                    class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl py-3 px-4 text-sm font-bold focus:ring-0 focus:border-slate-900 transition-all uppercase"
                                    placeholder="SK/001/WIL/2026">
                            </div>

                            <div>
                                <label
                                    class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                                    Masa Bakti Hingga <span class="text-red-600">*</span>
                                </label>
                                <input type="date" name="sk_expiry_date"
                                    value="{{ old('sk_expiry_date', $official->sk_expiry_date) }}"
                                    class="w-full bg-white border-2 border-slate-200 rounded-2xl py-3 px-4 text-sm font-bold focus:ring-0 focus:border-slate-900 transition-all"
                                    required>
                            </div>
                        </div>

                        <p class="mt-3 text-[10px] text-slate-500 italic">
                            * Wajib diisi. Gunakan tanggal akhir masa berlaku SK.
                        </p>
                    </section>

                    {{-- Mobile sticky actions --}}
                    <div
                        class="sm:hidden -mx-5 px-5 pt-4 pb-5 sticky bottom-0 bg-slate-50/95 backdrop-blur border-t border-slate-100">
                        <div class="grid grid-cols-2 gap-3">
                            <a href="{{ route('admin.officials.index') }}"
                                class="inline-flex justify-center items-center bg-white border-2 border-slate-200 text-slate-500 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-50 transition-all">
                                Batal
                            </a>
                            <button type="submit"
                                class="inline-flex justify-center items-center bg-slate-900 text-white py-3 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] hover:bg-red-600 transition-all border-b-4 border-slate-700 hover:border-red-800 active:translate-y-1">
                                Simpan
                            </button>
                        </div>
                    </div>

                    {{-- Desktop actions --}}
                    <div class="hidden sm:flex justify-end gap-3 pt-6 border-t-2 border-slate-50">
                        <a href="{{ route('admin.officials.index') }}"
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

    <script>
        const provinceSelect = document.getElementById('province_id');
        const citySelect = document.getElementById('city_id');
        const cityContainer = document.getElementById('city_container');
        const currentCityId = "{{ $official->city_id }}";

        function toggleWilayah(level) {
            if (level === 'pengcab') {
                cityContainer.classList.remove('hidden');
                citySelect.required = true;
                if (provinceSelect && provinceSelect.value) fetchCities(provinceSelect.value);
            } else {
                cityContainer.classList.add('hidden');
                citySelect.required = false;
                citySelect.value = "";
            }
        }

        function fetchCities(provinceId) {
            if (!provinceId) return;

            citySelect.innerHTML = '<option value="">Memuat data...</option>';

            fetch(`/api/cities/${provinceId}`)
                .then(res => res.json())
                .then(data => {
                    citySelect.innerHTML = '<option value="">Pilih Kota</option>';
                    data.forEach(city => {
                        const selected = (String(city.id) === String(currentCityId)) ? 'selected' : '';
                        citySelect.innerHTML += `<option value="${city.id}" ${selected}>${city.name}</option>`;
                    });
                })
                .catch(() => {
                    citySelect.innerHTML = '<option value="">Gagal memuat data</option>';
                });
        }

        if (provinceSelect) {
            provinceSelect.addEventListener('change', function() {
                const checkedLevel = document.querySelector('input[name="level"]:checked');
                if (checkedLevel && checkedLevel.value === 'pengcab' && this.value) {
                    fetchCities(this.value);
                }
            });
        }
    </script>
</x-app-layout>
