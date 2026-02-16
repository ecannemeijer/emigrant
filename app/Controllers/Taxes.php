<?php

namespace App\Controllers;

use App\Models\TaxModel;

class Taxes extends BaseController
{
    public function index()
    {
        $userId = session()->get('userId');
        $model = new TaxModel();
        
        $data = [
            'title' => 'Belastingen',
            'taxes' => $model->getByUserId($userId),
        ];

        return view('taxes/index', $data);
    }

    public function save()
    {
        $userId = session()->get('userId');
        $model = new TaxModel();

        $postData = [
            'user_id' => $userId,
            'forfettario_enabled' => $this->request->getPost('forfettario_enabled') ? 1 : 0,
            'forfettario_percentage' => $this->request->getPost('forfettario_percentage'),
            'normal_tax_percentage' => $this->request->getPost('normal_tax_percentage'),
            'imu_percentage' => $this->request->getPost('imu_percentage'),
            'tari_yearly' => $this->request->getPost('tari_yearly'),
            'social_contributions' => $this->request->getPost('social_contributions'),
            'road_tax_yearly' => $this->request->getPost('road_tax_yearly'),
        ];

        $existing = $model->getByUserId($userId);

        if ($existing) {
            $model->update($existing['id'], $postData);
        } else {
            $model->insert($postData);
        }

        return redirect()->to('/taxes')->with('success', 'Belastinggegevens opgeslagen!');
    }
}
