<?php

namespace App\Http\Controllers;

use App\Models\ExamFee;
use App\Models\FeeConfiguration;
use App\Models\Payment;
use App\Models\User;
use App\Services\Payment\DokuService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function __construct(
        protected DokuService $doku
    ) {
    }

    public function createIuran(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'belt_level_id' => ['required', 'integer'],
            'province_id' => ['nullable', 'integer'],
            'reference' => ['nullable', 'string', 'max:80'],
        ]);

        $provinceId = $this->resolveProvinceId($user, (int) $request->input('province_id', 0));
        if (!$provinceId) {
            return back()->withErrors([
                'payment' => 'Province tidak ditemukan. Pastikan user/dojo sudah punya province_id.',
            ]);
        }

        $fee = FeeConfiguration::query()
            ->where('province_id', $provinceId)
            ->where('belt_level_id', (int) $request->belt_level_id)
            ->first();

        if (!$fee) {
            return back()->withErrors([
                'payment' => 'Tarif iuran belum dikonfigurasi untuk provinsi dan tingkat sabuk tersebut.',
            ]);
        }

        $amount = (int) $fee->amount;
        $expiresAt = now()->addMinutes((int) config('services.doku.expire_minutes', 60));

        $paymentData = [
            'user_id' => $user->id,
            'type' => 'membership_fee',
            'reference' => $request->reference ?: ('IURAN:' . now()->format('Y-m')),
            'belt_level_id' => (int) $request->belt_level_id,
            'invoice_number' => $this->makeInvoiceNumber('IUR'),
            'amount' => $amount,
            'status' => 'pending',
            'expires_at' => $expiresAt,
            'expired_at' => $expiresAt,
            'payload' => [
                'province_id' => $provinceId,
                'fee_source' => 'fee_configurations',
                'fee_id' => $fee->id,
                'paid_for_members' => [
                    [
                        'user_id' => $user->id,
                        'name' => $user->name,
                        'whatsapp' => $user->whatsapp ?? null,
                        'belt_level_id' => (int) $request->belt_level_id,
                        'amount' => $amount,
                    ],
                ],
            ],
        ];

        if (Schema::hasColumn('payments', 'callback_payload')) {
            $paymentData['callback_payload'] = $paymentData['payload'];
        }

        $payment = Payment::create($paymentData);

        $result = $this->doku->createCheckout($payment, $this->customerFromUser($user));

        if (empty($result['payment_url'])) {
            return back()->withErrors([
                'payment' => $result['message'] ?? 'Gagal membuat payment URL dari DOKU.',
            ]);
        }

        $payment->update([
            'payment_url' => $result['payment_url'],
        ]);

        return redirect()->away($result['payment_url']);
    }

    public function createIuranBulk(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'members' => ['required', 'array', 'min:1'],
            'members.*.name' => ['required', 'string', 'max:120'],
            'members.*.whatsapp' => ['required', 'string', 'max:30'],
            'members.*.parent_name' => ['nullable', 'string', 'max:120'],
            'members.*.belt_level_id' => ['required', 'integer'],
            'province_id' => ['nullable', 'integer'],
            'reference' => ['nullable', 'string', 'max:80'],
        ]);

        $provinceId = $this->resolveProvinceId($user, (int) $request->input('province_id', 0));
        if (!$provinceId) {
            return back()->withErrors([
                'payment' => 'Province tidak ditemukan. Pastikan user/dojo sudah punya province_id.',
            ]);
        }

        $members = $request->members;
        $beltIds = collect($members)
            ->pluck('belt_level_id')
            ->map(fn ($value) => (int) $value)
            ->unique()
            ->values()
            ->all();

        $fees = FeeConfiguration::query()
            ->where('province_id', $provinceId)
            ->whereIn('belt_level_id', $beltIds)
            ->get()
            ->keyBy('belt_level_id');

        $total = 0;
        $breakdown = [];

        foreach ($members as $member) {
            $beltId = (int) $member['belt_level_id'];
            $feeRow = $fees->get($beltId);

            if (!$feeRow) {
                return back()->withErrors([
                    'payment' => "Tarif iuran belum dikonfigurasi untuk belt_level_id={$beltId} (province_id={$provinceId}).",
                ]);
            }

            $itemAmount = (int) $feeRow->amount;
            $total += $itemAmount;

            $breakdown[] = [
                'name' => $member['name'],
                'whatsapp' => $member['whatsapp'],
                'parent_name' => $member['parent_name'] ?? null,
                'belt_level_id' => $beltId,
                'amount' => $itemAmount,
                'fee_id' => $feeRow->id,
            ];
        }

        if ($total < 1000) {
            return back()->withErrors([
                'payment' => 'Total pembayaran tidak valid.',
            ]);
        }

        $reference = $request->reference ?: ('IURAN:BULK:' . now()->format('Y-m') . ':DOJO:' . ($user->dojo_id ?? '-'));
        $expiresAt = now()->addMinutes((int) config('services.doku.expire_minutes', 60));

        $paymentData = [
            'user_id' => $user->id,
            'type' => 'membership_fee',
            'reference' => $reference,
            'invoice_number' => $this->makeInvoiceNumber('IUR'),
            'amount' => $total,
            'status' => 'pending',
            'expires_at' => $expiresAt,
            'expired_at' => $expiresAt,
            'payload' => [
                'province_id' => $provinceId,
                'fee_source' => 'fee_configurations',
                'paid_for_members' => $breakdown,
                'bulk_count' => count($members),
                'source' => 'members.confirm',
            ],
        ];

        if (Schema::hasColumn('payments', 'callback_payload')) {
            $paymentData['callback_payload'] = $paymentData['payload'];
        }

        $payment = Payment::create($paymentData);

        $result = $this->doku->createCheckout($payment, $this->customerFromUser($user));

        if (empty($result['payment_url'])) {
            return back()->withErrors([
                'payment' => $result['message'] ?? 'Gagal membuat payment URL dari DOKU.',
            ]);
        }

        $payment->update([
            'payment_url' => $result['payment_url'],
        ]);

        return redirect()->away($result['payment_url']);
    }

    public function createUjian(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'belt_level_id' => ['required', 'integer'],
            'exam_id' => ['nullable', 'integer'],
            'reference' => ['nullable', 'string', 'max:80'],
        ]);

        $fee = ExamFee::query()
            ->where('belt_level_id', (int) $request->belt_level_id)
            ->first();

        if (!$fee) {
            return back()->withErrors([
                'payment' => 'Biaya ujian belum dikonfigurasi untuk tingkat sabuk tersebut.',
            ]);
        }

        $amount = (int) $fee->amount;
        $reference = $request->reference
            ?: ('UJIAN:' . ($request->exam_id ? ('EXAM-' . $request->exam_id) : now()->format('Ymd')));

        $expiresAt = now()->addMinutes((int) config('services.doku.expire_minutes', 60));

        $paymentData = [
            'user_id' => $user->id,
            'type' => 'exam_fee',
            'reference' => $reference,
            'belt_level_id' => (int) $request->belt_level_id,
            'invoice_number' => $this->makeInvoiceNumber('EXM'),
            'amount' => $amount,
            'status' => 'pending',
            'expires_at' => $expiresAt,
            'expired_at' => $expiresAt,
            'payload' => [
                'exam_id' => $request->exam_id,
                'fee_source' => 'exam_fees',
                'fee_id' => $fee->id,
            ],
        ];

        if (Schema::hasColumn('payments', 'callback_payload')) {
            $paymentData['callback_payload'] = $paymentData['payload'];
        }

        $payment = Payment::create($paymentData);

        $result = $this->doku->createCheckout($payment, $this->customerFromUser($user));

        if (empty($result['payment_url'])) {
            return back()->withErrors([
                'payment' => $result['message'] ?? 'Gagal membuat payment URL dari DOKU.',
            ]);
        }

        $payment->update([
            'payment_url' => $result['payment_url'],
        ]);

        return redirect()->away($result['payment_url']);
    }

    public function dokuReturn(Request $request)
    {
        $invoice = $request->query('invoice_number')
            ?? $request->query('order_id')
            ?? $request->query('invoice')
            ?? $request->query('merchant_invoice')
            ?? null;

        $targetRoute = $this->homeRoute();

        if (!$invoice) {
            return redirect()->route($targetRoute)
                ->with('success', 'Return DOKU diterima.');
        }

        $payment = Payment::where('invoice_number', $invoice)->first();

        if (!$payment) {
            return redirect()->route($targetRoute)
                ->withErrors(['payment' => 'Invoice tidak ditemukan.']);
        }

        $payload = is_array($payment->payload)
            ? $payment->payload
            : (json_decode($payment->payload ?? '[]', true) ?: []);

        $payload['return_query'] = $request->query();

        $updateData = [
            'payload' => $payload,
        ];

        if (Schema::hasColumn('payments', 'callback_payload')) {
            $updateData['callback_payload'] = $payload;
        }

        $payment->update($updateData);

        $statusText = strtolower((string) (
            $request->query('status')
            ?? $request->query('result')
            ?? $request->query('transaction_status')
            ?? ''
        ));

        if (in_array($statusText, ['success', 'paid', 'capture', 'settlement'], true) && (string) $payment->status !== 'paid') {
            $payment->update([
                'status' => 'paid',
                'paid_at' => $payment->paid_at ?? now(),
            ]);

            $this->onPaymentPaid($payment->fresh());
        }

        if ((string) $payment->fresh()->status === 'paid') {
            return redirect()->route($targetRoute)
                ->with('success', 'Pembayaran berhasil. Data member telah diaktifkan.');
        }

        return redirect()->route($targetRoute)
            ->with('success', 'Status pembayaran diproses. Silakan tunggu beberapa saat.');
    }

    public function dokuNotify(Request $request)
    {
        $rawBody = $request->getContent();

        $headers = [];
        foreach ($request->headers->all() as $key => $value) {
            $headers[$key] = is_array($value) ? implode(',', $value) : (string) $value;
        }

        $valid = $this->doku->verifyNotifySignature($headers, $rawBody);
        if (!$valid) {
            return response()->json(['message' => 'Invalid signature'], 401);
        }

        $data = $request->json()->all();

        $invoice = data_get($data, 'order.invoice_number')
            ?? data_get($data, 'invoice_number')
            ?? data_get($data, 'order_id')
            ?? data_get($data, 'merchant_invoice')
            ?? null;

        if (!$invoice) {
            return response()->json(['message' => 'Invoice not found in payload'], 422);
        }

        $payment = Payment::where('invoice_number', $invoice)->first();
        if (!$payment) {
            return response()->json(['message' => 'Payment not found'], 404);
        }

        $dokuStatus = strtoupper((string) (
            data_get($data, 'transaction.status')
            ?? data_get($data, 'payment.status')
            ?? data_get($data, 'status')
            ?? 'PENDING'
        ));

        $newStatus = match (true) {
            str_contains($dokuStatus, 'SUCCESS') || str_contains($dokuStatus, 'PAID') => 'paid',
            str_contains($dokuStatus, 'FAILED') => 'failed',
            str_contains($dokuStatus, 'EXPIRED') => 'expired',
            str_contains($dokuStatus, 'CANCEL') => 'canceled',
            default => 'pending',
        };

        $transactionId = data_get($data, 'transaction.id')
            ?? data_get($data, 'transaction_id')
            ?? data_get($data, 'payment.transaction_id')
            ?? $payment->doku_transaction_id;

        $payload = is_array($payment->payload)
            ? $payment->payload
            : (json_decode($payment->payload ?? '[]', true) ?: []);

        $payload['notify'] = $data;

        $updateData = [
            'status' => $newStatus,
            'paid_at' => $newStatus === 'paid' ? now() : $payment->paid_at,
            'doku_transaction_id' => $transactionId,
            'payload' => $payload,
            'doku_request_id' => $request->header('Request-Id') ?? $request->header('x-request-id'),
            'doku_request_time' => $request->header('Request-Timestamp') ?? $request->header('x-request-timestamp'),
        ];

        if (Schema::hasColumn('payments', 'callback_payload')) {
            $updateData['callback_payload'] = $payload;
        }

        $payment->update($updateData);

        if ($newStatus === 'paid') {
            $this->onPaymentPaid($payment->fresh());
        }

        return response()->json(['message' => 'OK']);
    }

    protected function onPaymentPaid(Payment $payment): void
    {
        $payload = is_array($payment->payload)
            ? $payment->payload
            : (json_decode($payment->payload ?? '[]', true) ?: []);

        $paidMembers = $payload['paid_for_members'] ?? [];

        if (!is_array($paidMembers) || empty($paidMembers)) {
            if (!empty($payment->user_id)) {
                $user = User::find($payment->user_id);

                if ($user) {
                    $user->is_active = true;
                    $user->role = 'member';
                    $user->roles = ['member'];
                    $user->save();
                }
            }

            return;
        }

        $userIds = collect($paidMembers)
            ->pluck('user_id')
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        if (empty($userIds)) {
            return;
        }

        $users = User::whereIn('id', $userIds)->get();

        foreach ($users as $user) {
            $user->is_active = true;
            $user->role = 'member';
            $user->roles = ['member'];
            $user->save();
        }
    }

    protected function makeInvoiceNumber(string $prefix): string
    {
        return strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $prefix))
            . now()->format('ymdHis')
            . strtoupper(Str::random(4));
    }

    protected function homeRoute(): string
    {
        if (Auth::check()) {
            $role = strtolower((string) (Auth::user()->role ?? 'member'));
            return $role === 'member' ? 'dashboard' : 'admin.dashboard';
        }

        return 'admin.dashboard';
    }

    protected function customerFromUser($user): array
    {
        return [
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->whatsapp ?? null,
        ];
    }

    protected function resolveProvinceId($user, int $overrideProvinceId = 0): ?int
    {
        if ($overrideProvinceId > 0) {
            return $overrideProvinceId;
        }

        if (!empty($user->province_id)) {
            return (int) $user->province_id;
        }

        if (method_exists($user, 'dojo') && $user->relationLoaded('dojo') && $user->dojo) {
            return (int) ($user->dojo->province_id ?? 0) ?: null;
        }

        if (method_exists($user, 'dojo')) {
            $dojo = $user->dojo()->first();
            if ($dojo && !empty($dojo->province_id)) {
                return (int) $dojo->province_id;
            }
        }

        return null;
    }
}