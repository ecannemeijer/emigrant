<?php

namespace App\Controllers;

use App\Libraries\FinanceCalculator;
use App\Libraries\FinanceDataMapper;
use App\Models\BnbExpenseModel;
use App\Models\BnbSettingModel;
use App\Models\TaxModel;
use App\Models\UserProfileModel;

class Bnb extends BaseController
{
    public function index()
    {
        $userId = (int) session()->get('userId');
        $settings = (new BnbSettingModel())->getByUserId($userId) ?? [];
        $expenses = (new BnbExpenseModel())->getByUserId($userId) ?? [];
        $taxes = (new TaxModel())->getByUserId($userId) ?? [];
        $profile = (new UserProfileModel())->where('user_id', $userId)->first() ?? [];

        return view('bnb/index', [
            'title' => 'B&B',
            'settings' => $settings,
            'expenses' => $expenses,
            'snapshot' => $this->snapshot($settings, $expenses, $taxes, $profile),
        ]);
    }

    public function save()
    {
        $this->persistSettings();
        $this->persistExpenses();

        return redirect()->to('/bnb')->with('success', 'B&B-gegevens opgeslagen. Ze tellen mee op het dashboard als de module aan staat.');
    }

    public function saveSettings()
    {
        $this->persistSettings();

        return redirect()->to('/bnb')->with('success', 'B&B instellingen opgeslagen!');
    }

    public function saveExpenses()
    {
        $this->persistExpenses();

        return redirect()->to('/bnb')->with('success', 'B&B kosten opgeslagen!');
    }

    public function breakeven()
    {
        return redirect()->to('/bnb#breakeven');
    }

