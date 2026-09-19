<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateExpenseItemsTable extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('expense_items')) {
            return;
        }

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
            'category' => [
                'type' => 'VARCHAR',
                'constraint' => 32,
                'default' => 'other',
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 120,
            ],
            'amount' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => '0.00',
            ],
            'sort_order' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
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
        $this->forge->addKey('user_id');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('expense_items');
    }

    public function down()
    {
        if ($this->db->tableExists('expense_items')) {
            $this->forge->dropTable('expense_items');
        }
    }
}
