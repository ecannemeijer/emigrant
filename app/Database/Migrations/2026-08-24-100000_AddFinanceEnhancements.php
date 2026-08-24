<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFinanceEnhancements extends Migration
{
    public function up()
    {
        if (!$this->db->fieldExists('selling_costs_percent', 'start_positions')) {
            $this->forge->addColumn('start_positions', [
                'selling_costs_percent' => [
                    'type' => 'DECIMAL',
                    'constraint' => '5,2',
                    'default' => '0.00',
                    'null' => false,
                    'after' => 'savings',
                ],
                'moving_costs' => [
                    'type' => 'DECIMAL',
                    'constraint' => '12,2',
                    'default' => '0.00',
                    'null' => false,
                    'after' => 'selling_costs_percent',
                ],
                'inflation_rate' => [
                    'type' => 'DECIMAL',
                    'constraint' => '5,2',
                    'default' => '2.00',
                    'null' => false,
                    'after' => 'interest_rate',
                ],
            ]);
        }

        if (!$this->db->fieldExists('income_stops_at_retirement', 'incomes')) {
            $this->forge->addColumn('incomes', [
                'income_stops_at_retirement' => [
                    'type' => 'TINYINT',
                    'constraint' => 1,
                    'default' => 1,
                    'null' => false,
                ],
            ]);
        }

        if (!$this->db->fieldExists('voluntary_aow_years', 'user_profiles')) {
            $this->forge->addColumn('user_profiles', [
                'voluntary_aow_years' => [
                    'type' => 'DECIMAL',
                    'constraint' => '4,1',
                    'default' => '0.0',
                    'null' => false,
                ],
            ]);
        }

        if (!$this->db->fieldExists('profitability_coefficient', 'taxes')) {
            $this->forge->addColumn('taxes', [
                'profitability_coefficient' => [
                    'type' => 'DECIMAL',
                    'constraint' => '5,2',
                    'default' => '67.00',
                    'null' => false,
                ],
                'startup_rate_enabled' => [
                    'type' => 'TINYINT',
                    'constraint' => 1,
                    'default' => 1,
                    'null' => false,
                ],
                'rental_tax_rate' => [
                    'type' => 'DECIMAL',
                    'constraint' => '5,2',
                    'default' => '21.00',
                    'null' => false,
                ],
                'forfettario_limit' => [
                    'type' => 'DECIMAL',
                    'constraint' => '12,2',
                    'default' => '85000.00',
                    'null' => false,
                ],
            ]);
        }

        if (!$this->db->tableExists('checklist_items')) {
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
                'item_key' => [
                    'type' => 'VARCHAR',
                    'constraint' => 80,
                ],
                'title' => [
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                ],
                'category' => [
                    'type' => 'VARCHAR',
                    'constraint' => 80,
                    'default' => 'algemeen',
                ],
                'done' => [
                    'type' => 'TINYINT',
                    'constraint' => 1,
                    'default' => 0,
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
            $this->forge->addKey(['user_id', 'item_key']);
            $this->forge->createTable('checklist_items');
        }
    }

    public function down()
    {
        if ($this->db->tableExists('checklist_items')) {
            $this->forge->dropTable('checklist_items');
        }
    }
}
