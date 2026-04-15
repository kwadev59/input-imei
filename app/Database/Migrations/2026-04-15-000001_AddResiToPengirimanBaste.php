<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddResiToPengirimanBaste extends Migration
{
    public function up()
    {
        $fields = [
            'no_resi'   => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true, 'after' => 'tanggal'],
            'foto_resi' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'after' => 'no_resi'],
        ];
        $this->forge->addColumn('pengiriman_baste', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('pengiriman_baste', ['no_resi', 'foto_resi']);
    }
}
