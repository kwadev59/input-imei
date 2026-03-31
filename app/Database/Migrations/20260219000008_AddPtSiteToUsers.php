<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPtSiteToUsers extends Migration
{
    public function up()
    {
        $this->forge->addColumn('users', [
            'pt_site' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
                'default' => null,
                'after' => 'afdeling_id',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('users', 'pt_site');
    }
}
