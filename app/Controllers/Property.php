<?php

namespace App\Controllers;

use App\Models\PropertyModel;

class Property extends BaseController
{
    public function index()
    {
        $userId = session()->get('userId');
        $model = new PropertyModel();
        
        $data = [
            'title' => 'Italiaans Vastgoed',
            'mainProperty' => $model->getMainProperty($userId),
            'secondProperty' => $model->getSecondProperty($userId),
        ];

        return view('property/index', $data);
    }

    public function save()
    {
        $userId = session()->get('userId');
        $model = new PropertyModel();

        // Main property
        $mainData = [
            'user_id' => $userId,
            'property_type' => 'main',
            'purchase_price' => $this->request->getPost('main_purchase_price'),
            'purchase_costs_percentage' => $this->request->getPost('main_purchase_costs_percentage'),
            'annual_costs' => $this->request->getPost('main_annual_costs'),
            'energy_monthly' => 0, // Energy for main property is in expenses page
            'other_monthly_costs' => 0, // Other costs for main property are in expenses page
            'tari_yearly' => $this->request->getPost('main_tari_yearly') ?? 0,
            'maintenance_yearly' => $this->request->getPost('main_maintenance_yearly'),
        ];

        $existingMain = $model->getMainProperty($userId);
        if ($existingMain) {
            $model->update($existingMain['id'], $mainData);
        } else {
            $model->insert($mainData);
        }

        // Second property (optional)
        if ($this->request->getPost('has_second_property')) {
            $secondData = [
                'user_id' => $userId,
                'property_type' => 'second',
                'purchase_price' => $this->request->getPost('second_purchase_price'),
                'purchase_costs_percentage' => $this->request->getPost('second_purchase_costs_percentage'),
                'annual_costs' => $this->request->getPost('second_annual_costs'),
                'energy_monthly' => $this->request->getPost('second_energy_monthly') ?? 0,
                'other_monthly_costs' => $this->request->getPost('second_other_monthly_costs') ?? 0,
                'imu_tax' => $this->request->getPost('second_imu_tax'),
                'tari_yearly' => $this->request->getPost('second_tari_yearly') ?? 0,
                'maintenance_yearly' => $this->request->getPost('second_maintenance_yearly'),
                'rental_income' => $this->request->getPost('second_rental_income'),
            ];

            $existingSecond = $model->getSecondProperty($userId);
            if ($existingSecond) {
                $model->update($existingSecond['id'], $secondData);
            } else {
                $model->insert($secondData);
            }
        } else {
            // Delete second property if exists and checkbox is unchecked
            $existingSecond = $model->getSecondProperty($userId);
            if ($existingSecond) {
                $model->delete($existingSecond['id']);
            }
        }

        return redirect()->to('/property')->with('success', 'Vastgoed gegevens opgeslagen!');
    }
}
