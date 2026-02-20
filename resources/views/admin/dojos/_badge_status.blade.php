@php
    // Kita hitung ulang di sini agar partial ini mandiri dan bisa dipakai di mana saja
    $expiryDate = \Carbon\Carbon::parse($dojo->sk_expiry_date)->startOfDay();
    $isExpired = $expiryDate->isPast();
    $daysRemaining = (int) \Carbon\Carbon::now()->startOfDay()->diffInDays($expiryDate, false);
    $isExpiringSoon = !$isExpired && $daysRemaining <= 30;
@endphp

@if ($isExpired)
    <span
        class="px-3 py-1 text-[9px] font-black uppercase rounded-full bg-red-100 text-red-700 border border-red-200 shadow-sm shadow-red-50">
        Demisioner
    </span>
@elseif ($isExpiringSoon)
    <span
        class="px-3 py-1 text-[9px] font-black uppercase rounded-full bg-amber-100 text-amber-700 border border-amber-200 animate-pulse shadow-sm shadow-amber-50">
        Hampir Habis
    </span>
@else
    <span
        class="px-3 py-1 text-[9px] font-black uppercase rounded-full bg-green-100 text-green-700 border border-green-200 shadow-sm shadow-green-50">
        Aktif
    </span>
@endif
