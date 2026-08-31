<?php

namespace App\Libraries;

use App\Models\UserProfileModel;

class SetupService
{
    public function needsSetup(int $userId): bool
    {
        if ($userId < 1) {
            return false;
        }

        try {
            $db = \Config\Database::connect();
            if (!$db->fieldExists('setup_completed', 'user_profiles')) {
                return false;
            }
            $profile = (new UserProfileModel())->where('user_id', $userId)->first();
            if (!$profile) {
                return true;
            }

            return empty($profile['setup_completed']);
        } catch (\Throwable $e) {
            log_message('error', 'Setup check failed: ' . $e->getMessage());
            return false;
        }
    }

    public function markComplete(int $userId): void
    {
        $model = new UserProfileModel();
        $profile = $model->where('user_id', $userId)->first();
        if ($profile) {
            $model->update($profile['id'], ['setup_completed' => 1]);
            return;
        }

        $model->insert([
            'user_id' => $userId,
            'language' => 'nl',
            'setup_completed' => 1,
        ]);
    }
}
