<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPartnerWiaFlagToIncomes extends Migration
{
    public function up()
    {
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

    public function down()
    {
        $this->forge->dropColumn('incomes', 'partner_has_wia');
    }
}
