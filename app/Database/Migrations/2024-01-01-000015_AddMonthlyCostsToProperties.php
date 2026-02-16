<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMonthlyCostsToProperties extends Migration
{
    public function up()
    {
        $this->forge->addColumn('properties', [
            'energy_monthly' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => '0.00',
                'after'      => 'annual_costs',
                'comment'    => 'Energie kosten per maand',
            ],
            'other_monthly_costs' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => '0.00',
                'after'      => 'energy_monthly',
                'comment'    => 'Overige kosten per maand',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('properties', ['energy_monthly', 'other_monthly_costs']);
    }
}
