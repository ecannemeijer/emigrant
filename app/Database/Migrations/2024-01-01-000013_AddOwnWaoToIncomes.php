<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddOwnWaoToIncomes extends Migration
{
    public function up()
    {
        $fields = [
            'own_wao' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
                'default'    => 0.00,
                'comment'    => 'Own WaO (not partner) - monthly net amount'
            ],
        ];
        
        $this->forge->addColumn('incomes', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('incomes', 'own_wao');
    }
}
