<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPartnerWiaFlagToIncomes extends Migration
{
    public function up()
    {
        // Check if column already exists
        $db = \Config\Database::connect();
        $fields = $db->getFieldNames('incomes');
        
        if (!in_array('partner_has_wia', $fields)) {
            $this->forge->addColumn('incomes', [
                'partner_has_wia' => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'default'    => 1,
                    'null'       => false,
                    'after'      => 'wia_wife',
                    'comment'    => 'Is partner WIA (1) or regular income (0)',
                ],
            ]);
        }
    }

    public function down()
    {
        $db = \Config\Database::connect();
        $fields = $db->getFieldNames('incomes');
        
        if (in_array('partner_has_wia', $fields)) {
            $this->forge->dropColumn('incomes', 'partner_has_wia');
        }
    }
}
