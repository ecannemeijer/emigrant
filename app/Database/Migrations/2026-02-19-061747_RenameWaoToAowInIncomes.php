<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RenameAOWToAowInIncomes extends Migration
{
    public function up()
    {
        // Rename AOW columns to aow (AOW = Algemene Ouderdomswet, not AOW)
        $this->forge->modifyColumn('incomes', [
            'aow_future' => [
                'name' => 'aow_future',
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => '0.00',
                'comment' => 'Toekomstige AOW per maand',
            ],
        ]);
        
        $this->forge->modifyColumn('incomes', [
            'own_aow' => [
                'name' => 'own_aow',
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => '0.00',
                'comment' => 'Eigen AOW per maand',
            ],
        ]);
        
        $this->forge->modifyColumn('incomes', [
            'aow_start_age' => [
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
        // Revert aow columns back to AOW
        $this->forge->modifyColumn('incomes', [
            'aow_future' => [
                'name' => 'aow_future',
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => '0.00',
                'comment' => 'Toekomstige AOW per maand',
            ],
        ]);
        
        $this->forge->modifyColumn('incomes', [
            'own_aow' => [
                'name' => 'own_aow',
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => '0.00',
                'comment' => 'Eigen AOW per maand',
            ],
        ]);
        
        $this->forge->modifyColumn('incomes', [
            'aow_start_age' => [
                'name' => 'aow_start_age',
                'type' => 'INT',
                'constraint' => 3,
                'null' => true,
                'comment' => 'Leeftijd waarop AOW (partner) ingaat',
            ],
        ]);
    }
}
