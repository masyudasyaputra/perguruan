<?php

namespace App\Observers;

use App\Models\User;

class MemberObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        //
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user)
{
    // Jika kolom belt_level_id berubah (artinya naik sabuk)
    if ($user->isDirty('belt_level_id')) {
        
        // 1. Cari harga iuran untuk sabuk baru ini
        $fee = FeeConfiguration::where('province_id', $user->province_id)
                ->where('belt_level_id', $user->belt_level_id)
                ->first() ?? FeeConfiguration::whereNull('province_id')
                ->where('belt_level_id', $user->belt_level_id)
                ->first();

        // 2. Buat record pembayaran PENDING baru otomatis
        if ($fee) {
            $user->payments()->create([
                'belt_level_id' => $user->belt_level_id,
                'amount' => $fee->amount,
                'status' => 'PENDING',
                // tambahkan kolom lain sesuai kebutuhan tabel payments kamu
            ]);
        }
    }
}

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
