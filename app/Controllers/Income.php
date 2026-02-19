<?php

namespace App\Controllers;

use App\Models\IncomeModel;
use App\Models\UserProfileModel;

class Income extends BaseController
{
    public function index()
    {
        $userId = session()->get('userId');
        $model = new IncomeModel();
        $profileModel = new UserProfileModel();
        
        $data = [
            'title' => 'Inkomsten',
            'income' => $model->where('user_id', $userId)->first(),
            'profile' => $profileModel->where('user_id', $userId)->first(),
        ];

        return view('income/index', $data);
    }

    public function save()
    {
        $userId = session()->get('userId');
        $model = new IncomeModel();

        $postData = [
            'user_id' => $userId,
            'wia_wife' => $this->request->getPost('wia_wife'),
            'partner_has_wia' => $this->request->getPost('partner_has_wia') ? 1 : 0,
            'own_income' => $this->request->getPost('own_income'),
            'wao_future' => $this->request->getPost('wao_future'),
            'own_wao' => $this->request->getPost('own_wao') ?: 0,
            'wao_start_age' => $this->request->getPost('wao_start_age') ?: null,
            'pension' => $this->request->getPost('pension'),
            'pension_start_age' => $this->request->getPost('pension_start_age') ?: 67,
            'other_income' => $this->request->getPost('other_income'),
            'minimum_monthly_income' => $this->request->getPost('minimum_monthly_income') ?: 0,
        ];

        $existing = $model->getByUserId($userId);

        if ($existing) {
            $model->update($existing['id'], $postData);
        } else {
            $model->insert($postData);
        }

        return redirect()->to('/income')->with('success', 'Inkomsten opgeslagen!');
    }
}
