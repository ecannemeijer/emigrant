<?php

namespace App\Models;

use CodeIgniter\Model;

class StartPositionModel extends Model
{
    protected $table            = 'start_positions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'house_sale_price',
        'mortgage_debt',
        'net_equity',
        'savings',
        'total_starting_capital',
        'interest_rate'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $beforeInsert = ['calculateTotals'];
    protected $beforeUpdate = ['calculateTotals'];

    protected function calculateTotals(array $data)
    {
        if (isset($data['data'])) {
            $houseSalePrice = $data['data']['house_sale_price'] ?? 0;
            $mortgageDebt = $data['data']['mortgage_debt'] ?? 0;
            $savings = $data['data']['savings'] ?? 0;

            $data['data']['net_equity'] = $houseSalePrice - $mortgageDebt;
            $data['data']['total_starting_capital'] = $data['data']['net_equity'] + $savings;
        }

        return $data;
    }

    public function getByUserId($userId)
    {
        return $this->where('user_id', $userId)->first();
    }
}
