<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTaxesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'forfettario_enabled' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
                'comment'    => 'Forfettario regeling aan/uit',
            ],
            'forfettario_percentage' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'default'    => '15.00',
                'comment'    => 'Belastingpercentage forfettario',
            ],
            'normal_tax_percentage' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'default'    => '23.00',
                'comment'    => 'Normaal belastingpercentage',
            ],
            'imu_percentage' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'default'    => '0.76',
                'comment'    => 'IMU percentage',
            ],
            'tari_yearly' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => '250.00',
                'comment'    => 'TARI jaarlijks',
            ],
            'social_contributions' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => '0.00',
                'comment'    => 'Sociale bijdragen per maand',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('taxes');
    }

    public function down()
    {
        $this->forge->dropTable('taxes');
    }
}
