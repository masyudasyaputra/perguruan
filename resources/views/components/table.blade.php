@props(['headers' => [], 'title' => '', 'subtitle' => ''])

<div class="flex flex-col gap-4">
    {{-- Toolbar Area --}}
    @if ($title || isset($action))
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4 px-1">
            <div>
                @if ($title)
                    <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest">{{ $title }}</h3>
                @endif
                @if ($subtitle)
                    <p class="text-[10px] text-slate-400 font-bold uppercase mt-1">{{ $subtitle }}</p>
                @endif
            </div>
            <div class="w-full md:w-auto">
                {{ $action ?? '' }}
            </div>
        </div>
    @endif

    {{-- Table Container --}}
    <div class="bg-white rounded-[1.5rem] border-2 border-slate-100 shadow-xl shadow-slate-200/50 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead>
                    <tr class="bg-slate-900 text-white">
                        @foreach ($headers as $header)
                            <th scope="col"
                                class="px-8 py-5 {{ $header['align'] ?? 'text-left' }} text-[10px] font-black uppercase tracking-[0.2em] opacity-80">
                                {{ $header['label'] }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    {{ $slot }}
                </tbody>
            </table>
        </div>
    </div>
</div>
