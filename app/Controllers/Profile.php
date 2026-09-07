<?php

namespace App\Controllers;

use App\Libraries\AccountPurge;
use App\Libraries\RequestThrottle;
use App\Models\UserModel;
use App\Models\UserProfileModel;

class Profile extends BaseController
{
    public function index()
    {
        $userId = (int) session()->get('userId');
        $model = new UserProfileModel();
        $users = new UserModel();
        $user = $users->find($userId) ?? [];

        $data = [
            'title' => 'Profiel',
            'profile' => $model->where('user_id', $userId)->first() ?? [],
            'canDeleteAccount' => !$this->purge()->isLastActiveAdmin($user, $users),
        ];

        return view('profile/index', $data);
    }

    public function update()
    {
        $userId = session()->get('userId');
        $model = new UserProfileModel();

        $postData = [
            'first_name' => $this->request->getPost('first_name'),
            'last_name' => $this->request->getPost('last_name'),
            'phone' => $this->request->getPost('phone'),
            'language' => $this->request->getPost('language'),
            'partner_name' => $this->request->getPost('partner_name'),
            'has_partner' => $this->request->getPost('has_partner') ? 1 : 0,
            'date_of_birth' => $this->request->getPost('date_of_birth') ?: null,
            'partner_date_of_birth' => $this->request->getPost('partner_date_of_birth') ?: null,
            'retirement_age' => $this->request->getPost('retirement_age') ?: 67,
            'partner_retirement_age' => $this->request->getPost('partner_retirement_age') ?: 67,
            'emigration_date' => $this->request->getPost('emigration_date') ?: null,
            'voluntary_aow_years' => $this->request->getPost('voluntary_aow_years') ?: 0,
            'children_count' => max(0, min(6, (int) $this->request->getPost('children_count'))),
            'cars_count' => max(0, min(2, (int) $this->request->getPost('cars_count'))),
        ];

        $existing = $model->where('user_id', $userId)->first();

        if ($existing) {
            $model->update($existing['id'], $postData);
        } else {
            $postData['user_id'] = $userId;
            $model->insert($postData);
        }

        return redirect()->to('/profile')->with('success', 'Profiel bijgewerkt!');
    }

    public function deleteAccount()
    {
        $userId = (int) session()->get('userId');
        if ($userId < 1) {
            return redirect()->to('/login');
        }

        if (!RequestThrottle::allow('account-delete', 5, HOUR, (string) $userId)) {
            return redirect()->to('/profile')->with('error', 'Te veel pogingen. Probeer het later opnieuw.');
        }

        $confirm = strtoupper(trim((string) $this->request->getPost('confirm')));
        if ($confirm !== 'VERWIJDEREN') {
            return redirect()->to('/profile')->with('error', 'Typ VERWIJDEREN om te bevestigen dat je je account wilt wissen.');
        }

        $users = new UserModel();
        $user = $users->find($userId);
        if (!is_array($user) || !password_verify((string) $this->request->getPost('password'), (string) ($user['password'] ?? ''))) {
            return redirect()->to('/profile')->with('error', 'Wachtwoord is onjuist.');
        }

        if ($this->purge()->isLastActiveAdmin($user, $users)) {
            return redirect()->to('/profile')->with('error', 'Je bent de laatste beheerder. Maak eerst een andere admin aan.');
        }

        try {
            $this->purge()->purge($userId);
        } catch (\Throwable $e) {
            log_message('error', 'Account purge failed: ' . $e->getMessage());

            return redirect()->to('/profile')->with('error', 'Account kon niet worden verwijderd. Probeer het later of neem contact op.');
        }

        session()->destroy();

        return redirect()->to('/login')->with('success', 'Je account en alle bijbehorende gegevens zijn verwijderd.');
    }

    private function purge(): AccountPurge
    {
        return new AccountPurge();
    }
}
