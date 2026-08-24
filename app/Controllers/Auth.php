<?php

namespace App\Controllers;

use App\Libraries\BillingService;
use App\Libraries\RequestThrottle;
use App\Models\UserModel;
use App\Models\UserProfileModel;

class Auth extends BaseController
{
    public function login()
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/dashboard');
        }

        return view('auth/login');
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

        session()->regenerate(true);
        session()->set([
            'userId' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'role' => $user['role'],
            'isLoggedIn' => true,
        ]);

        $target = '/dashboard';
        try {
            $billing = new BillingService();
            if ($user['role'] !== 'admin' && !$billing->hasAccess((int) $user['id'])) {
                $target = '/subscription';
            }
        } catch (\Throwable $e) {
            log_message('error', 'Billing check at login failed: ' . $e->getMessage());
        }

        return redirect()->to($target)->with('success', 'Welkom terug.');
    }

    public function register()
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/dashboard');
        }

        return view('auth/register');
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
            // Create profile
            $profileModel->insert([
                'user_id' => $userId,
                'language' => 'nl',
            ]);

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
                $emailService->setSubject('Welkom bij Emigrant — jouw account is aangemaakt');

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

            return redirect()->to('/login')->with('success', 'Account aangemaakt! Je kunt nu inloggen.');
        }

        return redirect()->back()->with('error', 'Er ging iets mis bij het aanmaken van je account.');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login')->with('success', 'Je bent uitgelogd.');
    }
}
