<?php

namespace App\Controllers;

use App\Libraries\NoteSanitizer;
use App\Models\RenovationItemModel;
use App\Models\RenovationSettingModel;
use App\Models\StartPositionModel;

class Renovation extends BaseController
{
    public function index()
    {
        $userId = session()->get('userId');
        $itemsModel = new RenovationItemModel();
        $settingsModel = new RenovationSettingModel();
        $itemsModel->ensureTemplates($userId);

        $settings = $settingsModel->getByUserId($userId);
        $items = $itemsModel->forUser($userId);
        $totals = $itemsModel->totals($items, (float) ($settings['contingency_percent'] ?? 10));

        $start = (new StartPositionModel())->getByUserId($userId);
        $startingCapital = 0.0;
        if ($start) {
            $startingCapital = (new \App\Libraries\FinanceCalculator())->calculateStartingCapital($start);
        }

        $grouped = [];
        foreach ($items as $item) {
            $grouped[$item['room'] ?: 'Overig'][] = $item;
        }

        $notesById = [];
        foreach ($items as $item) {
            $notesById[(int) $item['id']] = $item['notes'] ?? '';
        }

        return view('renovation/index', [
            'title' => 'Verbouwen',
            'items' => $items,
            'grouped' => $grouped,
            'settings' => $settings,
            'totals' => $totals,
            'startingCapital' => $startingCapital,
            'notesById' => $notesById,
            'rooms' => RenovationItemModel::ROOMS,
            'statuses' => RenovationItemModel::STATUSES,
            'priorities' => RenovationItemModel::PRIORITIES,
        ]);
    }

    public function saveSettings()
    {
        $userId = session()->get('userId');
        $model = new RenovationSettingModel();
        $existing = $model->getByUserId($userId);
        $model->update($existing['id'], [
            'contingency_percent' => (float) ($this->request->getPost('contingency_percent') ?: 0),
        ]);

        return redirect()->to('/renovation')->with('success', 'Onvoorzien percentage opgeslagen.');
    }

    public function saveItem()
    {
        $userId = session()->get('userId');
        $model = new RenovationItemModel();
        $id = (int) $this->request->getPost('id');
        $data = $this->itemFromPost($userId);

        if ($id > 0) {
            $item = $model->where('id', $id)->where('user_id', $userId)->first();
            if ($item) {
                $model->update($id, $data);
            }
        } else {
            $last = $model->where('user_id', $userId)->orderBy('sort_order', 'DESC')->first();
            $data['sort_order'] = ((int) ($last['sort_order'] ?? 0)) + 10;
            $model->insert($data);
        }

        return redirect()->to('/renovation')->with('success', 'Verbouwpost opgeslagen.');
    }

    public function deleteItem($id)
    {
        $userId = session()->get('userId');
        $model = new RenovationItemModel();
        $item = $model->where('id', $id)->where('user_id', $userId)->first();
        if ($item) {
            $model->delete($id);
        }

        return redirect()->to('/renovation')->with('success', 'Verbouwpost verwijderd.');
    }

    public function note($id)
    {
        $userId = session()->get('userId');
        $model = new RenovationItemModel();
        $item = $model->where('id', $id)->where('user_id', $userId)->first();
        if ($item) {
            $model->update($id, ['notes' => NoteSanitizer::clean((string) $this->request->getPost('notes'))]);
        }

        return redirect()->to('/renovation')->with('success', 'Notitie opgeslagen.');
    }

    private function itemFromPost(int $userId): array
    {
        return [
            'user_id' => $userId,
            'title' => trim((string) $this->request->getPost('title')) ?: 'Nieuwe post',
            'room' => $this->request->getPost('room') ?: 'Overig',
            'status' => $this->request->getPost('status') ?: 'planned',
            'priority' => $this->request->getPost('priority') ?: 'medium',
            'estimated_cost' => (float) ($this->request->getPost('estimated_cost') ?: 0),
            'actual_cost' => (float) ($this->request->getPost('actual_cost') ?: 0),
            'vat_rate' => (float) ($this->request->getPost('vat_rate') ?: 10),
            'contractor' => trim((string) $this->request->getPost('contractor')) ?: null,
            'planned_year' => $this->request->getPost('planned_year') ?: null,
            'include_in_capital' => $this->request->getPost('include_in_capital') ? 1 : 0,
        ];
    }
}
