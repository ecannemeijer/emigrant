<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SetYearlySubscriptionPrice69 extends Migration
{
    public function up()
    {
        if (!$this->db->tableExists('app_settings')) {
            return;
        }

        $now = date('Y-m-d H:i:s');
        $row = $this->db->table('app_settings')->where('setting_key', 'price_year')->get()->getRowArray();
        if ($row) {
            $this->db->table('app_settings')->where('setting_key', 'price_year')->update([
                'setting_value' => '69.00',
                'updated_at' => $now,
            ]);
            return;
        }

        $this->db->table('app_settings')->insert([
            'setting_key' => 'price_year',
            'setting_value' => '69.00',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    public function down()
    {
        if (!$this->db->tableExists('app_settings')) {
            return;
        }
        $this->db->table('app_settings')->where('setting_key', 'price_year')->update([
            'setting_value' => '99.00',
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
