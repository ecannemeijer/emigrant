<?php

namespace App\Models;

use CodeIgniter\Model;

class RenovationCategoryModel extends Model
{
    protected $table            = 'renovation_categories';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = ['user_id', 'name', 'sort_order'];
    protected $useTimestamps    = true;
    protected $dateFormat       = 'datetime';
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    public function forUser(int $userId): array
    {
        return $this->where('user_id', $userId)->orderBy('sort_order', 'ASC')->orderBy('id', 'ASC')->findAll();
    }
}
