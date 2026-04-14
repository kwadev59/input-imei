<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMandorSelfReportsTable extends Migration
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
            'npk' => [
                'type'           => 'VARCHAR',
                'constraint'     => '50',
            ],
            'imei' => [
                'type'           => 'VARCHAR',
                'constraint'     => '20',
            ],
            'aplikasi' => [
                'type'           => 'VARCHAR',
                'constraint'     => '100',
            ],
            'created_at' => [
                'type'           => 'DATETIME',
                'null'           => true,
            ],
            'updated_at' => [
                'type'           => 'DATETIME',
                'null'           => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('npk');
        $this->forge->addKey('imei');
        $this->forge->createTable('mandor_self_reports');
    }

    public function down()
    {
        $this->forge->dropTable('mandor_self_reports');
    }
}
