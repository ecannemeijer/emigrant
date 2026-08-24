<?php

namespace App\Controllers;

use App\Libraries\BillingService;

class Subscription extends BaseController
{
    public function index()
    {
        $billing = new BillingService();
        $userId = (int) session()->get('userId');
        $settings = $billing->getSettings();
        $subscription = $billing->getSubscription($userId);
        $active = $subscription
            && $subscription['status'] === 'active'
            && strtotime((string) $subscription['ends_at']) > time();

        return view('subscription/index', [
            'title' => 'Abonnement',
            'settings' => $settings,
            'subscription' => $subscription,
            'active' => $active,
            'hasAccess' => $billing->hasAccess($userId),
            'isAdmin' => session()->get('role') === 'admin',
        ]);
    }

    public function checkout(string $plan)
    {
        $billing = new BillingService();
        if (!$billing->isBillingEnabled()) {
            return redirect()->to('/subscription')->with('info', 'Betalen is nog niet verplicht.');
        }

        $result = $billing->createCheckout((int) session()->get('userId'), $plan);
        if (!$result['ok']) {
            return redirect()->to('/subscription')->with('error', $result['error'] ?? 'Checkout mislukt.');
        }

        return redirect()->to($result['approve_url']);
    }

    public function paypalReturn()
    {
        $orderId = (string) $this->request->getGet('token');
        if ($orderId === '') {
            return redirect()->to('/subscription')->with('error', 'Geen PayPal-bestelling gevonden.');
        }

        $billing = new BillingService();
        $result = $billing->captureOrder($orderId, (int) session()->get('userId'));
        if (!$result['ok']) {
            return redirect()->to('/subscription')->with('error', $result['error'] ?? 'Betaling mislukt.');
        }

        $ends = $result['subscription']['ends_at'] ?? null;
        $msg = 'Betaling ontvangen. Je abonnement is actief';
        if ($ends) {
            $msg .= ' tot ' . date('d-m-Y', strtotime($ends));
        }

        return redirect()->to('/subscription')->with('success', $msg . '.');
    }

    public function paypalCancel()
    {
        return redirect()->to('/subscription')->with('info', 'PayPal-betaling geannuleerd.');
    }
}
