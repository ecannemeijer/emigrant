<?php

namespace App\Controllers;

use App\Libraries\BillingService;

class Home extends BaseController
{
    public function index()
    {
        if (session()->get('isLoggedIn')) {
            $user = (new \App\Models\UserModel())->find((int) session()->get('userId'));
            if (is_array($user)) {
                return redirect()->to((new \App\Libraries\SetupService())->redirectAfterAuth($user));
            }
            return redirect()->to('/dashboard');
        }

        $settings = [];
        try {
            $settings = (new BillingService())->getSettings();
        } catch (\Throwable $e) {
            log_message('error', 'Landing billing settings: ' . $e->getMessage());
            $settings = [
                'billing_enabled' => false,
                'price_month' => 9.90,
                'price_year' => 69.00,
                'sale_price_month' => 9.90,
                'sale_price_year' => 69.00,
                'discount_active' => false,
                'discount_percent' => 0,
                'discount_until_label' => null,
            ];
        }

        return view('home', [
            'title' => 'Emigreren naar Italië',
            'settings' => $settings,
        ]);
    }
}
