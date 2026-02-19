<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateIncomesTable extends Migration
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
            'wia_wife' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => '1550.00',
                'comment'    => 'WIA vrouw per maand',
            ],
            'own_income' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => '0.00',
                'comment'    => 'Eigen inkomen per maand',
            ],
            'aow_future' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => '0.00',
                'comment'    => 'Toekomstige AOW per maand',
            ],
            'pension' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => '0.00',
                'comment'    => 'Pensioen per maand',
            ],
            'other_income' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => '0.00',
                'comment'    => 'Overig inkomen per maand',
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
        $this->forge->createTable('incomes');
    }

    public function down()
    {
        $this->forge->dropTable('incomes');
    }
}
