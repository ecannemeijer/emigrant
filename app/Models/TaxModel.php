<?php

namespace App\Models;

use CodeIgniter\Model;

class TaxModel extends Model
{
    protected $table            = 'taxes';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'forfettario_enabled',
        'forfettario_percentage',
        'normal_tax_percentage',
        'imu_percentage',
        'tari_yearly',
        'social_contributions',
        'road_tax_yearly'
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
}
