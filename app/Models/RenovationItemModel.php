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
        'planned_year', 'include_in_capital', 'notes', 'sort_order',
    ];
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public const ROOMS = [
        'Dak & constructie',
        'Gevel & isolatie',
        'Elektra',
        'Water & sanitair',
        'Keuken',
        'Badkamer',
        'Vloeren & afwerking',
        'Ramen & deuren',
        'Tuin & buiten',
        'Vergunningen & extra',
        'Overig',
    ];

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
        return [
            ['room' => 'Dak & constructie', 'title' => 'Dakherstel / isolatie dak', 'priority' => 'high', 'sort_order' => 10],
            ['room' => 'Gevel & isolatie', 'title' => 'Gevelisolatie of voegwerk', 'priority' => 'high', 'sort_order' => 20],
            ['room' => 'Elektra', 'title' => 'Elektra vernieuwen / meterkast', 'priority' => 'high', 'sort_order' => 30],
            ['room' => 'Water & sanitair', 'title' => 'Leidingen en cv/warmtepomp', 'priority' => 'high', 'sort_order' => 40],
            ['room' => 'Keuken', 'title' => 'Keuken (meubels, apparatuur, tegels)', 'priority' => 'medium', 'sort_order' => 50],
            ['room' => 'Badkamer', 'title' => 'Badkamer 1', 'priority' => 'medium', 'sort_order' => 60],
            ['room' => 'Vloeren & afwerking', 'title' => 'Vloeren, stucwerk, verf', 'priority' => 'medium', 'sort_order' => 70],
            ['room' => 'Ramen & deuren', 'title' => 'Ramen (dubbel glas / infissi)', 'priority' => 'medium', 'sort_order' => 80],
            ['room' => 'Tuin & buiten', 'title' => 'Tuin, terras, hekwerk', 'priority' => 'low', 'sort_order' => 90],
            ['room' => 'Vergunningen & extra', 'title' => 'Vergunningen, geometra, onvoorzien', 'priority' => 'high', 'sort_order' => 100],
        ];
    }

    public function ensureTemplates(int $userId): void
    {
        if ($this->where('user_id', $userId)->countAllResults() > 0) {
            return;
        }
        foreach (self::templates() as $item) {
            $item['user_id'] = $userId;
            $item['status'] = 'planned';
            $item['estimated_cost'] = 0;
            $item['actual_cost'] = 0;
            $item['vat_rate'] = 10;
            $item['include_in_capital'] = 1;
            $this->insert($item);
        }
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
}
