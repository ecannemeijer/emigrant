<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIrpefAndPartnerPension extends Migration
{
    public function up()
    {
        $taxCols = [];
        foreach ([
            'irpef_salary_percent' => 'IRPEF-schatting op NL-loon (%)',
            'irpef_benefit_percent' => 'IRPEF-schatting op WIA/uitkering (%)',
            'irpef_aow_percent' => 'IRPEF-schatting op AOW (%)',
            'irpef_pension_percent' => 'IRPEF-schatting op aanvullend pensioen (%)',
        ] as $col => $comment) {
            if (!$this->db->fieldExists($col, 'taxes')) {
                $taxCols[$col] = [
                    'type' => 'DECIMAL',
                    'constraint' => '5,2',
                    'default' => '0.00',
                    'null' => false,
                    'comment' => $comment,
                ];
            }
        }
        if ($taxCols) {
            $this->forge->addColumn('taxes', $taxCols);
        }

        $incomeCols = [];
        if (!$this->db->fieldExists('partner_pension', 'incomes')) {
            $incomeCols['partner_pension'] = [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => '0.00',
                'null' => false,
            ];
        }
        if (!$this->db->fieldExists('partner_pension_start_age', 'incomes')) {
            $incomeCols['partner_pension_start_age'] = [
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
        foreach (['irpef_salary_percent', 'irpef_benefit_percent', 'irpef_aow_percent', 'irpef_pension_percent'] as $col) {
            if ($this->db->fieldExists($col, 'taxes')) {
                $this->forge->dropColumn('taxes', $col);
            }
        }
        foreach (['partner_pension', 'partner_pension_start_age'] as $col) {
            if ($this->db->fieldExists($col, 'incomes')) {
                $this->forge->dropColumn('incomes', $col);
            }
        }
    }
}
