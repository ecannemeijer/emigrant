<?php

namespace App\Filters;

use App\Libraries\MaintenanceService;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class MaintenanceFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!(new MaintenanceService())->isEnabled()) {
            return;
        }

        if (session()->get('role') === 'admin') {
            return;
        }

        $path = trim($request->getUri()->getPath(), '/');
        if ($this->isAllowed($path)) {
            return;
        }

        return service('response')
            ->setStatusCode(503)
            ->setHeader('Retry-After', '3600')
            ->setBody(view('errors/maintenance', [
                'showLogout' => (bool) session()->get('isLoggedIn'),
            ]));
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }

    private function isAllowed(string $path): bool
    {
        $allowed = [
            'login',
            'logout',
            'favicon.ico',
            'favicon.svg',
            'favicon.png',
            'apple-touch-icon.png',
        ];
        if (in_array($path, $allowed, true)) {
            return true;
        }

        foreach (['admin', 'css/', 'js/', 'webhooks/paypal'] as $prefix) {
            if ($path === rtrim($prefix, '/') || str_starts_with($path, $prefix)) {
                return true;
            }
        }

        return false;
    }
}
