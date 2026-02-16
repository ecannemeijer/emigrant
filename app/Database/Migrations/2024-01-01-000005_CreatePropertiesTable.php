<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePropertiesTable extends Migration
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
            'property_type' => [
                'type'       => 'ENUM',
                'constraint' => ['main', 'second'],
                'default'    => 'main',
            ],
            'purchase_price' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => '160000.00',
            ],
            'purchase_costs_percentage' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'default'    => '10.00',
                'comment'    => 'Percentage aankoopkosten',
            ],
            'purchase_costs' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => '16000.00',
            ],
            'annual_costs' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => '1200.00',
                'comment'    => 'Jaarlijkse vaste lasten',
            ],
            'imu_tax' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => '0.00',
                'comment'    => 'IMU belasting voor tweede woning',
            ],
            'maintenance_yearly' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => '1000.00',
            ],
            'rental_income' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => '0.00',
                'comment'    => 'Huurinkomsten per maand',
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
        $this->forge->createTable('properties');
    }

    public function down()
    {
        $this->forge->dropTable('properties');
    }
}
