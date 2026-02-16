<?php

namespace App\Controllers;

use App\Models\ExpenseModel;

class Expenses extends BaseController
{
    public function index()
    {
        $userId = session()->get('userId');
        $model = new ExpenseModel();
        
        $data = [
            'title' => 'Maandelijkse Lasten',
            'expenses' => $model->getByUserId($userId),
        ];

        return view('expenses/index', $data);
    }

    public function save()
    {
        $userId = session()->get('userId');
        $model = new ExpenseModel();

        $postData = [
            'user_id' => $userId,
            'energy' => $this->request->getPost('energy'),
            'water' => $this->request->getPost('water'),
            'internet' => $this->request->getPost('internet'),
            'health_insurance' => $this->request->getPost('health_insurance'),
            'car_insurance' => $this->request->getPost('car_insurance'),
            'car_fuel' => $this->request->getPost('car_fuel'),
            'car_maintenance' => $this->request->getPost('car_maintenance'),
            'groceries' => $this->request->getPost('groceries'),
            'leisure' => $this->request->getPost('leisure'),
            'unforeseen' => $this->request->getPost('unforeseen'),
            'other' => $this->request->getPost('other'),
        ];

        $existing = $model->getByUserId($userId);

        if ($existing) {
            $model->update($existing['id'], $postData);
        } else {
            $model->insert($postData);
        }

        return redirect()->to('/expenses')->with('success', 'Lasten opgeslagen!');
    }
}
