<?php

namespace App\Libraries;

use App\Models\UserModel;

class Impersonation
{
    public const SESSION_ADMIN_ID = 'impersonatorId';
    public const SESSION_ADMIN_NAME = 'impersonatorUsername';

    public function isActive(): bool
    {
        $adminId = (int) session()->get(self::SESSION_ADMIN_ID);
        if ($adminId < 1) {
            return false;
        }

        return $adminId !== (int) session()->get('userId');
    }

    /**
     * @param array<string, mixed> $admin
     * @param array<string, mixed> $target
     */
    public function start(array $admin, array $target): void
    {
        $this->applySession($target, [
            self::SESSION_ADMIN_ID   => (int) $admin['id'],
            self::SESSION_ADMIN_NAME => (string) ($admin['username'] ?? $admin['email'] ?? ''),
        ]);
    }

    /**
     * @return array<string, mixed>|null
     */
    public function stop(): ?array
    {
        $adminId = (int) session()->get(self::SESSION_ADMIN_ID);
        if ($adminId < 1) {
            return null;
        }

        $admin = (new UserModel())->find($adminId);
        if (!is_array($admin) || ($admin['role'] ?? '') !== 'admin' || empty($admin['is_active'])) {
            return null;
        }

        $this->applySession($admin);

        return $admin;
    }

    /**
     * @param array<string, mixed> $user
     * @param array<string, mixed> $extra
     */
    private function applySession(array $user, array $extra = []): void
    {
        session()->regenerate(true);
        session()->remove([self::SESSION_ADMIN_ID, self::SESSION_ADMIN_NAME]);
        session()->set(array_merge([
            'userId'     => $user['id'],
            'username'   => $user['username'],
            'email'      => $user['email'],
            'role'       => $user['role'],
            'isLoggedIn' => true,
        ], $extra));
    }
}
