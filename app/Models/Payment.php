<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'belt_level_id',
        'invoice_number',
        'amount',
        'status',
        'doku_transaction_id',
        'payment_url',
        'meta',
        'callback_payload',
        'paid_at',
        'expired_at',
    ];

    protected $casts = [
        'meta' => 'array',
        'callback_payload' => 'array',
        'paid_at' => 'datetime',
        'expired_at' => 'datetime',
    ];

    public const TYPE_MEMBERSHIP = 'membership_fee';
    public const TYPE_EXAM = 'exam_fee';

    public const STATUS_PENDING = 'pending';
    public const STATUS_PAID = 'paid';
    public const STATUS_FAILED = 'failed';
    public const STATUS_EXPIRED = 'expired';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function beltLevel()
    {
        return $this->belongsTo(\App\Models\BeltLevel::class, 'belt_level_id');
    }
}