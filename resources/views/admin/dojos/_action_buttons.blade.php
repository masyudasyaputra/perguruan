{{-- Tombol Edit: Modern Glassmorphism --}}
<a href="{{ route('admin.dojos.edit', $dojo->id) }}"
    class="group relative inline-flex items-center justify-center p-2.5 bg-white text-indigo-600 rounded-xl shadow-[0_4px_12px_rgba(79,70,229,0.1)] border border-indigo-50/50 hover:bg-indigo-600 hover:text-white hover:shadow-[0_8px_20px_rgba(79,70,229,0.3)] hover:-translate-y-0.5 transition-all duration-300 active:scale-95"
    title="Edit Dojo">

    {{-- Tooltip --}}
    <span
        class="absolute -top-10 left-1/2 -translate-x-1/2 bg-gray-900 text-white text-[10px] px-2 py-1 rounded shadow-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none uppercase font-black tracking-widest z-10">
        Edit
    </span>

    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
        </path>
    </svg>
</a>

{{-- Tombol Hapus: Clean & Dangerous --}}
<form action="{{ route('admin.dojos.destroy', $dojo->id) }}" method="POST" class="inline"
    onsubmit="return confirm('Hapus dojo {{ $dojo->name }}?')">
    @csrf
    @method('DELETE')
    <button type="submit"
        class="group relative inline-flex items-center justify-center p-2.5 bg-white text-rose-500 rounded-xl shadow-[0_4px_12px_rgba(244,63,94,0.1)] border border-rose-50/50 hover:bg-rose-500 hover:text-white hover:shadow-[0_8px_20px_rgba(244,63,94,0.3)] hover:-translate-y-0.5 transition-all duration-300 active:scale-95">

        {{-- Tooltip --}}
        <span
            class="absolute -top-10 left-1/2 -translate-x-1/2 bg-rose-600 text-white text-[10px] px-2 py-1 rounded shadow-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none uppercase font-black tracking-widest z-10">
            Hapus
        </span>

        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
            </path>
        </svg>
    </button>
</form>
