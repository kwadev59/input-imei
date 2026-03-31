<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DistribusiGadget extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'karyawan_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'status_gadget' => [
                'type' => 'ENUM',
                'constraint' => ['Ada', 'Tidak Ada'],
            ],
            'imei' => [
                'type' => 'VARCHAR',
                'constraint' => '15',
                'null' => true,
            ],
            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'input_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'input_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'is_verified' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
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
        $this->forge->addForeignKey('karyawan_id', 'karyawan', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('input_by', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('distribusi_gadget');
    }

    public function down()
    {
        $this->forge->dropTable('distribusi_gadget');
    }
}
