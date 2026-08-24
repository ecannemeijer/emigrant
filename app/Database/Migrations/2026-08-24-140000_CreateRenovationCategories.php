<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRenovationCategories extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('renovation_categories')) {
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
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 80,
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
        $this->forge->addKey('user_id');
        $this->forge->createTable('renovation_categories');
    }

    public function down()
    {
        if ($this->db->tableExists('renovation_categories')) {
            $this->forge->dropTable('renovation_categories');
        }
    }
}
