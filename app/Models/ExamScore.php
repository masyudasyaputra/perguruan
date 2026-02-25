<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamScore extends Model
{
    // Gunakan fillable ATAU guarded, jangan keduanya. 
    // Di sini saya rapikan fillable-nya agar lebih aman.
    protected $fillable = [
        'exam_id',
        'member_id',
        'examiner_id',
        'kihon',
        'kata',
        'kumite',
        'result',
        'notes', // Tambahkan ini jika ada kolom catatan di DB
        'new_belt_level_id',
    ];

    /**
     * Relasi ke tabel belt_levels
     * Menghubungkan new_belt_level_id ke id di tabel belt_levels
     */
    public function newBeltLevel()
    {
        // Pastikan nama model 'BeltLevel' sesuai dengan file model Anda
        return $this->belongsTo(BeltLevel::class, 'new_belt_level_id');
    }

    /**
     * Relasi ke tabel users (Member)
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(User::class, 'member_id');
    }

    /**
     * Relasi ke tabel users (Penguji/Examiner)
     */
    public function examiner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'examiner_id');
    }
}