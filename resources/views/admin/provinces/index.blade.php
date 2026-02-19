<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-gray-800 leading-tight">
                {{ __('Data Wilayah: Provinsi') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
                <div class="p-6 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="font-black text-gray-700 uppercase text-xs tracking-widest">Daftar Wilayah & Sebaran Data
                    </h3>
                    <span class="text-[10px] bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full font-bold">
                        Total: {{ $provinces->count() }} Provinsi
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50/50">
                            <tr>
                                <th
                                    class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                    ID</th>
                                <th
                                    class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                    Nama Provinsi</th>
                                <th
                                    class="px-6 py-4 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                    Jumlah Dojo</th>
                                <th
                                    class="px-6 py-4 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                    Pengurus Cabang</th>
                                <th
                                    class="px-6 py-4 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @foreach ($provinces as $province)
                                <tr class="hover:bg-indigo-50/50 transition duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400 font-mono">
                                        {{ str_pad($province->id, 2, '0', STR_PAD_LEFT) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-gray-800 uppercase tracking-tight">
                                            {{ $province->name }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-black {{ $province->dojos_count > 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-400' }}">
                                            {{ $province->dojos_count }} Dojo
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        {{-- Menggunakan pengcab_count sesuai logika jumlah Kota yang memiliki pengurus --}}
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-black {{ $province->pengcab_count > 0 ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-400' }}">
                                            {{ $province->pengcab_count }} Pengcab
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                        <a href="{{ route('admin.provinces.show', $province->id) }}"
                                            class="inline-flex items-center bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-xs font-black uppercase tracking-widest transition shadow-sm hover:shadow-indigo-200">
                                            <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                </path>
                                            </svg>
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Info Footer --}}
            <div class="mt-6 bg-amber-50 border-l-4 border-amber-400 p-4 rounded-r-xl">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-amber-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-xs text-amber-800 font-medium">
                            Kolom <strong>Pengurus Cabang</strong> menampilkan jumlah Kota/Kabupaten yang telah memiliki
                            kepengurusan (Pengcab) aktif di provinsi tersebut.
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
