<?php

namespace App\Libraries;

use App\Models\AppSettingModel;

class MaintenanceService
{
    public function isEnabled(): bool
    {
        try {
            return (new AppSettingModel())->getValue('maintenance_mode', '0') === '1';
        } catch (\Throwable $e) {
            log_message('error', 'Maintenance check failed: ' . $e->getMessage());
            return false;
        }
    }

    public function setEnabled(bool $enabled): void
    {
        (new AppSettingModel())->setValue('maintenance_mode', $enabled ? '1' : '0');
    }
}
