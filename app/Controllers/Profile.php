<?php

namespace App\Controllers;

use App\Models\UserProfileModel;

class Profile extends BaseController
{
    public function index()
    {
        $userId = session()->get('userId');
        $model = new UserProfileModel();
        
        $data = [
            'title' => 'Profiel',
            'profile' => $model->where('user_id', $userId)->first(),
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
            'date_of_birth' => $this->request->getPost('date_of_birth') ?: null,
            'partner_date_of_birth' => $this->request->getPost('partner_date_of_birth') ?: null,
            'retirement_age' => $this->request->getPost('retirement_age') ?: 67,
            'partner_retirement_age' => $this->request->getPost('partner_retirement_age') ?: 67,
            'emigration_date' => $this->request->getPost('emigration_date') ?: null,
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
}
