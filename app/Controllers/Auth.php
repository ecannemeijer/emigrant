<?php

namespace App\Controllers;

use App\Libraries\BillingService;
use App\Libraries\MaintenanceService;
use App\Libraries\RequestThrottle;
use App\Libraries\SetupService;
use App\Models\UserModel;
use App\Models\UserProfileModel;

class Auth extends BaseController
{
    public function login()
    {
        if (session()->get('isLoggedIn')) {
            $user = (new UserModel())->find((int) session()->get('userId'));
            if (is_array($user)) {
                return redirect()->to((new SetupService())->redirectAfterAuth($user));
            }
            return redirect()->to('/dashboard');
        }

        $maintenance = false;
        try {
            $maintenance = (new MaintenanceService())->isEnabled();
        } catch (\Throwable $e) {
            $maintenance = false;
        }

        return view('auth/login', [
            'title' => 'Inloggen',
            'maintenance' => $maintenance,
        ]);
    }

    public function attemptLogin()
    {
        $email = (string) $this->request->getPost('email');
        $password = (string) $this->request->getPost('password');

        if (!RequestThrottle::allow('login', 5, MINUTE, $email)) {
            return redirect()->back()->with('error', 'Email of wachtwoord is onjuist.');
        }

        $userModel = new UserModel();
        $user = $userModel->where('email', $email)->first();

        if (!$user) {
            return redirect()->back()->with('error', 'Email of wachtwoord is onjuist.');
        }

        if (!password_verify($password, $user['password'])) {
            return redirect()->back()->with('error', 'Email of wachtwoord is onjuist.');
        }

        if (!$user['is_active']) {
            return redirect()->back()->with('error', 'Account is gedeactiveerd.');
        }

        try {
            if ((new MaintenanceService())->isEnabled() && ($user['role'] ?? '') !== 'admin') {
                return redirect()->back()->with('error', 'De site is in onderhoud. Probeer het later opnieuw.');
            }
        } catch (\Throwable $e) {
            // ga door met inloggen
        }

        session()->regenerate(true);
        session()->set([
            'userId' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'role' => $user['role'],
            'isLoggedIn' => true,
        ]);

        return redirect()->to((new SetupService())->redirectAfterAuth($user))->with('success', 'Welkom terug.');
    }

    public function register()
    {
        if (session()->get('isLoggedIn')) {
            $user = (new UserModel())->find((int) session()->get('userId'));
            if (is_array($user)) {
                return redirect()->to((new SetupService())->redirectAfterAuth($user));
            }
            return redirect()->to('/dashboard');
        }

        $freeMonth = true;
        try {
            $freeMonth = !(new BillingService())->isBillingEnabled();
        } catch (\Throwable $e) {
            $freeMonth = true;
        }

        return view('auth/register', [
            'title' => 'Account maken',
            'freeMonth' => $freeMonth,
        ]);
    }

    public function attemptRegister()
    {
        if (!RequestThrottle::allow('register', 10, HOUR)) {
            return redirect()->back()->withInput()->with('error', 'Te veel pogingen. Probeer het later opnieuw.');
        }
        $userModel = new UserModel();
        $profileModel = new UserProfileModel();

        $rules = [
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[8]',
            'password_confirm' => 'required|matches[password]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        
        $userData = [
            'username' => $email,
            'email' => $email,
            'password' => $password,
        ];

        $userId = $userModel->insert($userData);

        if ($userId) {
            try {
                (new SetupService())->ensureSchema();
            } catch (\Throwable $e) {
                log_message('error', 'Setup schema at register: ' . $e->getMessage());
            }
            $profileData = [
                'user_id' => $userId,
                'language' => 'nl',
            ];
            try {
                $db = \Config\Database::connect();
                if ($db->fieldExists('setup_completed', 'user_profiles')) {
                    $profileData['setup_completed'] = 0;
                }
            } catch (\Throwable $e) {
                // zonder kolom is default al "niet voltooid"
            }
            $profileModel->insert($profileData);

            try {
                (new BillingService())->grantComplimentaryYearIfBillingDisabled((int) $userId);
            } catch (\Throwable $e) {
                log_message('error', 'Complimentary subscription failed: ' . $e->getMessage());
            }

            // Send welcome email (best effort — failure does not block registration)
            try {
                $emailService = \Config\Services::email();

                $emailService->setFrom($emailService->fromEmail, $emailService->fromName);
                $emailService->setTo($userData['email']);
                $emailService->setSubject('Welkom bij EmigreerItalia — jouw account is aangemaakt');

                $emailData = [
                    'username' => $userData['username'],
                    'email' => $userData['email'],
                    'loginUrl' => base_url('login'),
                    'profileUrl' => base_url('profile'),
                    'supportEmail' => $emailService->fromEmail,
                ];

                // HTML version
                $htmlMessage = view('emails/welcome', $emailData);
                $emailService->setMessage($htmlMessage);
                
                // Plain text alternative
                $textMessage = view('emails/welcome_text', $emailData);
                $emailService->setAltMessage($textMessage);

                if ($emailService->send()) {
                    log_message('info', "Welcome email sent successfully to {$userData['email']}");
                } else {
                    log_message('error', 'Welcome email failed to send: ' . $emailService->printDebugger(['headers', 'subject', 'body']));
                }
            } catch (\Throwable $e) {
                log_message('error', 'Failed to send welcome email to ' . $userData['email'] . ': ' . $e->getMessage());
            }

            $user = $userModel->find($userId);
            if (!is_array($user)) {
                return redirect()->to('/login')->with('success', 'Account aangemaakt! Je kunt nu inloggen.');
            }

            session()->regenerate(true);
            session()->set([
                'userId' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email'],
                'role' => $user['role'],
                'isLoggedIn' => true,
            ]);

            return redirect()->to((new SetupService())->redirectAfterAuth($user))->with('success', 'Account aangemaakt. Eerst een paar vragen over je huishouden.');
        }

        return redirect()->back()->with('error', 'Er ging iets mis bij het aanmaken van je account.');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login')->with('success', 'Je bent uitgelogd.');
    }
}
