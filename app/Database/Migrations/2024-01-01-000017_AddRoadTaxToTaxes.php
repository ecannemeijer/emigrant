<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRoadTaxToTaxes extends Migration
{
    public function up()
    {
        $this->forge->addColumn('taxes', [
            'road_tax_yearly' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => '0.00',
                'null'       => false,
                'comment'    => 'Wegenbelasting per jaar',
                'after'      => 'social_contributions',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('taxes', 'road_tax_yearly');
    }
}
