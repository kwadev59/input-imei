<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPtSiteToKaryawan extends Migration
{
    public function up()
    {
        $this->forge->addColumn('karyawan', [
            'pt_site' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
                'default' => null,
                'after' => 'afdeling',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('karyawan', 'pt_site');
    }
}
