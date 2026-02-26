@props(['href'])

<div class="hidden md:block">
    <a href="{{ $href }}"
        {{ $attributes->merge(['class' => 'group relative inline-flex items-center justify-center bg-slate-900 hover:bg-red-600 text-white px-6 py-3 rounded-xl text-[10px] font-black shadow-lg transition-all duration-300 hover:-translate-y-1 active:scale-95 uppercase tracking-widest border-b-4 border-slate-700 hover:border-red-800']) }}>
        <svg class="w-4 h-4 me-2 stroke-[3]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="square" stroke-linejoin="square" d="M12 4v16m8-8H4"></path>
        </svg>
        {{ $slot }}
    </a>
</div>

{{-- Mobile FAB --}}
<div class="fixed bottom-6 right-6 z-50 md:hidden">
    <a href="{{ $href }}"
        class="flex items-center justify-center w-14 h-14 bg-red-600 text-white rounded-2xl shadow-[0_10px_25px_rgba(220,38,38,0.4)] active:scale-90 transition-transform border-b-4 border-red-800">
        <svg class="w-7 h-7 stroke-[3]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="square" stroke-linejoin="square" d="M12 4v16m8-8H4"></path>
        </svg>
    </a>
</div>
