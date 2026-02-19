<?php

namespace App\Models;

use CodeIgniter\Model;

class IncomeModel extends Model
{
    protected $table            = 'incomes';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'wia_wife',
        'partner_has_wia',
        'own_income',
        'aow_future',
        'own_aow',
        'aow_start_age',
        'pension',
        'pension_start_age',
        'other_income',
        'minimum_monthly_income'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getByUserId($userId)
    {
        return $this->where('user_id', $userId)->first();
    }

    public function getTotalMonthlyIncome($userId)
    {
        $income = $this->getByUserId($userId);
        
        if (!$income) {
            return 0;
        }

        return ($income['wia_wife'] ?? 0) +
               ($income['own_income'] ?? 0) +
               ($income['aow_future'] ?? 0) +
               ($income['pension'] ?? 0) +
               ($income['other_income'] ?? 0);
    }
}
