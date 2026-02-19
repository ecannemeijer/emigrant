<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RenameWaoToAowInIncomes extends Migration
{
    public function up()
    {
        // Rename wao columns to aow (AOW = Algemene Ouderdomswet, not WaO)
        $this->forge->modifyColumn('incomes', [
            'wao_future' => [
                'name' => 'aow_future',
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => '0.00',
                'comment' => 'Toekomstige AOW per maand',
            ],
        ]);
        
        $this->forge->modifyColumn('incomes', [
            'own_wao' => [
                'name' => 'own_aow',
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => '0.00',
                'comment' => 'Eigen AOW per maand',
            ],
        ]);
        
        $this->forge->modifyColumn('incomes', [
            'wao_start_age' => [
                'name' => 'aow_start_age',
                'type' => 'INT',
                'constraint' => 3,
                'null' => true,
                'comment' => 'Leeftijd waarop AOW (partner) ingaat',
            ],
        ]);
    }

    public function down()
    {
        // Revert aow columns back to wao
        $this->forge->modifyColumn('incomes', [
            'aow_future' => [
                'name' => 'wao_future',
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => '0.00',
                'comment' => 'Toekomstige WaO per maand',
            ],
        ]);
        
        $this->forge->modifyColumn('incomes', [
            'own_aow' => [
                'name' => 'own_wao',
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => '0.00',
                'comment' => 'Eigen WaO per maand',
            ],
        ]);
        
        $this->forge->modifyColumn('incomes', [
            'aow_start_age' => [
                'name' => 'wao_start_age',
                'type' => 'INT',
                'constraint' => 3,
                'null' => true,
                'comment' => 'Leeftijd waarop WaO (partner) ingaat',
            ],
        ]);
    }
}
