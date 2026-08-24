<?php

namespace App\Models;

use CodeIgniter\Model;

class RenovationItemModel extends Model
{
    protected $table            = 'renovation_items';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id', 'title', 'room', 'status', 'priority',
        'estimated_cost', 'actual_cost', 'vat_rate', 'contractor',
        'planned_year', 'planned_date', 'include_in_capital', 'notes', 'sort_order',
    ];
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public const ROOMS = [];

    public const STATUSES = [
        'planned' => 'Gepland',
        'quoted' => 'Offerte',
        'in_progress' => 'Bezig',
        'done' => 'Klaar',
        'skipped' => 'Vervalt',
    ];

    public const PRIORITIES = [
        'high' => 'Hoog',
        'medium' => 'Midden',
        'low' => 'Laag',
    ];

    public static function templates(): array
    {
        return [];
    }

    public function ensureTemplates(int $userId): void
    {
        // Categorieën en posten start leeg; gebruiker maakt ze zelf.
    }

    public function forUser(int $userId): array
    {
        return $this->where('user_id', $userId)->orderBy('sort_order', 'ASC')->orderBy('id', 'ASC')->findAll();
    }

    public function lineCost(array $item): float
    {
        $actual = (float) ($item['actual_cost'] ?? 0);
        $estimated = (float) ($item['estimated_cost'] ?? 0);
        if (($item['status'] ?? '') === 'skipped') {
            return 0.0;
        }
        if ($actual > 0) {
            return $actual;
        }

        return $estimated;
    }

    public function totals(array $items, float $contingencyPercent = 10.0): array
    {
        $estimated = 0.0;
        $actual = 0.0;
        $open = 0.0;
        $openIncluded = 0.0;
        $capital = 0.0;
        $done = 0;
        $active = 0;

        foreach ($items as $item) {
            if (($item['status'] ?? '') === 'skipped') {
                continue;
            }
            $active++;
            $est = (float) ($item['estimated_cost'] ?? 0);
            $act = (float) ($item['actual_cost'] ?? 0);
            $estimated += $est;
            $actual += $act;
            $line = $this->lineCost($item);
            $isDone = ($item['status'] ?? '') === 'done' || $act > 0;
            if (($item['status'] ?? '') === 'done') {
                $done++;
            }
            if (!$isDone) {
                $open += $est;
            }
            if (!empty($item['include_in_capital'])) {
                $capital += $line;
                if (!$isDone) {
                    $openIncluded += $est;
                }
            }
        }

        $contingency = $open * ($contingencyPercent / 100);

        return [
            'estimated' => $estimated,
            'actual' => $actual,
            'open' => $open,
            'contingency' => $contingency,
            'forecast' => $actual + $open + $contingency,
            'capital' => $capital + $openIncluded * ($contingencyPercent / 100),
            'done' => $done,
            'active' => $active,
        ];
    }

    public function capitalOutlay(int $userId, float $contingencyPercent = 10.0): float
    {
        $totals = $this->totals($this->forUser($userId), $contingencyPercent);

        return (float) $totals['capital'];
    }

    public function plannedYear(array $item, int $fallbackYear): int
    {
        if (!empty($item['planned_date']) && preg_match('/^(\d{4})/', (string) $item['planned_date'], $m)) {
            return (int) $m[1];
        }
        if (!empty($item['planned_year'])) {
            return (int) $item['planned_year'];
        }

        return $fallbackYear;
    }

    public function includedAmount(array $item, float $contingencyPercent = 10.0): float
    {
        if (($item['status'] ?? '') === 'skipped' || empty($item['include_in_capital'])) {
            return 0.0;
        }
        $line = $this->lineCost($item);
        $isDone = ($item['status'] ?? '') === 'done' || (float) ($item['actual_cost'] ?? 0) > 0;
        if (!$isDone) {
            $line += (float) ($item['estimated_cost'] ?? 0) * ($contingencyPercent / 100);
        }

        return $line;
    }

    /**
     * @return array<int, float> year => amount from remaining capital
     */
    public function outlayByYear(int $userId, float $contingencyPercent, int $fallbackYear): array
    {
        $byYear = [];
        foreach ($this->forUser($userId) as $item) {
            $amount = $this->includedAmount($item, $contingencyPercent);
            if ($amount <= 0) {
                continue;
            }
            $year = $this->plannedYear($item, $fallbackYear);
            $byYear[$year] = ($byYear[$year] ?? 0.0) + $amount;
        }

        return $byYear;
    }
}
