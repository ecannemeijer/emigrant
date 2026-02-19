<?php

namespace App\Controllers;

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
        $userModel = new UserModel();
        
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

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

        session()->set([
            'userId' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'role' => $user['role'],
            'isLoggedIn' => true,
        ]);

        return redirect()->to('/dashboard')->with('success', 'Welkom terug, ' . $user['username'] . '!');
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
        $userModel = new UserModel();
        $profileModel = new UserProfileModel();

        $rules = [
            'username' => 'required|min_length[3]|max_length[50]|is_unique[users.username]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[8]',
            'password_confirm' => 'required|matches[password]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userData = [
            'username' => $this->request->getPost('username'),
            'email' => $this->request->getPost('email'),
            'password' => $this->request->getPost('password'),
            'role' => 'user',
            'is_active' => 1,
        ];

        $userId = $userModel->insert($userData);

        if ($userId) {
            // Create profile
            $profileModel->insert([
                'user_id' => $userId,
                'language' => 'nl',
            ]);

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
