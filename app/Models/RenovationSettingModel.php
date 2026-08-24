<?php

namespace App\Models;

use CodeIgniter\Model;

class RenovationSettingModel extends Model
{
    protected $table            = 'renovation_settings';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = ['user_id', 'contingency_percent'];
    protected $useTimestamps    = true;
    protected $dateFormat       = 'datetime';
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    public function getByUserId(int $userId): array
    {
        $row = $this->where('user_id', $userId)->first();
        if ($row) {
            return $row;
        }

        $this->insert([
            'user_id' => $userId,
            'contingency_percent' => 10,
        ]);

        return $this->where('user_id', $userId)->first() ?? [
            'user_id' => $userId,
            'contingency_percent' => 10,
        ];
    }
}
