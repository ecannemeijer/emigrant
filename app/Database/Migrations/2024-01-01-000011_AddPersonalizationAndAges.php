<?php

use CodeIgniter\Database\Migration;

class AddPersonalizationAndAges extends Migration
{
    public function up()
    {
        // Add personalization fields to user_profiles
        $fields = [
            'partner_name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'after' => 'language',
            ],
            'date_of_birth' => [
                'type' => 'DATE',
                'null' => true,
                'after' => 'partner_name',
            ],
            'partner_date_of_birth' => [
                'type' => 'DATE',
                'null' => true,
                'after' => 'date_of_birth',
            ],
            'retirement_age' => [
                'type' => 'INT',
                'constraint' => 3,
                'default' => 67,
                'after' => 'partner_date_of_birth',
            ],
            'partner_retirement_age' => [
                'type' => 'INT',
                'constraint' => 3,
                'default' => 67,
                'after' => 'retirement_age',
            ],
        ];

        $this->forge->addColumn('user_profiles', $fields);

        // Add start year fields to incomes table for WaO and pension
        $incomeFields = [
            'wao_start_age' => [
                'type' => 'INT',
                'constraint' => 3,
                'null' => true,
                'comment' => 'Leeftijd waarop WaO (partner) ingaat',
                'after' => 'wao_future',
            ],
            'pension_start_age' => [
                'type' => 'INT',
                'constraint' => 3,
                'default' => 67,
                'comment' => 'Leeftijd waarop pensioen ingaat',
                'after' => 'pension',
            ],
        ];

        $this->forge->addColumn('incomes', $incomeFields);
    }

    public function down()
    {
        // Remove columns from user_profiles
        $this->forge->dropColumn('user_profiles', [
            'partner_name',
            'date_of_birth',
            'partner_date_of_birth',
            'retirement_age',
            'partner_retirement_age',
        ]);

        // Remove columns from incomes
        $this->forge->dropColumn('incomes', [
            'wao_start_age',
            'pension_start_age',
        ]);
    }
}
