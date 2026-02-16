<?php

namespace App\Controllers;

use App\Models\ScenarioModel;
use App\Models\StartPositionModel;
use App\Models\IncomeModel;
use App\Models\PropertyModel;
use App\Models\ExpenseModel;
use App\Models\TaxModel;
use App\Models\BnbSettingModel;
use App\Models\BnbExpenseModel;

class Scenarios extends BaseController
{
    public function index()
    {
        $userId = session()->get('userId');
        $model = new ScenarioModel();
        
        $data = [
            'title' => 'Scenario\'s',
            'scenarios' => $model->getUserScenarios($userId),
        ];

        return view('scenarios/index', $data);
    }

    public function save()
    {
        $userId = session()->get('userId');
        $model = new ScenarioModel();

        // Gather all current financial data
        $startPositionModel = new StartPositionModel();
        $incomeModel = new IncomeModel();
        $propertyModel = new PropertyModel();
        $expenseModel = new ExpenseModel();
        $taxModel = new TaxModel();
        $bnbSettingModel = new BnbSettingModel();
        $bnbExpenseModel = new BnbExpenseModel();

        $scenarioData = [
            'start_position' => $startPositionModel->getByUserId($userId),
            'income' => $incomeModel->getByUserId($userId),
            'properties' => $propertyModel->getUserProperties($userId),
            'expenses' => $expenseModel->getByUserId($userId),
            'taxes' => $taxModel->getByUserId($userId),
            'bnb_settings' => $bnbSettingModel->getByUserId($userId),
            'bnb_expenses' => $bnbExpenseModel->getByUserId($userId),
        ];

        $bnbEnabled = $scenarioData['bnb_settings']['enabled'] ?? 0;
        $hasSecondProperty = $propertyModel->getSecondProperty($userId) !== null;

        $postData = [
            'user_id' => $userId,
            'name' => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'with_bnb' => $bnbEnabled,
            'with_second_property' => $hasSecondProperty ? 1 : 0,
            'data' => json_encode($scenarioData),
        ];

        $model->insert($postData);

        return redirect()->to('/scenarios')->with('success', 'Scenario opgeslagen!');
    }

    public function load($scenarioId)
    {
        $userId = session()->get('userId');
        $model = new ScenarioModel();

        $scenario = $model->getScenario($scenarioId, $userId);

        if (!$scenario) {
            return redirect()->to('/scenarios')->with('error', 'Scenario niet gevonden.');
        }

        $data = [
            'title' => 'Scenario: ' . $scenario['name'],
            'scenario' => $scenario,
            'scenarioData' => json_decode($scenario['data'], true),
        ];

        return view('scenarios/view', $data);
    }

    public function delete($scenarioId)
    {
        $userId = session()->get('userId');
        $model = new ScenarioModel();

        $scenario = $model->getScenario($scenarioId, $userId);

        if ($scenario) {
            $model->delete($scenarioId);
            return redirect()->to('/scenarios')->with('success', 'Scenario verwijderd!');
        }

        return redirect()->to('/scenarios')->with('error', 'Scenario niet gevonden.');
    }
}
