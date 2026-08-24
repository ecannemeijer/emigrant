<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRenovationTables extends Migration
{
    public function up()
    {
        if (!$this->db->tableExists('renovation_settings')) {
            $this->forge->addField([
                'id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'auto_increment' => true,
                ],
                'user_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                ],
                'contingency_percent' => [
                    'type' => 'DECIMAL',
                    'constraint' => '5,2',
                    'default' => '10.00',
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
            $this->forge->addKey('user_id');
            $this->forge->createTable('renovation_settings');
        }

        if (!$this->db->tableExists('renovation_items')) {
            $this->forge->addField([
                'id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'auto_increment' => true,
                ],
                'user_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                ],
                'title' => [
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                ],
                'room' => [
                    'type' => 'VARCHAR',
                    'constraint' => 80,
                    'default' => 'Overig',
                ],
                'status' => [
                    'type' => 'VARCHAR',
                    'constraint' => 32,
                    'default' => 'planned',
                ],
                'priority' => [
                    'type' => 'VARCHAR',
                    'constraint' => 16,
                    'default' => 'medium',
                ],
                'estimated_cost' => [
                    'type' => 'DECIMAL',
                    'constraint' => '12,2',
                    'default' => '0.00',
                ],
                'actual_cost' => [
                    'type' => 'DECIMAL',
                    'constraint' => '12,2',
                    'default' => '0.00',
                ],
                'vat_rate' => [
                    'type' => 'DECIMAL',
                    'constraint' => '5,2',
                    'default' => '10.00',
                ],
                'contractor' => [
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                    'null' => true,
                ],
                'planned_year' => [
                    'type' => 'INT',
                    'constraint' => 4,
                    'null' => true,
                ],
                'include_in_capital' => [
                    'type' => 'TINYINT',
                    'constraint' => 1,
                    'default' => 1,
                ],
                'notes' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'sort_order' => [
                    'type' => 'INT',
                    'constraint' => 5,
                    'default' => 0,
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
            $this->forge->addKey(['user_id', 'sort_order']);
            $this->forge->createTable('renovation_items');
        }
    }

    public function down()
    {
        if ($this->db->tableExists('renovation_items')) {
            $this->forge->dropTable('renovation_items');
        }
        if ($this->db->tableExists('renovation_settings')) {
            $this->forge->dropTable('renovation_settings');
        }
    }
}
