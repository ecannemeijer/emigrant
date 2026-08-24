<?php

namespace App\Models;

use CodeIgniter\Model;

class BnbSettingModel extends Model
{
    protected $table            = 'bnb_settings';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'enabled',
        'number_of_rooms',
        'price_per_room_per_night',
        'occupancy_rate',
        'high_season_percentage',
        'low_season_percentage',
        'high_season_months',
        'low_season_months'
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

    public function calculateMonthlyRevenue($userId)
    {
        $settings = $this->getByUserId($userId);
        $calculator = new \App\Libraries\FinanceCalculator();
        $mapped = \App\Libraries\FinanceDataMapper::bnbSettings($settings);

        return $calculator->calculateBnbMonthlyRevenue($mapped);
    }

    public function calculateYearlyRevenue($userId)
    {
        return $this->calculateMonthlyRevenue($userId) * 12;
    }
}
