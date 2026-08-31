<?php

namespace App\Controllers;

use App\Libraries\ExpenseEstimator;
use App\Libraries\SetupService;
use App\Models\ExpenseModel;
use App\Models\IncomeModel;
use App\Models\UserProfileModel;

class Setup extends BaseController
{
    public function index()
    {
        $userId = (int) session()->get('userId');
        $profile = (new UserProfileModel())->where('user_id', $userId)->first() ?? [];
        $income = (new IncomeModel())->getByUserId($userId) ?? [];
        $savedExpenses = (new ExpenseModel())->getByUserId($userId) ?? [];

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

        return redirect()->to('/dashboard')->with('success', 'Je startgegevens staan erin. Je kunt alles later nog aanpassen.');
    }
}
