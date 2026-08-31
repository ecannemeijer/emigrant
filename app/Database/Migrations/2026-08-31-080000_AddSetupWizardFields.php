<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSetupWizardFields extends Migration
{
    public function up()
    {
        if (!$this->db->tableExists('user_profiles')) {
            return;
        }

        if (!$this->db->fieldExists('setup_completed', 'user_profiles')) {
            $this->forge->addColumn('user_profiles', [
                'setup_completed' => [
                    'type' => 'TINYINT',
                    'constraint' => 1,
                    'default' => 0,
                    'null' => false,
                    'after' => 'voluntary_aow_years',
                ],
            ]);
        }
        if (!$this->db->fieldExists('children_count', 'user_profiles')) {
            $this->forge->addColumn('user_profiles', [
                'children_count' => [
                    'type' => 'TINYINT',
                    'constraint' => 2,
                    'unsigned' => true,
                    'default' => 0,
                    'null' => false,
                ],
            ]);
        }
        if (!$this->db->fieldExists('cars_count', 'user_profiles')) {
            $this->forge->addColumn('user_profiles', [
                'cars_count' => [
                    'type' => 'TINYINT',
                    'constraint' => 2,
                    'unsigned' => true,
                    'default' => 0,
                    'null' => false,
                ],
            ]);
        }

        if ($this->db->fieldExists('date_of_birth', 'user_profiles')) {
            $this->db->query("UPDATE user_profiles SET setup_completed = 1 WHERE date_of_birth IS NOT NULL AND date_of_birth != '' AND date_of_birth != '0000-00-00'");
        }
        if ($this->db->tableExists('expenses')) {
            $this->db->query('UPDATE user_profiles SET setup_completed = 1 WHERE user_id IN (SELECT user_id FROM expenses)');
        }
    }

    public function down()
    {
        if (!$this->db->tableExists('user_profiles')) {
            return;
        }
        foreach (['cars_count', 'children_count', 'setup_completed'] as $col) {
            if ($this->db->fieldExists($col, 'user_profiles')) {
                $this->forge->dropColumn('user_profiles', $col);
            }
        }
    }
}
