<?php

namespace App\Services\Payment;

use App\Models\Payment;
use Illuminate\Support\Str;

class PaymentService
{
    public function createMembershipPayment(int $userId, int $amount, ?int $beltLevelId = null, array $meta = []): Payment
    {
        return Payment::create([
            'user_id' => $userId,
            'type' => Payment::TYPE_MEMBERSHIP,
            'belt_level_id' => $beltLevelId,
            'invoice_number' => $this->makeInvoice('IUR'),
            'amount' => $amount,
            'status' => Payment::STATUS_PENDING,
            'meta' => $meta,
        ]);
    }

    public function createExamPayment(int $userId, int $amount, ?int $beltLevelId = null, array $meta = []): Payment
    {
        // beltLevelId opsional (kalau ujian ikut sabuk), sisanya simpan di meta: exam_id, etc
        return Payment::create([
            'user_id' => $userId,
            'type' => Payment::TYPE_EXAM,
            'belt_level_id' => $beltLevelId,
            'invoice_number' => $this->makeInvoice('EXM'),
            'amount' => $amount,
            'status' => Payment::STATUS_PENDING,
            'meta' => $meta,
        ]);
    }

    private function makeInvoice(string $prefix): string
    {
        return $prefix . '-' . now()->format('Ymd') . '-' . strtoupper(Str::random(10));
    }
}