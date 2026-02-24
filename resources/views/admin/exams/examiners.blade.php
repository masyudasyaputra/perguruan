@extends('layouts.admin')

@section('content')
    <div class="max-w-4xl mx-auto">
        <h1 class="text-xl font-bold mb-4">Assign Penguji - {{ $exam->name ?? 'Ujian' }}</h1>

        <form method="POST" action="{{ route('admin.exams.examiners.update', $exam) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="bg-white rounded p-4 border">
                <p class="text-sm text-gray-600 mb-3">
                    Pilih penguji yang bertugas pada ujian ini.
                </p>

                <div class="space-y-2">
                    @foreach ($examiners as $u)
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="examiner_ids[]" value="{{ $u->id }}"
                                @checked(in_array($u->id, $selected)) />
                            <span class="font-medium">{{ $u->name }}</span>
                            <span class="text-xs text-gray-500">
                                @if ($u->dojo_id)
                                    (Penguji Dojo)
                                @else
                                    (Penguji Eksternal)
                                @endif
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="flex gap-2">
                <a href="{{ route('admin.exams.show', $exam) }}" class="px-4 py-2 border rounded">
                    Kembali
                </a>
                <button class="px-4 py-2 bg-black text-white rounded">
                    Simpan
                </button>
            </div>
        </form>
    </div>
@endsection
