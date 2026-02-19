<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddownAowToIncomes extends Migration
{
    public function up()
    {
        $fields = [
            'own_aow' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
                'default'    => 0.00,
                'comment'    => 'Own AOW (not partner) - monthly net amount'
            ],
        ];
        
        $this->forge->addColumn('incomes', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('incomes', 'own_aow');
    }
}
