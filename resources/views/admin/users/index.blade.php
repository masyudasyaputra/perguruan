<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Manajemen User ({{ strtoupper(auth()->user()->role) }})
            </h2>
            <a href="{{ route('admin.users.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm">
                + Tambah User Baru
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="GET" class="mb-6">
                    <input type="text" name="search" placeholder="Cari nama atau email..."
                        class="border-gray-300 rounded-md shadow-sm w-full md:w-1/3" value="{{ request('search') }}">
                </form>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th class="px-6 py-3">Nama</th>
                                <th class="px-6 py-3">Role</th>
                                <th class="px-6 py-3">Wilayah/Dojo</th>
                                <th class="px-6 py-3">Status</th>
                                <th class="px-6 py-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr class="border-b">
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-gray-900">{{ $user->name }}</div>
                                        <div class="text-xs">{{ $user->email }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="px-2 py-1 rounded text-xs font-semibold bg-indigo-100 text-indigo-700 uppercase">
                                            {{ $user->role }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-xs">
                                        {{ $user->province->name ?? 'Nasional' }} <br>
                                        <span class="text-gray-400">{{ $user->dojo->name ?? '' }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        {!! $user->is_active
                                            ? '<span class="text-green-600">● Aktif</span>'
                                            : '<span class="text-red-600">● Non-Aktif</span>' !!}
                                    </td>
                                    <td class="px-6 py-4">
                                        <a href="#" class="text-blue-600 hover:underline">Edit</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
