<?php

namespace App\Services\Payment;

use App\Models\Payment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class DokuService
{
    /**
     * Create checkout/payment URL ke DOKU.
     * Return array minimal: ['payment_url' => '...','raw'=>...]
     *
     * NOTE:
     * - Endpoint dan payload DOKU harus disesuaikan dengan produk DOKU yang kamu gunakan.
     * - Untuk saat ini kita buat struktur yang rapi + mudah disesuaikan.
     */
    public function createCheckout(Payment $payment, array $customer = []): array
    {
        $baseUrl = rtrim(config('services.doku.base_url'), '/');
        $endpoint = config('services.doku.checkout_endpoint'); // contoh: '/checkout/v1/payment'
        $url = $baseUrl . $endpoint;

        $requestId = (string) Str::uuid();
        $requestTs = gmdate('Y-m-d\TH:i:s\Z'); // UTC ISO8601

        // Payload dasar (sesuaikan dengan format DOKU kamu)
        $payload = [
            'order' => [
                'invoice_number' => $payment->invoice_number,
                'amount' => (int) $payment->amount,
            ],
            'customer' => [
                'name' => $customer['name'] ?? '',
                'email' => $customer['email'] ?? '',
                'phone' => $customer['phone'] ?? '',
            ],
            'callback' => [
                'return_url' => route('payments.doku.return', ['invoice_number' => $payment->invoice_number]),
                'notify_url' => route('payments.doku.notify'),
            ],
            'metadata' => [
                'payment_id' => $payment->id,
                'type' => $payment->type->value,
                'reference' => (string) ($payment->reference ?? ''),
            ],
        ];

        $json = json_encode($payload, JSON_UNESCAPED_SLASHES);

        // ====== SIGNATURE (placeholder framework) ======
        // Beberapa produk DOKU menggunakan:
        // - Digest: base64(sha256(body))
        // - Signature: HMAC/ RSA dengan string-to-sign tertentu
        // Kamu tinggal sesuaikan method buildSignatureHeaders() sesuai docs DOKU.
        $headers = $this->buildSignatureHeaders($requestId, $requestTs, $json);

        $resp = Http::withHeaders($headers)
            ->timeout(30)
            ->acceptJson()
            ->withBody($json, 'application/json')
            ->post($url);

        $raw = $resp->json();

        // Simpan audit request id/time (optional)
        $payment->update([
            'doku_request_id' => $requestId,
            'doku_request_time' => $requestTs,
            'payload' => array_merge((array) ($payment->payload ?? []), [
                'checkout_request' => $payload,
                'checkout_response' => $raw,
            ]),
            // payment_url bisa kamu simpan kalau response ada
            'payment_url' => data_get($raw, 'payment.url') ?? data_get($raw, 'payment_url'),
        ]);

        return [
            'payment_url' => $payment->payment_url,
            'raw' => $raw,
        ];
    }

    /**
     * Verify signature notify dari DOKU (webhook).
     * Ini kerangka yang benar:
     * - ambil headers penting
     * - hitung ulang signature/digest
     * - compare constant time
     */
    public function verifyNotifySignature(array $headers, string $rawBody): bool
    {
        // Normalisasi header (Laravel kasih array of array)
        $get = function (string $key) use ($headers) {
            $lk = strtolower($key);
            foreach ($headers as $k => $v) {
                if (strtolower($k) === $lk) {
                    return is_array($v) ? ($v[0] ?? null) : $v;
                }
            }
            return null;
        };

        $signature = $get('Signature') ?? $get('X-Signature') ?? $get('Http-Signature');
        $requestId = $get('Request-Id') ?? $get('X-Request-Id');
        $requestTs = $get('Request-Timestamp') ?? $get('X-Request-Timestamp');

        if (!$signature) {
            return false;
        }

        // ====== SESUAIKAN DENGAN DOKU DOCS ======
        // Banyak skema: signature = base64(hmacSHA256(stringToSign, secret))
        $expected = $this->buildNotifySignature($requestId, $requestTs, $rawBody);

        return hash_equals((string) $expected, (string) $signature);
    }

    /**
     * Build headers untuk request create checkout.
     * Sesuaikan stringToSign + algo sesuai DOKU.
     */
    protected function buildSignatureHeaders(string $requestId, string $requestTs, string $bodyJson): array
    {
        $clientId = config('services.doku.client_id');
        $secret = config('services.doku.secret_key');

        // Digest umum (sha256 body)
        $digest = base64_encode(hash('sha256', $bodyJson, true));

        // String-to-sign (placeholder)
        // Contoh format umum: "Client-Id:{id}\nRequest-Id:{rid}\nRequest-Timestamp:{ts}\nDigest:{digest}"
        $stringToSign = "Client-Id:$clientId\nRequest-Id:$requestId\nRequest-Timestamp:$requestTs\nDigest:$digest";

        // Signature HMAC SHA256 (placeholder)
        $sig = base64_encode(hash_hmac('sha256', $stringToSign, $secret, true));

        return [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',

            // header umum
            'Client-Id' => $clientId,
            'Request-Id' => $requestId,
            'Request-Timestamp' => $requestTs,
            'Digest' => $digest,

            // nama header signature bisa beda sesuai docs
            'Signature' => $sig,
        ];
    }

    /**
     * Build expected signature untuk notify.
     * Sesuaikan dengan docs DOKU kamu (kadang stringToSign beda dengan request checkout).
     */
    protected function buildNotifySignature(?string $requestId, ?string $requestTs, string $rawBody): string
    {
        $clientId = config('services.doku.client_id');
        $secret = config('services.doku.secret_key');

        $requestId = $requestId ?: '';
        $requestTs = $requestTs ?: '';

        $digest = base64_encode(hash('sha256', $rawBody, true));
        $stringToSign = "Client-Id:$clientId\nRequest-Id:$requestId\nRequest-Timestamp:$requestTs\nDigest:$digest";

        return base64_encode(hash_hmac('sha256', $stringToSign, $secret, true));
    }
}