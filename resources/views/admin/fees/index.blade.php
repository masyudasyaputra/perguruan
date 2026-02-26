<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div>
                <h2 class="font-black text-xl sm:text-3xl text-slate-900 leading-tight tracking-tighter uppercase">
                    Konfigurasi <span class="text-red-600">Iuran</span>
                </h2>
                <p class="text-[8px] sm:text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mt-0.5">
                    Biaya per tingkatan sabuk (auto-save)
                </p>
            </div>
        </div>
    </x-slot>

    @php
        if (!function_exists('beltBadge')) {
            function beltBadge($beltName)
            {
                $name = strtolower($beltName);

                if (str_contains($name, 'putih')) {
                    return 'bg-white text-slate-500 border-slate-200';
                }
                if (str_contains($name, 'kuning muda')) {
                    return 'bg-yellow-50 text-yellow-600 border-yellow-200';
                }
                if (str_contains($name, 'kuning')) {
                    return 'bg-yellow-400 text-yellow-900 border-yellow-500';
                }
                if (str_contains($name, 'orange')) {
                    return 'bg-orange-500 text-white border-orange-600';
                }
                if (str_contains($name, 'hijau')) {
                    return 'bg-emerald-600 text-white border-emerald-700';
                }
                if (str_contains($name, 'biru')) {
                    return 'bg-blue-600 text-white border-blue-700';
                }
                if (str_contains($name, 'ungu')) {
                    return 'bg-purple-600 text-white border-purple-700';
                }
                if (str_contains($name, 'cokelat') || str_contains($name, 'coklat')) {
                    return 'bg-amber-800 text-white border-amber-900';
                }
                if (str_contains($name, 'hitam')) {
                    return 'bg-slate-900 text-white border-slate-950';
                }

                return 'bg-slate-50 text-slate-600 border-slate-200';
            }
        }
    @endphp

    <div class="py-6 sm:py-8 bg-slate-50 min-h-screen">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5">

            {{-- Info/Status bar --}}
            <div
                class="bg-white rounded-[1.5rem] border border-slate-200 shadow-sm p-4 flex items-center justify-between gap-4">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="relative flex h-2 w-2 shrink-0">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-30"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[7px] font-black text-slate-400 uppercase tracking-widest leading-none">
                            Wilayah Aktif
                        </p>
                        <p class="text-[10px] font-black text-slate-900 uppercase tracking-tight truncate">
                            {{ Auth::user()->province->name ?? 'Nasional' }}
                        </p>
                    </div>
                </div>

                {{-- saving indicator --}}
                <div class="flex items-center gap-2">
                    <div id="save-status" class="hidden items-center gap-2">
                        <span class="relative flex h-2 w-2">
                            <span
                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-40"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
                        </span>
                        <span class="text-[9px] font-black uppercase tracking-widest text-slate-400">Menyimpan...</span>
                    </div>

                    <div id="save-ok" class="hidden items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span class="text-[9px] font-black uppercase tracking-widest text-slate-400">Tersimpan</span>
                    </div>

                    <span class="text-[9px] font-black text-slate-500 uppercase tracking-widest">
                        {{ $beltLevels->count() }} SABUK
                    </span>
                </div>
            </div>

            {{-- TABLE --}}
            <div
                class="bg-white rounded-[2rem] border-2 border-slate-100 shadow-xl shadow-slate-200/50 overflow-hidden">
                <div class="px-5 sm:px-7 py-4 bg-slate-900 flex items-center justify-between gap-4">
                    <div class="min-w-0">
                        <p class="text-white text-[10px] sm:text-xs font-black uppercase tracking-[0.2em]">
                            Master Tarif Iuran
                        </p>
                        <p class="mt-1 text-[9px] font-black uppercase tracking-widest text-slate-400">
                            Ubah nilai → tersimpan otomatis (tanpa tombol)
                        </p>
                    </div>

                    <span
                        class="hidden sm:inline-flex items-center gap-2 bg-white/5 border border-white/10 text-slate-200 px-3 py-2 rounded-2xl text-[9px] font-black uppercase tracking-widest shrink-0">
                        <span class="w-2 h-2 rounded-full bg-red-600"></span>
                        AUTO SAVE
                    </span>
                </div>
                <div class="h-1 bg-gradient-to-r from-slate-900 via-slate-700 to-red-600/60"></div>

                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="hidden md:table-header-group">
                            <tr class="bg-slate-50 border-b border-slate-100">
                                <th
                                    class="px-8 py-4 text-left text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">
                                    Urutan</th>
                                <th
                                    class="px-8 py-4 text-left text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">
                                    Sabuk</th>
                                <th
                                    class="px-8 py-4 text-left text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">
                                    Kyu/Dan</th>
                                <th
                                    class="px-8 py-4 text-right text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">
                                    Biaya (Rp)</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">
                            @foreach ($beltLevels as $belt)
                                @php
                                    $amount = $fees[$belt->id]->amount ?? 0;
                                    $badgeClass = beltBadge($belt->name);
                                @endphp

                                <tr class="hover:bg-slate-50 transition-colors">
                                    {{-- Mobile compact row --}}
                                    <td class="px-5 sm:px-8 py-4 align-top">
                                        <div class="md:hidden flex items-start justify-between gap-4">
                                            <div class="min-w-0">
                                                <p class="text-[10px] font-black text-slate-900 uppercase">
                                                    #{{ $belt->order }}
                                                </p>
                                                <div class="mt-1 flex items-center gap-2">
                                                    <span
                                                        class="inline-flex px-3 py-1 rounded-2xl text-[9px] font-black uppercase border {{ $badgeClass }}">
                                                        {{ $belt->name }}
                                                    </span>
                                                    <span
                                                        class="text-[9px] font-black text-slate-400 uppercase tracking-widest">
                                                        {{ $belt->kyu_dan }}
                                                    </span>
                                                </div>

                                                <p
                                                    class="mt-2 text-[8px] font-bold text-slate-400 uppercase tracking-widest">
                                                    Biaya (Rp)
                                                </p>

                                                <div class="relative mt-1 max-w-[200px]">
                                                    <span
                                                        class="absolute left-4 top-1/2 -translate-y-1/2 text-[10px] font-black text-slate-300 uppercase">
                                                        Rp
                                                    </span>

                                                    <input type="text"
                                                        class="currency-input autosave w-full bg-white border-2 border-slate-200 rounded-2xl pl-10 pr-4 py-3 text-sm font-black text-slate-900 focus:ring-0 focus:border-slate-900 transition-all tabular-nums"
                                                        value="{{ number_format($amount, 0, ',', '.') }}"
                                                        data-belt-id="{{ $belt->id }}" inputmode="numeric"
                                                        autocomplete="off" placeholder="0">
                                                </div>

                                                <p class="mt-2 text-[10px] text-slate-500 italic">
                                                    * otomatis tersimpan setelah berhenti mengetik
                                                </p>
                                            </div>
                                        </div>

                                        {{-- Desktop cells --}}
                                        <div class="hidden md:block text-sm font-black text-slate-900">
                                            {{ $belt->order }}
                                        </div>
                                    </td>

                                    <td class="hidden md:table-cell px-8 py-4">
                                        <div class="flex items-center gap-3">
                                            <span
                                                class="inline-flex px-3 py-1 rounded-2xl text-[9px] font-black uppercase border {{ $badgeClass }}">
                                                {{ $belt->name }}
                                            </span>
                                            <span
                                                class="text-[9px] font-black text-slate-400 uppercase tracking-widest">
                                                {{ $belt->kyu_dan }}
                                            </span>
                                        </div>
                                    </td>

                                    <td class="hidden md:table-cell px-8 py-4">
                                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                            {{ $belt->kyu_dan }}
                                        </span>
                                    </td>

                                    <td class="hidden md:table-cell px-8 py-4">
                                        <div class="flex justify-end">
                                            <div class="w-56">
                                                <div class="relative">
                                                    <span
                                                        class="absolute left-4 top-1/2 -translate-y-1/2 text-[10px] font-black text-slate-300 uppercase">
                                                        Rp
                                                    </span>

                                                    <input type="text"
                                                        class="currency-input autosave w-full bg-slate-50 border-2 border-slate-100 rounded-2xl pl-10 pr-4 py-3 text-sm font-black text-slate-900 focus:ring-0 focus:border-slate-900 transition-all tabular-nums text-right"
                                                        value="{{ number_format($amount, 0, ',', '.') }}"
                                                        data-belt-id="{{ $belt->id }}" inputmode="numeric"
                                                        autocomplete="off" placeholder="0">
                                                </div>
                                                {{-- <p class="mt-2 text-[10px] text-slate-500 italic text-right">
                                                    auto-save
                                                </p> --}}
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="px-5 sm:px-7 py-4 bg-white border-t border-slate-100">
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">
                        * Tidak ada tombol simpan — sistem menyimpan otomatis saat input berubah.
                    </p>
                </div>
            </div>

            <div class="h-20 md:hidden"></div>
        </div>
    </div>

    <script>
        (function() {
            const statusSaving = document.getElementById('save-status');
            const statusOk = document.getElementById('save-ok');

            const showSaving = () => {
                statusOk?.classList.add('hidden');
                statusOk?.classList.remove('flex');
                statusSaving?.classList.remove('hidden');
                statusSaving?.classList.add('flex');
            };

            const showOk = () => {
                statusSaving?.classList.add('hidden');
                statusSaving?.classList.remove('flex');
                statusOk?.classList.remove('hidden');
                statusOk?.classList.add('flex');
                setTimeout(() => {
                    statusOk?.classList.add('hidden');
                    statusOk?.classList.remove('flex');
                }, 1200);
            };

            const formatID = (val) => {
                const digits = (val || '').toString().replace(/\D/g, '');
                return digits ? new Intl.NumberFormat('id-ID').format(digits) : '';
            };

            const debounce = (fn, delay = 450) => {
                let t;
                return (...args) => {
                    clearTimeout(t);
                    t = setTimeout(() => fn(...args), delay);
                };
            };

            async function saveFee(beltId, amount) {
                showSaving();

                try {
                    const res = await fetch("{{ route('admin.fees.store') }}", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': "{{ csrf_token() }}",
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            amounts: {
                                [beltId]: Number(amount || 0)
                            }
                        })
                    });

                    if (!res.ok) throw new Error('Save failed');
                    showOk();
                } catch (e) {
                    // fallback: biarkan indicator hilang, user bisa lanjut edit
                    statusSaving?.classList.add('hidden');
                    statusSaving?.classList.remove('flex');
                    console.error(e);
                    alert('Gagal menyimpan. Periksa koneksi atau coba lagi.');
                }
            }

            const debouncedSave = debounce(saveFee, 500);

            document.querySelectorAll('.currency-input.autosave').forEach((input) => {
                // initial normalize
                input.value = formatID(input.value);

                input.addEventListener('input', function() {
                    const beltId = this.dataset.beltId;
                    const digits = this.value.replace(/\D/g, '');
                    this.value = formatID(digits);

                    // autosave on pause typing
                    debouncedSave(beltId, digits);
                });

                // optional: save immediately on blur
                input.addEventListener('blur', function() {
                    const beltId = this.dataset.beltId;
                    const digits = this.value.replace(/\D/g, '');
                    this.value = formatID(digits);
                    saveFee(beltId, digits);
                });
            });
        })();
    </script>
</x-app-layout>
