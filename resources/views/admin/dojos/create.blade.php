<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <div class="min-w-0">
                <h2 class="font-black text-xl sm:text-3xl text-slate-900 leading-tight tracking-tighter uppercase">
                    {{ __('Registrasi') }} <span class="text-slate-900">Dojo</span>
                    <span class="text-red-600">•</span>
                </h2>
                <p class="text-[8px] sm:text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mt-1">
                    Pendaftaran dojo baru • identitas • legalitas • wilayah
                </p>
            </div>

            <div
                class="sm:hidden shrink-0 inline-flex items-center gap-2 bg-slate-900 text-white px-3 py-2 rounded-2xl text-[9px] font-black uppercase tracking-widest">
                <span class="w-2 h-2 rounded-full bg-red-600"></span>
                DOJO
            </div>
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
                                Formulir Registrasi Dojo Baru
                            </h3>
                            <p class="mt-1 text-[9px] font-black uppercase tracking-widest text-slate-400">
                                Identitas • Legalitas • Wilayah & Lokasi
                            </p>
                        </div>
                        <span
                            class="hidden sm:inline-flex items-center gap-2 bg-white/5 border border-white/10 text-slate-200 px-4 py-2 rounded-2xl text-[9px] font-black uppercase tracking-widest">
                            <span class="w-2 h-2 rounded-full bg-red-600"></span>
                            {{ strtoupper(str_replace('_', ' ', auth()->user()->role)) }}
                        </span>
                    </div>
                </div>
                <div class="h-1 bg-gradient-to-r from-slate-900 via-slate-700 to-red-600/60"></div>

                <form action="{{ route('admin.dojos.store') }}" method="POST"
                    class="px-5 sm:px-7 md:px-8 pt-5 sm:pt-6 pb-6 sm:pb-8 space-y-6 sm:space-y-8">
                    @csrf

                    {{-- Section 1: Informasi Dasar --}}
                    <section class="rounded-[1.5rem] border-2 border-slate-100 bg-white p-4 sm:p-6">
                        <div class="flex items-center justify-between mb-4 sm:mb-5">
                            <h4
                                class="text-[10px] font-black uppercase tracking-[0.15em] text-slate-900 flex items-center gap-3">
                                <span
                                    class="w-8 h-8 bg-slate-100 flex items-center justify-center rounded-xl border-2 border-slate-200">
                                    01
                                </span>
                                Identitas Dojo
                            </h4>
                            <span
                                class="hidden sm:inline-flex items-center gap-2 text-[9px] font-black uppercase tracking-widest text-slate-400">
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span> Informasi Dasar
                            </span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-5 md:gap-6">
                            <div class="md:col-span-2">
                                <label
                                    class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                                    Nama Dojo <span class="text-red-600">*</span>
                                </label>
                                <input type="text" name="name" value="{{ old('name') }}"
                                    class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl py-3 px-4 text-sm font-bold focus:ring-0 focus:border-slate-900 transition-all uppercase"
                                    required placeholder="Contoh: Dojo Garuda Bandung">
                            </div>

                            <div>
                                <label
                                    class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                                    Nama Sensei / Kepala Dojo
                                </label>
                                <input type="text" name="sensei_name" value="{{ old('sensei_name') }}"
                                    class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl py-3 px-4 text-sm font-bold focus:ring-0 focus:border-slate-900 transition-all uppercase"
                                    placeholder="Masukkan nama lengkap">
                            </div>

                            <div>
                                <label
                                    class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                                    Nomor Telepon
                                </label>
                                <input type="text" name="phone_number" value="{{ old('phone_number') }}"
                                    class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl py-3 px-4 text-sm font-bold focus:ring-0 focus:border-slate-900 transition-all"
                                    placeholder="0812xxxxxx">
                            </div>
                        </div>
                    </section>

                    {{-- Section 2: Legalitas SK --}}
                    <section class="rounded-[1.5rem] border-2 border-slate-100 bg-slate-50 p-4 sm:p-6">
                        <div class="flex items-center justify-between mb-4 sm:mb-5">
                            <h4
                                class="text-[10px] font-black uppercase tracking-[0.15em] text-slate-900 flex items-center gap-3">
                                <span
                                    class="w-8 h-8 bg-white flex items-center justify-center rounded-xl border-2 border-slate-200 shadow-sm">
                                    02
                                </span>
                                Legalitas & Masa Berlaku
                            </h4>
                            <span
                                class="hidden sm:inline-flex items-center gap-2 text-[9px] font-black uppercase tracking-widest text-slate-400">
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span> Dokumen SK
                            </span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-5 md:gap-6">
                            <div>
                                <label
                                    class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                                    Nomor SK Dojo
                                </label>
                                <input type="text" name="sk_number" value="{{ old('sk_number') }}"
                                    class="w-full bg-white border-2 border-slate-200 rounded-2xl py-3 px-4 text-sm font-bold focus:ring-0 focus:border-slate-900 transition-all uppercase"
                                    placeholder="No. SK/Tahun/PB">
                            </div>

                            <div>
                                <label
                                    class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                                    Tanggal Berakhir SK <span class="text-red-600">*</span>
                                </label>
                                <input type="date" name="sk_expiry_date" value="{{ old('sk_expiry_date') }}"
                                    class="w-full bg-white border-2 border-slate-200 rounded-2xl py-3 px-4 text-sm font-bold focus:ring-0 focus:border-slate-900 transition-all"
                                    required>
                                <p class="mt-2 text-[10px] text-slate-500 italic">
                                    *Dojo otomatis non-aktif jika melewati tanggal ini.
                                </p>
                            </div>
                        </div>
                    </section>

                    {{-- Section 3: Wilayah & Lokasi (Adaptive) --}}
                    <section class="rounded-[1.5rem] border-2 border-slate-100 bg-white p-4 sm:p-6">
                        <div class="flex items-center justify-between mb-4 sm:mb-5">
                            <h4
                                class="text-[10px] font-black uppercase tracking-[0.15em] text-slate-900 flex items-center gap-3">
                                <span
                                    class="w-8 h-8 bg-slate-100 flex items-center justify-center rounded-xl border-2 border-slate-200">
                                    03
                                </span>
                                Wilayah & Lokasi Latihan
                            </h4>
                            <span
                                class="hidden sm:inline-flex items-center gap-2 text-[9px] font-black uppercase tracking-widest text-slate-400">
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span> Lokasi
                            </span>
                        </div>

                        @if (auth()->user()->role === 'pengcab')
                            <input type="hidden" name="province_id" value="{{ auth()->user()->province_id }}">
                            <input type="hidden" name="city_id" value="{{ auth()->user()->city_id }}">

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4 mb-4">
                                <div class="bg-slate-50 p-4 rounded-2xl border-2 border-slate-100">
                                    <span
                                        class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Provinsi</span>
                                    <p class="text-xs sm:text-sm font-black text-slate-700 uppercase mt-1">
                                        {{ auth()->user()->province->name }}
                                    </p>
                                </div>
                                <div class="bg-slate-50 p-4 rounded-2xl border-2 border-slate-100">
                                    <span
                                        class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Kota/Cabang</span>
                                    <p class="text-xs sm:text-sm font-black text-slate-700 uppercase mt-1">
                                        {{ auth()->user()->city->name }}
                                    </p>
                                </div>
                            </div>
                        @else
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-5 md:gap-6 mb-4">
                                <div>
                                    <label
                                        class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                                        Provinsi <span class="text-red-600">*</span>
                                    </label>
                                    <select name="province_id" id="province_id"
                                        class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl py-3 px-4 text-sm font-bold focus:ring-0 focus:border-slate-900 transition-all uppercase"
                                        required>
                                        <option value="">Pilih Provinsi</option>
                                        @foreach ($provinces as $p)
                                            <option value="{{ $p->id }}"
                                                {{ (string) old('province_id') === (string) $p->id ? 'selected' : '' }}>
                                                {{ $p->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label
                                        class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                                        Kota / Cabang <span class="text-red-600">*</span>
                                    </label>
                                    <select name="city_id" id="city_id"
                                        class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl py-3 px-4 text-sm font-bold focus:ring-0 focus:border-slate-900 transition-all uppercase"
                                        required>
                                        <option value="">Pilih Kota</option>
                                    </select>
                                </div>
                            </div>
                        @endif

                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                                Alamat Lengkap Latihan <span class="text-red-600">*</span>
                            </label>
                            <textarea name="address" rows="3"
                                class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl py-3 px-4 text-sm font-bold focus:ring-0 focus:border-slate-900 transition-all"
                                required placeholder="Contoh: GOR Serbaguna Lantai 2, Jl. Merdeka...">{{ old('address') }}</textarea>
                        </div>
                    </section>

                    {{-- Mobile sticky actions --}}
                    <div
                        class="sm:hidden -mx-5 px-5 pt-4 pb-5 sticky bottom-0 bg-slate-50/95 backdrop-blur border-t border-slate-100">
                        <div class="grid grid-cols-2 gap-3">
                            <a href="{{ route('admin.dojos.index') }}"
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
                        <a href="{{ route('admin.dojos.index') }}"
                            class="inline-flex justify-center items-center bg-white border-2 border-slate-200 text-slate-500 px-8 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-50 transition-all">
                            Batal
                        </a>
                        <button type="submit"
                            class="inline-flex justify-center items-center bg-slate-900 text-white px-10 py-3 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] hover:bg-red-600 shadow-lg shadow-slate-200 transition-all border-b-4 border-slate-700 hover:border-red-800 active:translate-y-1">
                            Daftarkan Dojo
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    {{-- Script AJAX Wilayah --}}
    <script>
        const provinceSelect = document.getElementById('province_id');
        if (provinceSelect) {
            provinceSelect.addEventListener('change', function() {
                const provinceId = this.value;
                const citySelect = document.getElementById('city_id');
                citySelect.innerHTML = '<option value="">Memuat data...</option>';

                if (provinceId) {
                    fetch(`/api/cities/${provinceId}`)
                        .then(res => res.json())
                        .then(data => {
                            citySelect.innerHTML = '<option value="">Pilih Kota</option>';
                            data.forEach(city => {
                                citySelect.innerHTML +=
                                    `<option value="${city.id}">${city.name}</option>`;
                            });
                        })
                        .catch(() => {
                            citySelect.innerHTML = '<option value="">Gagal memuat data</option>';
                        });
                } else {
                    citySelect.innerHTML = '<option value="">Pilih Kota</option>';
                }
            });
        }
    </script>
</x-app-layout>
