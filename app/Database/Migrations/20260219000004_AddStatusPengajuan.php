<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStatusPengajuan extends Migration
{
    public function up()
    {
        $this->forge->addColumn('distribusi_gadget', [
            'status_pengajuan' => [
                'type' => 'ENUM',
                'constraint' => ['Draft', 'Submitted'],
                'default' => 'Submitted',
                'null' => false,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('distribusi_gadget', 'status_pengajuan');
    }
}
