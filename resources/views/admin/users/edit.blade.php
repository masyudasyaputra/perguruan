<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Akun Pengurus') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm rounded-lg border-l-4 border-red-600">
                <div class="mb-6 flex justify-between items-start">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Perbarui Profil Pengurus</h3>
                        <p class="text-sm text-gray-600">Ubah informasi akun dan hak akses wilayah untuk pengurus ini.
                        </p>
                    </div>
                    <a href="{{ route('admin.users.index') }}"
                        class="text-sm text-gray-500 hover:text-red-600 transition-colors font-medium">
                        &larr; Kembali ke Daftar
                    </a>
                </div>

                {{-- Menampilkan Error Validation --}}
                @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-600 rounded-md text-sm">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="space-y-6 mb-8">
                        {{-- Bagian Select Role --}}
                        <div x-data="{ selectedRoles: @js(old('roles', is_array($user->roles) ? $user->roles : [$user->role])) }">
                            <label class="block text-sm font-bold text-gray-700 mb-3 uppercase tracking-wider">
                                Hak Akses Administrator (Bisa Pilih Lebih Dari 1)
                            </label>

                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                                @foreach ($availableRoles as $key => $label)
                                    <label
                                        class="relative flex flex-col p-4 rounded-xl border-2 cursor-pointer transition-all duration-200 group"
                                        :class="selectedRoles.includes('{{ $key }}') ?
                                            'border-red-600 bg-red-50 shadow-sm' :
                                            'border-gray-100 bg-white hover:border-gray-300'">

                                        <input type="checkbox" name="roles[]" value="{{ $key }}" class="hidden"
                                            x-model="selectedRoles">

                                        <div class="flex justify-between items-start">
                                            <span class="font-bold text-sm transition-colors"
                                                :class="selectedRoles.includes('{{ $key }}') ? 'text-red-700' :
                                                    'text-gray-700'">
                                                {{ $label }}
                                            </span>

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
                                            Akses as {{ $label }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- Input Data Diri --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}"
                                    class="w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500"
                                    required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Email Login</label>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                    class="w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500"
                                    required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Status Akun</label>
                                <select name="is_active" required
                                    class="w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
                                    <option value="1"
                                        {{ old('is_active', $user->is_active) == '1' ? 'selected' : '' }}>Aktif
                                    </option>
                                    <option value="0"
                                        {{ old('is_active', $user->is_active) == '0' ? 'selected' : '' }}>Non-Aktif
                                    </option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Password (Kosongkan jika tidak
                                    diganti)</label>
                                <input type="password" name="password"
                                    class="w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500"
                                    placeholder="••••••••">
                            </div>
                        </div>
                    </div>

                    {{-- Cakupan Wilayah --}}
                    <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                        <h4 class="text-sm font-bold mb-4 uppercase text-gray-500 tracking-wider">Wilayah Wewenang</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="text-xs font-bold text-gray-600 uppercase">Provinsi</label>
                                <select name="province_id" id="province_id" required
                                    class="w-full mt-1 border-gray-300 rounded-md text-sm focus:ring-red-500">
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
                                <label class="text-xs font-bold text-gray-600 uppercase">Kota/Cabang</label>
                                <select name="city_id" id="city_id"
                                    data-selected="{{ old('city_id', $user->city_id) }}"
                                    class="w-full mt-1 border-gray-300 rounded-md text-sm focus:ring-red-500">
                                    <option value="">Pilih Kota</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-xs font-bold text-gray-600 uppercase">Dojo</label>
                                <select name="dojo_id" id="dojo_id"
                                    data-selected="{{ old('dojo_id', $user->dojo_id) }}"
                                    class="w-full mt-1 border-gray-300 rounded-md text-sm focus:ring-red-500">
                                    <option value="">Pilih Dojo</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end gap-3">
                        <a href="{{ route('admin.users.index') }}"
                            class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 font-bold hover:bg-gray-50">Batal</a>
                        <button type="submit"
                            class="bg-red-600 hover:bg-red-700 text-white px-8 py-2 rounded-lg font-bold shadow-md transition duration-200">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const provinceSelect = document.getElementById('province_id');
            const citySelect = document.getElementById('city_id');
            const dojoSelect = document.getElementById('dojo_id');

            function fetchCities(provinceId, selectedId = null) {
                if (!provinceId) return;
                citySelect.innerHTML = '<option value="">Loading...</option>';
                fetch(`/api/cities/${provinceId}`)
                    .then(r => r.json())
                    .then(data => {
                        citySelect.innerHTML = '<option value="">Pilih Kota</option>';
                        data.forEach(city => {
                            const opt = document.createElement('option');
                            opt.value = city.id;
                            opt.text = city.name;
                            if (selectedId && city.id == selectedId) opt.selected = true;
                            citySelect.appendChild(opt);
                        });
                        if (citySelect.value) fetchDojos(citySelect.value, dojoSelect.dataset.selected);
                    });
            }

            function fetchDojos(cityId, selectedId = null) {
                if (!cityId) return;
                dojoSelect.innerHTML = '<option value="">Loading...</option>';
                fetch(`/api/dojos/${cityId}`)
                    .then(r => r.json())
                    .then(data => {
                        dojoSelect.innerHTML = '<option value="">Pilih Dojo</option>';
                        data.forEach(dojo => {
                            const opt = document.createElement('option');
                            opt.value = dojo.id;
                            opt.text = dojo.name;
                            if (selectedId && dojo.id == selectedId) opt.selected = true;
                            dojoSelect.appendChild(opt);
                        });
                    });
            }

            provinceSelect.addEventListener('change', function() {
                citySelect.innerHTML = '<option value="">Pilih Kota</option>';
                dojoSelect.innerHTML = '<option value="">Pilih Dojo</option>';
                fetchCities(this.value);
            });

            citySelect.addEventListener('change', function() {
                fetchDojos(this.value);
            });

            if (provinceSelect.value) {
                fetchCities(provinceSelect.value, citySelect.dataset.selected);
            }
        });
    </script>
</x-app-layout>
