<?php

namespace App\Libraries;

use App\Models\AppSettingModel;
use App\Models\PaymentModel;
use App\Models\SubscriptionModel;
use Config\PayPal as PayPalConfig;
use Config\Services;

class BillingService
{
    public const PLAN_MONTH = 'month';
    public const PLAN_YEAR = 'year';

    private AppSettingModel $settings;
    private SubscriptionModel $subscriptions;
    private PaymentModel $payments;
    private PayPalConfig $paypal;

    public function __construct()
    {
        $this->settings = new AppSettingModel();
        $this->subscriptions = new SubscriptionModel();
        $this->payments = new PaymentModel();
        $this->paypal = config('PayPal');
    }

    public function isBillingEnabled(): bool
    {
        return $this->settings->getValue('billing_enabled', '0') === '1';
    }

    public function setBillingEnabled(bool $enabled): void
    {
        $this->settings->setValue('billing_enabled', $enabled ? '1' : '0');
    }

    public function getCurrency(): string
    {
        return strtoupper($this->settings->getValue('currency', 'EUR') ?: 'EUR');
    }

    public function getPrice(string $plan): float
    {
        $key = $plan === self::PLAN_MONTH ? 'price_month' : 'price_year';
        $default = $plan === self::PLAN_MONTH ? '9.90' : '99.00';

        return (float) $this->settings->getValue($key, $default);
    }

    public function setPrices(float $month, float $year): void
    {
        $this->settings->setValue('price_month', number_format($month, 2, '.', ''));
        $this->settings->setValue('price_year', number_format($year, 2, '.', ''));
    }

    public function getSettings(): array
    {
        return [
            'billing_enabled' => $this->isBillingEnabled(),
            'price_month' => $this->getPrice(self::PLAN_MONTH),
            'price_year' => $this->getPrice(self::PLAN_YEAR),
            'currency' => $this->getCurrency(),
            'paypal_configured' => $this->isPayPalConfigured(),
            'paypal_mode' => $this->paypal->mode ?: 'sandbox',
        ];
    }

    public function isPayPalConfigured(): bool
    {
        return $this->paypal->clientId !== '' && $this->paypal->clientSecret !== '';
    }

    public function getSubscription(int $userId): ?array
    {
        return $this->subscriptions->getByUserId($userId);
    }

    public function hasAccess(int $userId): bool
    {
        if (!$this->isBillingEnabled()) {
            return true;
        }

        $sub = $this->getSubscription($userId);
        if (!$sub) {
            return false;
        }

        return $sub['status'] === 'active' && strtotime((string) $sub['ends_at']) > time();
    }

    public function grantComplimentaryYearIfBillingDisabled(int $userId): ?array
    {
        if ($this->isBillingEnabled()) {
            return $this->getSubscription($userId);
        }

        return $this->grantComplimentaryYear($userId);
    }

    public function grantComplimentaryYear(int $userId): array
    {
        $existing = $this->getSubscription($userId);
        if ($existing) {
            return $existing;
        }

        $now = date('Y-m-d H:i:s');
        $this->subscriptions->insert([
            'user_id' => $userId,
            'plan' => self::PLAN_YEAR,
            'source' => 'complimentary',
            'status' => 'active',
            'starts_at' => $now,
            'ends_at' => date('Y-m-d H:i:s', strtotime('+1 year')),
        ]);

        return $this->getSubscription($userId);
    }

    public function extendSubscription(int $userId, string $plan, string $source = 'paypal'): array
    {
        $months = $plan === self::PLAN_MONTH ? 1 : 12;
        $existing = $this->getSubscription($userId);
        $now = time();
        $base = $now;

        if ($existing && strtotime((string) $existing['ends_at']) > $now) {
            $base = strtotime((string) $existing['ends_at']);
        }

        $endsAt = date('Y-m-d H:i:s', strtotime("+{$months} months", $base));
        $startsAt = $existing['starts_at'] ?? date('Y-m-d H:i:s', $now);

        $data = [
            'user_id' => $userId,
            'plan' => $plan,
            'source' => $source,
            'status' => 'active',
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
        ];

        if ($existing) {
            $this->subscriptions->update($existing['id'], $data);
        } else {
            $this->subscriptions->insert($data);
        }

        return $this->getSubscription($userId);
    }

