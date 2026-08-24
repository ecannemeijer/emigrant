<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBillingTables extends Migration
{
    public function up()
    {
        if (!$this->db->tableExists('app_settings')) {
            $this->forge->addField([
                'id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'auto_increment' => true,
                ],
                'setting_key' => [
                    'type' => 'VARCHAR',
                    'constraint' => 80,
                ],
                'setting_value' => [
                    'type' => 'TEXT',
                    'null' => true,
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
            $this->forge->addUniqueKey('setting_key');
            $this->forge->createTable('app_settings');
        }

        $now = date('Y-m-d H:i:s');
        $defaults = [
            'billing_enabled' => '0',
            'price_month' => '9.90',
            'price_year' => '99.00',
            'currency' => 'EUR',
        ];
        foreach ($defaults as $key => $value) {
            $exists = $this->db->table('app_settings')->where('setting_key', $key)->countAllResults();
            if ($exists === 0) {
                $this->db->table('app_settings')->insert([
                    'setting_key' => $key,
                    'setting_value' => $value,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        if (!$this->db->tableExists('subscriptions')) {
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
                'plan' => [
                    'type' => 'VARCHAR',
                    'constraint' => 16,
                    'default' => 'year',
                ],
                'source' => [
                    'type' => 'VARCHAR',
                    'constraint' => 24,
                    'default' => 'complimentary',
                ],
                'status' => [
                    'type' => 'VARCHAR',
                    'constraint' => 24,
                    'default' => 'active',
                ],
                'starts_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'ends_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
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
            $this->forge->addUniqueKey('user_id');
            $this->forge->createTable('subscriptions');
        }

        if (!$this->db->tableExists('payments')) {
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
                'plan' => [
                    'type' => 'VARCHAR',
                    'constraint' => 16,
                ],
                'amount' => [
                    'type' => 'DECIMAL',
                    'constraint' => '10,2',
                    'default' => '0.00',
                ],
                'currency' => [
                    'type' => 'VARCHAR',
                    'constraint' => 8,
                    'default' => 'EUR',
                ],
                'status' => [
                    'type' => 'VARCHAR',
                    'constraint' => 24,
                    'default' => 'pending',
                ],
                'paypal_order_id' => [
                    'type' => 'VARCHAR',
                    'constraint' => 64,
                    'null' => true,
                ],
                'paypal_capture_id' => [
                    'type' => 'VARCHAR',
                    'constraint' => 64,
                    'null' => true,
                ],
                'payload' => [
                    'type' => 'TEXT',
                    'null' => true,
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
            $this->forge->addUniqueKey('paypal_order_id');
            $this->forge->addUniqueKey('paypal_capture_id');
            $this->forge->createTable('payments');
        }

        if ($this->db->tableExists('users')) {
            $users = $this->db->table('users')->select('id')->get()->getResultArray();
            $ends = date('Y-m-d H:i:s', strtotime('+1 year'));
            foreach ($users as $user) {
                $exists = $this->db->table('subscriptions')->where('user_id', $user['id'])->countAllResults();
                if ($exists === 0) {
                    $this->db->table('subscriptions')->insert([
                        'user_id' => $user['id'],
                        'plan' => 'year',
                        'source' => 'complimentary',
                        'status' => 'active',
                        'starts_at' => $now,
                        'ends_at' => $ends,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }
        }
    }

    public function down()
    {
        if ($this->db->tableExists('payments')) {
            $this->forge->dropTable('payments');
        }
        if ($this->db->tableExists('subscriptions')) {
            $this->forge->dropTable('subscriptions');
        }
        if ($this->db->tableExists('app_settings')) {
            $this->forge->dropTable('app_settings');
        }
    }
}
