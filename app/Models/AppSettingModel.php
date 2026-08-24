<?php

namespace App\Models;

use CodeIgniter\Model;

class AppSettingModel extends Model
{
    protected $table            = 'app_settings';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = ['setting_key', 'setting_value'];
    protected $useTimestamps    = true;
    protected $dateFormat       = 'datetime';
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    public function getValue(string $key, ?string $default = null): ?string
    {
        $row = $this->where('setting_key', $key)->first();

        return $row['setting_value'] ?? $default;
    }

    public function setValue(string $key, string $value): void
    {
        $row = $this->where('setting_key', $key)->first();
        if ($row) {
            $this->update($row['id'], ['setting_value' => $value]);
            return;
        }

        $this->insert([
            'setting_key' => $key,
            'setting_value' => $value,
        ]);
    }
}
