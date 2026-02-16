<?php

namespace App\Models;

use CodeIgniter\Model;

class ExpenseModel extends Model
{
    protected $table            = 'expenses';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'energy',
        'water',
        'internet',
        'health_insurance',
        'car_insurance',
        'car_fuel',
        'car_maintenance',
        'groceries',
        'leisure',
        'unforeseen',
        'other'
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

    public function getTotalMonthlyExpenses($userId)
    {
        $expense = $this->getByUserId($userId);
        
        if (!$expense) {
            return 0;
        }

        return ($expense['energy'] ?? 0) +
               ($expense['water'] ?? 0) +
               ($expense['internet'] ?? 0) +
               ($expense['health_insurance'] ?? 0) +
               ($expense['car_insurance'] ?? 0) +
               ($expense['car_fuel'] ?? 0) +
               ($expense['car_maintenance'] ?? 0) +
               ($expense['groceries'] ?? 0) +
               ($expense['leisure'] ?? 0) +
               ($expense['unforeseen'] ?? 0) +
               ($expense['other'] ?? 0);
    }
}
