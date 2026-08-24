<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class PerPersonIncome extends Migration
{
    public function up()
    {
        if (!$this->db->fieldExists('has_partner', 'user_profiles')) {
            $this->forge->addColumn('user_profiles', [
                'has_partner' => [
                    'type' => 'TINYINT',
                    'constraint' => 1,
                    'null' => true,
                    'comment' => '1 = partner meenemen, 0 = alleen, null = afleiden uit partnergegevens',
                ],
            ]);
        }

        $incomeCols = [];
        if (!$this->db->fieldExists('own_benefit_type', 'incomes')) {
            $incomeCols['own_benefit_type'] = [
                'type' => 'VARCHAR',
                'constraint' => 16,
                'default' => 'other',
                'null' => false,
            ];
        }
        if (!$this->db->fieldExists('partner_benefit_type', 'incomes')) {
            $incomeCols['partner_benefit_type'] = [
                'type' => 'VARCHAR',
                'constraint' => 16,
                'default' => 'wia',
                'null' => false,
            ];
        }
        if (!$this->db->fieldExists('own_other_income', 'incomes')) {
            $incomeCols['own_other_income'] = [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => '0.00',
                'null' => false,
            ];
        }
        if (!$this->db->fieldExists('partner_other_income', 'incomes')) {
            $incomeCols['partner_other_income'] = [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => '0.00',
                'null' => false,
            ];
        }
        if (!$this->db->fieldExists('own_aow_start_age', 'incomes')) {
            $incomeCols['own_aow_start_age'] = [
                'type' => 'INT',
                'constraint' => 3,
                'null' => true,
            ];
        }
        if (!$this->db->fieldExists('partner_aow_start_age', 'incomes')) {
            $incomeCols['partner_aow_start_age'] = [
                'type' => 'INT',
                'constraint' => 3,
                'null' => true,
            ];
        }
        if ($incomeCols) {
            $this->forge->addColumn('incomes', $incomeCols);
        }
    }

    public function down()
    {
        foreach (['own_benefit_type', 'partner_benefit_type', 'own_other_income', 'partner_other_income', 'own_aow_start_age', 'partner_aow_start_age'] as $col) {
            if ($this->db->fieldExists($col, 'incomes')) {
                $this->forge->dropColumn('incomes', $col);
            }
        }
        if ($this->db->fieldExists('has_partner', 'user_profiles')) {
            $this->forge->dropColumn('user_profiles', 'has_partner');
        }
    }
}
