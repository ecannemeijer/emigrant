<?php

namespace App\Models;

use App\Libraries\ChecklistGuide;
use CodeIgniter\Model;

class ChecklistItemModel extends Model
{
    protected $table            = 'checklist_items';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id', 'item_key', 'title', 'category', 'done', 'notes', 'sort_order',
    ];
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public static function defaults(): array
    {
        return ChecklistGuide::catalog();
    }

    public function ensureDefaults(int $userId): void
    {
        foreach (self::defaults() as $item) {
            $exists = $this->where('user_id', $userId)->where('item_key', $item['item_key'])->first();
            if (!$exists) {
                $item['user_id'] = $userId;
                $item['done'] = 0;
                $this->insert($item);
            } elseif (($exists['title'] ?? '') !== $item['title'] || ($exists['category'] ?? '') !== $item['category']) {
                $this->update($exists['id'], [
                    'title' => $item['title'],
                    'category' => $item['category'],
                    'sort_order' => $item['sort_order'],
                ]);
            }
        }
    }
}
