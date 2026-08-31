<?php

namespace App\Controllers;

use App\Libraries\ExpenseEstimator;
use App\Libraries\SetupService;
use App\Models\ExpenseModel;
use App\Models\IncomeModel;
use App\Models\PropertyModel;
use App\Models\StartPositionModel;
use App\Models\UserProfileModel;

class Setup extends BaseController
{
    public function index()
    {
        $userId = (int) session()->get('userId');
        $profile = (new UserProfileModel())->where('user_id', $userId)->first() ?? [];
        $income = (new IncomeModel())->getByUserId($userId) ?? [];
        $savedExpenses = (new ExpenseModel())->getByUserId($userId) ?? [];
        $startPosition = (new StartPositionModel())->getByUserId($userId) ?? [];
        $mainProperty = (new PropertyModel())->getMainProperty($userId) ?? [];

        $adults = !empty($profile['has_partner']) ? 2 : 1;
        $children = (int) ($profile['children_count'] ?? 0);
        $cars = (int) ($profile['cars_count'] ?? 0);
        $estimate = ExpenseEstimator::estimate($adults, $children, $cars);

        return view('setup/index', [
            'title' => 'Aan de slag',
            'profile' => $profile,
            'income' => $income,
            'expenses' => $savedExpenses ?: $estimate,
            'estimate' => $estimate,
            'hasSavedExpenses' => !empty($savedExpenses),
            'startPosition' => $startPosition,
            'mainProperty' => $mainProperty,
        ]);
    }

    public function skip()
    {
        (new SetupService())->markComplete((int) session()->get('userId'));

        return redirect()->to('/dashboard')->with('info', 'Je kunt de gegevens later invullen via het menu.');
    }

    public function save()
    {
        $userId = (int) session()->get('userId');
        $hasPartner = $this->request->getPost('has_partner') === '1';
        $children = max(0, min(6, (int) $this->request->getPost('children_count')));
        $cars = max(0, min(2, (int) $this->request->getPost('cars_count')));
        $firstName = trim((string) $this->request->getPost('first_name'));
        $dob = trim((string) $this->request->getPost('date_of_birth'));
        $emigration = trim((string) $this->request->getPost('emigration_date'));

        if ($firstName === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $dob) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $emigration)) {
            return redirect()->back()->withInput()->with('error', 'Vul je naam, geboortedatum en emigratiedatum in.');
        }

        $partnerName = trim((string) $this->request->getPost('partner_name'));
        $partnerDob = trim((string) $this->request->getPost('partner_date_of_birth'));
        if ($hasPartner && ($partnerName === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $partnerDob))) {
            return redirect()->back()->withInput()->with('error', 'Vul de naam en geboortedatum van je partner in.');
        }

        $profileModel = new UserProfileModel();
        $profileData = [
            'first_name' => $firstName,
            'has_partner' => $hasPartner ? 1 : 0,
            'partner_name' => $hasPartner ? $partnerName : null,
            'date_of_birth' => $dob,
            'partner_date_of_birth' => $hasPartner ? $partnerDob : null,
            'emigration_date' => $emigration,
            'children_count' => $children,
            'cars_count' => $cars,
            'setup_completed' => 1,
        ];
        $existingProfile = $profileModel->where('user_id', $userId)->first();
        if ($existingProfile) {
            $profileModel->update($existingProfile['id'], $profileData);
        } else {
            $profileData['user_id'] = $userId;
            $profileData['language'] = 'nl';
            $profileModel->insert($profileData);
        }

        $ownOther = max(0, (float) str_replace(',', '.', (string) $this->request->getPost('own_other_income')));
        $partnerOther = $hasPartner ? max(0, (float) str_replace(',', '.', (string) $this->request->getPost('partner_other_income'))) : 0.0;
        $incomeModel = new IncomeModel();
        $incomeData = [
            'user_id' => $userId,
            'own_other_income' => $ownOther,
            'partner_other_income' => $partnerOther,
            'other_income' => $ownOther + $partnerOther,
            'own_benefit_type' => 'none',
            'partner_benefit_type' => 'none',
            'own_income' => 0,
            'wia_wife' => 0,
            'partner_has_wia' => 0,
        ];
        $existingIncome = $incomeModel->getByUserId($userId);
        if ($existingIncome) {
            $incomeModel->update($existingIncome['id'], $incomeData);
        } else {
            $incomeModel->insert($incomeData);
        }

        $expenseModel = new ExpenseModel();
        $expenseData = ['user_id' => $userId];
        foreach (ExpenseEstimator::fields() as $field) {
            $raw = str_replace(',', '.', (string) $this->request->getPost($field));
            $expenseData[$field] = is_numeric($raw) ? max(0, (float) $raw) : 0;
        }
        $existingExpense = $expenseModel->getByUserId($userId);
        if ($existingExpense) {
            $expenseModel->update($existingExpense['id'], $expenseData);
        } else {
            $expenseModel->insert($expenseData);
        }

        $this->saveStartAndProperty($userId);

        return redirect()->to('/dashboard')->with('success', 'Je startgegevens staan erin. Je kunt alles later nog aanpassen.');
    }

    private function saveStartAndProperty(int $userId): void
    {
        $sells = $this->request->getPost('sells_house') === '1';
        $sale = $sells ? $this->parseMoney('house_sale_price') : 0.0;
        $hasMortgage = $sells && $this->request->getPost('has_mortgage') === '1';
        $mortgage = $hasMortgage ? $this->parseMoney('mortgage_debt') : 0.0;
        $savings = $this->parseMoney('savings');

        $startModel = new StartPositionModel();
        $existingStart = $startModel->getByUserId($userId);
        $startData = [
            'user_id' => $userId,
            'house_sale_price' => $sale,
            'mortgage_debt' => $mortgage,
            'savings' => $savings,
            'selling_costs_percent' => $existingStart['selling_costs_percent'] ?? 0,
            'moving_costs' => $existingStart['moving_costs'] ?? 0,
            'interest_rate' => $existingStart['interest_rate'] ?? 2.00,
            'inflation_rate' => $existingStart['inflation_rate'] ?? 2.00,
        ];
        if ($existingStart) {
            $startModel->update($existingStart['id'], $startData);
        } else {
            $startModel->insert($startData);
        }

        if ($this->request->getPost('buys_italy') !== '1') {
            return;
        }

        $price = $this->parseMoney('purchase_price');
        $propertyModel = new PropertyModel();
        $existingMain = $propertyModel->getMainProperty($userId);
        $propertyData = [
            'user_id' => $userId,
            'property_type' => 'main',
            'purchase_price' => $price,
            'purchase_costs_percentage' => $existingMain['purchase_costs_percentage'] ?? 10,
        ];
        if ($existingMain) {
            $propertyModel->update($existingMain['id'], $propertyData);
            return;
        }

        $propertyData['annual_costs'] = 0;
        $propertyData['maintenance_yearly'] = 0;
        $propertyData['energy_monthly'] = 0;
        $propertyData['other_monthly_costs'] = 0;
        $propertyModel->insert($propertyData);
    }

    private function parseMoney(string $field): float
    {
        $raw = str_replace(',', '.', (string) $this->request->getPost($field));

        return is_numeric($raw) ? max(0, (float) $raw) : 0.0;
    }
}
