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

        $ownType = $this->request->getPost('own_benefit_type') ?: 'none';
        $partnerType = $this->request->getPost('partner_benefit_type') ?: 'wia';
        $hasPartner = $this->request->getPost('has_partner') ? 1 : 0;
        $ownOther = (float) $this->request->getPost('own_other_income');
        $partnerOther = (float) $this->request->getPost('partner_other_income');

        $postData = [
            'user_id' => $userId,
            'wia_wife' => $this->request->getPost('wia_wife') ?: 0,
            'partner_has_wia' => $partnerType === 'wia' ? 1 : 0,
            'own_income' => $this->request->getPost('own_income') ?: 0,
            'aow_future' => $this->request->getPost('aow_future') ?: 0,
            'own_aow' => $this->request->getPost('own_aow') ?: 0,
            'aow_start_age' => $this->request->getPost('partner_aow_start_age') ?: null,
            'pension' => $this->request->getPost('pension') ?: 0,
            'pension_start_age' => $this->request->getPost('pension_start_age') ?: 67,
            'other_income' => $ownOther + ($hasPartner ? $partnerOther : 0),
            'minimum_monthly_income' => $this->request->getPost('minimum_monthly_income') ?: 0,
            'income_stops_at_retirement' => 1,
            'own_benefit_type' => $ownType,
            'partner_benefit_type' => $partnerType,
            'own_other_income' => $ownOther,
            'partner_other_income' => $hasPartner ? $partnerOther : 0,
            'own_aow_start_age' => $this->request->getPost('own_aow_start_age') ?: null,
            'partner_aow_start_age' => $this->request->getPost('partner_aow_start_age') ?: null,
        ];

        $existing = $model->getByUserId($userId);

        if ($existing) {
            $model->update($existing['id'], $postData);
        } else {
            $model->insert($postData);
        }

        $profileModel = new UserProfileModel();
        $profile = $profileModel->where('user_id', $userId)->first();
        if ($profile) {
            $profileModel->update($profile['id'], ['has_partner' => $hasPartner]);
        }

        return redirect()->to('/income')->with('success', 'Inkomsten opgeslagen!');
    }
}
