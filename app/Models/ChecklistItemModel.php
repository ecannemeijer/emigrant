<?php

namespace App\Models;

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
        return [
            ['item_key' => 'svb_aow', 'category' => 'Nederland', 'title' => 'SVB informeren / AOW-opbouw of vrijwillige verzekering', 'sort_order' => 10],
            ['item_key' => 'zorg_nl', 'category' => 'Nederland', 'title' => 'Zorgverzekering NL stopzetten of voortzetten', 'sort_order' => 20],
            ['item_key' => 'belasting_nl', 'category' => 'Nederland', 'title' => 'Belastingdienst: emigratieformulier M-formulier / C-formulier', 'sort_order' => 30],
            ['item_key' => 'gemeente_nl', 'category' => 'Nederland', 'title' => 'Uitschrijven BRP bij gemeente', 'sort_order' => 40],
            ['item_key' => 'codice_fiscale', 'category' => 'Italië', 'title' => 'Codice fiscale aanvragen', 'sort_order' => 50],
            ['item_key' => 'anagrafe', 'category' => 'Italië', 'title' => 'Inschrijving anagrafe / residenza', 'sort_order' => 60],
            ['item_key' => 'aire', 'category' => 'Italië', 'title' => 'AIRE-registratie bij consulaat', 'sort_order' => 70],
            ['item_key' => 'ssn', 'category' => 'Italië', 'title' => 'Tessera sanitaria / SSN inschrijving', 'sort_order' => 80],
            ['item_key' => 'bank_it', 'category' => 'Italië', 'title' => 'Italiaanse bankrekening', 'sort_order' => 90],
            ['item_key' => 'rijbewijs', 'category' => 'Vervoer', 'title' => 'Rijbewijs omwisselen of EU-rijbewijs geldig houden', 'sort_order' => 100],
            ['item_key' => 'auto_bollo', 'category' => 'Vervoer', 'title' => 'Auto importeren / bollo auto', 'sort_order' => 110],
            ['item_key' => 'partita_iva', 'category' => 'Ondernemen', 'title' => 'Partita IVA / forfettario (indien B&B)', 'sort_order' => 120],
        ];
    }

    public function ensureDefaults(int $userId): void
    {
        foreach (self::defaults() as $item) {
            $exists = $this->where('user_id', $userId)->where('item_key', $item['item_key'])->first();
            if (!$exists) {
                $item['user_id'] = $userId;
                $item['done'] = 0;
                $this->insert($item);
            }
        }
    }
}
