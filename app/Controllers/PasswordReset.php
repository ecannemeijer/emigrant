<?php

namespace App\Controllers;

use App\Libraries\RequestThrottle;
use App\Models\UserModel;
use CodeIgniter\I18n\Time;

class PasswordReset extends BaseController
{
    public function forgot()
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/dashboard');
        }

        return view('auth/forgot_password', ['title' => 'Wachtwoord vergeten']);
    }

    public function sendResetLink()
    {
        $email = trim((string) $this->request->getPost('email'));

        if (!RequestThrottle::allow('password-reset', 5, HOUR, $email !== '' ? $email : null)) {
            return redirect()->to('/password-reset/forgot')->with('success', $this->successMessage());
        }

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect()->back()->withInput()->with('error', 'Voer een geldig e-mailadres in.');
        }

        $user = (new UserModel())->where('email', $email)->first();
        if ($user) {
            $this->createAndSendToken($user);
        }

        return redirect()->to('/password-reset/forgot')->with('success', $this->successMessage());
    }

    public function reset($token = null)
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/dashboard');
        }

        $token = is_string($token) ? $token : '';
        if ($token === '' || !$this->findValidReset($token)) {
            return redirect()->to('/password-reset/forgot')->with('error', 'Deze resetlink is verlopen of ongeldig. Vraag een nieuwe aan.');
        }

        return view('auth/reset_password', [
            'title' => 'Wachtwoord wijzigen',
            'token' => $token,
        ]);
    }

    public function updatePassword()
    {
        $token = (string) $this->request->getPost('token');
        $rules = [
            'password' => 'required|min_length[8]',
            'password_confirm' => 'required|matches[password]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $validReset = $this->findValidReset($token);
        if (!$validReset) {
            return redirect()->to('/password-reset/forgot')->with('error', 'Deze resetlink is verlopen of ongeldig. Vraag een nieuwe aan.');
        }

        $userModel = new UserModel();
        $user = $userModel->where('email', $validReset['email'])->first();
        if (!$user) {
            return redirect()->to('/login')->with('error', 'Gebruiker niet gevonden.');
        }

        $userModel->update($user['id'], [
            'password' => (string) $this->request->getPost('password'),
        ]);

        $this->resetsTable()->where('email', $validReset['email'])->delete();

        return redirect()->to('/login')->with('success', 'Wachtwoord gewijzigd. Je kunt nu inloggen.');
    }

    private function successMessage(): string
    {
        return 'Als dit e-mailadres bij een account hoort, ontvang je binnen enkele minuten een link om je wachtwoord te wijzigen.';
    }

    private function createAndSendToken(array $user): void
    {
        $email = $user['email'];
        $token = bin2hex(random_bytes(32));

        try {
            $table = $this->resetsTable();
            $table->where('email', $email)->delete();
            $table->insert([
                'email' => $email,
                'token' => hash('sha256', $token),
                'created_at' => Time::now()->toDateTimeString(),
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Password reset token store failed: ' . $e->getMessage());
            return;
        }

        try {
            $emailService = \Config\Services::email();
            $emailService->setFrom($emailService->fromEmail, $emailService->fromName);
            $emailService->setTo($email);
            $emailService->setSubject('Wachtwoord wijzigen — Emigrant');
            $emailService->setMailType('html');

            $resetLink = base_url('password-reset/reset/' . $token);
            $emailService->setMessage(view('emails/password_reset', [
                'username' => $user['username'] ?? $email,
                'resetLink' => $resetLink,
            ]));
            $emailService->setAltMessage(view('emails/password_reset_text', [
                'username' => $user['username'] ?? $email,
                'resetLink' => $resetLink,
            ]));

            if (!$emailService->send()) {
                log_message('error', 'Password reset email failed: ' . $emailService->printDebugger(['headers', 'subject']));
            }
        } catch (\Throwable $e) {
            log_message('error', 'Failed to send password reset email: ' . $e->getMessage());
        }
    }

    private function findValidReset(string $token): ?array
    {
        if ($token === '' || !preg_match('/^[a-f0-9]{64}$/', $token)) {
            return null;
        }

        try {
            $row = $this->resetsTable()
                ->where('token', hash('sha256', $token))
                ->where('created_at >', Time::now()->subHours(1)->toDateTimeString())
                ->get()
                ->getRowArray();
        } catch (\Throwable $e) {
            log_message('error', 'Password reset lookup failed: ' . $e->getMessage());
            return null;
        }

        return $row ?: null;
    }

    private function resetsTable()
    {
        return \Config\Database::connect()->table('password_resets');
    }
}
