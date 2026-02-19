<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Pengurus ') }}
            {{ auth()->user()->role === 'pb' ? 'Wilayah/Pusat' : 'Cabang (Pengcab)' }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-8 shadow-sm rounded-xl border-l-8 border-indigo-600">
                <form action="{{ route('admin.officials.store') }}" method="POST">
                    @csrf

                    {{-- Section 1: Profil & Jabatan --}}
                    <div class="mb-8">
                        <h4 class="text-xs font-black uppercase tracking-widest text-indigo-600 mb-4 flex items-center">
                            <span class="bg-indigo-100 p-1 rounded-md mr-2">01</span>
                            Profil Pengurus
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-bold text-gray-700 uppercase tracking-tight">Nama
                                    Lengkap & Gelar</label>
                                <input type="text" name="name"
                                    class="w-full mt-1 border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    required placeholder="Contoh: Dr. H. Ahmad Fauzi, M.Pd">
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-bold text-gray-700 uppercase tracking-tight">Jabatan</label>
                                <select name="position"
                                    class="w-full mt-1 border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    required>
                                    <option value="">Pilih Jabatan</option>
                                    <option value="Ketua Umum">Ketua Umum</option>
                                    <option value="Sekretaris">Sekretaris</option>
                                    <option value="Bendahara">Bendahara</option>
                                    <option value="Ketua Bidang Prestasi">Ketua Bidang Prestasi</option>
                                    <option value="Anggota">Anggota</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 uppercase tracking-tight">Nomor
                                    WhatsApp</label>
                                <input type="text" name="phone_number"
                                    class="w-full mt-1 border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    placeholder="0812xxxx">
                            </div>
                        </div>
                    </div>

                    {{-- Section 2: Level & Wilayah (LOGIKNYA DISINI) --}}
                    <div class="mb-8 bg-gray-50 p-6 rounded-xl border border-gray-200">
                        <h4 class="text-xs font-black uppercase tracking-widest text-gray-600 mb-4 flex items-center">
                            <span class="bg-gray-200 p-1 rounded-md mr-2">02</span>
                            Cakupan Wilayah Tugas
                        </h4>

                        <div class="mb-6">
                            <label class="block text-sm font-bold text-gray-700 uppercase tracking-tight mb-2">Tingkat
                                Kepengurusan</label>
                            <div class="flex gap-6">
                                {{-- Jika Admin PB, bisa pilih Provinsi --}}
                                @if (auth()->user()->role === 'pb')
                                    <label class="inline-flex items-center cursor-pointer">
                                        <input type="radio" name="level" value="provinsi"
                                            class="text-indigo-600 focus:ring-indigo-500" checked
                                            onchange="toggleWilayah(this.value)">
                                        <span class="ml-2 text-sm font-semibold text-gray-700">Provinsi
                                            (Pengprov)</span>
                                    </label>
                                @endif

                                {{-- Pengprov & PB bisa buat Pengcab --}}
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="radio" name="level" value="pengcab"
                                        class="text-indigo-600 focus:ring-indigo-500"
                                        {{ auth()->user()->role === 'pengprov' ? 'checked' : '' }}
                                        onchange="toggleWilayah(this.value)">
                                    <span class="ml-2 text-sm font-semibold text-gray-700">Kota/Kab (Pengcab)</span>
                                </label>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label
                                    class="text-xs font-bold text-gray-500 uppercase tracking-tighter">Provinsi</label>
                                {{-- Jika Pengprov, kunci pilihan ke provinsinya saja --}}
                                @if (auth()->user()->role === 'pengprov')
                                    <input type="hidden" name="province_id" value="{{ auth()->user()->province_id }}">
                                    <select
                                        class="w-full mt-1 border-gray-300 rounded-lg text-sm bg-gray-100 cursor-not-allowed"
                                        disabled>
                                        <option value="">{{ auth()->user()->province->name }}</option>
                                    </select>
                                @else
                                    {{-- Jika PB, tampilkan semua provinsi --}}
                                    <select name="province_id" id="province_id"
                                        class="w-full mt-1 border-gray-300 rounded-lg text-sm" required>
                                        <option value="">Pilih Provinsi</option>
                                        @foreach ($provinces as $p)
                                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>

                            <div id="city_container" class="{{ auth()->user()->role === 'pengprov' ? '' : 'hidden' }}">
                                <label
                                    class="text-xs font-bold text-gray-500 uppercase tracking-tighter text-emerald-600">Kota/Kabupaten</label>
                                <select name="city_id" id="city_id"
                                    class="w-full mt-1 border-gray-300 rounded-lg text-sm border-emerald-300 focus:ring-emerald-500"
                                    {{ auth()->user()->role === 'pengprov' ? 'required' : '' }}>
                                    <option value="">Pilih Kota</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Section 3: SK --}}
                    <div class="mb-8">
                        <h4 class="text-xs font-black uppercase tracking-widest text-indigo-600 mb-4 flex items-center">
                            <span class="bg-indigo-100 p-1 rounded-md mr-2">03</span>
                            SK Kepengurusan
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 uppercase tracking-tight">Nomor
                                    SK</label>
                                <input type="text" name="sk_number"
                                    class="w-full mt-1 border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    placeholder="SK/001/WIL/2026">
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-bold text-gray-700 uppercase tracking-tight text-red-600">Masa
                                    Bakti Hingga</label>
                                <input type="date" name="sk_expiry_date"
                                    class="w-full mt-1 border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    required>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                        <a href="{{ route('admin.officials.index') }}"
                            class="bg-white border border-gray-300 text-gray-600 px-6 py-2 rounded-lg font-bold text-sm hover:bg-gray-50 transition">Batal</a>
                        <button type="submit"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white px-10 py-2 rounded-lg font-black text-sm shadow-lg shadow-indigo-200 transition-all uppercase tracking-widest">
                            Simpan Pengurus
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
        const userRole = "{{ auth()->user()->role }}";
        const userProvinceId = "{{ auth()->user()->province_id }}";

        // Inisialisasi Jika User adalah Pengprov
        if (userRole === 'pengprov') {
            fetchCities(userProvinceId);
        }

        function toggleWilayah(level) {
            if (level === 'pengcab') {
                cityContainer.classList.remove('hidden');
                citySelect.required = true;
                if (provinceSelect && provinceSelect.value) fetchCities(provinceSelect.value);
                else if (userProvinceId) fetchCities(userProvinceId);
            } else {
                cityContainer.classList.add('hidden');
                citySelect.required = false;
                citySelect.value = "";
            }
        }

        function fetchCities(provinceId) {
            if (!provinceId) return;
            citySelect.innerHTML = '<option value="">Memuat...</option>';
            fetch(`/api/cities/${provinceId}`)
                .then(res => res.json())
                .then(data => {
                    citySelect.innerHTML = '<option value="">Pilih Kota</option>';
                    data.forEach(city => {
                        citySelect.innerHTML += `<option value="${city.id}">${city.name}</option>`;
                    });
                })
                .catch(err => {
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
