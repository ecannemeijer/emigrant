<?php

namespace App\Controllers;

use App\Models\StartPositionModel;
use App\Models\IncomeModel;
use App\Models\PropertyModel;
use App\Models\ExpenseModel;
use App\Models\TaxModel;
use App\Models\BnbSettingModel;
use App\Models\BnbExpenseModel;
use App\Models\UserProfileModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $userId = session()->get('userId');

        // Load all models
        $startPositionModel = new StartPositionModel();
        $incomeModel = new IncomeModel();
        $propertyModel = new PropertyModel();
        $expenseModel = new ExpenseModel();
        $taxModel = new TaxModel();
        $bnbSettingModel = new BnbSettingModel();
        $bnbExpenseModel = new BnbExpenseModel();
        $profileModel = new UserProfileModel();

        // Get data
        $startPosition = $startPositionModel->getByUserId($userId);
        $income = $incomeModel->getByUserId($userId);
        $mainProperty = $propertyModel->getMainProperty($userId);
        $secondProperty = $propertyModel->getSecondProperty($userId);
        $expenses = $expenseModel->getByUserId($userId);
        $taxes = $taxModel->getByUserId($userId);
        $bnbSettings = $bnbSettingModel->getByUserId($userId);
        $bnbExpenses = $bnbExpenseModel->getByUserId($userId);
        $profile = $profileModel->where('user_id', $userId)->first();

        // Calculate finances
        $calculations = $this->calculateFinances(
            $startPosition,
            $income,
            $mainProperty,
            $secondProperty,
            $expenses,
            $taxes,
            $bnbSettings,
            $bnbExpenses,
            $profile
        );

        // Calculate multi-year projections
        $yearlyProjections = $this->calculateYearlyProjections(
            $startPosition,
            $income,
            $expenses,
            $taxes,
            $bnbSettings,
            $bnbExpenses,
            $profile,
            $calculations
        );

        $data = [
            'title' => 'Dashboard',
            'startPosition' => $startPosition,
            'calculations' => $calculations,
            'yearlyProjections' => $yearlyProjections,
            'profile' => $profile,
            'income' => $income,
            'expenses' => $expenses,
            'taxes' => $taxes,
            'mainProperty' => $mainProperty,
            'secondProperty' => $secondProperty,
            'bnbSettings' => $bnbSettings,
            'bnbExpenses' => $bnbExpenses,
        ];

        return view('dashboard/index', $data);
    }

    private function calculateFinances($startPosition, $income, $mainProperty, $secondProperty, $expenses, $taxes, $bnbSettings, $bnbExpenses, $profile)
    {
        // Calculate WaO reduction based on emigration date (for partner WaO)
        $partnerWaoPercentage = 100.0;
        if (!empty($profile['emigration_date']) && !empty($profile['partner_date_of_birth'])) {
            $partnerWaoPercentage = calculate_wao_percentage(
                $profile['emigration_date'],
                $profile['partner_date_of_birth'],
                $profile['partner_retirement_age'] ?? 67
            );
        }
        
        // Calculate own WaO reduction
        $ownWaoPercentage = 100.0;
        if (!empty($profile['emigration_date']) && !empty($profile['date_of_birth'])) {
            $ownWaoPercentage = calculate_wao_percentage(
                $profile['emigration_date'],
                $profile['date_of_birth'],
                $profile['retirement_age'] ?? 67
            );
        }
        
        // Monthly income (with WaO reduction applied)
        $partnerWaoAmount = ($income['wao_future'] ?? 0) * ($partnerWaoPercentage / 100);
        $ownWaoAmount = ($income['own_wao'] ?? 0) * ($ownWaoPercentage / 100);
        
        $monthlyIncome = ($income['wia_wife'] ?? 0) +
                        ($income['own_income'] ?? 0) +
                        $partnerWaoAmount +
                        $ownWaoAmount +
                        ($income['pension'] ?? 0) +
                        ($income['other_income'] ?? 0);

        // B&B Income
        $bnbMonthlyRevenue = 0;
        $bnbMonthlyExpenses = 0;
        $bnbNetIncome = 0;

        if ($bnbSettings && $bnbSettings['enabled']) {
            $bnbSettingModel = new BnbSettingModel();
            $bnbExpenseModel = new BnbExpenseModel();
            
            $bnbMonthlyRevenue = $bnbSettingModel->calculateMonthlyRevenue(session()->get('userId'));
            $bnbMonthlyExpenses = $bnbExpenseModel->getTotalMonthlyExpenses(session()->get('userId'), $bnbMonthlyRevenue);
            $bnbNetIncome = $bnbMonthlyRevenue - $bnbMonthlyExpenses;
        }

        $totalMonthlyIncome = $monthlyIncome + $bnbNetIncome;

        // Monthly expenses
        $monthlyExpenses = ($expenses['energy'] ?? 0) +
                          ($expenses['water'] ?? 0) +
                          ($expenses['internet'] ?? 0) +
                          ($expenses['health_insurance'] ?? 0) +
                          ($expenses['car_insurance'] ?? 0) +
                          ($expenses['car_fuel'] ?? 0) +
                          ($expenses['car_maintenance'] ?? 0) +
                          ($expenses['groceries'] ?? 0) +
                          ($expenses['leisure'] ?? 0) +
                          ($expenses['unforeseen'] ?? 0) +
                          ($expenses['other'] ?? 0);

        // Property costs
        // Main property: annual_costs only (energy/water/insurance are in expenses page, TARI at taxes page)
        $mainPropertyMonthly = ($mainProperty['annual_costs'] ?? 0) / 12;
        
        // Second property: all costs (not in expenses page)
        $secondPropertyMonthly = 0;
        $secondPropertyTari = 0;
        
        if ($secondProperty) {
            $secondPropertyMonthly = (($secondProperty['annual_costs'] ?? 0) + ($secondProperty['maintenance_yearly'] ?? 0)) / 12;
            $secondPropertyMonthly += ($secondProperty['energy_monthly'] ?? 0);
            $secondPropertyMonthly += ($secondProperty['other_monthly_costs'] ?? 0);
            $secondPropertyTari = ($secondProperty['tari_yearly'] ?? 0) / 12;
        }

        $totalMonthlyExpenses = $monthlyExpenses + $mainPropertyMonthly + $secondPropertyMonthly;
        
        // TARI: main property from taxes table, second property from properties table
        $tariMonthly = $secondPropertyTari;
        if ($taxes) {
            $tariMonthly += ($taxes['tari_yearly'] ?? 0) / 12; // Main property TARI
        }

        // Taxes
        $monthlyTaxes = 0;
        if ($taxes) {
            $socialContributions = $taxes['social_contributions'] ?? 0;
            $roadTaxMonthly = ($taxes['road_tax_yearly'] ?? 0) / 12;
            $monthlyTaxes = $tariMonthly + $socialContributions + $roadTaxMonthly;

            // IMU tax for second property (yearly property tax on cadastral value)
            if ($secondProperty && ($secondProperty['imu_tax'] ?? 0) > 0) {
                $imuMonthly = ($secondProperty['imu_tax'] ?? 0) / 12;
                $monthlyTaxes += $imuMonthly;
            }

            // B&B taxes
            if ($bnbSettings && $bnbSettings['enabled'] && $bnbNetIncome > 0) {
                if ($taxes['forfettario_enabled']) {
                    $bnbTax = ($bnbMonthlyRevenue * ($taxes['forfettario_percentage'] ?? 15)) / 100;
                } else {
                    $bnbTax = ($bnbNetIncome * ($taxes['normal_tax_percentage'] ?? 23)) / 100;
                }
                $monthlyTaxes += $bnbTax;
            }
        }

        // Net disposable income (can be negative if expenses > income)
        $netDisposable = $totalMonthlyIncome - $totalMonthlyExpenses - $monthlyTaxes;

        // Calculate capital after emigration
        $totalStartingCapital = $startPosition['total_starting_capital'] ?? 0;
        $mainPropertyCost = ($mainProperty['purchase_price'] ?? 0) + ($mainProperty['purchase_costs'] ?? 0);
        $secondPropertyCost = 0;
        
        if ($secondProperty) {
            $secondPropertyCost = ($secondProperty['purchase_price'] ?? 0) + ($secondProperty['purchase_costs'] ?? 0);
        }

        $remainingCapital = $totalStartingCapital - $mainPropertyCost - $secondPropertyCost;

        return [
            'monthly_income' => $monthlyIncome,
            'bnb_revenue' => $bnbMonthlyRevenue,
            'bnb_expenses' => $bnbMonthlyExpenses,
            'bnb_net_income' => $bnbNetIncome,
            'total_monthly_income' => $totalMonthlyIncome,
            'monthly_expenses' => $totalMonthlyExpenses,
            'monthly_taxes' => $monthlyTaxes,
            'net_disposable' => $netDisposable,
            'remaining_capital' => $remainingCapital,
            'capital_12_months' => $remainingCapital + ($netDisposable * 12),
            'capital_24_months' => $remainingCapital + ($netDisposable * 24),
            'capital_36_months' => $remainingCapital + ($netDisposable * 36),
        ];
    }

    private function calculateYearlyProjections($startPosition, $income, $expenses, $taxes, $bnbSettings, $bnbExpenses, $profile, $baseCalculations)
    {
        $projections = [];
        
        // Get interest rate
        $interestRate = $startPosition['interest_rate'] ?? 2.00;
        
        // Get current ages
        $currentUserAge = !empty($profile['date_of_birth']) ? calculate_age($profile['date_of_birth']) : null;
        $currentPartnerAge = !empty($profile['partner_date_of_birth']) ? calculate_age($profile['partner_date_of_birth']) : null;
        
        // Get retirement ages from profile
        $userRetirementAge = $profile['retirement_age'] ?? 67;
        $partnerRetirementAge = $profile['partner_retirement_age'] ?? 67;
        
        // Calculate WaO reduction based on emigration date (for partner WaO)
        $partnerWaoPercentage = 100.0;
        if (!empty($profile['emigration_date']) && !empty($profile['partner_date_of_birth'])) {
            $partnerWaoPercentage = calculate_wao_percentage(
                $profile['emigration_date'],
                $profile['partner_date_of_birth'],
                $partnerRetirementAge
            );
        }
        
        // Calculate own WaO reduction
        $ownWaoPercentage = 100.0;
        if (!empty($profile['emigration_date']) && !empty($profile['date_of_birth'])) {
            $ownWaoPercentage = calculate_wao_percentage(
                $profile['emigration_date'],
                $profile['date_of_birth'],
                $userRetirementAge
            );
        }
        
        // Calculate how many years to project
        // Project until partner reaches 68 (or at least 15 years)
        $yearsToProject = 15; // minimum
        if ($currentPartnerAge && $currentPartnerAge < 68) {
            $yearsToProject = max(15, 68 - $currentPartnerAge);
        }
        
        // Starting capital
        $currentCapital = $baseCalculations['remaining_capital'];
        
        // Project for calculated years
        for ($year = 0; $year <= $yearsToProject; $year++) {
            $userAge = $currentUserAge ? $currentUserAge + $year : null;
            $partnerAge = $currentPartnerAge ? $currentPartnerAge + $year : null;
            
            // Start with base income
            $monthlyIncomeBase = ($income['own_income'] ?? 0);
            $hasPartnerWao = false;
            $hasPartnerIncome = false;
            $hasOwnPension = false;
            $hasOwnWao = false;
            $hasWia = false;
            $partnerWaoAmount = 0;
            $partnerIncomeAmount = 0;
            $ownWaoAmount = 0;
            $pensionAmount = 0;
            $wiaAmount = 0;
            
            // Check partner income (WIA or regular)
            $partnerHasWia = ($income['partner_has_wia'] ?? 1) == 1;
            $partnerIncome = ($income['wia_wife'] ?? 0);
            
            if ($partnerHasWia) {
                // WIA: stops at retirement, then WaO starts
                if ($partnerAge && $partnerAge >= $partnerRetirementAge) {
                    // Partner is retired: WIA stops, partner WaO starts
                    $partnerWaoAmount = ($income['wao_future'] ?? 0) * ($partnerWaoPercentage / 100);
                    $monthlyIncomeBase += $partnerWaoAmount;
                    $hasPartnerWao = true;
                } else {
                    // Partner not retired yet: WIA continues
                    $wiaAmount = $partnerIncome;
                    $monthlyIncomeBase += $wiaAmount;
                    $hasWia = $wiaAmount > 0;
                }
            } else {
                // Regular partner income: continues always (doesn't stop at retirement)
                $partnerIncomeAmount = $partnerIncome;
                $monthlyIncomeBase += $partnerIncomeAmount;
                $hasPartnerIncome = $partnerIncomeAmount > 0;
            }
            
            // Check if user has reached pension age
            if ($userAge && $userAge >= $userRetirementAge) {
                // User is retired: pension and own WaO start
                $pensionAmount = ($income['pension'] ?? 0);
                $ownWaoAmount = ($income['own_wao'] ?? 0) * ($ownWaoPercentage / 100);
                $monthlyIncomeBase += $pensionAmount + $ownWaoAmount;
                $hasOwnPension = true;
                $hasOwnWao = $ownWaoAmount > 0;
            }
            
            // Calculate with and without B&B
            $bnbMonthly = $baseCalculations['bnb_net_income'];
            $monthlyIncomeWithBnb = $monthlyIncomeBase + $bnbMonthly;
            $monthlyIncomeWithoutBnb = $monthlyIncomeBase;
            
            // Store amounts for display
            $displayPartnerWaoAmount = $hasPartnerWao ? $partnerWaoAmount : 0;
            $displayPartnerIncomeAmount = $hasPartnerIncome ? $partnerIncomeAmount : 0;
            $displayOwnWaoAmount = $hasOwnWao ? $ownWaoAmount : 0;
            $displayPensionAmount = $hasOwnPension ? $pensionAmount : 0;
            $displayWiaAmount = $hasWia ? $wiaAmount : 0;
            
            // Calculate interest on capital (yearly)
            $yearlyInterest = $currentCapital * ($interestRate / 100);
            $monthlyInterest = $yearlyInterest / 12;
            
            // Add interest to monthly income for display
            $monthlyIncomeWithInterest = $monthlyIncomeWithBnb + $monthlyInterest;
            $monthlyIncomeWithInterestWithoutBnb = $monthlyIncomeWithoutBnb + $monthlyInterest;
            
            // Calculate yearly values
            $yearlyIncome = $monthlyIncomeWithInterest * 12;
            $yearlyIncomeWithoutBnb = $monthlyIncomeWithInterestWithoutBnb * 12;
            $yearlyExpenses = $baseCalculations['monthly_expenses'] * 12;
            $yearlyTaxes = $baseCalculations['monthly_taxes'] * 12;
            $yearlyNet = $yearlyIncome - $yearlyExpenses - $yearlyTaxes;
            $yearlyNetWithoutBnb = $yearlyIncomeWithoutBnb - $yearlyExpenses - $yearlyTaxes;
            
            // Monthly net values
            $monthlyNet = $yearlyNet / 12;
            $monthlyNetWithoutBnb = $yearlyNetWithoutBnb / 12;
            
            // Update capital (if yearlyNet is negative, capital decreases automatically)
            $currentCapital += $yearlyNet;
            
            $projections[] = [
                'year' => date('Y') + $year,
                'age_offset' => $year,
                'user_age' => $userAge,
                'partner_age' => $partnerAge,
                'monthly_income' => $monthlyIncomeWithInterest,
                'monthly_income_without_bnb' => $monthlyIncomeWithInterestWithoutBnb,
                'monthly_net' => $monthlyNet,
                'monthly_net_without_bnb' => $monthlyNetWithoutBnb,
                'bnb_monthly' => $bnbMonthly,
                'monthly_interest' => $monthlyInterest,
                'yearly_income' => $yearlyIncome,
                'yearly_income_without_bnb' => $yearlyIncomeWithoutBnb,
                'yearly_expenses' => $yearlyExpenses,
                'yearly_taxes' => $yearlyTaxes,
                'yearly_net' => $yearlyNet,
                'yearly_net_without_bnb' => $yearlyNetWithoutBnb,
                'capital' => $currentCapital,
                'has_partner_wao' => $hasPartnerWao,
                'has_partner_income' => $hasPartnerIncome,
                'has_own_pension' => $hasOwnPension,
                'has_own_wao' => $hasOwnWao,
                'has_wia' => $hasWia,
                'partner_wao_amount' => $displayPartnerWaoAmount,
                'partner_income_amount' => $displayPartnerIncomeAmount,
                'own_wao_amount' => $displayOwnWaoAmount,
                'pension_amount' => $displayPensionAmount,
                'wia_amount' => $displayWiaAmount,
                'has_partner_retired' => ($partnerAge && $partnerAge >= $partnerRetirementAge),
                'has_user_retired' => ($userAge && $userAge >= $userRetirementAge),
            ];
        }
        
        return $projections;
    }
}