    /**
     * @return array{ok: bool, approve_url?: string, order_id?: string, error?: string}
     */
    public function createCheckout(int $userId, string $plan): array
    {
        if (!in_array($plan, [self::PLAN_MONTH, self::PLAN_YEAR], true)) {
            return ['ok' => false, 'error' => 'Ongeldig abonnement.'];
        }

        if (!$this->isPayPalConfigured()) {
            return ['ok' => false, 'error' => 'PayPal is nog niet geconfigureerd.'];
        }

        $amount = number_format($this->getPrice($plan), 2, '.', '');
        $currency = $this->getCurrency();
        $label = $plan === self::PLAN_MONTH ? 'Maandabonnement' : 'Jaarabonnement';

        $payload = [
            'intent' => 'CAPTURE',
            'purchase_units' => [[
                'custom_id' => $userId . ':' . $plan,
                'description' => $label . ' Emigrant',
                'amount' => [
                    'currency_code' => $currency,
                    'value' => $amount,
                ],
            ]],
            'application_context' => [
                'brand_name' => 'Emigrant',
                'landing_page' => 'NO_PREFERENCE',
                'user_action' => 'PAY_NOW',
                'return_url' => site_url('subscription/return'),
                'cancel_url' => site_url('subscription/cancel'),
            ],
        ];

        $response = $this->paypalRequest('POST', '/v2/checkout/orders', $payload);
        if (($response['status'] ?? 0) >= 300 || empty($response['body']['id'])) {
            log_message('error', 'PayPal create order failed: ' . json_encode($response));
            return ['ok' => false, 'error' => 'PayPal-bestelling aanmaken mislukt.'];
        }

        $orderId = $response['body']['id'];
        $this->payments->insert([
            'user_id' => $userId,
            'plan' => $plan,
            'amount' => $amount,
            'currency' => $currency,
            'status' => 'pending',
            'paypal_order_id' => $orderId,
            'payload' => json_encode($response['body']),
        ]);

        $approveUrl = null;
        foreach ($response['body']['links'] ?? [] as $link) {
            if (($link['rel'] ?? '') === 'approve') {
                $approveUrl = $link['href'];
                break;
            }
        }

        if (!$approveUrl) {
            return ['ok' => false, 'error' => 'PayPal gaf geen betaallink terug.'];
        }

        return ['ok' => true, 'approve_url' => $approveUrl, 'order_id' => $orderId];
    }

    /**
     * @return array{ok: bool, subscription?: array, error?: string}
     */
    public function captureOrder(string $orderId, ?int $expectedUserId = null): array
    {
        $payment = $this->payments->findByOrderId($orderId);
        if ($payment && $payment['status'] === 'completed' && !empty($payment['paypal_capture_id'])) {
            return ['ok' => true, 'subscription' => $this->getSubscription((int) $payment['user_id'])];
        }

        $response = $this->paypalRequest('POST', '/v2/checkout/orders/' . rawurlencode($orderId) . '/capture', new \stdClass());
        if (($response['status'] ?? 0) >= 300) {
            log_message('error', 'PayPal capture failed: ' . json_encode($response));
            return ['ok' => false, 'error' => 'Betaling kon niet worden afgerond.'];
        }

        return $this->applyCapturePayload($response['body'] ?? [], $expectedUserId);
    }

    /**
     * @param array<string, mixed> $body
     * @return array{ok: bool, subscription?: array, error?: string}
     */
    public function applyCapturePayload(array $body, ?int $expectedUserId = null): array
    {
        $unit = $body['purchase_units'][0] ?? [];
        $capture = $unit['payments']['captures'][0] ?? [];
        $captureId = (string) ($capture['id'] ?? '');
        $orderId = (string) ($body['id'] ?? '');
        $customId = (string) ($unit['custom_id'] ?? $capture['custom_id'] ?? '');
        $status = strtoupper((string) ($capture['status'] ?? $body['status'] ?? ''));

        if ($captureId !== '') {
            $existingCapture = $this->payments->findByCaptureId($captureId);
            if ($existingCapture && $existingCapture['status'] === 'completed') {
                return ['ok' => true, 'subscription' => $this->getSubscription((int) $existingCapture['user_id'])];
            }
        }

        if (!in_array($status, ['COMPLETED', 'CAPTURED'], true) && ($body['status'] ?? '') !== 'COMPLETED') {
            return ['ok' => false, 'error' => 'Betaling is nog niet voltooid.'];
        }

        [$userId, $plan] = $this->parseCustomId($customId);
        if ($userId === null || $plan === null) {
            if ($orderId !== '') {
                $pending = $this->payments->findByOrderId($orderId);
                if ($pending) {
                    $userId = (int) $pending['user_id'];
                    $plan = $pending['plan'];
                }
            }
        }

        if ($userId === null || $plan === null) {
            return ['ok' => false, 'error' => 'Betaling kon niet aan een account worden gekoppeld.'];
        }

        if ($expectedUserId !== null && $expectedUserId !== $userId) {
            return ['ok' => false, 'error' => 'Deze betaling hoort bij een ander account.'];
        }

        $amount = $capture['amount']['value'] ?? $this->getPrice($plan);
        $currency = $capture['amount']['currency_code'] ?? $this->getCurrency();

        $payment = $orderId !== '' ? $this->payments->findByOrderId($orderId) : null;
        $payload = json_encode($body);

        if ($payment) {
            $this->payments->update($payment['id'], [
                'status' => 'completed',
                'paypal_capture_id' => $captureId ?: ($payment['paypal_capture_id'] ?? null),
                'amount' => $amount,
                'currency' => $currency,
                'payload' => $payload,
            ]);
        } else {
            $this->payments->insert([
                'user_id' => $userId,
                'plan' => $plan,
                'amount' => $amount,
                'currency' => $currency,
                'status' => 'completed',
                'paypal_order_id' => $orderId ?: null,
                'paypal_capture_id' => $captureId ?: null,
                'payload' => $payload,
            ]);
        }

        $subscription = $this->extendSubscription($userId, $plan, 'paypal');

        return ['ok' => true, 'subscription' => $subscription];
    }

