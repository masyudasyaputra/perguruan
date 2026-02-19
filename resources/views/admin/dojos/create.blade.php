<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Registrasi Dojo Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            {{-- Form Card --}}
            <div class="bg-white p-8 shadow-sm rounded-xl border-l-8 border-indigo-600">
                <form action="{{ route('admin.dojos.store') }}" method="POST">
                    @csrf

                    {{-- Section 1: Informasi Dasar --}}
                    <div class="mb-8">
                        <h4 class="text-xs font-black uppercase tracking-widest text-indigo-600 mb-4 flex items-center">
                            <span class="bg-indigo-100 p-1 rounded-md mr-2">01</span>
                            Informasi Identitas Dojo
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-bold text-gray-700 uppercase tracking-tight">Nama
                                    Dojo</label>
                                <input type="text" name="name"
                                    class="w-full mt-1 border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    required placeholder="Contoh: Dojo Garuda Bandung">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 uppercase tracking-tight">Nama
                                    Sensei (Kepala)</label>
                                <input type="text" name="sensei_name"
                                    class="w-full mt-1 border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    placeholder="Masukkan nama lengkap">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 uppercase tracking-tight">Nomor
                                    Telepon</label>
                                <input type="text" name="phone_number"
                                    class="w-full mt-1 border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    placeholder="0812xxxxxx">
                            </div>
                        </div>
                    </div>

                    <hr class="mb-8 border-gray-100">

                    {{-- Section 2: Legalitas SK --}}
                    <div class="mb-8 bg-indigo-50/50 p-4 rounded-xl border border-indigo-100">
                        <h4 class="text-xs font-black uppercase tracking-widest text-indigo-600 mb-4 flex items-center">
                            <span class="bg-indigo-200 p-1 rounded-md mr-2">02</span>
                            Legalitas & Masa Berlaku
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 uppercase tracking-tight">Nomor SK
                                    Dojo</label>
                                <input type="text" name="sk_number"
                                    class="w-full mt-1 border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    placeholder="No. SK/Tahun/PB">
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-bold text-gray-700 uppercase tracking-tight text-red-600">Tanggal
                                    Berakhir SK</label>
                                <input type="date" name="sk_expiry_date"
                                    class="w-full mt-1 border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    required>
                                <p class="mt-1 text-[10px] text-gray-500 italic">*Dojo otomatis non-aktif jika melewati
                                    tanggal ini.</p>
                            </div>
                        </div>
                    </div>

                    {{-- Section 3: Lokasi --}}
                    <div class="mb-8">
                        <h4 class="text-xs font-black uppercase tracking-widest text-indigo-600 mb-4 flex items-center">
                            <span class="bg-indigo-100 p-1 rounded-md mr-2">03</span>
                            Wilayah & Lokasi Latihan
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label
                                    class="text-xs font-bold text-gray-500 uppercase tracking-tighter">Provinsi</label>
                                <select id="province_id"
                                    class="w-full mt-1 border-gray-300 rounded-lg text-sm focus:ring-indigo-500">
                                    <option value="">Pilih Provinsi</option>
                                    @foreach ($provinces as $p)
                                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-tighter">Kota /
                                    Cabang</label>
                                <select name="city_id" id="city_id"
                                    class="w-full mt-1 border-gray-300 rounded-lg text-sm focus:ring-indigo-500"
                                    required>
                                    <option value="">Pilih Kota</option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-tighter">Alamat Lengkap
                                Latihan</label>
                            <textarea name="address" rows="3"
                                class="w-full mt-1 border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required
                                placeholder="Contoh: GOR Serbaguna Lantai 2, Jl. Merdeka..."></textarea>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                        <a href="{{ route('admin.dojos.index') }}"
                            class="bg-white border border-gray-300 text-gray-600 px-6 py-2 rounded-lg font-bold text-sm hover:bg-gray-50 transition">
                            Batal
                        </a>
                        <button type="submit"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white px-10 py-2 rounded-lg font-black text-sm shadow-lg shadow-indigo-200 transition-all uppercase tracking-widest">
                            Daftarkan Dojo
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Script AJAX Wilayah --}}
    <script>
        document.getElementById('province_id').addEventListener('change', function() {
            const provinceId = this.value;
            const citySelect = document.getElementById('city_id');
            citySelect.innerHTML = '<option value="">Memuat data...</option>';

            if (provinceId) {
                fetch(`/api/cities/${provinceId}`)
                    .then(res => res.json())
                    .then(data => {
                        citySelect.innerHTML = '<option value="">Pilih Kota</option>';
                        data.forEach(city => {
                            citySelect.innerHTML += `<option value="${city.id}">${city.name}</option>`;
                        });
                    });
            } else {
                citySelect.innerHTML = '<option value="">Pilih Kota</option>';
            }
        });
    </script>
</x-app-layout>
