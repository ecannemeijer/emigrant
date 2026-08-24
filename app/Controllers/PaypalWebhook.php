<?php

namespace App\Controllers;

use App\Libraries\BillingService;

class PaypalWebhook extends BaseController
{
    public function handle()
    {
        $raw = $this->request->getBody();
        $billing = new BillingService();

        $headers = [
            'PAYPAL-AUTH-ALGO' => $this->request->getHeaderLine('PAYPAL-AUTH-ALGO'),
            'PAYPAL-CERT-URL' => $this->request->getHeaderLine('PAYPAL-CERT-URL'),
            'PAYPAL-TRANSMISSION-ID' => $this->request->getHeaderLine('PAYPAL-TRANSMISSION-ID'),
            'PAYPAL-TRANSMISSION-SIG' => $this->request->getHeaderLine('PAYPAL-TRANSMISSION-SIG'),
            'PAYPAL-TRANSMISSION-TIME' => $this->request->getHeaderLine('PAYPAL-TRANSMISSION-TIME'),
        ];

        if (!$billing->verifyWebhookSignature($raw, $headers)) {
            return $this->response->setStatusCode(400)->setJSON(['ok' => false]);
        }

        $event = json_decode($raw, true) ?: [];
        $type = $event['event_type'] ?? '';
        $resource = $event['resource'] ?? [];

        if ($type === 'CHECKOUT.ORDER.APPROVED') {
            $orderId = (string) ($resource['id'] ?? '');
            if ($orderId !== '') {
                $billing->captureOrder($orderId);
            }
        }

        if ($type === 'PAYMENT.CAPTURE.COMPLETED') {
            $orderId = (string) ($resource['supplementary_data']['related_ids']['order_id'] ?? '');
            if ($orderId !== '') {
                $billing->captureOrder($orderId);
            } else {
                $billing->applyCapturePayload([
                    'id' => '',
                    'status' => 'COMPLETED',
                    'purchase_units' => [[
                        'custom_id' => $resource['custom_id'] ?? '',
                        'payments' => [
                            'captures' => [$resource],
                        ],
                    ]],
                ]);
            }
        }

        return $this->response->setJSON(['ok' => true]);
    }
}