    public function verifyWebhookSignature(string $rawBody, array $headers): bool
    {
        if ($this->paypal->webhookId === '') {
            log_message('warning', 'PayPal webhook received without webhookId configured; skipping signature check.');
            return true;
        }

        $payload = [
            'auth_algo' => $this->header($headers, 'PAYPAL-AUTH-ALGO'),
            'cert_url' => $this->header($headers, 'PAYPAL-CERT-URL'),
            'transmission_id' => $this->header($headers, 'PAYPAL-TRANSMISSION-ID'),
            'transmission_sig' => $this->header($headers, 'PAYPAL-TRANSMISSION-SIG'),
            'transmission_time' => $this->header($headers, 'PAYPAL-TRANSMISSION-TIME'),
            'webhook_id' => $this->paypal->webhookId,
            'webhook_event' => json_decode($rawBody, true),
        ];

        $response = $this->paypalRequest('POST', '/v1/notifications/verify-webhook-signature', $payload);
        $ok = ($response['body']['verification_status'] ?? '') === 'SUCCESS';
        if (!$ok) {
            log_message('error', 'PayPal webhook signature invalid: ' . json_encode($response));
        }

        return $ok;
    }

    /**
     * @return array{0: ?int, 1: ?string}
     */
    private function parseCustomId(string $customId): array
    {
        if (!preg_match('/^(\d+):(month|year)$/', $customId, $m)) {
            return [null, null];
        }

        return [(int) $m[1], $m[2]];
    }

    private function apiBase(): string
    {
        return strtolower($this->paypal->mode) === 'live'
            ? 'https://api-m.paypal.com'
            : 'https://api-m.sandbox.paypal.com';
    }

    private function accessToken(): ?string
    {
        $client = Services::curlrequest(['http_errors' => false, 'timeout' => 20]);
        $response = $client->post($this->apiBase() . '/v1/oauth2/token', [
            'auth' => [$this->paypal->clientId, $this->paypal->clientSecret, 'basic'],
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            'body' => 'grant_type=client_credentials',
        ]);

        $body = json_decode((string) $response->getBody(), true);
        $token = $body['access_token'] ?? null;
        if (!$token) {
            log_message('error', 'PayPal token failed: ' . (string) $response->getBody());
        }

        return $token;
    }

    /**
     * @param array<string, mixed>|\stdClass $payload
     * @return array{status: int, body: array}
     */
    private function paypalRequest(string $method, string $path, $payload): array
    {
        $token = $this->accessToken();
        if (!$token) {
            return ['status' => 401, 'body' => []];
        }

        $client = Services::curlrequest(['http_errors' => false, 'timeout' => 30]);
        $options = [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'body' => json_encode($payload),
        ];

        $response = $client->request($method, $this->apiBase() . $path, $options);
        $decoded = json_decode((string) $response->getBody(), true);

        return [
            'status' => $response->getStatusCode(),
            'body' => is_array($decoded) ? $decoded : [],
        ];
    }

    private function header(array $headers, string $name): string
    {
        foreach ($headers as $key => $value) {
            if (strcasecmp((string) $key, $name) === 0) {
                return is_array($value) ? (string) ($value[0] ?? '') : (string) $value;
            }
        }

        return '';
    }
}