    private function persistSettings(): void
    {
        $userId = (int) session()->get('userId');
        $model = new BnbSettingModel();
        $highSeasonPercentage = $this->parseNumber('high_season_percentage', 80);
        $highSeasonMonths = (int) $this->request->getPost('high_season_months') ?: 4;
        $lowSeasonPercentage = $this->parseNumber('low_season_percentage', 40);
        $lowSeasonMonths = (int) $this->request->getPost('low_season_months') ?: 8;
        $monthTotal = max(1, $highSeasonMonths + $lowSeasonMonths);
        $occupancyRate = (($highSeasonPercentage * $highSeasonMonths) + ($lowSeasonPercentage * $lowSeasonMonths)) / $monthTotal;

        $postData = [
            'user_id' => $userId,
            'enabled' => $this->request->getPost('enabled') ? 1 : 0,
            'number_of_rooms' => max(0, (int) $this->request->getPost('number_of_rooms')),
            'price_per_room_per_night' => $this->parseNumber('price_per_room_per_night', 0),
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
    }

    private function persistExpenses(): void
    {
        $userId = (int) session()->get('userId');
        $model = new BnbExpenseModel();
        $postData = [
            'user_id' => $userId,
            'extra_energy_water' => $this->parseNumber('extra_energy_water', 0),
            'insurance' => $this->parseNumber('insurance', 0),
            'cleaning' => $this->parseNumber('cleaning', 0),
            'linen_laundry' => $this->parseNumber('linen_laundry', 0),
            'breakfast_per_guest' => $this->parseNumber('breakfast_per_guest', 0),
            'platform_commission' => $this->parseNumber('platform_commission', 0),
            'marketing' => $this->parseNumber('marketing', 0),
            'maintenance' => $this->parseNumber('maintenance', 0),
            'administration' => $this->parseNumber('administration', 0),
        ];

        $existing = $model->getByUserId($userId);
        if ($existing) {
            $model->update($existing['id'], $postData);
        } else {
            $model->insert($postData);
        }
    }

    /**
     * @param array<string, mixed> $settings
     * @param array<string, mixed> $expenses
     * @param array<string, mixed> $taxes
     * @param array<string, mixed> $profile
     * @return array<string, mixed>
     */
    private function snapshot(array $settings, array $expenses, array $taxes, array $profile): array
    {
        $calc = new FinanceCalculator();
        $mappedTaxes = FinanceDataMapper::taxes($taxes);
        $mappedExpenses = FinanceDataMapper::bnbExpenses($expenses);
        $years = 0;
        if (!empty($profile['emigration_date'])) {
            $years = max(0, (int) date('Y') - (int) substr((string) $profile['emigration_date'], 0, 4));
        }

        $preview = FinanceDataMapper::bnbSettings($settings);
        $preview['enabled'] = 1;
        $rooms = (float) ($preview['number_of_rooms'] ?? 0);
        $price = (float) ($preview['price_per_room_per_night'] ?? 0);
        $highMonths = (float) ($preview['high_season_months'] ?? 0);
        $lowMonths = (float) ($preview['low_season_months'] ?? 0);
        $highPct = (float) ($preview['high_season_percentage'] ?? 0);
        $lowPct = (float) ($preview['low_season_percentage'] ?? 0);
        $days = FinanceCalculator::DAYS_PER_MONTH;

        $highNights = $highMonths * $days * ($highPct / 100) * $rooms;
        $lowNights = $lowMonths * $days * ($lowPct / 100) * $rooms;
        $nights = $highNights + $lowNights;
        $highRevenueYear = $highNights * $price;
        $lowRevenueYear = $lowNights * $price;
        $yearlyRevenue = $highRevenueYear + $lowRevenueYear;
        $monthlyRevenue = $yearlyRevenue / 12.0;

        $fixed = (float) ($mappedExpenses['extra_energy_water'] ?? 0)
            + (float) ($mappedExpenses['insurance'] ?? 0)
            + (float) ($mappedExpenses['cleaning'] ?? 0)
            + (float) ($mappedExpenses['linen_laundry'] ?? 0)
            + (float) ($mappedExpenses['marketing'] ?? 0)
            + (float) ($mappedExpenses['maintenance'] ?? 0)
            + (float) ($mappedExpenses['administration'] ?? 0);
        $commission = $monthlyRevenue * (((float) ($mappedExpenses['platform_commission'] ?? 0)) / 100);
        $breakfast = $nights * ((float) ($mappedExpenses['breakfast_per_guest'] ?? 0)) / 12.0;
        $monthlyExpenses = $fixed + $commission + $breakfast;
        $monthlyTax = $calc->calculateBnbTax($monthlyRevenue, $monthlyExpenses, $mappedTaxes, $years);
        $netMonthly = $monthlyRevenue - $monthlyExpenses;
        $netAfterTax = $netMonthly - $monthlyTax;

        $monthTotal = $highMonths + $lowMonths;
        $occupancy = $monthTotal > 0
            ? (($highPct * $highMonths) + ($lowPct * $lowMonths)) / $monthTotal
            : 0.0;

        $maxMonthlyRevenue = $rooms * $days * $price;
        $breakEven = $calc->calculateBreakevenPercentage($preview, $mappedExpenses, $mappedTaxes, $years);

        $forfettario = !empty($mappedTaxes['forfettario_enabled']);
        $taxRate = $forfettario
            ? $calc->forfettarioRate($mappedTaxes, $years)
            : (float) ($mappedTaxes['normal_tax_percentage'] ?? 23);
        $limit = (float) ($mappedTaxes['forfettario_limit'] ?? FinanceCalculator::FORFETTARIO_LIMIT);

        $scenarios = [];
        foreach ([40, 50, 60, 70, 80] as $pct) {
            $probe = $preview;
            $probe['high_season_percentage'] = $pct;
            $probe['low_season_percentage'] = $pct;
            $probe['high_season_months'] = 6;
            $probe['low_season_months'] = 6;
            $rev = $calc->calculateBnbMonthlyRevenue($probe);
            $exp = $calc->calculateBnbMonthlyExpenses($probe, $mappedExpenses, $rev);
            $tax = $calc->calculateBnbTax($rev, $exp, $mappedTaxes, $years);
            $scenarios[] = [
                'occupancy' => $pct,
                'revenue' => $rev,
                'expenses' => $exp,
                'tax' => $tax,
                'net' => $rev - $exp - $tax,
            ];
        }

        return [
            'enabled' => !empty($settings['enabled']),
            'nights' => $nights,
            'high_nights' => $highNights,
            'low_nights' => $lowNights,
            'occupancy' => $occupancy,
            'monthly_revenue' => $monthlyRevenue,
            'yearly_revenue' => $yearlyRevenue,
            'high_revenue_year' => $highRevenueYear,
            'low_revenue_year' => $lowRevenueYear,
            'monthly_expenses' => $monthlyExpenses,
            'fixed_monthly' => $fixed,
            'commission_monthly' => $commission,
            'breakfast_monthly' => $breakfast,
            'monthly_tax' => $monthlyTax,
            'net_monthly' => $netMonthly,
            'net_after_tax' => $netAfterTax,
            'break_even' => $breakEven,
            'max_monthly_revenue' => $maxMonthlyRevenue,
            'months_total' => $monthTotal,
            'forfettario' => $forfettario,
            'tax_rate' => $taxRate,
            'profitability_coefficient' => (float) ($mappedTaxes['profitability_coefficient'] ?? 67),
            'forfettario_limit' => $limit,
            'over_limit' => $yearlyRevenue > $limit,
            'years_since_emigration' => $years,
            'startup_rate' => $forfettario && $years < 5 && !empty($mappedTaxes['startup_rate_enabled']),
            'cost_rows' => [
                ['label' => 'Extra energie/water', 'amount' => (float) ($mappedExpenses['extra_energy_water'] ?? 0)],
                ['label' => 'Verzekering', 'amount' => (float) ($mappedExpenses['insurance'] ?? 0)],
                ['label' => 'Schoonmaak', 'amount' => (float) ($mappedExpenses['cleaning'] ?? 0)],
                ['label' => 'Linnen & was', 'amount' => (float) ($mappedExpenses['linen_laundry'] ?? 0)],
                ['label' => 'Marketing', 'amount' => (float) ($mappedExpenses['marketing'] ?? 0)],
                ['label' => 'Onderhoud', 'amount' => (float) ($mappedExpenses['maintenance'] ?? 0)],
                ['label' => 'Administratie', 'amount' => (float) ($mappedExpenses['administration'] ?? 0)],
                ['label' => 'Platformcommissie', 'amount' => $commission],
                ['label' => 'Ontbijt', 'amount' => $breakfast],
            ],
            'scenarios' => $scenarios,
            'js' => [
                'days' => $days,
                'forfettario' => $forfettario ? 1 : 0,
                'taxRate' => $taxRate,
                'coeff' => ((float) ($mappedTaxes['profitability_coefficient'] ?? 67)) / 100,
                'limit' => $limit,
            ],
        ];
    }

    private function parseNumber(string $field, float $default = 0.0): float
    {
        $raw = str_replace(',', '.', (string) $this->request->getPost($field));

        return is_numeric($raw) ? max(0, (float) $raw) : $default;
    }
}
