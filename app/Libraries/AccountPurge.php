<?php

namespace App\Libraries;

use App\Models\UserModel;
use RuntimeException;

class AccountPurge
{
    /**
     * Delete a user and every row that belongs to that account.
     */
    public function purge(int $userId): void
    {
        if ($userId < 1) {
            throw new RuntimeException('Ongeldig gebruikers-id.');
        }

        $db = db_connect();
        $user = $db->table('users')->where('id', $userId)->get()->getRowArray();
        if (!$user) {
            return;
        }

        $email = (string) ($user['email'] ?? '');
        $db->transStart();
        $this->withoutForeignKeyChecks($db, function () use ($db, $userId, $email) {
            foreach ($db->listTables() as $table) {
                $name = $this->unprefixed($db, (string) $table);
                if (in_array($name, ['users', 'app_settings', 'migrations'], true)) {
                    continue;
                }

                try {
                    $fields = $db->getFieldNames($name);
                } catch (\Throwable $e) {
                    continue;
                }

                if (in_array('user_id', $fields, true)) {
                    $db->table($name)->where('user_id', $userId)->delete();
                }
            }

            if ($email !== '' && $db->tableExists('password_resets')) {
                $db->table('password_resets')->where('email', $email)->delete();
            }

            $db->table('users')->where('id', $userId)->delete();
        });
        $db->transComplete();

        if ($db->transStatus() === false) {
            throw new RuntimeException('Account kon niet worden verwijderd.');
        }
    }

    public function isLastActiveAdmin(array $user, UserModel $users): bool
    {
        return ($user['role'] ?? '') === 'admin'
            && !empty($user['is_active'])
            && $users->countActiveAdmins() <= 1;
    }

    /**
     * @param callable(): void $callback
     */
    private function withoutForeignKeyChecks($db, callable $callback): void
    {
        $driver = strtolower((string) $db->DBDriver);
        $disabled = false;
        if (in_array($driver, ['mysqli', 'mysql'], true)) {
            $db->query('SET FOREIGN_KEY_CHECKS = 0');
            $disabled = true;
        }

        try {
            $callback();
        } finally {
            if ($disabled) {
                $db->query('SET FOREIGN_KEY_CHECKS = 1');
            }
        }
    }

    private function unprefixed($db, string $table): string
    {
        $prefix = (string) $db->getPrefix();
        if ($prefix !== '' && str_starts_with($table, $prefix)) {
            return substr($table, strlen($prefix));
        }

        return $table;
    }
}
