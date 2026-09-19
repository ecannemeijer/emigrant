<?php

namespace App\Models;

use CodeIgniter\Model;

class ExpenseItemModel extends Model
{
    protected $table            = 'expense_items';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'category',
        'name',
        'amount',
        'sort_order',
    ];
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public const CATEGORIES = [
        'subscription' => 'Abonnement',
        'insurance' => 'Verzekering',
        'phone' => 'Telefoon',
        'membership' => 'Lidmaatschap',
        'other' => 'Overig',
    ];

    public const CATEGORY_ICONS = [
        'subscription' => 'bi-tv',
        'insurance' => 'bi-shield-check',
        'phone' => 'bi-phone',
        'membership' => 'bi-person-badge',
        'other' => 'bi-tag',
    ];

    public const CATEGORY_BADGES = [
        'subscription' => 'bg-primary',
        'insurance' => 'bg-danger',
        'phone' => 'bg-success',
        'membership' => 'bg-warning text-dark',
        'other' => 'bg-secondary',
    ];

    public const CATEGORY_PLACEHOLDERS = [
        'subscription' => 'Netflix, Spotify, cloud',
        'insurance' => 'Inboedel, aansprakelijkheid',
        'phone' => 'Mobiel abonnement',
        'membership' => 'Sportschool, ANWB',
        'other' => 'Naam van de kostenpost',
    ];

    public function tableReady(): bool
    {
        try {
            return $this->db->tableExists($this->table);
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function forUser(int $userId): array
    {
        if ($userId < 1 || !$this->tableReady()) {
            return [];
        }

        return $this->where('user_id', $userId)
            ->orderBy('sort_order', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();
    }

    public function totalForUser(int $userId): float
    {
        $total = 0.0;
        foreach ($this->forUser($userId) as $item) {
            $total += (float) ($item['amount'] ?? 0);
        }

        return $total;
    }

    /**
     * @param list<array<string, mixed>> $items
     */
    public function replaceForUser(int $userId, array $items): void
    {
        if ($userId < 1 || !$this->tableReady()) {
            return;
        }

        $this->where('user_id', $userId)->delete();

        $order = 0;
        foreach ($items as $item) {
            if (!is_array($item)) {
                continue;
            }
            $name = trim((string) ($item['name'] ?? ''));
            $amount = (float) str_replace(',', '.', (string) ($item['amount'] ?? 0));
            $category = (string) ($item['category'] ?? 'other');
            if ($name === '' || $amount <= 0) {
                continue;
            }
            if (!isset(self::CATEGORIES[$category])) {
                $category = 'other';
            }
            if (function_exists('mb_substr')) {
                $name = mb_substr($name, 0, 120);
            } else {
                $name = substr($name, 0, 120);
            }

            $this->insert([
                'user_id' => $userId,
                'category' => $category,
                'name' => $name,
                'amount' => $amount,
                'sort_order' => $order++,
            ]);
        }
    }

    public static function categoryLabel(string $category): string
    {
        return self::CATEGORIES[$category] ?? self::CATEGORIES['other'];
    }
}
