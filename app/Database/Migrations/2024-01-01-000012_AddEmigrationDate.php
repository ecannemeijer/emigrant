<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddEmigrationDate extends Migration
{
    public function up()
    {
        // Add emigration_date to user_profiles table
        $fields = [
            'emigration_date' => [
                'type'       => 'DATE',
                'null'       => true,
                'comment'    => 'Date of emigration to Italy'
            ],
        ];
        
        $this->forge->addColumn('user_profiles', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('user_profiles', 'emigration_date');
    }
}
