<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FixImeiColumnSize extends Migration
{
    public function up()
    {
        $this->forge->modifyColumn('distribusi_gadget', [
            'imei' => [
                'type' => 'VARCHAR',
                'constraint' => '16',
                'null' => true,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->modifyColumn('distribusi_gadget', [
            'imei' => [
                'type' => 'VARCHAR',
                'constraint' => '15',
                'null' => true,
            ],
        ]);
    }
}
