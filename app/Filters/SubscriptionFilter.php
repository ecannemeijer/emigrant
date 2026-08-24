<?php

namespace App\Filters;

use App\Libraries\BillingService;
use App\Models\UserModel;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class SubscriptionFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $userId = (int) session()->get('userId');
        if ($userId < 1) {
            return redirect()->to('/login');
        }

        try {
            $user = (new UserModel())->find($userId);
            if ($user && ($user['role'] ?? '') === 'admin' && !empty($user['is_active'])) {
                return;
            }

            $billing = new BillingService();
            if ($billing->hasAccess($userId)) {
                return;
            }
        } catch (\Throwable $e) {
            log_message('error', 'Subscription filter failed: ' . $e->getMessage());
            return redirect()->to('/subscription')->with('error', 'Toegang kon niet worden gecontroleerd. Probeer het later opnieuw.');
        }

        return redirect()->to('/subscription')->with('error', 'Je hebt een geldig abonnement nodig om deze pagina te gebruiken.');
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
