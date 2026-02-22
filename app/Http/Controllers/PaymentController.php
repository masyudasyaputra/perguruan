<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PaymentController extends Controller
{
    /**
     * Membuat Request ke DOKU Checkout
     */
    public function createDokuCheckout(Payment $payment)
    {
        $user = $payment->user;
        $requestId = (string) Str::uuid();
        $timestamp = Carbon::now()->utc()->format('Y-m-d\TH:i:s\Z');
        
        // Payload sesuai dokumentasi DOKU Jokul
        $body = [
            'order' => [
                'amount' => $payment->amount,
                'invoice_number' => $payment->external_id,
                'callback_url' => route('dashboard'), // Ke mana user balik setelah bayar
                'auto_redirect' => true,
            ],
            'customer' => [
                'name' => $user->name,
                'email' => $user->email,
            ],
        ];

        // 1. Generate Signature (DOKU memerlukan signature HMAC-SHA256)
        $signature = $this->generateSignature($body, $requestId, $timestamp);

        // 2. Kirim Request ke DOKU
        $response = Http::withHeaders([
            'Client-Id' => env('DOKU_CLIENT_ID'),
            'Request-Id' => $requestId,
            'Request-Timestamp' => $timestamp,
            'Signature' => "HMACSHA256=$signature",
        ])->post(env('DOKU_API_URL') . '/checkout/v1/payment', $body);

        if ($response->successful()) {
            $data = $response->json();
            // Redirect user ke halaman pembayaran DOKU
            return redirect($data['response']['payment']['url']);
        }

        return back()->with('error', 'Gagal menghubungkan ke layanan pembayaran.');
    }

    /**
     * Logic Signature DOKU
     */
    private function generateSignature($body, $requestId, $timestamp)
    {
        $clientId = env('DOKU_CLIENT_ID');
        $secretKey = env('DOKU_SECRET_KEY');
        $digest = base64_encode(hash('sha256', json_encode($body), true));
        
        $rawSignature = "Client-Id:$clientId\n" .
                        "Request-Id:$requestId\n" .
                        "Request-Timestamp:$timestamp\n" .
                        "Request-Target:/checkout/v1/payment\n" .
                        "Digest:$digest";

        return base64_encode(hash_hmac('sha256', $rawSignature, $secretKey, true));
    }
}