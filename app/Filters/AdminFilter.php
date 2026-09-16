<?php

namespace App\Filters;

use App\Libraries\Impersonation;
use App\Models\UserModel;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AdminFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Please login first.');
        }

        if ((new Impersonation())->isActive()) {
            return redirect()->to('/dashboard')->with('error', 'Je bent ingelogd als een gebruiker. Ga eerst terug naar je admin-account.');
        }

        $userId = (int) session()->get('userId');
        $user = $userId > 0 ? (new UserModel())->find($userId) : null;

        if (!$user || ($user['role'] ?? '') !== 'admin' || empty($user['is_active'])) {
            session()->destroy();
            return redirect()->to('/login')->with('error', 'Access denied.');
        }

        session()->set('role', 'admin');
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
