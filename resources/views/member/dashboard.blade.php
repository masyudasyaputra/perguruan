<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profil Anggota Karate') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <div class="md:col-span-1 bg-white p-6 shadow-sm rounded-lg border-t-4 border-indigo-500 text-center">
                    <div class="mb-4">
                        <div class="h-24 w-24 rounded-full bg-gray-200 mx-auto flex items-center justify-center">
                            <span class="text-3xl text-gray-500 uppercase">{{ substr($user->name, 0, 1) }}</span>
                        </div>
                    </div>
                    <h3 class="text-xl font-bold">{{ $user->name }}</h3>
                    <p class="text-gray-500 text-sm mb-4">{{ $user->email }}</p>
                    <div class="inline-block px-4 py-1 rounded-full text-sm font-bold bg-gray-800 text-white">
                        Sabuk {{ $user->beltLevel->name ?? 'Belum Diatur' }}
                    </div>
                </div>

                <div class="md:col-span-2 space-y-6">
                    <div class="bg-white p-6 shadow-sm rounded-lg">
                        <h4 class="font-bold border-b pb-2 mb-4">Informasi Unit Latihan (Dojo)</h4>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <p class="text-gray-500 italic">Dojo:</p>
                                <p class="font-semibold">{{ $user->dojo->name ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 italic">Pengcab:</p>
                                <p class="font-semibold">{{ $user->city->name ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 italic">Pengprov:</p>
                                <p class="font-semibold">{{ $user->province->name ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 italic">Alamat Dojo:</p>
                                <p class="font-semibold text-xs text-gray-600">{{ $user->dojo->address ?? '-' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-6 shadow-sm rounded-lg">
                        <h4 class="font-bold border-b pb-2 mb-4 text-orange-600">Status Iuran & Tagihan</h4>
                        @if ($unpaidBill)
                            <div
                                class="flex justify-between items-center p-4 bg-orange-50 border border-orange-200 rounded-lg">
                                <div>
                                    <p class="text-sm text-orange-800 font-bold">Iuran Belum Dibayar</p>
                                    <p class="text-2xl font-black text-orange-900">Rp
                                        {{ number_format($unpaidBill->amount) }}</p>
                                </div>
                                <a href="#"
                                    class="bg-orange-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-orange-700">
                                    Bayar Sekarang
                                </a>
                            </div>
                        @else
                            <div class="p-4 bg-green-50 border border-green-200 rounded-lg flex items-center gap-3">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                <p class="text-green-800">Iuran bulan ini sudah lunas. Terima kasih!</p>
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
