<?php

namespace App\Controllers;

class Contact extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Contact',
        ];

        return view('contact/index', $data);
    }

    public function send()
    {
        $rules = [
            'name' => 'required|min_length[3]|max_length[100]',
            'email' => 'required|valid_email',
            'subject' => 'required|min_length[3]|max_length[200]',
            'message' => 'required|min_length[10]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $name = $this->request->getPost('name');
        $email = $this->request->getPost('email');
        $subject = $this->request->getPost('subject');
        $message = $this->request->getPost('message');

        // Send email to support (e.cannemeijer@gmail.com)
        try {
            $emailService = \Config\Services::email();

            $emailService->setFrom($emailService->fromEmail, $emailService->fromName);
            $emailService->setReplyTo($email, $name);
            $emailService->setTo('e.cannemeijer@gmail.com');
            $emailService->setSubject("[Emigrant Contact] {$subject}");

            $emailMessage = view('emails/contact', [
                'name' => $name,
                'email' => $email,
                'subject' => $subject,
                'userMessage' => $message,
            ]);

            $emailService->setMessage($emailMessage);

            $textMessage = view('emails/contact_text', [
                'name' => $name,
                'email' => $email,
                'subject' => $subject,
                'userMessage' => $message,
            ]);

            $emailService->setAltMessage($textMessage);

            if ($emailService->send()) {
                log_message('info', "Contact form submitted by {$email}");
                return redirect()->to('/contact')->with('success', 'Je bericht is verzonden! We nemen zo spoedig mogelijk contact met je op.');
            } else {
                log_message('error', 'Contact form email failed: ' . $emailService->printDebugger(['headers', 'subject']));
                return redirect()->back()->withInput()->with('error', 'Er ging iets mis bij het verzenden. Probeer het later opnieuw.');
            }
        } catch (\Throwable $e) {
            log_message('error', 'Failed to send contact form email: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Er ging iets mis bij het verzenden. Probeer het later opnieuw.');
        }
    }
}
