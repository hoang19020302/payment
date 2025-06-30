<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PayPalService
{
    protected PayPalClient $provider;

    public function __construct()
    {
        $this->initProvider();
    }

    /**
     * Khởi tạo provider với token mới nhất từ cache
     */
    public function initProvider(): void
    {
        $this->provider = new PayPalClient;
        $this->provider->setApiCredentials(config('paypal'));
        $this->provider->setAccessToken($this->cacheToken());
    }

    /**
     * Lấy access token từ cache hoặc throw exception nếu không tìm thấy
     */
    public function getAccessToken(): string
    {
        return $this->cacheToken()['access_token'] 
            ?? throw new \Exception('Access token not found');
    }

    /**
     * Lấy access token raw (đã cache)
     */
    public function cacheToken(): array
    {
        return Cache::remember('paypal_access_token_raw', now()->addMinutes(110), function () {
            return $this->provider->getAccessToken();
        });
    }
    /**
     * Làm mới provider và token (nếu cần thủ công)
     */
    public function refreshProvider(): void
    {
        Cache::forget('paypal_access_token');
        $this->initProvider();
    }

    /**
     * Tạo order
     */
    public function createOrder(array $data): array
    {
        return $this->provider->createOrder($data);
    }

    /**
     * Capture order
     */
    public function captureOrder(string $orderId): array
    {
        return retry(3, function () use ($orderId) {
            return $this->provider->capturePaymentOrder($orderId);
        }, 2000);
    }

    /**
     * Tạo payout
     */
    public function createPayout(array $data): array
    {
        return $this->provider->createBatchPayout($data);
    }

    /**
     * Xác minh webhook
     */
    public function verifyWebhookSignature(array $headers, array $body): bool
    {
        if (config('paypal.mode') === 'sandbox') {
            return true; // ✅ Sandbox bỏ qua
        }

        $url = 'https://api-m.paypal.com/v1/notifications/verify-webhook-signature';

        $response = Http::withToken($this->getAccessToken())
            ->post($url, [
                'auth_algo'         => $headers['PAYPAL-AUTH-ALGO'] ?? '',
                'cert_url'          => $headers['PAYPAL-CERT-URL'] ?? '',
                'transmission_id'   => $headers['PAYPAL-TRANSMISSION-ID'] ?? '',
                'transmission_sig'  => $headers['PAYPAL-TRANSMISSION-SIG'] ?? '',
                'transmission_time' => $headers['PAYPAL-TRANSMISSION-TIME'] ?? '',
                'webhook_id'        => config('paypal.webhook_id'),
                'webhook_event'     => $body,
            ]);

        return ($response->json('verification_status') ?? '') === 'SUCCESS';
    }
}
