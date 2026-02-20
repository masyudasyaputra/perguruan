@php
    // Kita hitung logikanya di sini supaya tidak perlu kirim variabel dari luar
    $expiryDate = \Carbon\Carbon::parse($official->sk_expiry_date)->startOfDay();
    $today = \Carbon\Carbon::now()->startOfDay();

    $isExpired = $expiryDate->isPast();
    $daysRemaining = (int) $today->diffInDays($expiryDate, false);
    $isExpiringSoon = !$isExpired && $daysRemaining <= 30;
@endphp

@if ($isExpired)
    <span class="px-3 py-1 text-[9px] font-black uppercase rounded-full bg-red-100 text-red-700 border border-red-200">
        Demisioner
    </span>
@elseif ($isExpiringSoon)
    <span
        class="px-3 py-1 text-[9px] font-black uppercase rounded-full bg-amber-100 text-amber-700 border border-amber-200 animate-pulse">
        Hampir Habis
    </span>
@else
    <span
        class="px-3 py-1 text-[9px] font-black uppercase rounded-full bg-green-100 text-green-700 border border-green-200">
        Aktif
    </span>
@endif
