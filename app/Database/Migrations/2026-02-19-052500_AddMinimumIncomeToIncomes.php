<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMinimumIncomeToIncomes extends Migration
{
    public function up()
    {
        $this->forge->addColumn('incomes', [
            'minimum_monthly_income' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => '0.00',
                'null'       => false,
                'comment'    => 'Minimaal gewenst inkomen per maand (wordt aangevuld vanuit vermogen indien nodig)',
                'after'      => 'other_income',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('incomes', 'minimum_monthly_income');
    }
}
