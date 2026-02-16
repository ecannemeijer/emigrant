<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBnbExpensesTable extends Migration
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
            'extra_energy_water' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => '100.00',
                'comment'    => 'Extra energie/water per maand',
            ],
            'insurance' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => '50.00',
                'comment'    => 'Verzekering per maand',
            ],
            'cleaning' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => '200.00',
                'comment'    => 'Schoonmaak per maand',
            ],
            'linen_laundry' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => '100.00',
                'comment'    => 'Linnen & was per maand',
            ],
            'breakfast_per_guest' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => '5.00',
                'comment'    => 'Ontbijtkosten per gast',
            ],
            'platform_commission' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'default'    => '15.00',
                'comment'    => 'Boekingsplatform commissie percentage',
            ],
            'marketing' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => '50.00',
                'comment'    => 'Marketing per maand',
            ],
            'maintenance' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => '150.00',
                'comment'    => 'Onderhoud per maand',
            ],
            'administration' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => '100.00',
                'comment'    => 'Administratie/boekhouder per maand',
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
        $this->forge->createTable('bnb_expenses');
    }

    public function down()
    {
        $this->forge->dropTable('bnb_expenses');
    }
}
