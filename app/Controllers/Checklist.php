<?php

namespace App\Controllers;

use App\Models\ChecklistItemModel;

class Checklist extends BaseController
{
    public function index()
    {
        $userId = session()->get('userId');
        $model = new ChecklistItemModel();
        $model->ensureDefaults($userId);

        $items = $model->where('user_id', $userId)->orderBy('sort_order', 'ASC')->findAll();
        $grouped = [];
        foreach ($items as $item) {
            $grouped[$item['category']][] = $item;
        }

        $done = count(array_filter($items, static fn ($i) => (int) $i['done'] === 1));

        return view('checklist/index', [
            'title' => 'Emigratie-checklist',
            'grouped' => $grouped,
            'done' => $done,
            'total' => count($items),
        ]);
    }

    public function toggle($id)
    {
        $userId = session()->get('userId');
        $model = new ChecklistItemModel();
        $item = $model->where('id', $id)->where('user_id', $userId)->first();
        if ($item) {
            $model->update($id, ['done' => ((int) $item['done'] === 1) ? 0 : 1]);
        }

        return redirect()->to('/checklist');
    }

    public function note($id)
    {
        $userId = session()->get('userId');
        $model = new ChecklistItemModel();
        $item = $model->where('id', $id)->where('user_id', $userId)->first();
        if ($item) {
            $model->update($id, ['notes' => $this->request->getPost('notes')]);
        }

        return redirect()->to('/checklist')->with('success', 'Notitie opgeslagen.');
    }
}
