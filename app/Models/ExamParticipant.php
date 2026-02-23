<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamParticipant extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'user_id',
        'dojo_id',
        'current_belt_id',
        'target_belt_id',
        'fee_amount',
        'result',
        'payment_status'
    ];

    /**
     * Relasi ke User (Peserta)
     * Ini yang menyebabkan error jika tidak ada atau namanya berbeda
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke Dojo
     */
    public function dojo()
    {
        return $this->belongsTo(Dojo::class, 'dojo_id');
    }

    /**
     * Relasi ke Sabuk Saat Ini
     */
    public function currentBelt()
    {
        return $this->belongsTo(BeltLevel::class, 'current_belt_id');
    }

    /**
     * Relasi ke Sabuk Target
     */
    public function targetBelt()
    {
        return $this->belongsTo(BeltLevel::class, 'target_belt_id');
    }
}