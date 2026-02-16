<?php

namespace App\Controllers;

use App\Models\BnbSettingModel;
use App\Models\BnbExpenseModel;

class Bnb extends BaseController
{
    public function index()
    {
        $userId = session()->get('userId');
        $settingModel = new BnbSettingModel();
        $expenseModel = new BnbExpenseModel();
        
        $settings = $settingModel->getByUserId($userId);
        $expenses = $expenseModel->getByUserId($userId);

        // Calculate revenue and expenses
        $monthlyRevenue = 0;
        $yearlyRevenue = 0;
        $monthlyExpenses = 0;
        $netMonthlyIncome = 0;

        if ($settings && $settings['enabled']) {
            $monthlyRevenue = $settingModel->calculateMonthlyRevenue($userId);
            $yearlyRevenue = $settingModel->calculateYearlyRevenue($userId);
            $monthlyExpenses = $expenseModel->getTotalMonthlyExpenses($userId, $monthlyRevenue);
            $netMonthlyIncome = $monthlyRevenue - $monthlyExpenses;
        }

        $data = [
            'title' => 'B&B Module',
            'settings' => $settings,
            'expenses' => $expenses,
            'calculations' => [
                'monthly_revenue' => $monthlyRevenue,
                'yearly_revenue' => $yearlyRevenue,
                'monthly_expenses' => $monthlyExpenses,
                'net_monthly_income' => $netMonthlyIncome,
            ],
        ];

        return view('bnb/index', $data);
    }

    public function saveSettings()
    {
        $userId = session()->get('userId');
        $model = new BnbSettingModel();

        // Calculate weighted average occupancy rate
        $highSeasonPercentage = $this->request->getPost('high_season_percentage') ?: 80;
        $highSeasonMonths = $this->request->getPost('high_season_months') ?: 4;
        $lowSeasonPercentage = $this->request->getPost('low_season_percentage') ?: 40;
        $lowSeasonMonths = $this->request->getPost('low_season_months') ?: 8;
        
        $occupancyRate = (($highSeasonPercentage * $highSeasonMonths) + ($lowSeasonPercentage * $lowSeasonMonths)) / 12;

        $postData = [
            'user_id' => $userId,
            'enabled' => $this->request->getPost('enabled') ? 1 : 0,
            'number_of_rooms' => $this->request->getPost('number_of_rooms'),
            'price_per_room_per_night' => $this->request->getPost('price_per_room_per_night'),
            'occupancy_rate' => $occupancyRate,
            'high_season_percentage' => $highSeasonPercentage,
            'low_season_percentage' => $lowSeasonPercentage,
            'high_season_months' => $highSeasonMonths,
            'low_season_months' => $lowSeasonMonths,
        ];

        $existing = $model->getByUserId($userId);

        if ($existing) {
            $model->update($existing['id'], $postData);
        } else {
            $model->insert($postData);
        }

        return redirect()->to('/bnb')->with('success', 'B&B instellingen opgeslagen!');
    }

    public function saveExpenses()
    {
        $userId = session()->get('userId');
        $model = new BnbExpenseModel();

        $postData = [
            'user_id' => $userId,
            'extra_energy_water' => $this->request->getPost('extra_energy_water'),
            'insurance' => $this->request->getPost('insurance'),
            'cleaning' => $this->request->getPost('cleaning'),
            'linen_laundry' => $this->request->getPost('linen_laundry'),
            'breakfast_per_guest' => $this->request->getPost('breakfast_per_guest'),
            'platform_commission' => $this->request->getPost('platform_commission'),
            'marketing' => $this->request->getPost('marketing'),
            'maintenance' => $this->request->getPost('maintenance'),
            'administration' => $this->request->getPost('administration'),
        ];

        $existing = $model->getByUserId($userId);

        if ($existing) {
            $model->update($existing['id'], $postData);
        } else {
            $model->insert($postData);
        }

        return redirect()->to('/bnb')->with('success', 'B&B kosten opgeslagen!');
    }

    public function breakeven()
    {
        $userId = session()->get('userId');
        $settingModel = new BnbSettingModel();
        $expenseModel = new BnbExpenseModel();

        $settings = $settingModel->getByUserId($userId);
        $expenses = $expenseModel->getByUserId($userId);

        if (!$settings) {
            return redirect()->to('/bnb')->with('error', 'Geen B&B instellingen gevonden.');
        }

        // Calculate break-even occupancy rate
        $fixedMonthlyExpenses = ($expenses['extra_energy_water'] ?? 0) +
                               ($expenses['insurance'] ?? 0) +
                               ($expenses['cleaning'] ?? 0) +
                               ($expenses['linen_laundry'] ?? 0) +
                               ($expenses['marketing'] ?? 0) +
                               ($expenses['maintenance'] ?? 0) +
                               ($expenses['administration'] ?? 0);

        $rooms = $settings['number_of_rooms'];
        $pricePerNight = $settings['price_per_room_per_night'];
        $commission = ($expenses['platform_commission'] ?? 15) / 100;

        // Average monthly nights available (365 / 12 ≈ 30.4167)
        $avgMonthlyNights = 30.4167;
        $maxMonthlyRevenue = $rooms * $avgMonthlyNights * $pricePerNight;
        $revenueAfterCommission = $maxMonthlyRevenue * (1 - $commission);

        // Break-even percentage
        $breakEvenPercentage = ($fixedMonthlyExpenses / $revenueAfterCommission) * 100;

        $data = [
            'title' => 'B&B Break-even Analyse',
            'settings' => $settings,
            'expenses' => $expenses,
            'fixed_monthly_expenses' => $fixedMonthlyExpenses,
            'break_even_percentage' => $breakEvenPercentage,
            'max_monthly_revenue' => $maxMonthlyRevenue,
        ];

        return view('bnb/breakeven', $data);
    }
}
