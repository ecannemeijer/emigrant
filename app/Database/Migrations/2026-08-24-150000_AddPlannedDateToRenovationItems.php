<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPlannedDateToRenovationItems extends Migration
{
    public function up()
    {
        if (!$this->db->tableExists('renovation_items')) {
            return;
        }
        $fields = $this->db->getFieldNames('renovation_items');
        if (in_array('planned_date', $fields, true)) {
            return;
        }

        $this->forge->addColumn('renovation_items', [
            'planned_date' => [
                'type' => 'DATE',
                'null' => true,
                'after' => 'planned_year',
            ],
        ]);
    }

    public function down()
    {
        if ($this->db->tableExists('renovation_items')) {
            $fields = $this->db->getFieldNames('renovation_items');
            if (in_array('planned_date', $fields, true)) {
                $this->forge->dropColumn('renovation_items', 'planned_date');
            }
        }
    }
}
