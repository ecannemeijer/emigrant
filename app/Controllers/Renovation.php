<?php

namespace App\Controllers;

use App\Libraries\NoteSanitizer;
use App\Models\RenovationCategoryModel;
use App\Models\RenovationItemModel;
use App\Models\RenovationSettingModel;
use App\Models\StartPositionModel;

class Renovation extends BaseController
{
    public function index()
    {
        return view('renovation/index', $this->pageData() + [
            'title' => 'Verbouwen',
            'returnTo' => 'list',
        ]);
    }

    public function planning()
    {
        return view('renovation/planning', $this->pageData() + [
            'title' => 'Planning',
            'returnTo' => 'planning',
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

        return redirect()->to($this->afterSave())->with('success', 'Onvoorzien percentage opgeslagen.');
    }

    public function saveCategory()
    {
        $userId = (int) session()->get('userId');
        $model = new RenovationCategoryModel();
        $id = (int) $this->request->getPost('id');
        $name = trim((string) $this->request->getPost('name'));
        if ($name === '') {
            return redirect()->to($this->afterSave())->with('error', 'Vul een categorienaam in.');
        }

        if ($id > 0) {
            $existing = $model->where('id', $id)->where('user_id', $userId)->first();
            if ($existing) {
                $oldName = $existing['name'];
                $model->update($id, ['name' => $name]);
                if ($oldName !== $name) {
                    (new RenovationItemModel())
                        ->where('user_id', $userId)
                        ->where('room', $oldName)
                        ->set(['room' => $name])
                        ->update();
                }
            }
            return redirect()->to($this->afterSave())->with('success', 'Categorie bijgewerkt.');
        }

        $last = $model->where('user_id', $userId)->orderBy('sort_order', 'DESC')->first();
        $model->insert([
            'user_id' => $userId,
            'name' => $name,
            'sort_order' => ((int) ($last['sort_order'] ?? 0)) + 10,
        ]);

        return redirect()->to($this->afterSave())->with('success', 'Categorie aangemaakt.');
    }

    public function deleteCategory($id)
    {
        $userId = (int) session()->get('userId');
        $model = new RenovationCategoryModel();
        $cat = $model->where('id', $id)->where('user_id', $userId)->first();
        if ($cat) {
            $model->delete($id);
        }

        return redirect()->to($this->afterSave())->with('success', 'Categorie verwijderd. Eventuele posten blijven bestaan.');
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

        return redirect()->to($this->afterSave())->with('success', 'Verbouwpost opgeslagen.');
    }

    public function deleteItem($id)
    {
        $userId = session()->get('userId');
        $model = new RenovationItemModel();
        $item = $model->where('id', $id)->where('user_id', $userId)->first();
        if ($item) {
            $model->delete($id);
        }

        return redirect()->to($this->afterSave())->with('success', 'Verbouwpost verwijderd.');
    }

    public function note($id)
    {
        $userId = session()->get('userId');
        $model = new RenovationItemModel();
        $item = $model->where('id', $id)->where('user_id', $userId)->first();
        if ($item) {
            $model->update($id, ['notes' => NoteSanitizer::clean((string) $this->request->getPost('notes'))]);
        }

        return redirect()->to($this->afterSave())->with('success', 'Notitie opgeslagen.');
    }

    private function pageData(): array
    {
        $userId = session()->get('userId');
        $itemsModel = new RenovationItemModel();
        $settingsModel = new RenovationSettingModel();
        $categoryModel = new RenovationCategoryModel();

        $settings = $settingsModel->getByUserId($userId);
        $items = $itemsModel->forUser($userId);
        $categories = [];
        try {
            $categories = $categoryModel->forUser($userId);
        } catch (\Throwable $e) {
            log_message('error', 'Renovation categories table missing: ' . $e->getMessage());
        }
        $totals = $itemsModel->totals($items, (float) ($settings['contingency_percent'] ?? 10));

        $start = (new StartPositionModel())->getByUserId($userId);
        $startingCapital = 0.0;
        if ($start) {
            $startingCapital = (new \App\Libraries\FinanceCalculator())->calculateStartingCapital($start);
        }

        $grouped = [];
        foreach ($categories as $cat) {
            $grouped[$cat['name']] = [];
        }
        foreach ($items as $item) {
            $name = $item['room'] ?: 'Overig';
            if (!isset($grouped[$name])) {
                $grouped[$name] = [];
            }
            $grouped[$name][] = $item;
        }

        $notesById = [];
        $calendarItems = [];
        foreach ($items as $item) {
            $notesById[(int) $item['id']] = $item['notes'] ?? '';
            $cal = $item;
            unset($cal['notes']);
            $calendarItems[] = $cal;
        }

        return [
            'items' => $items,
            'grouped' => $grouped,
            'settings' => $settings,
            'totals' => $totals,
            'startingCapital' => $startingCapital,
            'notesById' => $notesById,
            'calendarItems' => $calendarItems,
            'categories' => $categories,
            'rooms' => array_keys($grouped),
            'statuses' => RenovationItemModel::STATUSES,
            'priorities' => RenovationItemModel::PRIORITIES,
        ];
    }

    private function afterSave(): string
    {
        return $this->request->getPost('return_to') === 'planning'
            ? '/renovation/planning'
            : '/renovation';
    }

    private function itemFromPost(int $userId): array
    {
        $day = (int) $this->request->getPost('planned_day');
        $month = (int) $this->request->getPost('planned_month');
        $year = (int) $this->request->getPost('planned_year');
        $plannedDate = trim((string) $this->request->getPost('planned_date'));

        if ($year > 0 && $month > 0 && $day > 0 && checkdate($month, $day, $year)) {
            $plannedDate = sprintf('%04d-%02d-%02d', $year, $month, $day);
        } elseif (preg_match('/^\d{4}-\d{2}-\d{2}$/', $plannedDate)) {
            $year = (int) substr($plannedDate, 0, 4);
        } else {
            $plannedDate = null;
            $year = $year > 1900 ? $year : null;
        }

        return [
            'user_id' => $userId,
            'title' => trim((string) $this->request->getPost('title')) ?: 'Nieuwe post',
            'room' => trim((string) $this->request->getPost('room')) ?: 'Overig',
            'status' => $this->request->getPost('status') ?: 'planned',
            'priority' => $this->request->getPost('priority') ?: 'medium',
            'estimated_cost' => (float) ($this->request->getPost('estimated_cost') ?: 0),
            'actual_cost' => (float) ($this->request->getPost('actual_cost') ?: 0),
            'vat_rate' => (float) ($this->request->getPost('vat_rate') ?: 10),
            'contractor' => trim((string) $this->request->getPost('contractor')) ?: null,
            'planned_year' => $year,
            'planned_date' => $plannedDate,
            'include_in_capital' => $this->request->getPost('include_in_capital') ? 1 : 0,
        ];
    }
}
