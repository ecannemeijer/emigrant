<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateStartPositionsTable extends Migration
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
            'house_sale_price' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => '350000.00',
            ],
            'mortgage_debt' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => '100000.00',
            ],
            'net_equity' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => '250000.00',
            ],
            'savings' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => '100000.00',
            ],
            'total_starting_capital' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => '350000.00',
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
        $this->forge->createTable('start_positions');
    }

    public function down()
    {
        $this->forge->dropTable('start_positions');
    }
}
