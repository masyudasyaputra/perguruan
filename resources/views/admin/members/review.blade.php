<x-app-layout>
    @php
        /**
         * =========================================
         * Breakdown biaya per sabuk (grouped)
         * =========================================
         * INPUT dari Controller:
         * - $member_data (array of members) WAJIB mengandung:
         *   - belt_level_id
         *   - belt_name
         *   - unit_fee   <-- hasil hitung server di review()
         * - $admin_fee (int)
         * - $provinceId (int) (opsional)
         *
         * FIX:
         * - Satuan diambil dari unit_fee (bukan beltFees),
         *   supaya pasti tampil walaupun beltFees tidak dikirim ke view.
         */

        $adminFee = (int) ($admin_fee ?? 0);

        $beltGroups = collect($member_data ?? [])
            ->groupBy(fn($m) => (string) ($m['belt_level_id'] ?? '0'))
            ->map(function ($items, $beltId) {
                $first = $items->first();

                $beltName = $first['belt_name'] ?? 'SABUK';
                $unit = (int) ($first['unit_fee'] ?? 0); // ✅ sumber utama satuan
                $qty = $items->count();
                $subtotal = $unit * $qty;

                return [
                    'belt_level_id' => (int) $beltId,
                    'belt_name' => $beltName,
                    'unit' => $unit,
                    'qty' => $qty,
                    'subtotal' => $subtotal,
                    'missing_fee' => $unit <= 0,
                ];
            })
            ->values();

        $subtotalMembers = (int) $beltGroups->sum('subtotal');
        $grandTotal = $subtotalMembers + $adminFee;

        $totalMembers = count($member_data ?? []);
        $missingAnyFee = $beltGroups->contains(fn($g) => $g['missing_fee'] === true);
    @endphp

    <div class="py-10 md:py-12 bg-slate-50 min-h-screen">
        <div class="max-w-3xl mx-auto px-4">

            <div class="text-center mb-8 md:mb-10">
                <h2 class="text-2xl md:text-3xl font-black text-slate-800 italic uppercase">
                    Konfirmasi Pendaftaran
                </h2>
                <p class="text-slate-500 font-bold text-[10px] uppercase tracking-[0.2em] mt-1">
                    Dojo {{ auth()->user()->dojo->name ?? '-' }}
                </p>
            </div>

            <div class="bg-white rounded-[2.5rem] md:rounded-[3rem] shadow-xl overflow-hidden border border-slate-100">

                {{-- Daftar Member --}}
                <div class="p-6 md:p-8 border-b border-slate-50">
                    <div class="flex items-start justify-between gap-4 mb-5">
                        <div class="min-w-0">
                            <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                Daftar Calon Anggota
                            </h4>
                            <p class="mt-1 text-[10px] font-bold text-slate-500">
                                Periksa nama, WA, dan sabuk sebelum melanjutkan.
                            </p>
                        </div>
                        <span
                            class="shrink-0 inline-flex items-center gap-2 px-4 py-2 rounded-2xl border border-slate-200 bg-slate-50 text-[10px] font-black uppercase tracking-widest text-slate-700">
                            Total: {{ number_format($totalMembers) }}
                        </span>
                    </div>

                    <div class="space-y-4 md:space-y-5">
                        @foreach ($member_data as $index => $item)
                            <div class="flex items-center justify-between gap-4">
                                <div class="flex items-center gap-3 md:gap-4 min-w-0">
                                    <div
                                        class="w-10 h-10 bg-slate-900 text-white rounded-2xl flex items-center justify-center font-black text-sm italic shrink-0">
                                        {{ $index + 1 }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-black text-slate-800 uppercase text-sm truncate">
                                            {{ $item['name'] }}
                                        </p>
                                        <p
                                            class="text-[10px] text-emerald-600 font-black uppercase tracking-widest mt-0.5 truncate">
                                            {{ $item['whatsapp'] }}
                                        </p>
                                    </div>
                                </div>

                                <div class="text-right shrink-0 space-y-2">
                                    <span
                                        class="inline-flex items-center justify-center px-3 py-1 rounded-2xl text-[9px] font-black uppercase tracking-widest border bg-slate-100 text-slate-700 border-slate-200 leading-none">
                                        {{ $item['belt_name'] ?? 'SABUK' }}
                                    </span>

                                    {{-- OPTIONAL: tampilkan satuan per member (biar terlihat jelas) --}}
                                    <div
                                        class="text-[9px] font-black uppercase tracking-widest text-slate-500 tabular-nums">
                                        Rp {{ number_format((int) ($item['unit_fee'] ?? 0), 0, ',', '.') }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Perhitungan --}}
                <div class="p-6 md:p-8 bg-slate-50">
                    <div class="flex items-start justify-between gap-4 mb-4">
                        <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                            Rincian Invoice
                        </h4>

                        <span
                            class="hidden md:inline-flex items-center gap-2 px-4 py-2 rounded-2xl bg-white border border-slate-200 text-[10px] font-black uppercase tracking-widest text-slate-700">
                            Subtotal: Rp {{ number_format($subtotalMembers, 0, ',', '.') }}
                        </span>
                    </div>

                    @if ($missingAnyFee)
                        <div class="mb-4 bg-amber-50 border border-amber-200 rounded-2xl p-4">
                            <div class="flex items-start gap-3">
                                <div class="p-2 rounded-xl bg-amber-500 text-white shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="square" stroke-linejoin="square" stroke-width="3"
                                            d="M12 9v4m0 4h.01M10 3h4l7 18H3L10 3z" />
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-[10px] font-black text-amber-800 uppercase tracking-widest">
                                        Biaya Sabuk Belum Lengkap
                                    </p>
                                    <p class="mt-1 text-[10px] font-bold text-amber-800/80 leading-relaxed">
                                        Ada sabuk yang belum punya biaya (hasil hitung server = Rp 0).
                                        Lengkapi biaya di <b>fee_configurations</b> untuk provinsi terkait.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Breakdown per sabuk --}}
                    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
                        <div class="px-4 py-3 bg-slate-900 text-white">
                            <div class="grid grid-cols-12 gap-2 text-[9px] font-black uppercase tracking-[0.2em]">
                                <div class="col-span-5">Sabuk</div>
                                <div class="col-span-3 text-right">Satuan</div>
                                <div class="col-span-2 text-center">Qty</div>
                                <div class="col-span-2 text-right">Subtotal</div>
                            </div>
                        </div>

                        <div class="divide-y divide-slate-100">
                            @foreach ($beltGroups as $g)
                                <div class="px-4 py-3">
                                    <div class="grid grid-cols-12 gap-2 items-center">
                                        <div class="col-span-5 min-w-0">
                                            <p
                                                class="text-[10px] font-black text-slate-800 uppercase tracking-tight truncate">
                                                {{ $g['belt_name'] }}
                                            </p>
                                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">
                                                ID: {{ $g['belt_level_id'] }}
                                            </p>
                                        </div>

                                        <div class="col-span-3 text-right">
                                            <p class="text-[10px] font-black text-slate-800 tabular-nums">
                                                Rp {{ number_format($g['unit'], 0, ',', '.') }}
                                            </p>
                                            @if ($g['missing_fee'])
                                                <p
                                                    class="text-[9px] font-black text-amber-600 uppercase tracking-widest">
                                                    belum diset
                                                </p>
                                            @endif
                                        </div>

                                        <div class="col-span-2 text-center">
                                            <span
                                                class="inline-flex items-center justify-center min-w-[40px] px-3 py-1 rounded-xl bg-slate-100 border border-slate-200 text-[10px] font-black text-slate-800 tabular-nums">
                                                {{ $g['qty'] }}
                                            </span>
                                        </div>

                                        <div class="col-span-2 text-right">
                                            <p class="text-[10px] font-black text-slate-900 tabular-nums">
                                                Rp {{ number_format($g['subtotal'], 0, ',', '.') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Admin fee + Total --}}
                    <div class="mt-4 space-y-3">
                        <div class="flex justify-between text-sm font-bold text-slate-600">
                            <span>Admin Sistem</span>
                            <span class="tabular-nums">Rp {{ number_format($adminFee, 0, ',', '.') }}</span>
                        </div>

                        <hr class="border-slate-200 my-3">

                        <div class="flex justify-between items-end">
                            <span class="text-base md:text-lg font-black text-slate-800 italic uppercase">
                                Total Bayar
                            </span>
                            <span class="text-xl md:text-2xl font-black text-emerald-600 tabular-nums">
                                Rp {{ number_format($grandTotal, 0, ',', '.') }}
                            </span>
                        </div>

                        <p class="text-[10px] text-slate-500 italic mt-2 leading-relaxed">
                            * Setelah klik bayar, sistem akan membuat invoice dan mengarahkan ke halaman pembayaran
                            (DOKU).
                        </p>
                        <p class="text-[10px] text-slate-500 italic leading-relaxed">
                            * Total tetap dihitung ulang di server demi keamanan (anti manipulasi).
                        </p>
                    </div>
                </div>

                {{-- Tombol Final --}}
                <div class="p-6 md:p-8 bg-white">
                    <form action="{{ route('admin.members.store') }}" method="POST">
                        @csrf

                        @if (!empty($provinceId))
                            <input type="hidden" name="province_id" value="{{ $provinceId }}">
                        @endif

                        {{-- Hidden: oper semua data member --}}
                        @foreach ($member_data as $index => $item)
                            <input type="hidden" name="members[{{ $index }}][name]"
                                value="{{ $item['name'] }}">
                            <input type="hidden" name="members[{{ $index }}][whatsapp]"
                                value="{{ $item['whatsapp'] }}">
                            <input type="hidden" name="members[{{ $index }}][belt_level_id]"
                                value="{{ $item['belt_level_id'] }}">
                            <input type="hidden" name="members[{{ $index }}][parent_name]"
                                value="{{ $item['parent_name'] ?? '' }}">

                            @if (!empty($item['email']))
                                <input type="hidden" name="members[{{ $index }}][email]"
                                    value="{{ $item['email'] }}">
                            @endif
                        @endforeach

                        <button type="submit" @if ($missingAnyFee) disabled @endif
                            class="w-full py-5 md:py-6 rounded-2xl font-black uppercase tracking-[0.2em] transition-all flex items-center justify-center gap-3
                            {{ $missingAnyFee ? 'bg-slate-300 text-slate-600 cursor-not-allowed' : 'bg-emerald-500 hover:bg-emerald-600 text-white shadow-lg shadow-emerald-100 border-b-4 border-emerald-700 active:translate-y-1 active:scale-[0.99]' }}">
                            Konfirmasi & Bayar Sekarang
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="square" stroke-linejoin="square" stroke-width="3"
                                    d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </button>

                        @if ($missingAnyFee)
                            <p class="mt-3 text-[10px] font-black text-amber-700 uppercase tracking-widest text-center">
                                Lengkapi biaya sabuk di fee_configurations dulu
                            </p>
                        @endif

                        <a href="{{ url()->previous() }}"
                            class="block text-center mt-5 text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-red-600 transition-all">
                            Kembali Edit Data
                        </a>
                    </form>
                </div>
            </div>

            <div class="mt-7 text-center">
                <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest px-6 md:px-10 leading-relaxed">
                    Dengan menekan tombol bayar, Anda menyetujui bahwa data yang diinput akan didaftarkan ke sistem
                    manajemen dojo secara permanen.
                </p>
            </div>
        </div>
    </div>
</x-app-layout>
