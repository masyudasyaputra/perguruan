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
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Pilih Role Admin</label>
                            <select name="role" required
                                class="w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
                                @foreach ($availableRoles as $key => $label)
                                    <option value="{{ $key }}" {{ old('role') == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
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
