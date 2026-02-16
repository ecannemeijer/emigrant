<?php

namespace App\Models;

use CodeIgniter\Model;

class ScenarioModel extends Model
{
    protected $table            = 'scenarios';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'name',
        'description',
        'with_bnb',
        'with_second_property',
        'data'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getUserScenarios($userId)
    {
        return $this->where('user_id', $userId)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    public function getScenario($scenarioId, $userId)
    {
        return $this->where('id', $scenarioId)
                    ->where('user_id', $userId)
                    ->first();
    }
}
