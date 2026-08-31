<?php

namespace App\Libraries;

use App\Models\ExpenseModel;
use App\Models\UserProfileModel;

class SetupService
{
    private static bool $schemaChecked = false;

    public function needsSetup(int $userId): bool
    {
        if ($userId < 1) {
            return false;
        }

        try {
            $this->ensureSchema();
            $db = \Config\Database::connect();
            $profile = (new UserProfileModel())->where('user_id', $userId)->first();
            if (!$profile) {
                return true;
            }

            if ($db->fieldExists('setup_completed', 'user_profiles')) {
                return empty($profile['setup_completed']);
            }

            return $this->looksIncomplete($profile, $userId);
        } catch (\Throwable $e) {
            log_message('error', 'Setup check failed: ' . $e->getMessage());
            return true;
        }
    }

    public function markComplete(int $userId): void
    {
        $this->ensureSchema();
        $model = new UserProfileModel();
        $profile = $model->where('user_id', $userId)->first();
        $data = ['setup_completed' => 1];
        if ($profile) {
            $model->update($profile['id'], $data);
            return;
        }

        $model->insert([
            'user_id' => $userId,
            'language' => 'nl',
            'setup_completed' => 1,
        ]);
    }

    /**
     * Voegt setup-kolommen toe als de migratie nog niet is gedraaid,
     * en markeert bestaande accounts met geboortedatum of lasten als klaar.
     */
    public function ensureSchema(): void
    {
        if (self::$schemaChecked) {
            return;
        }
        self::$schemaChecked = true;

        try {
            $db = \Config\Database::connect();
            if (!$db->tableExists('user_profiles')) {
                return;
            }

            $forge = \Config\Database::forge();
            $addedCompleted = false;

            if (!$db->fieldExists('setup_completed', 'user_profiles')) {
                $forge->addColumn('user_profiles', [
                    'setup_completed' => [
                        'type' => 'TINYINT',
                        'constraint' => 1,
                        'default' => 0,
                        'null' => false,
                    ],
                ]);
                $addedCompleted = true;
            }
            if (!$db->fieldExists('children_count', 'user_profiles')) {
                $forge->addColumn('user_profiles', [
                    'children_count' => [
                        'type' => 'TINYINT',
                        'constraint' => 2,
                        'unsigned' => true,
                        'default' => 0,
                        'null' => false,
                    ],
                ]);
            }
            if (!$db->fieldExists('cars_count', 'user_profiles')) {
                $forge->addColumn('user_profiles', [
                    'cars_count' => [
                        'type' => 'TINYINT',
                        'constraint' => 2,
                        'unsigned' => true,
                        'default' => 0,
                        'null' => false,
                    ],
                ]);
            }

            if ($addedCompleted) {
                $this->grandfatherExisting($db);
            }
        } catch (\Throwable $e) {
            log_message('error', 'Setup schema check failed: ' . $e->getMessage());
        }
    }

    /**
     * @param array<string, mixed> $user
     */
    public function redirectAfterAuth(array $user): string
    {
        $userId = (int) ($user['id'] ?? 0);
        if (($user['role'] ?? '') !== 'admin') {
            try {
                if (!(new BillingService())->hasAccess($userId)) {
                    return '/subscription';
                }
            } catch (\Throwable $e) {
                log_message('error', 'Billing check after auth failed: ' . $e->getMessage());
            }
        }

        if ($this->needsSetup($userId)) {
            return '/setup';
        }

        return '/dashboard';
    }

    private function grandfatherExisting($db): void
    {
        if ($db->fieldExists('date_of_birth', 'user_profiles')) {
            $db->query("UPDATE user_profiles SET setup_completed = 1 WHERE date_of_birth IS NOT NULL AND date_of_birth != '' AND date_of_birth != '0000-00-00'");
        }
        if ($db->tableExists('expenses')) {
            $db->query('UPDATE user_profiles SET setup_completed = 1 WHERE user_id IN (SELECT user_id FROM expenses)');
        }
    }

    /**
     * @param array<string, mixed> $profile
     */
    private function looksIncomplete(array $profile, int $userId): bool
    {
        $dob = trim((string) ($profile['date_of_birth'] ?? ''));
        if ($dob !== '' && $dob !== '0000-00-00') {
            return false;
        }

        try {
            return empty((new ExpenseModel())->getByUserId($userId));
        } catch (\Throwable $e) {
            return true;
        }
    }
}
