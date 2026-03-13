<?php

namespace App\Services\Payment;

use App\Models\Payment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class DokuService
{
    public function createCheckout(Payment $payment, array $customer = []): array
    {
        $baseUrl = rtrim((string) config('services.doku.base_url', ''), '/');
        $requestTarget = '/' . ltrim((string) config('services.doku.checkout_endpoint', '/checkout/v1/payment'), '/');
        $url = $baseUrl . $requestTarget;

        if ($baseUrl === '' || trim($requestTarget, '/') === '') {
            return [
                'success' => false,
                'payment_url' => null,
                'raw' => null,
                'message' => 'Konfigurasi DOKU belum lengkap.',
            ];
        }

        $requestId = (string) Str::uuid();
        $requestTimestamp = gmdate('Y-m-d\TH:i:s\Z');

        // Aman untuk kanal yang membatasi simbol / panjang invoice
        $invoiceNumber = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', (string) $payment->invoice_number));
        $invoiceNumber = substr($invoiceNumber, 0, 30);

        $payload = [
            'order' => [
                'amount' => (int) $payment->amount,
                'invoice_number' => $invoiceNumber,
                'currency' => 'IDR',
                'callback_url_result' => route('payments.doku.return', [
                    'invoice_number' => $payment->invoice_number,
                ]),
                'language' => 'EN',
                'auto_redirect' => false,
            ],
            'payment' => [
                'payment_due_date' => (int) config('services.doku.expire_minutes', 60),
            ],
        ];

        $bodyJson = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        if ($bodyJson === false) {
            return [
                'success' => false,
                'payment_url' => null,
                'raw' => null,
                'message' => 'Gagal encode payload DOKU.',
            ];
        }

        $headers = $this->buildSignatureHeaders(
            requestId: $requestId,
            requestTimestamp: $requestTimestamp,
            requestTarget: $requestTarget,
            bodyJson: $bodyJson
        );

        try {
            $response = Http::withHeaders($headers)
                ->withBody($bodyJson, 'application/json')
                ->acceptJson()
                ->timeout(30)
                ->post($url);

            $raw = $response->json();

            if (!is_array($raw)) {
                $raw = [
                    'http_status' => $response->status(),
                    'body' => $response->body(),
                    'headers' => $response->headers(),
                ];
            }

            $paymentUrl = data_get($raw, 'response.payment.url')
                ?? data_get($raw, 'payment.url')
                ?? data_get($raw, 'payment_url')
                ?? data_get($raw, 'url');

            $audit = $this->normalizeToArray($payment->payload ?? []);
            $audit['checkout_request'] = $payload;
            $audit['checkout_response'] = $raw;
            $audit['checkout_http_status'] = $response->status();
            $audit['checkout_headers'] = $headers;

            $update = [
                'doku_request_id' => $requestId,
                'doku_request_time' => $requestTimestamp,
                'payload' => $audit,
                'payment_url' => $paymentUrl,
            ];

            if (Schema::hasColumn($payment->getTable(), 'callback_payload')) {
                $update['callback_payload'] = $audit;
            }

            $payment->update($update);

            return [
                'success' => $response->successful() && !empty($paymentUrl),
                'payment_url' => $paymentUrl,
                'raw' => $raw,
                'message' => $response->successful()
                    ? 'Checkout response diterima.'
                    : 'DOKU merespons HTTP ' . $response->status(),
            ];
        } catch (\Throwable $e) {
            $audit = $this->normalizeToArray($payment->payload ?? []);
            $audit['checkout_exception'] = $e->getMessage();

            $update = [
                'doku_request_id' => $requestId,
                'doku_request_time' => $requestTimestamp,
                'payload' => $audit,
            ];

            if (Schema::hasColumn($payment->getTable(), 'callback_payload')) {
                $update['callback_payload'] = $audit;
            }

            $payment->update($update);

            return [
                'success' => false,
                'payment_url' => null,
                'raw' => ['exception' => $e->getMessage()],
                'message' => 'Gagal menghubungi DOKU: ' . $e->getMessage(),
            ];
        }
    }

    protected function buildSignatureHeaders(
        string $requestId,
        string $requestTimestamp,
        string $requestTarget,
        string $bodyJson
    ): array {
        $clientId = (string) config('services.doku.client_id', '');
        $secretKey = (string) config('services.doku.secret_key', '');

        $digest = base64_encode(hash('sha256', $bodyJson, true));

        $stringToSign = implode("\n", [
            "Client-Id:$clientId",
            "Request-Id:$requestId",
            "Request-Timestamp:$requestTimestamp",
            "Request-Target:$requestTarget",
            "Digest:$digest",
        ]);

        $signature = 'HMACSHA256=' . base64_encode(
            hash_hmac('sha256', $stringToSign, $secretKey, true)
        );

        return [
            'Client-Id' => $clientId,
            'Request-Id' => $requestId,
            'Request-Timestamp' => $requestTimestamp,
            'Request-Target' => $requestTarget,
            'Digest' => $digest,
            'Signature' => $signature,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }

    protected function normalizeToArray($value): array
    {
        if (is_array($value)) {
            return $value;
        }

        if (is_string($value) && $value !== '') {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : [];
        }

        if (is_object($value)) {
            return (array) $value;
        }

        return [];
    }
}