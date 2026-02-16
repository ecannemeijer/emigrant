<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTariToProperties extends Migration
{
    public function up()
    {
        $this->forge->addColumn('properties', [
            'tari_yearly' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => '0.00',
                'after'      => 'imu_tax',
                'comment'    => 'TARI afvalbelasting per jaar',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('properties', 'tari_yearly');
    }
}
