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

        $calculator = new \App\Libraries\FinanceCalculator();
        $mappedSettings = \App\Libraries\FinanceDataMapper::bnbSettings($settings);
        $mappedExpenses = \App\Libraries\FinanceDataMapper::bnbExpenses($expenses);
        $taxesModel = new \App\Models\TaxModel();
        $taxes = \App\Libraries\FinanceDataMapper::taxes($taxesModel->getByUserId($userId));
        $profile = (new \App\Models\UserProfileModel())->where('user_id', $userId)->first();
        $years = 0;
        if (!empty($profile['emigration_date'])) {
            $years = max(0, (int) date('Y') - (int) substr($profile['emigration_date'], 0, 4));
        }

        $monthlyRevenue = $calculator->calculateBnbMonthlyRevenue($mappedSettings);
        $monthlyExpensesCalc = $calculator->calculateBnbMonthlyExpenses($mappedSettings, $mappedExpenses, $monthlyRevenue);
        $breakEvenPercentage = $calculator->calculateBreakevenPercentage($mappedSettings, $mappedExpenses, $taxes, $years);
        $maxMonthlyRevenue = ((float) ($settings['number_of_rooms'] ?? 0))
            * \App\Libraries\FinanceCalculator::DAYS_PER_MONTH
            * ((float) ($settings['price_per_room_per_night'] ?? 0));
        $fixedMonthlyExpenses = $monthlyExpensesCalc - ($monthlyRevenue * (((float) ($expenses['platform_commission'] ?? 0)) / 100));

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
