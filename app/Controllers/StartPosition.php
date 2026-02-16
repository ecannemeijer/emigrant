<?php

namespace App\Controllers;

use App\Models\StartPositionModel;

class StartPosition extends BaseController
{
    public function index()
    {
        $userId = session()->get('userId');
        $model = new StartPositionModel();
        
        $data = [
            'title' => 'Startpositie Nederland',
            'startPosition' => $model->getByUserId($userId),
        ];

        return view('start_position/index', $data);
    }

    public function save()
    {
        $userId = session()->get('userId');
        $model = new StartPositionModel();

        $postData = [
            'user_id' => $userId,
            'house_sale_price' => $this->request->getPost('house_sale_price'),
            'mortgage_debt' => $this->request->getPost('mortgage_debt'),
            'savings' => $this->request->getPost('savings'),            'interest_rate' => $this->request->getPost('interest_rate') ?? 2.00,        ];

        $existing = $model->getByUserId($userId);

        if ($existing) {
            $model->update($existing['id'], $postData);
        } else {
            $model->insert($postData);
        }

        return redirect()->to('/start-position')->with('success', 'Startpositie opgeslagen!');
    }
}
