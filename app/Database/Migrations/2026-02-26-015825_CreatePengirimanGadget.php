<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePengirimanGadget extends Migration
{
    public function up()
    {
        // Table pengiriman_baste
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'no_baste'   => ['type' => 'VARCHAR', 'constraint' => 100],
            'tanggal'    => ['type' => 'DATE'],
            'created_by' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('pengiriman_baste', true);

        // Table pengiriman_items
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'baste_id'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'imei'       => ['type' => 'VARCHAR', 'constraint' => 20],
            'kerusakan'  => ['type' => 'TEXT', 'null' => true],
            'created_by' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('baste_id');
        $this->forge->createTable('pengiriman_items', true);
    }

    public function down()
    {
        $this->forge->dropTable('pengiriman_items', true);
        $this->forge->dropTable('pengiriman_baste', true);
    }
}
