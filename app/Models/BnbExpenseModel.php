<?php

namespace App\Models;

use CodeIgniter\Model;

class BnbExpenseModel extends Model
{
    protected $table            = 'bnb_expenses';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'extra_energy_water',
        'insurance',
        'cleaning',
        'linen_laundry',
        'breakfast_per_guest',
        'platform_commission',
        'marketing',
        'maintenance',
        'administration'
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

    public function getTotalMonthlyExpenses($userId, $grossRevenue = 0)
    {
        $expense = $this->getByUserId($userId);
        
        if (!$expense) {
            return 0;
        }

        $calculator = new \App\Libraries\FinanceCalculator();
        $settingsModel = new BnbSettingModel();
        $settings = \App\Libraries\FinanceDataMapper::bnbSettings($settingsModel->getByUserId($userId));
        $mapped = \App\Libraries\FinanceDataMapper::bnbExpenses($expense);
        $revenue = $grossRevenue > 0
            ? $grossRevenue
            : $calculator->calculateBnbMonthlyRevenue($settings);

        return $calculator->calculateBnbMonthlyExpenses($settings, $mapped, $revenue);
    }
}
