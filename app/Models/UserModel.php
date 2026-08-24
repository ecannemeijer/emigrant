<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['username', 'email', 'password'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['hashPassword'];
    protected $beforeUpdate   = ['hashPassword'];

    protected function hashPassword(array $data)
    {
        if (!isset($data['data']['password'])) {
            return $data;
        }

        // Only hash if not already hashed (bcrypt hashes start with $2y$)
        if (strpos($data['data']['password'], '$2y$') !== 0) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        }
        
        return $data;
    }

    public function getUserWithProfile($userId)
    {
        return $this->select('users.*, user_profiles.first_name, user_profiles.last_name, user_profiles.phone, user_profiles.language')
            ->join('user_profiles', 'user_profiles.user_id = users.id', 'left')
            ->where('users.id', $userId)
            ->first();
    }

    public function countActiveAdmins(): int
    {
        return $this->where('role', 'admin')->where('is_active', 1)->countAllResults();
    }

    /**
     * @param array<string, mixed> $data
     */
    public function savePrivileged(int $userId, array $data): bool
    {
        $this->protect(false);
        $ok = $this->update($userId, $data);
        $this->protect(true);

        return (bool) $ok;
    }

    /**
     * @param array<string, mixed> $data
     * @return int|string|false
     */
    public function insertPrivileged(array $data)
    {
        $this->protect(false);
        $id = $this->insert($data);
        $this->protect(true);

        return $id;
    }
}
