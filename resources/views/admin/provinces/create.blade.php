<x-app-layout>
    {{-- Tambahkan Asset Tom Select di Slot Head atau Header --}}
    <x-slot name="header">
        <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css"
            rel="stylesheet">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight uppercase tracking-tight">
            {{ __('Tambah Wilayah Provinsi Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            {{-- Form Card --}}
            <div class="bg-white p-8 shadow-sm rounded-xl border-l-8 border-indigo-600">
                <form action="{{ route('admin.provinces.store') }}" method="POST">
                    @csrf

                    {{-- Section 1: Data Wilayah --}}
                    <div class="mb-8">
                        <h4 class="text-xs font-black uppercase tracking-widest text-indigo-600 mb-4 flex items-center">
                            <span class="bg-indigo-100 p-1 rounded-md mr-2">01</span>
                            Informasi Wilayah
                        </h4>
                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 uppercase tracking-tight mb-1">
                                    Nama Provinsi
                                </label>
                                {{-- Menggunakan Select dengan Fitur Pencarian --}}
                                <select id="select-province" name="name" required>
                                    <option value="">Cari nama provinsi...</option>
                                    @foreach ($availableProvinces as $prov)
                                        <option value="{{ $prov }}">{{ $prov }}</option>
                                    @endforeach
                                </select>

                                @error('name')
                                    <p class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Section 2: Administrasi & SK --}}
                    <div class="mb-8 bg-gray-50 p-6 rounded-xl border border-gray-200">
                        <h4 class="text-xs font-black uppercase tracking-widest text-gray-600 mb-4 flex items-center">
                            <span class="bg-gray-200 p-1 rounded-md mr-2">02</span>
                            Legalitas & Kepengurusan
                        </h4>
                        <div class="grid grid-cols-1 gap-6">
                            {{-- Input Nama Ketua --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-700 uppercase tracking-tight">
                                    Nama Ketua Pengprov
                                </label>
                                <input type="text" name="leader_name" value="{{ old('leader_name') }}"
                                    class="w-full mt-1 border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    required placeholder="Nama Lengkap dan Gelar">
                                @error('leader_name')
                                    <p class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 uppercase tracking-tight">
                                        Nomor SK Pengprov
                                    </label>
                                    <input type="text" name="sk_number" value="{{ old('sk_number') }}"
                                        class="w-full mt-1 border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                        placeholder="SK/001/PENGPROV/2026">
                                </div>

                                <div>
                                    <label
                                        class="block text-sm font-bold text-gray-700 uppercase tracking-tight text-red-600">
                                        Masa Bakti Hingga
                                    </label>
                                    <input type="date" name="sk_expiry_date" value="{{ old('sk_expiry_date') }}"
                                        class="w-full mt-1 border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                        required>
                                    <p class="text-[10px] text-gray-500 mt-1 italic leading-tight">
                                        *Sistem akan memberikan notifikasi otomatis 30 hari sebelum tanggal ini
                                        berakhir.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Footer Buttons --}}
                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                        <a href="{{ route('admin.provinces.index') }}"
                            class="bg-white border border-gray-300 text-gray-600 px-6 py-2 rounded-lg font-bold text-sm hover:bg-gray-50 transition">
                            Batal
                        </a>
                        <button type="submit"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white px-10 py-2 rounded-lg font-black text-sm shadow-lg shadow-indigo-200 transition-all uppercase tracking-widest">
                            Simpan Provinsi
                        </button>
                    </div>
                </form>
            </div>

            {{-- Info Box --}}
            <div class="mt-6 flex items-start p-4 bg-blue-50 rounded-xl border border-blue-100">
                <svg class="w-5 h-5 text-blue-500 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-[11px] text-blue-700 leading-relaxed font-medium">
                    Pastikan nama ketua dan masa bakti SK diisi dengan benar untuk keperluan administrasi dan pelaporan
                    organisasi.
                </p>
            </div>
        </div>
    </div>

    {{-- Script Inisialisasi Tom Select --}}
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            if (document.querySelector('#select-province')) {
                new TomSelect("#select-province", {
                    create: false,
                    sortField: {
                        field: "text",
                        direction: "asc"
                    },
                    placeholder: "Ketik nama provinsi..."
                });
            }
        });
    </script>

    <style>
        /* Menyesuaikan Tom Select dengan gaya Tailwind */
        .ts-control {
            border-radius: 0.5rem !important;
            padding: 0.6rem 0.75rem !important;
            border-color: #d1d5db !important;
            font-size: 0.875rem !important;
        }

        .ts-wrapper.focus .ts-control {
            box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.2) !important;
            border-color: #4f46e5 !important;
        }

        .ts-dropdown .active {
            background-color: #4f46e5 !important;
            color: white !important;
        }
    </style>
</x-app-layout>
