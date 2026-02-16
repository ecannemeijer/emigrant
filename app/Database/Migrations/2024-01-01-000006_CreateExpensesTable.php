<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateExpensesTable extends Migration
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
            'energy' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => '150.00',
                'comment'    => 'Energie per maand',
            ],
            'water' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => '30.00',
                'comment'    => 'Water per maand',
            ],
            'internet' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => '30.00',
                'comment'    => 'Internet per maand',
            ],
            'health_insurance' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => '200.00',
                'comment'    => 'Zorgverzekering per maand',
            ],
            'car_insurance' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => '80.00',
                'comment'    => 'Auto verzekering per maand',
            ],
            'car_fuel' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => '150.00',
                'comment'    => 'Brandstof per maand',
            ],
            'car_maintenance' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => '50.00',
                'comment'    => 'Auto onderhoud per maand',
            ],
            'groceries' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => '400.00',
                'comment'    => 'Boodschappen per maand',
            ],
            'leisure' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => '200.00',
                'comment'    => 'Vrije tijd per maand',
            ],
            'unforeseen' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => '100.00',
                'comment'    => 'Onvoorzien per maand',
            ],
            'other' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => '0.00',
                'comment'    => 'Overige kosten per maand',
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
        $this->forge->createTable('expenses');
    }

    public function down()
    {
        $this->forge->dropTable('expenses');
    }
}
