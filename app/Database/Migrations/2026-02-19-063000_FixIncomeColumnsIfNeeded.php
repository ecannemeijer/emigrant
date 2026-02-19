<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FixIncomeColumnsIfNeeded extends Migration
{
    public function up()
    {
        // Get existing column names
        $db = \Config\Database::connect();
        $fields = $db->getFieldNames('incomes');
        
        // Check if we need to rename wao_future to aow_future
        if (in_array('wao_future', $fields) && !in_array('aow_future', $fields)) {
            $this->forge->modifyColumn('incomes', [
                'wao_future' => [
                    'name' => 'aow_future',
                    'type' => 'DECIMAL',
                    'constraint' => '10,2',
                    'default' => '0.00',
                    'comment' => 'Toekomstige AOW per maand',
                ],
            ]);
        }
        
        // Check if we need to rename own_wao to own_aow
        if (in_array('own_wao', $fields) && !in_array('own_aow', $fields)) {
            $this->forge->modifyColumn('incomes', [
                'own_wao' => [
                    'name' => 'own_aow',
                    'type' => 'DECIMAL',
                    'constraint' => '10,2',
                    'default' => '0.00',
                    'comment' => 'Eigen AOW per maand',
                ],
            ]);
        }
        
        // Check if we need to rename wao_start_age to aow_start_age
        if (in_array('wao_start_age', $fields) && !in_array('aow_start_age', $fields)) {
            $this->forge->modifyColumn('incomes', [
                'wao_start_age' => [
                    'name' => 'aow_start_age',
                    'type' => 'INT',
                    'constraint' => 3,
                    'null' => true,
                    'comment' => 'Leeftijd waarop AOW (partner) ingaat',
                ],
            ]);
        }
        
        // Refresh field list after potential renames
        $fields = $db->getFieldNames('incomes');
        
        // Add aow_future if it doesn't exist at all
        if (!in_array('aow_future', $fields)) {
            $this->forge->addColumn('incomes', [
                'aow_future' => [
                    'type' => 'DECIMAL',
                    'constraint' => '10,2',
                    'default' => '0.00',
                    'comment' => 'Toekomstige AOW per maand',
                    'after' => 'own_income',
                ],
            ]);
        }
        
        // Add own_aow if it doesn't exist at all
        if (!in_array('own_aow', $fields)) {
            $this->forge->addColumn('incomes', [
                'own_aow' => [
                    'type' => 'DECIMAL',
                    'constraint' => '10,2',
                    'default' => '0.00',
                    'comment' => 'Eigen AOW per maand',
                ],
            ]);
        }
        
        // Add aow_start_age if it doesn't exist at all
        if (!in_array('aow_start_age', $fields)) {
            $this->forge->addColumn('incomes', [
                'aow_start_age' => [
                    'type' => 'INT',
                    'constraint' => 3,
                    'null' => true,
                    'comment' => 'Leeftijd waarop AOW (partner) ingaat',
                    'after' => 'aow_future',
                ],
            ]);
        }
        
        // Add partner_has_wia if it doesn't exist
        if (!in_array('partner_has_wia', $fields)) {
            $this->forge->addColumn('incomes', [
                'partner_has_wia' => [
                    'type' => 'TINYINT',
                    'constraint' => 1,
                    'default' => 1,
                    'null' => false,
                    'after' => 'wia_wife',
                    'comment' => 'Is partner WIA (1) or regular income (0)',
                ],
            ]);
        }
    }

    public function down()
    {
        // No need to revert - this is a fix migration
    }
}
