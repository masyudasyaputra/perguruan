<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.users.index') }}"
                class="p-2.5 bg-white rounded-2xl shadow-sm border border-gray-100 hover:bg-gray-50 transition-all group">
                <svg class="w-5 h-5 text-gray-500 group-hover:text-indigo-600 transition-colors" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                    </path>
                </svg>
            </a>
            <div>
                <h2 class="font-black text-2xl text-gray-800 tracking-tight leading-tight">
                    {{ __('Edit User') }}
                </h2>
                <p class="text-sm text-gray-500 font-medium">Perbarui profil dan kendali akses pengguna</p>
            </div>
        </div>
    </x-slot>

    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
    <style>
        .ts-control {
            border-radius: 1rem !important;
            padding: 1rem !important;
            border: 1px solid #f3f4f6 !important;
            background-color: #f9fafb !important;
            font-weight: 700 !important;
        }

        .ts-wrapper.focus .ts-control {
            border-color: #6366f1 !important;
            box-shadow: 0 0 0 2px #6366f1 !important;
        }
    </style>

    <div class="py-8 md:py-12 bg-gray-50/50">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="space-y-8">
                @csrf
                @method('PUT')

                <div
                    class="bg-white p-6 md:p-10 rounded-[2.5rem] shadow-sm border border-gray-100/80 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-50/50 rounded-full -mr-16 -mt-16"></div>

                    <div class="relative grid grid-cols-1 gap-8">
                        {{-- Nama Lengkap --}}
                        <div>
                            <label
                                class="block text-[11px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ps-1">Nama
                                Lengkap</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                                class="w-full bg-gray-50/50 border-gray-100 rounded-2xl focus:ring-2 focus:ring-indigo-500 focus:bg-white p-4 text-gray-900 font-bold shadow-sm transition-all placeholder-gray-300">
                        </div>

                        {{-- Email --}}
                        <div>
                            <label
                                class="block text-[11px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ps-1">Alamat
                                Email</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                                class="w-full bg-gray-50/50 border-gray-100 rounded-2xl focus:ring-2 focus:ring-indigo-500 focus:bg-white p-4 text-gray-900 font-bold shadow-sm transition-all">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            {{-- Role --}}
                            <div>
                                <label
                                    class="block text-[11px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ps-1">Role
                                    / Hak Akses</label>
                                <div class="relative">
                                    <select name="role" id="role-select"
                                        class="w-full bg-gray-50/50 border-gray-100 rounded-2xl focus:ring-2 focus:ring-indigo-500 p-4 text-gray-900 font-bold shadow-sm transition-all appearance-none">
                                        <option value="pb" {{ $user->role == 'pb' ? 'selected' : '' }}>Pengurus
                                            Besar (PB)</option>
                                        <option value="pengprov" {{ $user->role == 'pengprov' ? 'selected' : '' }}>
                                            Pengurus Provinsi</option>
                                        <option value="pengcab" {{ $user->role == 'pengcab' ? 'selected' : '' }}>
                                            Pengurus Cabang</option>
                                        <option value="admin_dojo" {{ $user->role == 'admin_dojo' ? 'selected' : '' }}>
                                            Admin Dojo</option>
                                        <option value="member" {{ $user->role == 'member' ? 'selected' : '' }}>Member
                                        </option>
                                    </select>
                                </div>
                            </div>

                            {{-- Status Aktif --}}
                            <div>
                                <label
                                    class="block text-[11px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ps-1">Status
                                    Akun</label>
                                <select name="is_active"
                                    class="w-full bg-gray-50/50 border-gray-100 rounded-2xl focus:ring-2 focus:ring-indigo-500 p-4 text-gray-900 font-bold shadow-sm transition-all appearance-none">
                                    <option value="1" {{ $user->is_active ? 'selected' : '' }}>AKTIF</option>
                                    <option value="0" {{ !$user->is_active ? 'selected' : '' }}>NON-AKTIF</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Pengaturan Wilayah --}}
                <div id="wilayah-section"
                    class="bg-white p-6 md:p-10 rounded-[2.5rem] shadow-sm border border-gray-100/80">
                    <h3 class="text-gray-800 font-black text-lg mb-8 flex items-center">
                        <span
                            class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center me-4 shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                </path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </span>
                        Pengaturan Wilayah
                    </h3>

                    <div class="space-y-8">
                        <div id="provinsi-div">
                            <label
                                class="block text-[11px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ps-1">Provinsi</label>
                            <select name="province_id" id="province-select"
                                class="w-full bg-gray-50/50 border-gray-100 rounded-2xl focus:ring-2 focus:ring-indigo-500 p-4 text-gray-900 font-bold shadow-sm transition-all">
                                <option value="">Pilih Provinsi</option>
                                @foreach ($provinces as $province)
                                    <option value="{{ $province->id }}"
                                        {{ $user->province_id == $province->id ? 'selected' : '' }}>
                                        {{ $province->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div id="city-div">
                            <label
                                class="block text-[11px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ps-1">Kota
                                / Cabang</label>
                            <select name="city_id" id="city-select" data-selected="{{ $user->city_id }}"
                                class="w-full bg-gray-50/50 border-gray-100 rounded-2xl focus:ring-2 focus:ring-indigo-500 p-4 text-gray-900 font-bold shadow-sm transition-all">
                                <option value="">Pilih Kota/Kabupaten</option>
                            </select>
                        </div>

                        <div id="dojo-div">
                            <label
                                class="block text-[11px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ps-1">Unit
                                Dojo (Bisa Ketik Cari)</label>
                            <select name="dojo_id" id="dojo-select" data-selected="{{ $user->dojo_id }}"
                                placeholder="Ketik nama dojo...">
                                <option value="">Pilih Dojo</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Password Card --}}
                <div
                    class="bg-indigo-900 p-8 md:p-10 rounded-[2.5rem] shadow-xl shadow-indigo-200/50 text-white relative overflow-hidden group">
                    <h3 class="font-black text-lg mb-2 flex items-center relative">
                        <svg class="w-5 h-5 me-3 text-indigo-300" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                            </path>
                        </svg>
                        Ganti Password
                    </h3>
                    <p class="text-indigo-200 text-xs mb-8 font-medium">Kosongkan jika tidak ingin mengubah password.
                    </p>
                    <input type="password" name="password" placeholder="••••••••"
                        class="relative w-full bg-white/10 border-white/20 rounded-2xl focus:ring-2 focus:ring-white focus:bg-white focus:text-gray-900 p-4 text-white transition-all shadow-inner">
                </div>

                <div class="flex flex-col md:flex-row gap-4 pt-6">
                    <button type="submit"
                        class="flex-[2] bg-indigo-600 text-white font-black py-5 rounded-[1.5rem] shadow-xl hover:bg-indigo-700 transition-all uppercase tracking-widest text-sm">Simpan
                        Perubahan</button>
                    <a href="{{ route('admin.users.index') }}"
                        class="flex-1 bg-white text-gray-500 font-black py-5 rounded-[1.5rem] text-center border border-gray-100 hover:bg-gray-50 transition-all uppercase tracking-widest text-sm">Batal</a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const roleSelect = document.getElementById('role-select');
            const provinceSelect = document.getElementById('province-select');
            const citySelect = document.getElementById('city-select');
            const dojoSelect = document.getElementById('dojo-select');
            const sectionWilayah = document.getElementById('wilayah-section');

            // Inisialisasi Tom Select untuk fitur pencarian pada Dojo
            let dojoTomSelect = new TomSelect(dojoSelect, {
                create: false,
                sortField: {
                    field: "text",
                    order: "asc"
                }
            });

            function toggleFields() {
                const role = roleSelect.value;
                sectionWilayah.classList.remove('hidden');
                document.getElementById('city-div').classList.toggle('hidden', role === 'pengprov' || role ===
                'pb');
                document.getElementById('dojo-div').classList.toggle('hidden', role !== 'admin_dojo' && role !==
                    'member');
                if (role === 'pb') sectionWilayah.classList.add('hidden');
            }

            async function fetchCities(provinceId) {
                if (!provinceId) return;
                const response = await fetch(`/api/cities/${provinceId}`);
                const cities = await response.json();
                citySelect.innerHTML = '<option value="">Pilih Kota/Kabupaten</option>';
                cities.forEach(city => {
                    const selected = city.id == citySelect.dataset.selected ? 'selected' : '';
                    citySelect.innerHTML +=
                        `<option value="${city.id}" ${selected}>${city.name}</option>`;
                });
                if (citySelect.value) fetchDojos(citySelect.value);
            }

            async function fetchDojos(cityId) {
                if (!cityId) return;
                const response = await fetch(`/api/dojos/${cityId}`);
                const dojos = await response.json();

                dojoTomSelect.clearOptions();
                dojos.forEach(dojo => {
                    dojoTomSelect.addOption({
                        value: dojo.id,
                        text: dojo.name
                    });
                });

                if (dojoSelect.dataset.selected) {
                    dojoTomSelect.setValue(dojoSelect.dataset.selected);
                    dojoSelect.dataset.selected = ""; // Clear after init
                }
            }

            roleSelect.addEventListener('change', toggleFields);
            provinceSelect.addEventListener('change', (e) => fetchCities(e.target.value));
            citySelect.addEventListener('change', (e) => fetchDojos(e.target.value));

            // Init
            toggleFields();
            if (provinceSelect.value) fetchCities(provinceSelect.value);
        });
    </script>
</x-app-layout>
