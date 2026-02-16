<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\UserProfileModel;

class Admin extends BaseController
{
    public function index()
    {
        return redirect()->to('/admin/users');
    }

    public function users()
    {
        $userModel = new UserModel();
        
        $data = [
            'title' => 'Gebruikersbeheer',
            'users' => $userModel->findAll(),
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

        $userId = $userModel->insert($userData);

        if ($userId) {
            $profileModel->insert([
                'user_id' => $userId,
                'first_name' => $this->request->getPost('first_name'),
                'last_name' => $this->request->getPost('last_name'),
                'language' => 'nl',
            ]);

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

        $data = [
            'title' => 'Gebruiker bewerken',
            'user' => $user,
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

        $userData = [
            'username' => $this->request->getPost('username'),
            'email' => $this->request->getPost('email'),
            'role' => $this->request->getPost('role'),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
        ];

        // Update password only if provided
        $newPassword = $this->request->getPost('password');
        if (!empty($newPassword)) {
            $userData['password'] = $newPassword;
        }

        $userModel->update($userId, $userData);

        // Update profile
        $profile = $profileModel->where('user_id', $userId)->first();
        $profileData = [
            'first_name' => $this->request->getPost('first_name'),
            'last_name' => $this->request->getPost('last_name'),
        ];

        if ($profile) {
            $profileModel->update($profile['id'], $profileData);
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

        $userModel->delete($userId);

        return redirect()->to('/admin/users')->with('success', 'Gebruiker verwijderd!');
    }
}
