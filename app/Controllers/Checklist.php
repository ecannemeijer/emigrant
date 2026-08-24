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

        $notesById = [];
        foreach ($items as $item) {
            $notesById[(int) $item['id']] = $item['notes'] ?? '';
        }

        return view('checklist/index', [
            'title' => 'Emigratie-checklist',
            'grouped' => $grouped,
            'done' => $done,
            'total' => count($items),
            'notesById' => $notesById,
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
            $model->update($id, ['notes' => $this->sanitizeNotes((string) $this->request->getPost('notes'))]);
        }

        return redirect()->to('/checklist')->with('success', 'Notitie opgeslagen.');
    }

    private function sanitizeNotes(string $html): string
    {
        $html = preg_replace('#<script\b[^>]*>.*?</script>#is', '', $html) ?? '';
        $html = preg_replace('/\son\w+="[^"]*"/i', '', $html) ?? '';
        $html = preg_replace("/\son\w+='[^']*'/i", '', $html) ?? '';
        $html = strip_tags($html, '<p><br><div><span><b><strong><i><em><u><ul><ol><li><a><h3><blockquote>');
        $html = preg_replace_callback('/<a\s+[^>]*href=["\']([^"\']+)["\'][^>]*>/i', static function ($m) {
            $href = $m[1];
            if (!preg_match('#^https?://#i', $href)) {
                return '<a>';
            }

            return '<a href="' . htmlspecialchars($href, ENT_QUOTES, 'UTF-8') . '" target="_blank" rel="noopener">';
        }, $html) ?? $html;

        return trim($html);
    }
}
