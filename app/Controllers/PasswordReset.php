<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\I18n\Time;

class PasswordReset extends BaseController
{
    public function forgot()
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/dashboard');
        }

        return view('auth/forgot_password');
    }

    public function sendResetLink()
    {
        $email = $this->request->getPost('email');

        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect()->back()->with('error', 'Voer een geldig e-mailadres in.');
        }

        $userModel = new UserModel();
        $user = $userModel->where('email', $email)->first();

        // Always show success message (prevent user enumeration)
        $successMessage = 'Als dit e-mailadres bij een account hoort, ontvang je binnen enkele minuten een wachtwoord reset link.';

        if (!$user) {
            // User doesn't exist, but don't reveal that
            return redirect()->to('/login')->with('success', $successMessage);
        }

        // Generate secure token
        $token = bin2hex(random_bytes(32));

        // Store token in database
        $db = \Config\Database::connect();
        
        // Delete old tokens for this email
        $db->table('password_resets')->where('email', $email)->delete();
        
        // Insert new token
        $db->table('password_resets')->insert([
            'email' => $email,
            'token' => password_hash($token, PASSWORD_DEFAULT),
            'created_at' => Time::now()->toDateTimeString(),
        ]);

        // Send reset email
        try {
            $emailService = \Config\Services::email();

            $emailService->setFrom($emailService->fromEmail, $emailService->fromName);
            $emailService->setTo($email);
            $emailService->setSubject('Wachtwoord resetten — Emigrant Platform');

            $resetLink = base_url("password-reset/reset/{$token}");

            $message = view('emails/password_reset', [
                'username' => $user['username'],
                'resetLink' => $resetLink,
            ]);

            $emailService->setMessage($message);

            $textMessage = view('emails/password_reset_text', [
                'username' => $user['username'],
                'resetLink' => $resetLink,
            ]);

            $emailService->setAltMessage($textMessage);

            if ($emailService->send()) {
                log_message('info', "Password reset email sent to {$email}");
            } else {
                log_message('error', 'Password reset email failed: ' . $emailService->printDebugger(['headers', 'subject']));
            }
        } catch (\Throwable $e) {
            log_message('error', 'Failed to send password reset email to ' . $email . ': ' . $e->getMessage());
        }

        return redirect()->to('/login')->with('success', $successMessage);
    }

    public function reset($token = null)
    {
        if (!$token) {
            return redirect()->to('/login')->with('error', 'Ongeldige reset link.');
        }

        $data = [
            'title' => 'Wachtwoord resetten',
            'token' => $token,
        ];

        return view('auth/reset_password', $data);
    }

    public function updatePassword()
    {
        $token = $this->request->getPost('token');
        $password = $this->request->getPost('password');
        $passwordConfirm = $this->request->getPost('password_confirm');

        // Validation
        $rules = [
            'password' => 'required|min_length[8]',
            'password_confirm' => 'required|matches[password]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Find valid token (created within last hour)
        $db = \Config\Database::connect();
        $resets = $db->table('password_resets')
            ->where('created_at >', Time::now()->subHours(1)->toDateTimeString())
            ->get()
            ->getResultArray();

        $validReset = null;
        foreach ($resets as $reset) {
            if (password_verify($token, $reset['token'])) {
                $validReset = $reset;
                break;
            }
        }

        if (!$validReset) {
            return redirect()->to('/password-reset/forgot')->with('error', 'Deze reset link is verlopen of ongeldig. Vraag een nieuw aan.');
        }

        // Update password
        $userModel = new UserModel();
        $user = $userModel->where('email', $validReset['email'])->first();

        if (!$user) {
            return redirect()->to('/login')->with('error', 'Gebruiker niet gevonden.');
        }

        $userModel->update($user['id'], [
            'password' => $password, // Will be hashed by beforeUpdate callback
        ]);

        // Delete used token
        $db->table('password_resets')->where('email', $validReset['email'])->delete();

        return redirect()->to('/login')->with('success', 'Wachtwoord succesvol gewijzigd! Je kunt nu inloggen.');
    }
}
