<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddInterestRateToStartPositions extends Migration
{
    public function up()
    {
        $this->forge->addColumn('start_positions', [
            'interest_rate' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'default'    => '2.00',
                'after'      => 'total_starting_capital',
                'comment'    => 'Spaarrente percentage per jaar',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('start_positions', 'interest_rate');
    }
}
