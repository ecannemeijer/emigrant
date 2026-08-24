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
            'profile' => (new \App\Models\UserProfileModel())->where('user_id', $userId)->first(),
            'renovation_settings' => (new \App\Models\RenovationSettingModel())->where('user_id', $userId)->first(),
            'renovation_items' => (new \App\Models\RenovationItemModel())->forUser($userId),
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

    public function restore($scenarioId)
    {
        $userId = session()->get('userId');
        $model = new ScenarioModel();
        $scenario = $model->getScenario($scenarioId, $userId);
        if (!$scenario) {
            return redirect()->to('/scenarios')->with('error', 'Scenario niet gevonden.');
        }

        $payload = json_decode($scenario['data'], true) ?? [];
        $this->upsertRow(new StartPositionModel(), $userId, $payload['start_position'] ?? null);
        $this->upsertRow(new IncomeModel(), $userId, $payload['income'] ?? null);
        $this->upsertRow(new ExpenseModel(), $userId, $payload['expenses'] ?? null);
        $this->upsertRow(new TaxModel(), $userId, $payload['taxes'] ?? null);
        $this->upsertRow(new BnbSettingModel(), $userId, $payload['bnb_settings'] ?? null);
        $this->upsertRow(new BnbExpenseModel(), $userId, $payload['bnb_expenses'] ?? null);

        $propertyModel = new PropertyModel();
        if (!empty($payload['properties']) && is_array($payload['properties'])) {
            foreach ($payload['properties'] as $property) {
                if (empty($property['property_type'])) {
                    continue;
                }
                $existing = $property['property_type'] === 'second'
                    ? $propertyModel->getSecondProperty($userId)
                    : $propertyModel->getMainProperty($userId);
                unset($property['id']);
                $property['user_id'] = $userId;
                if ($existing) {
                    $propertyModel->update($existing['id'], $property);
                } else {
                    $propertyModel->insert($property);
                }
            }
        }

        return redirect()->to('/dashboard')->with('success', 'Scenario geladen als huidige situatie.');
    }

    public function compare()
    {
        $userId = session()->get('userId');
        $model = new ScenarioModel();
        $ids = array_filter([
            (int) $this->request->getGet('a'),
            (int) $this->request->getGet('b'),
        ]);

        $scenarios = $model->getUserScenarios($userId);
        $comparisons = [];
        $calculator = new \App\Libraries\FinanceCalculator();

        foreach ($ids as $id) {
            $scenario = $model->getScenario($id, $userId);
            if (!$scenario) {
                continue;
            }
            $payload = json_decode($scenario['data'], true) ?? [];
            $main = null;
            $second = null;
            foreach ($payload['properties'] ?? [] as $property) {
                if (($property['property_type'] ?? '') === 'second') {
                    $second = $property;
                } else {
                    $main = $property;
                }
            }
            $packed = \App\Libraries\FinanceDataMapper::pack(
                $payload['profile'] ?? [],
                $payload['start_position'] ?? [],
                $payload['income'] ?? [],
                $payload['expenses'] ?? [],
                $payload['taxes'] ?? [],
                $main,
                $second,
                $payload['bnb_settings'] ?? [],
                $payload['bnb_expenses'] ?? []
            );
            $analysis = $calculator->analyze($packed);
            $comparisons[] = [
                'scenario' => $scenario,
                'calculations' => $analysis['calculations'],
            ];
        }

        return view('scenarios/compare', [
            'title' => 'Scenario\'s vergelijken',
            'scenarios' => $scenarios,
            'comparisons' => $comparisons,
            'selected' => $ids,
        ]);
    }

    private function upsertRow($model, int $userId, ?array $row): void
    {
        if (!$row) {
            return;
        }
        unset($row['id']);
        $row['user_id'] = $userId;
        $existing = method_exists($model, 'getByUserId')
            ? $model->getByUserId($userId)
            : $model->where('user_id', $userId)->first();
        if ($existing) {
            $model->update($existing['id'], $row);
        } else {
            $model->insert($row);
        }
    }
}
