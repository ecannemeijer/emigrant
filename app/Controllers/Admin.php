<?php

namespace App\Controllers;

use App\Libraries\BillingService;
use App\Models\UserModel;
use App\Models\UserProfileModel;
use App\Models\AuditLogModel;

class Admin extends BaseController
{
    public function index()
    {
        return redirect()->to('/admin/users');
    }

    public function users()
    {
        $userModel = new UserModel();
        $subscriptions = [];
        try {
            $subscriptions = (new BillingService())->subscriptionsByUserId();
        } catch (\Throwable $e) {
            log_message('error', 'Could not load subscriptions: ' . $e->getMessage());
        }

        $data = [
            'title' => 'Gebruikersbeheer',
            'users' => $userModel->orderBy('id', 'ASC')->findAll(),
            'subscriptions' => $subscriptions,
        ];

        return view('admin/users', $data);
    }

    public function createUser()
    {
        $data = [
            'title' => 'Nieuwe gebruiker',
        ];

        return view('admin/create_user', $data);
    }

    public function storeUser()
    {
        $userModel = new UserModel();
        $profileModel = new UserProfileModel();

        $rules = [
            'username' => 'required|min_length[3]|max_length[50]|is_unique[users.username]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[8]',
            'role' => 'required|in_list[admin,user]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userData = [
            'username' => $this->request->getPost('username'),
            'email' => $this->request->getPost('email'),
            'password' => $this->request->getPost('password'),
            'role' => $this->request->getPost('role'),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
        ];

        $userId = $userModel->insertPrivileged($userData);

        if ($userId) {
            $profileModel->insert([
                'user_id' => $userId,
                'first_name' => $this->request->getPost('first_name'),
                'last_name' => $this->request->getPost('last_name'),
                'language' => 'nl',
            ]);

            try {
                (new BillingService())->grantComplimentaryYearIfBillingDisabled((int) $userId);
            } catch (\Throwable $e) {
                log_message('error', 'Complimentary subscription failed: ' . $e->getMessage());
            }

            return redirect()->to('/admin/users')->with('success', 'Gebruiker aangemaakt!');
        }

        return redirect()->back()->with('error', 'Er ging iets mis.');
    }

    public function editUser($userId)
    {
        $userModel = new UserModel();
        
        $user = $userModel->getUserWithProfile($userId);

        if (!$user) {
            return redirect()->to('/admin/users')->with('error', 'Gebruiker niet gevonden.');
        }

        $billing = new BillingService();
        $subscription = null;
        $payments = [];
        try {
            $subscription = $billing->getSubscription((int) $userId);
            $payments = $billing->paymentsForUser((int) $userId);
        } catch (\Throwable $e) {
            log_message('error', 'Could not load billing for user: ' . $e->getMessage());
        }

        $data = [
            'title' => 'Gebruiker bewerken',
            'user' => $user,
            'subscription' => $subscription,
            'payments' => $payments,
        ];

        return view('admin/edit_user', $data);
    }

