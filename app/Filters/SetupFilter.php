<?php

namespace App\Filters;

use App\Libraries\SetupService;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class SetupFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!session()->get('isLoggedIn')) {
            return;
        }

        $userId = (int) session()->get('userId');
        if ($userId < 1) {
            return;
        }

        $path = trim((string) uri_string(), '/');
        if (str_starts_with($path, 'index.php/')) {
            $path = substr($path, strlen('index.php/'));
        }
        $path = trim($path, '/');
        if ($this->isExempt($path)) {
            return;
        }

        try {
            if (!(new SetupService())->needsSetup($userId)) {
                return;
            }
        } catch (\Throwable $e) {
            log_message('error', 'Setup filter failed: ' . $e->getMessage());
            return redirect()->to('/setup');
        }

        return redirect()->to('/setup');
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }

    private function isExempt(string $path): bool
    {
        if ($path === 'setup' || str_starts_with($path, 'setup/')) {
            return true;
        }

        $exact = [
            'login', 'register', 'logout', 'help', 'contact',
            'subscription', 'webhooks/paypal',
        ];
        if (in_array($path, $exact, true)) {
            return true;
        }

        $prefixes = [
            'subscription/',
            'password-reset/',
            'contact/',
            'webhooks/',
            'admin/',
        ];
        foreach ($prefixes as $prefix) {
            if (str_starts_with($path, $prefix)) {
                return true;
            }
        }

        return $path === 'admin';
    }
}
