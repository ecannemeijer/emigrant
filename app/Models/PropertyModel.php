<?php

namespace App\Models;

use CodeIgniter\Model;

class PropertyModel extends Model
{
    protected $table            = 'properties';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'property_type',
        'purchase_price',
        'purchase_costs_percentage',
        'purchase_costs',
        'annual_costs',
        'energy_monthly',
        'other_monthly_costs',
        'imu_tax',
        'tari_yearly',
        'maintenance_yearly',
        'rental_income'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $beforeInsert = ['calculatePurchaseCosts'];
    protected $beforeUpdate = ['calculatePurchaseCosts'];

    protected function calculatePurchaseCosts(array $data)
    {
        if (isset($data['data']['purchase_price']) && isset($data['data']['purchase_costs_percentage'])) {
            $data['data']['purchase_costs'] = 
                ($data['data']['purchase_price'] * $data['data']['purchase_costs_percentage']) / 100;
        }

        return $data;
    }

    public function getMainProperty($userId)
    {
        return $this->where('user_id', $userId)
                    ->where('property_type', 'main')
                    ->first();
    }

    public function getSecondProperty($userId)
    {
        return $this->where('user_id', $userId)
                    ->where('property_type', 'second')
                    ->first();
    }

    public function getUserProperties($userId)
    {
        return $this->where('user_id', $userId)->findAll();
    }
}
