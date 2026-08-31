<?php

namespace App\Controllers;

use App\Libraries\FinanceCalculator;
use App\Libraries\FinanceDataMapper;
use App\Models\StartPositionModel;
use App\Models\IncomeModel;
use App\Models\PropertyModel;
use App\Models\ExpenseModel;
use App\Models\TaxModel;
use App\Models\BnbSettingModel;
use App\Models\BnbExpenseModel;
use App\Models\UserProfileModel;
use App\Models\RenovationItemModel;
use App\Models\RenovationSettingModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $userId = session()->get('userId');
        try {
            if ((new \App\Libraries\SetupService())->needsSetup((int) $userId)) {
                return redirect()->to('/setup');
            }
        } catch (\Throwable $e) {
            return redirect()->to('/setup');
        }
        $result = $this->analyzeForUser($userId);
        $profileModel = new UserProfileModel();

        return view('dashboard/index', [
            'title' => 'Dashboard',
            'startPosition' => $result['raw']['start_position'],
            'calculations' => $result['calculations'],
            'yearlyProjections' => $result['yearlyProjections'],
            'warnings' => $result['warnings'],
            'profile' => $profileModel->where('user_id', $userId)->first(),
            'income' => $result['raw']['income'],
            'expenses' => $result['raw']['expenses'],
            'taxes' => $result['raw']['taxes'],
            'mainProperty' => $result['raw']['main_property'],
            'secondProperty' => $result['raw']['second_property'],
            'bnbSettings' => $result['raw']['bnb_settings'],
            'bnbExpenses' => $result['raw']['bnb_expenses'],
        ]);
    }

    public static function analyzeForUser(int $userId): array
    {
        $startPositionModel = new StartPositionModel();
        $incomeModel = new IncomeModel();
        $propertyModel = new PropertyModel();
        $expenseModel = new ExpenseModel();
        $taxModel = new TaxModel();
        $bnbSettingModel = new BnbSettingModel();
        $bnbExpenseModel = new BnbExpenseModel();
        $profileModel = new UserProfileModel();

        $raw = [
            'start_position' => $startPositionModel->getByUserId($userId),
            'income' => $incomeModel->getByUserId($userId),
            'expenses' => $expenseModel->getByUserId($userId),
            'taxes' => $taxModel->getByUserId($userId),
            'main_property' => $propertyModel->getMainProperty($userId),
            'second_property' => $propertyModel->getSecondProperty($userId),
            'bnb_settings' => $bnbSettingModel->getByUserId($userId),
            'bnb_expenses' => $bnbExpenseModel->getByUserId($userId),
            'profile' => $profileModel->where('user_id', $userId)->first(),
        ];

        $raw['start_position'] = $raw['start_position'] ?? [];
        $raw['start_position']['renovation_outlay'] = 0;
        $raw['start_position']['renovation_by_year'] = [];
        try {
            $renoSettings = (new RenovationSettingModel())->getByUserId($userId);
            $renoModel = new RenovationItemModel();
            $contingency = (float) ($renoSettings['contingency_percent'] ?? 10);
            $byYear = $renoModel->outlayByYear($userId, $contingency, (int) date('Y'));
            $raw['start_position']['renovation_by_year'] = $byYear;
            $raw['start_position']['renovation_outlay'] = array_sum($byYear);
        } catch (\Throwable $e) {
            // Tabellen nog niet gemigreerd.
        }

        $calculator = new FinanceCalculator();
        $packed = FinanceDataMapper::pack(
            $raw['profile'],
            $raw['start_position'],
            $raw['income'],
            $raw['expenses'],
            $raw['taxes'],
            $raw['main_property'],
            $raw['second_property'],
            $raw['bnb_settings'],
            $raw['bnb_expenses']
        );

        $analysis = $calculator->analyze($packed);

        return array_merge($analysis, ['raw' => $raw, 'packed' => $packed]);
    }
}