    public function updateUser($userId)
    {
        $userModel = new UserModel();
        $profileModel = new UserProfileModel();

        $user = $userModel->find($userId);

        if (!$user) {
            return redirect()->to('/admin/users')->with('error', 'Gebruiker niet gevonden.');
        }

        $rules = [
            'username' => "required|min_length[3]|max_length[50]|is_unique[users.username,id,{$userId}]",
            'email' => "required|valid_email|is_unique[users.email,id,{$userId}]",
            'role' => 'required|in_list[admin,user]',
        ];
        if ($this->request->getPost('password')) {
            $rules['password'] = 'min_length[8]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $newRole = (string) $this->request->getPost('role');
        $newActive = $this->request->getPost('is_active') ? 1 : 0;
        $isCurrentlyAdmin = ($user['role'] ?? '') === 'admin' && !empty($user['is_active']);
        if ($isCurrentlyAdmin && ($newRole !== 'admin' || !$newActive) && $userModel->countActiveAdmins() <= 1) {
            return redirect()->back()->withInput()->with('error', 'Je kunt de laatste actieve admin niet demoten of deactiveren.');
        }

        $userData = [
            'username' => $this->request->getPost('username'),
            'email' => $this->request->getPost('email'),
            'role' => $newRole,
            'is_active' => $newActive,
        ];

        // Update password only if provided
        $newPassword = $this->request->getPost('password');
        if (!empty($newPassword)) {
            $userData['password'] = $newPassword;
        }

        $userModel->savePrivileged((int) $userId, $userData);

        // Update profile
        $profile = $profileModel->where('user_id', $userId)->first();
        $profileData = [
            'first_name' => $this->request->getPost('first_name'),
            'last_name' => $this->request->getPost('last_name'),
        ];

        if ($profile) {
            $profileModel->update($profile['id'], $profileData);
        }

        $startsRaw = trim((string) $this->request->getPost('subscription_starts_at'));
        $endsRaw = trim((string) $this->request->getPost('subscription_ends_at'));
        if ($startsRaw !== '' xor $endsRaw !== '') {
            return redirect()->back()->withInput()->with('error', 'Vul zowel start- als einddatum van het abonnement in.');
        }
        if ($startsRaw !== '' && $endsRaw !== '') {
            $startsAt = date('Y-m-d H:i:s', strtotime($startsRaw));
            $endsAt = date('Y-m-d H:i:s', strtotime($endsRaw));
            if ($startsAt && $endsAt && strtotime($endsAt) > 0) {
                try {
                    (new BillingService())->setManualSubscription(
                        (int) $userId,
                        $startsAt,
                        $endsAt,
                        (string) $this->request->getPost('subscription_plan') ?: 'year',
                        (string) $this->request->getPost('subscription_source') ?: 'admin'
                    );
                } catch (\Throwable $e) {
                    log_message('error', 'Admin subscription update failed: ' . $e->getMessage());
                    return redirect()->back()->withInput()->with('error', 'Gebruiker opgeslagen, maar abonnement kon niet worden bijgewerkt.');
                }
            }
        }

        return redirect()->to('/admin/users')->with('success', 'Gebruiker bijgewerkt!');
    }

    public function deleteUser($userId)
    {
        $userModel = new UserModel();

        // Prevent admin from deleting themselves
        if ($userId == session()->get('userId')) {
            return redirect()->to('/admin/users')->with('error', 'Je kunt jezelf niet verwijderen.');
        }

        $target = $userModel->find($userId);
        if ($target && ($target['role'] ?? '') === 'admin' && !empty($target['is_active']) && $userModel->countActiveAdmins() <= 1) {
            return redirect()->to('/admin/users')->with('error', 'Je kunt de laatste actieve admin niet verwijderen.');
        }

        $userModel->delete($userId);

        return redirect()->to('/admin/users')->with('success', 'Gebruiker verwijderd!');
    }

    public function auditLogs()
    {
        $auditModel = new AuditLogModel();
        $userModel  = new UserModel();

        $filterUserId = $this->request->getGet('user_id');
        $perPage      = 100;

        $result = $auditModel->getLogs($perPage, $filterUserId ?: null);

        $data = [
            'title'      => 'Audit Log',
            'logs'       => $result['logs'],
            'pager'      => $result['pager'],
            'users'      => $auditModel->getLoggedUsers(),
            'filterUser' => $filterUserId,
        ];

        return view('admin/audit_logs', $data);
    }

    public function clearAuditLogs()
    {
        $auditModel = new AuditLogModel();
        $auditModel->clearAll();

        return redirect()->to('/admin/audit-logs')->with('success', 'Audit log geleegd.');
    }

    public function deleteOldLogs()
    {
        $days = (int) ($this->request->getPost('days') ?? 90);
        $auditModel = new AuditLogModel();
        $deleted = $auditModel->deleteOlderThan($days);

        return redirect()->to('/admin/audit-logs')->with('success', "{$deleted} log regels ouder dan {$days} dagen verwijderd.");
    }

    public function config()
    {
        $billing = new BillingService();

        return view('admin/config', [
            'title' => 'Config',
            'settings' => $billing->getSettings(),
        ]);
    }

    public function saveConfig()
    {
        $rules = [
            'price_month' => 'required',
            'price_year' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $monthRaw = str_replace(',', '.', (string) $this->request->getPost('price_month'));
        $yearRaw = str_replace(',', '.', (string) $this->request->getPost('price_year'));

        if (!is_numeric($monthRaw) || !is_numeric($yearRaw) || (float) $monthRaw < 0 || (float) $yearRaw < 0) {
            return redirect()->back()->withInput()->with('error', 'Vul geldige prijzen in.');
        }

        $month = (float) $monthRaw;
        $year = (float) $yearRaw;

        $billing = new BillingService();
        $billing->setBillingEnabled((bool) $this->request->getPost('billing_enabled'));
        $billing->setPrices($month, $year);

        return redirect()->to('/admin/config')->with('success', 'Configuratie opgeslagen.');
    }

    public function payments()
    {
        $userId = $this->request->getGet('user_id');
        $payments = [];
        try {
            $billing = new BillingService();
            $payments = $billing->listPayments($userId ? (int) $userId : null);
        } catch (\Throwable $e) {
            log_message('error', 'Could not load payments: ' . $e->getMessage());
        }

        return view('admin/payments', [
            'title' => 'Betalingen',
            'payments' => $payments,
            'filterUser' => $userId,
            'users' => (new UserModel())->orderBy('username', 'ASC')->findAll(),
        ]);
    }
}
