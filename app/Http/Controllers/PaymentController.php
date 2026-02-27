<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\Payment\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    // Contoh: buat payment iuran
    public function createMembership(Request $request, PaymentService $svc)
    {
        $request->validate([
            'amount' => ['required', 'integer', 'min:1000'],
            'belt_level_id' => ['nullable', 'integer'],
            'period' => ['nullable', 'string'], // contoh: 2026-02
        ]);

        $payment = $svc->createMembershipPayment(
            userId: Auth::id(),
            amount: (int) $request->amount,
            beltLevelId: $request->belt_level_id ? (int) $request->belt_level_id : null,
            meta: [
                'period' => $request->period,
            ]
        );

        // TODO: panggil DOKU create transaction, simpan payment_url + doku_transaction_id
        // return redirect($payment->payment_url);

        return response()->json([
            'message' => 'payment created',
            'payment' => $payment,
        ]);
    }

    // Contoh: buat payment ujian
    public function createExam(Request $request, PaymentService $svc)
    {
        $request->validate([
            'amount' => ['required', 'integer', 'min:1000'],
            'exam_id' => ['required', 'integer'],
            'belt_level_id' => ['nullable', 'integer'],
        ]);

        $payment = $svc->createExamPayment(
            userId: Auth::id(),
            amount: (int) $request->amount,
            beltLevelId: $request->belt_level_id ? (int) $request->belt_level_id : null,
            meta: [
                'exam_id' => (int) $request->exam_id,
            ]
        );

        // TODO: panggil DOKU create transaction
        return response()->json([
            'message' => 'exam payment created',
            'payment' => $payment,
        ]);
    }

    // Return URL dari DOKU (jangan jadikan sumber kebenaran paid)
    public function return(Request $request)
    {
        return view('payments.return', [
            'query' => $request->all(),
        ]);
    }

    // Webhook notify dari DOKU (ini yang finalisasi)
    public function dokuNotify(Request $request)
    {
        // TODO: verifikasi signature DOKU
        // Ambil invoice_number dari payload DOKU
        $invoice = data_get($request->all(), 'order.invoice_number')
            ?? data_get($request->all(), 'invoice_number');

        if (!$invoice)
            return response()->json(['message' => 'missing invoice'], 400);

        $payment = Payment::where('invoice_number', $invoice)->first();
        if (!$payment)
            return response()->json(['message' => 'payment not found'], 404);

        // Simpan payload webhook
        $payment->update([
            'callback_payload' => $request->all(),
        ]);

        // TODO: mapping status DOKU → paid/failed/expired
        // Jika paid:
        // $payment->update(['status'=>'paid','paid_at'=>now()]);

        return response()->json(['message' => 'ok']);
    }
}