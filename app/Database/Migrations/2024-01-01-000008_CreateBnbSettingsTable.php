<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBnbSettingsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'enabled' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'comment'    => 'B&B module aan/uit',
            ],
            'number_of_rooms' => [
                'type'       => 'INT',
                'constraint' => 3,
                'default'    => 3,
            ],
            'price_per_room_per_night' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => '75.00',
            ],
            'occupancy_rate' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'default'    => '60.00',
                'comment'    => 'Bezettingsgraad percentage',
            ],
            'high_season_percentage' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'default'    => '80.00',
                'comment'    => 'Bezettingsgraad hoogseizoen',
            ],
            'low_season_percentage' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'default'    => '40.00',
                'comment'    => 'Bezettingsgraad laagseizoen',
            ],
            'high_season_months' => [
                'type'       => 'INT',
                'constraint' => 2,
                'default'    => 4,
                'comment'    => 'Aantal maanden hoogseizoen',
            ],
            'low_season_months' => [
                'type'       => 'INT',
                'constraint' => 2,
                'default'    => 8,
                'comment'    => 'Aantal maanden laagseizoen',
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
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('bnb_settings');
    }

    public function down()
    {
        $this->forge->dropTable('bnb_settings');
    }
}
