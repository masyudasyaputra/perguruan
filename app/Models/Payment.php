<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'reference',
        'belt_level_id',
        'invoice_number',
        'amount',
        'status',
        'doku_transaction_id',
        'payment_url',
        'expires_at',
        'paid_at',
        'payload',
        'doku_request_id',
        'doku_request_time',
    ];

    protected $casts = [
        'meta' => 'array',
        'payload' => 'array',
        'callback_payload' => 'array',
        'paid_at' => 'datetime',
        'expires_at' => 'datetime',
        'expired_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function beltLevel(): BelongsTo
    {
        return $this->belongsTo(BeltLevel::class);
    }

    public function isPaid(): bool
    {
        return ($this->status instanceof PaymentStatus)
            ? $this->status === PaymentStatus::Paid
            : $this->status === 'paid';
    }
}