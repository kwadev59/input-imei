<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMasterGadget extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'imei' => [
                'type' => 'VARCHAR',
                'constraint' => '20',
                'unique' => true,
            ],
            'aplikasi' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'pt' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'afd' => [
                'type' => 'VARCHAR',
                'constraint' => '10',
                'null' => true,
            ],
            'npk_pengguna' => [
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => true,
            ],
            'nama_pengguna' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'pos_title' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'group_asset' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'tipe_asset' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'part_asset' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'jumlah' => [
                'type' => 'INT',
                'default' => 1,
            ],
            'asal_desc' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'status_desc' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'note' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'action_desc' => [
                'type' => 'TEXT',
                'null' => true,
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
        $this->forge->createTable('master_gadget');
    }

    public function down()
    {
        $this->forge->dropTable('master_gadget');
    }
}
