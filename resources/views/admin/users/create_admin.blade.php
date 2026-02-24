<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Buat Akun Administrator') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm rounded-lg border-l-4 border-red-600">
                <div class="mb-6">
                    <h3 class="text-lg font-bold">Informasi Akun Pengurus</h3>
                    <p class="text-sm text-gray-600">Gunakan form ini hanya untuk mendaftarkan pengurus organisasi.</p>
                </div>

                {{-- Menampilkan Error Validation jika ada --}}
                @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-50 border-nb-red-200 text-red-600 rounded-md text-sm">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.users.store') }}" method="POST">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        {{-- Ganti bagian Select Role dengan kode di bawah ini --}}
                        <div class="md:col-span-2" x-data="{ selectedRoles: @js(old('roles', [])) }">
                            <label class="block text-sm font-bold text-gray-700 mb-3 uppercase tracking-wider">
                                Pilih Role Admin (Bisa Pilih Lebih Dari 1)
                            </label>

                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                                @foreach ($availableRoles as $key => $label)
                                    <label
                                        class="relative flex flex-col p-4 rounded-xl border-2 cursor-pointer transition-all duration-200 group"
                                        :class="selectedRoles.includes('{{ $key }}') ? 'border-red-600 bg-red-50' :
                                            'border-gray-100 bg-white hover:border-gray-300'">
                                        {{-- Hidden Checkbox --}}
                                        <input type="checkbox" name="roles[]" value="{{ $key }}" class="hidden"
                                            x-model="selectedRoles"
                                            {{ in_array($key, old('roles', [])) ? 'checked' : '' }}>

                                        <div class="flex justify-between items-start">
                                            <span class="font-bold text-sm transition-colors"
                                                :class="selectedRoles.includes('{{ $key }}') ? 'text-red-700' :
                                                    'text-gray-700'">
                                                {{ $label }}
                                            </span>

                                            {{-- Icon Centang muncul jika dipilih --}}
                                            <template x-if="selectedRoles.includes('{{ $key }}')">
                                                <svg class="w-5 h-5 text-red-600" fill="currentColor"
                                                    viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </template>
                                        </div>

                                        <span class="text-[10px] mt-1 leading-tight text-gray-400"
                                            :class="selectedRoles.includes('{{ $key }}') ? 'text-red-500/70' : ''">
                                            @if ($key === 'penguji')
                                                Memberikan akses penilaian ujian
                                            @else
                                                Akses administrator {{ $label }}
                                            @endif
                                        </span>
                                    </label>
                                @endforeach
                            </div>

                            <p class="mt-3 text-[10px] text-gray-500 italic">
                                * Klik pada kotak untuk memilih. Klik lagi untuk membatalkan.
                                Role pertama yang dipilih akan menjadi Role Utama sistem.
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nama Lengkap Pengurus</label>
                            <input type="text" name="name" value="{{ old('name') }}"
                                class="w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-red-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email Login</label>
                            <input type="email" name="email" value="{{ old('email') }}"
                                class="w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-red-500" required
                                placeholder="admin@karate.com">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Password</label>
                            <input type="password" name="password"
                                class="w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-red-500" required
                                value="password123">
                        </div>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <h4 class="text-sm font-bold mb-4 uppercase text-gray-500">Cakupan Wilayah Wewenang</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="text-xs font-bold text-gray-600 uppercase">Provinsi</label>
                                <select name="province_id" id="province_id" required
                                    class="w-full mt-1 border-gray-300 rounded-md text-sm focus:ring-red-500">
                                    <option value="">Pilih Provinsi</option>
                                    @foreach ($provinces as $p)
                                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="text-xs font-bold text-gray-600 uppercase">Kota/Cabang</label>
                                <select name="city_id" id="city_id"
                                    class="w-full mt-1 border-gray-300 rounded-md text-sm focus:ring-red-500">
                                    <option value="">Pilih Kota</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-xs font-bold text-gray-600 uppercase">Dojo (Opsional)</label>
                                <select name="dojo_id" id="dojo_id"
                                    class="w-full mt-1 border-gray-300 rounded-md text-sm focus:ring-red-500">
                                    <option value="">Pilih Dojo</option>
                                </select>
                            </div>
                        </div>
                        <p class="mt-3 text-[10px] text-gray-500 italic">
                            * Kosongkan Dojo jika mendaftarkan Admin Pengcab.
                            Kosongkan Kota jika mendaftarkan Admin Pengprov.
                        </p>
                    </div>

                    <div class="mt-8 flex justify-end">
                        <button type="submit"
                            class="bg-red-600 hover:bg-red-700 text-white px-8 py-2 rounded-lg font-bold shadow-md transition duration-200">
                            Daftarkan Admin
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

            // Saat Provinsi Berubah
            provinceSelect.addEventListener('change', function() {
                const provinceId = this.value;
                citySelect.innerHTML = '<option value="">Loading...</option>';
                dojoSelect.innerHTML = '<option value="">Pilih Dojo</option>';

                if (provinceId) {
                    fetch(`/api/cities/${provinceId}`)
                        .then(response => response.json())
                        .then(data => {
                            citySelect.innerHTML = '<option value="">Pilih Kota</option>';
                            data.forEach(city => {
                                const option = document.createElement('option');
                                option.value = city.id;
                                option.text = city.name;
                                citySelect.appendChild(option);
                            });
                        });
                } else {
                    citySelect.innerHTML = '<option value="">Pilih Kota</option>';
                }
            });

            // Saat Kota Berubah
            citySelect.addEventListener('change', function() {
                const cityId = this.value;
                dojoSelect.innerHTML = '<option value="">Loading...</option>';

                if (cityId) {
                    fetch(`/api/dojos/${cityId}`)
                        .then(response => response.json())
                        .then(data => {
                            dojoSelect.innerHTML = '<option value="">Pilih Dojo</option>';
                            data.forEach(dojo => {
                                const option = document.createElement('option');
                                option.value = dojo.id;
                                option.text = dojo.name;
                                dojoSelect.appendChild(option);
                            });
                        });
                } else {
                    dojoSelect.innerHTML = '<option value="">Pilih Dojo</option>';
                }
            });
        });
    </script>
</x-app-layout>
