<x-app-layout>
    <x-slot name="header">
        <div class="px-4 sm:px-0">
            <h2 class="font-black text-xl text-slate-900 uppercase tracking-tight">Status Pembayaran</h2>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mt-1">
                Invoice: {{ $invoice ?? '-' }}
            </p>
        </div>
    </x-slot>

    <div class="py-6 bg-slate-50 min-h-screen">
        <div class="max-w-xl mx-auto px-4">
            <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm">
                @if (!$payment)
                    <p class="text-sm font-bold text-slate-600">Payment tidak ditemukan.</p>
                @else
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[10px] font-black uppercase text-slate-400 tracking-widest">Status</p>
                            <p class="text-lg font-black uppercase text-slate-900">{{ $payment->status }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] font-black uppercase text-slate-400 tracking-widest">Nominal</p>
                            <p class="text-lg font-black text-slate-900">Rp
                                {{ number_format($payment->amount, 0, ',', '.') }}</p>
                        </div>
                    </div>

                    <div class="mt-4 pt-4 border-t border-slate-100">
                        <a href="{{ route('admin.dashboard') }}"
                            class="inline-flex items-center justify-center w-full px-6 py-3 bg-slate-900 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest">
                            Kembali ke Dashboard
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
