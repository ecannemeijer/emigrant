<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Create admin user
        $userData = [
            'username' => 'admin',
            'email' => 'admin@example.com',
            'password' => password_hash('admin123', PASSWORD_DEFAULT),
            'role' => 'admin',
            'is_active' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        $this->db->table('users')->insert($userData);
        $adminId = $this->db->insertID();

        // Create demo user
        $userData = [
            'username' => 'demo',
            'email' => 'demo@example.com',
            'password' => password_hash('demo123', PASSWORD_DEFAULT),
            'role' => 'user',
            'is_active' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        $this->db->table('users')->insert($userData);
        $demoId = $this->db->insertID();

        // Create profiles
        $this->db->table('user_profiles')->insertBatch([
            [
                'user_id' => $adminId,
                'first_name' => 'Admin',
                'last_name' => 'User',
                'phone' => null,
                'language' => 'nl',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'user_id' => $demoId,
                'first_name' => 'Demo',
                'last_name' => 'Gebruiker',
                'phone' => '+31612345678',
                'language' => 'nl',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ]);

        // Create start position for demo user
        $this->db->table('start_positions')->insert([
            'user_id' => $demoId,
            'house_sale_price' => 350000.00,
            'mortgage_debt' => 100000.00,
            'net_equity' => 250000.00,
            'savings' => 100000.00,
            'total_starting_capital' => 350000.00,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        // Create income for demo user
        $this->db->table('incomes')->insert([
            'user_id' => $demoId,
            'wia_wife' => 1550.00,
            'own_income' => 0.00,
            'aow_future' => 0.00,
            'pension' => 0.00,
            'other_income' => 0.00,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        // Create main property for demo user
        $this->db->table('properties')->insert([
            'user_id' => $demoId,
            'property_type' => 'main',
            'purchase_price' => 160000.00,
            'purchase_costs_percentage' => 10.00,
            'purchase_costs' => 16000.00,
            'annual_costs' => 1200.00,
            'imu_tax' => 0.00,
            'maintenance_yearly' => 1000.00,
            'rental_income' => 0.00,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        // Create expenses for demo user
        $this->db->table('expenses')->insert([
            'user_id' => $demoId,
            'energy' => 150.00,
            'water' => 30.00,
            'internet' => 30.00,
            'health_insurance' => 200.00,
            'car_insurance' => 80.00,
            'car_fuel' => 150.00,
            'car_maintenance' => 50.00,
            'groceries' => 400.00,
            'leisure' => 200.00,
            'unforeseen' => 100.00,
            'other' => 0.00,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        // Create taxes for demo user
        $this->db->table('taxes')->insert([
            'user_id' => $demoId,
            'forfettario_enabled' => 1,
            'forfettario_percentage' => 15.00,
            'normal_tax_percentage' => 23.00,
            'imu_percentage' => 0.76,
            'tari_yearly' => 250.00,
            'social_contributions' => 0.00,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        // Create B&B settings for demo user
        $this->db->table('bnb_settings')->insert([
            'user_id' => $demoId,
            'enabled' => 1,
            'number_of_rooms' => 3,
            'price_per_room_per_night' => 75.00,
            'occupancy_rate' => 60.00,
            'high_season_percentage' => 80.00,
            'low_season_percentage' => 40.00,
            'high_season_months' => 4,
            'low_season_months' => 8,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        // Create B&B expenses for demo user
        $this->db->table('bnb_expenses')->insert([
            'user_id' => $demoId,
            'extra_energy_water' => 100.00,
            'insurance' => 50.00,
            'cleaning' => 200.00,
            'linen_laundry' => 100.00,
            'breakfast_per_guest' => 5.00,
            'platform_commission' => 15.00,
            'marketing' => 50.00,
            'maintenance' => 150.00,
            'administration' => 100.00,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        echo "Database seeded successfully!\n";
        echo "Admin login: admin@example.com / admin123\n";
        echo "Demo login: demo@example.com / demo123\n";
    }
}
