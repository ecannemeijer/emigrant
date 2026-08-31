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
        if (session()->get('role') === 'admin') {
            return;
        }

        $userId = (int) session()->get('userId');
        if ($userId < 1 || !(new SetupService())->needsSetup($userId)) {
            return;
        }

        $path = trim(uri_string(), '/');
        if ($path === 'setup' || str_starts_with($path, 'setup/')) {
            return;
        }

        return redirect()->to('/setup');
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
