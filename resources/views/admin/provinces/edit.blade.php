<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight uppercase tracking-tight">
            {{ __('Edit Data Wilayah: Provinsi') }}
        </h2>
    </x-slot>

    {{-- 1. Tambahkan Asset Tom Select --}}
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <style>
        .ts-control {
            border-radius: 0.75rem !important;
            padding: 0.5rem 0.75rem !important;
            border-color: #d1d5db !important;
            font-weight: 700 !important;
            font-size: 0.875rem !important;
        }

        .ts-wrapper.focus .ts-control {
            border-color: #6366f1 !important;
            box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2) !important;
        }
    </style>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-8 shadow-sm rounded-2xl border-l-8 border-indigo-500">
                <form action="{{ route('admin.provinces.update', $province->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- Section 01: Identitas --}}
                    <div class="mb-8">
                        <h4 class="text-xs font-black uppercase tracking-widest text-indigo-600 mb-4 flex items-center">
                            <span class="bg-indigo-100 p-1 rounded-md mr-2">01</span>
                            Identitas Wilayah & Ketua
                        </h4>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Nama Provinsi dengan Searchable Select --}}
                            <div>
                                <label
                                    class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-1">Nama
                                    Provinsi</label>
                                <select id="province-select" name="name" required placeholder="Cari provinsi...">
                                    {{-- Provinsi yang sedang diedit --}}
                                    <option value="{{ $province->name }}" selected>{{ $province->name }}</option>

                                    {{-- List provinsi lainnya --}}
                                    @if (isset($availableProvinces))
                                        @foreach ($availableProvinces as $name)
                                            @if ($name !== $province->name)
                                                <option value="{{ $name }}"
                                                    {{ old('name') == $name ? 'selected' : '' }}>
                                                    {{ $name }}
                                                </option>
                                            @endif
                                        @endforeach
                                    @endif
                                </select>
                                @error('name')
                                    <p class="text-red-500 text-[10px] mt-1 font-bold">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Nama Ketua --}}
                            <div>
                                <label
                                    class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-1">Nama
                                    Ketua Pengprov</label>
                                <input type="text" name="leader_name"
                                    value="{{ old('leader_name', $province->leader_name) }}"
                                    class="w-full border-gray-300 rounded-xl shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm font-bold"
                                    placeholder="Masukkan Nama Lengkap" required>
                                @error('leader_name')
                                    <p class="text-red-500 text-[10px] mt-1 font-bold">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Section 02: Legalitas --}}
                    <div class="mb-8 bg-gray-50 p-6 rounded-2xl border border-gray-100">
                        <h4 class="text-xs font-black uppercase tracking-widest text-gray-600 mb-4 flex items-center">
                            <span class="bg-gray-200 p-1 rounded-md mr-2">02</span>
                            Administrasi & Legalitas SK
                        </h4>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Nomor SK --}}
                            <div>
                                <label
                                    class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-1">Nomor
                                    SK</label>
                                <input type="text" name="sk_number"
                                    value="{{ old('sk_number', $province->sk_number) }}"
                                    class="w-full border-gray-300 rounded-xl shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm font-bold"
                                    placeholder="Contoh: 123/SK/PB/2024">
                            </div>

                            {{-- Tanggal Kadaluarsa --}}
                            <div>
                                <label class="block text-xs font-black text-red-500 uppercase tracking-widest mb-1">Masa
                                    Berlaku SK</label>
                                <input type="date" name="sk_expiry_date"
                                    value="{{ old('sk_expiry_date', $province->sk_expiry_date) }}"
                                    class="w-full border-gray-300 rounded-xl shadow-sm focus:ring-red-500 focus:border-red-500 text-sm font-bold"
                                    required>
                                <p class="text-[9px] text-gray-400 mt-1 italic font-medium">*Sistem akan memberikan
                                    peringatan 30 hari sebelum tanggal ini.</p>
                            </div>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex justify-end gap-3 pt-6 border-t border-gray-100">
                        <a href="{{ route('admin.provinces.index') }}"
                            class="bg-white border border-gray-200 text-gray-500 px-6 py-2.5 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-gray-50 transition-all">
                            Batal
                        </a>
                        <button type="submit"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white px-10 py-2.5 rounded-xl font-black text-xs shadow-lg shadow-indigo-200 transition-all uppercase tracking-widest">
                            Update Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- 2. Inisialisasi Script Tom Select --}}
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            new TomSelect("#province-select", {
                create: false,
                sortField: {
                    field: "text",
                    direction: "asc"
                },
                maxOptions: 50
            });
        });
    </script>
</x-app-layout>
