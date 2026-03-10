<?php

namespace App\Http\Controllers;

use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use App\Models\ExamFee;
use App\Models\FeeConfiguration;
use App\Models\Payment;
use App\Services\Payment\DokuService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function __construct(
        protected DokuService $doku
    ) {
    }

    /**
     * ============================
     * IURAN: SINGLE (1 user)
     * ============================
     * Amount DIHITUNG dari tabel fee_configurations
     * Wajib:
     * - belt_level_id
     * Optional:
     * - province_id (kalau mau override; default ambil dari user/dojo)
     * - reference
     */
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
            return back()->withErrors(['payment' => 'Province tidak ditemukan. Pastikan user/dojo sudah punya province_id.']);
        }

        $fee = FeeConfiguration::query()
            ->where('province_id', $provinceId)
            ->where('belt_level_id', (int) $request->belt_level_id)
            ->first();

        if (!$fee) {
            return back()->withErrors(['payment' => 'Tarif iuran belum dikonfigurasi untuk provinsi & tingkat sabuk tersebut.']);
        }

        $amount = (int) $fee->amount;

        $payment = Payment::create([
            'user_id' => $user->id,
            'type' => PaymentType::Iuran,
            'reference' => $request->reference ?: ('IURAN:' . now()->format('Y-m')),
            'belt_level_id' => (int) $request->belt_level_id,
            'invoice_number' => $this->makeInvoiceNumber('IUR'),
            'amount' => $amount,
            'status' => PaymentStatus::Pending,
            'expires_at' => now()->addMinutes((int) config('services.doku.expire_minutes', 60)),
            'payload' => [
                'province_id' => $provinceId,
                'fee_source' => 'fee_configurations',
                'fee_id' => $fee->id,
            ],
        ]);

        $result = $this->doku->createCheckout($payment, $this->customerFromUser($user));

        if (empty($result['payment_url'])) {
            return back()->withErrors(['payment' => 'Gagal membuat payment URL dari DOKU.']);
        }

        $payment->update(['payment_url' => $result['payment_url']]);

        return redirect()->away($result['payment_url']);
    }

    /**
     * ============================
     * IURAN: BULK (kolektif)
     * ============================
     * Amount DIHITUNG dari fee_configurations per member (sum)
     * Body:
     * - members[] (name, whatsapp, parent_name?, belt_level_id)
     * Optional:
     * - province_id (kalau mau override; default ambil dari user/dojo)
     * - reference
     */
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
            return back()->withErrors(['payment' => 'Province tidak ditemukan. Pastikan user/dojo sudah punya province_id.']);
        }

        $members = $request->members;
        $beltIds = collect($members)->pluck('belt_level_id')->map(fn($v) => (int) $v)->unique()->values()->all();

        $fees = FeeConfiguration::query()
            ->where('province_id', $provinceId)
            ->whereIn('belt_level_id', $beltIds)
            ->get()
            ->keyBy('belt_level_id');

        // hitung total + siapkan breakdown
        $total = 0;
        $breakdown = [];

        foreach ($members as $idx => $m) {
            $beltId = (int) $m['belt_level_id'];
            $feeRow = $fees->get($beltId);

            if (!$feeRow) {
                return back()->withErrors([
                    'payment' => "Tarif iuran belum dikonfigurasi untuk belt_level_id={$beltId} (province_id={$provinceId}).",
                ]);
            }

            $itemAmount = (int) $feeRow->amount;
            $total += $itemAmount;

            $breakdown[] = [
                'name' => $m['name'],
                'whatsapp' => $m['whatsapp'],
                'parent_name' => $m['parent_name'] ?? null,
                'belt_level_id' => $beltId,
                'amount' => $itemAmount,
                'fee_id' => $feeRow->id,
            ];
        }

        if ($total < 1000) {
            return back()->withErrors(['payment' => 'Total pembayaran tidak valid.']);
        }

        $reference = $request->reference ?: ('IURAN:BULK:' . now()->format('Y-m') . ':DOJO:' . ($user->dojo_id ?? '-'));

        $payment = Payment::create([
            'user_id' => $user->id,
            'type' => PaymentType::Iuran,
            'reference' => $reference,
            'invoice_number' => $this->makeInvoiceNumber('IUR'),
            'amount' => $total,
            'status' => PaymentStatus::Pending,
            'expires_at' => now()->addMinutes((int) config('services.doku.expire_minutes', 60)),
            'payload' => [
                'province_id' => $provinceId,
                'fee_source' => 'fee_configurations',
                'bulk_members' => $breakdown,
                'bulk_count' => count($members),
                'source' => 'members.confirm',
            ],
        ]);

        $result = $this->doku->createCheckout($payment, $this->customerFromUser($user));

        if (empty($result['payment_url'])) {
            return back()->withErrors(['payment' => 'Gagal membuat payment URL dari DOKU.']);
        }

        $payment->update(['payment_url' => $result['payment_url']]);

        return redirect()->away($result['payment_url']);
    }

    /**
     * ============================
     * UJIAN: SINGLE
     * ============================
     * Amount DIAMBIL dari tabel exam_fees
     * Wajib:
     * - belt_level_id
     * Optional:
     * - exam_id (kalau ada relasi event ujian kamu)
     * - reference
     */
    public function createUjian(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'belt_level_id' => ['required', 'integer'],
            'exam_id' => ['nullable', 'integer'],
            'reference' => ['nullable', 'string', 'max:80'], // contoh: EXAM:12
        ]);

        $fee = ExamFee::query()
            ->where('belt_level_id', (int) $request->belt_level_id)
            ->first();

        if (!$fee) {
            return back()->withErrors(['payment' => 'Biaya ujian belum dikonfigurasi untuk tingkat sabuk tersebut.']);
        }

        $amount = (int) $fee->amount;

        $reference = $request->reference
            ?: ('UJIAN:' . ($request->exam_id ? ('EXAM-' . $request->exam_id) : now()->format('Ymd')));

        $payment = Payment::create([
            'user_id' => $user->id,
            'type' => PaymentType::Ujian,
            'reference' => $reference,
            'belt_level_id' => (int) $request->belt_level_id,
            'invoice_number' => $this->makeInvoiceNumber('EXM'),
            'amount' => $amount,
            'status' => PaymentStatus::Pending,
            'expires_at' => now()->addMinutes((int) config('services.doku.expire_minutes', 60)),
            'payload' => [
                'exam_id' => $request->exam_id,
                'fee_source' => 'exam_fees',
                'fee_id' => $fee->id,
            ],
        ]);

        $result = $this->doku->createCheckout($payment, $this->customerFromUser($user));

        if (empty($result['payment_url'])) {
            return back()->withErrors(['payment' => 'Gagal membuat payment URL dari DOKU.']);
        }

        $payment->update(['payment_url' => $result['payment_url']]);

        return redirect()->away($result['payment_url']);
    }

    /**
     * ============================
     * RETURN URL (redirect user dari DOKU)
     * ============================
     * Bukan sumber kebenaran utama, tetap NOTIFY.
     */
    public function dokuReturn(Request $request)
    {
        $invoice =
            $request->query('invoice_number')
            ?? $request->query('order_id')
            ?? $request->query('invoice')
            ?? $request->query('merchant_invoice')
            ?? null;

        $targetRoute = $this->homeRoute();

        if (!$invoice) {
            return redirect()->route($targetRoute)->with('success', 'Return DOKU diterima.');
        }

        $payment = Payment::where('invoice_number', $invoice)->first();

        if (!$payment) {
            return redirect()->route($targetRoute)->withErrors(['payment' => 'Invoice tidak ditemukan.']);
        }

        $payload = is_array($payment->payload) ? $payment->payload : (json_decode($payment->payload ?? '[]', true) ?: []);
        $payload['return_query'] = $request->query();

        $payment->update(['payload' => $payload]);

        if (method_exists($payment, 'isPaid') && $payment->isPaid()) {
            return redirect()->route($targetRoute)->with('success', 'Pembayaran berhasil.');
        }

        return redirect()->route($targetRoute)->with('success', 'Status pembayaran diproses (menunggu notifikasi).');
    }

    /**
     * ============================
     * NOTIFY WEBHOOK (POST) dari DOKU
     * ============================
     * - Validasi signature
     * - Update status payment + simpan payload
     */
    public function dokuNotify(Request $request)
    {
        $rawBody = $request->getContent();

        $headers = [];
        foreach ($request->headers->all() as $k => $v) {
            $headers[$k] = is_array($v) ? implode(',', $v) : (string) $v;
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
            str_contains($dokuStatus, 'SUCCESS') || str_contains($dokuStatus, 'PAID') => PaymentStatus::Paid,
            str_contains($dokuStatus, 'FAILED') => PaymentStatus::Failed,
            str_contains($dokuStatus, 'EXPIRED') => PaymentStatus::Expired,
            str_contains($dokuStatus, 'CANCEL') => PaymentStatus::Canceled,
            default => PaymentStatus::Pending,
        };

        $transactionId = data_get($data, 'transaction.id')
            ?? data_get($data, 'transaction_id')
            ?? data_get($data, 'payment.transaction_id')
            ?? $payment->doku_transaction_id;

        $payload = is_array($payment->payload) ? $payment->payload : (json_decode($payment->payload ?? '[]', true) ?: []);
        $payload['notify'] = $data;

        $payment->update([
            'status' => $newStatus,
            'paid_at' => $newStatus === PaymentStatus::Paid ? now() : $payment->paid_at,
            'doku_transaction_id' => $transactionId,
            'payload' => $payload,
            'doku_request_id' => $request->header('Request-Id') ?? $request->header('x-request-id'),
            'doku_request_time' => $request->header('Request-Timestamp') ?? $request->header('x-request-timestamp'),
        ]);

        if ($newStatus === PaymentStatus::Paid) {
            $this->onPaymentPaid($payment);
        }

        return response()->json(['message' => 'OK']);
    }

    /**
     * Hook bisnis ketika payment PAID.
     * - IURAN: buat/aktifkan member (bulk) / set valid_until / dsb
     * - UJIAN: tandai peserta ujian paid
     */
    protected function onPaymentPaid(Payment $payment): void
    {
        // Isi sesuai kebutuhan modul kamu.
    }

    protected function makeInvoiceNumber(string $prefix): string
    {
        return $prefix . '-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
    }

    protected function homeRoute(): string
    {
        if (Auth::check()) {
            $r = strtolower(Auth::user()->role ?? 'member');
            return $r === 'member' ? 'dashboard' : 'admin.dashboard';
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

    /**
     * Ambil province_id:
     * - kalau request override diberikan (dan >0) => pakai itu
     * - else coba dari user->province_id
     * - else coba dari user->dojo->province_id (kalau relasi ada)
     */
    protected function resolveProvinceId($user, int $overrideProvinceId = 0): ?int
    {
        if ($overrideProvinceId > 0)
            return $overrideProvinceId;

        if (!empty($user->province_id))
            return (int) $user->province_id;

        // kalau ada relasi dojo
        if (method_exists($user, 'dojo') && $user->relationLoaded('dojo') && $user->dojo) {
            return (int) ($user->dojo->province_id ?? 0) ?: null;
        }

        // coba lazy load
        if (method_exists($user, 'dojo')) {
            $dojo = $user->dojo()->first();
            if ($dojo && !empty($dojo->province_id))
                return (int) $dojo->province_id;
        }

        return null;
    }
}