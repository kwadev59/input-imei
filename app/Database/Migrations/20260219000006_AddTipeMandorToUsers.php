<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTipeMandorToUsers extends Migration
{
    public function up()
    {
        $this->forge->addColumn('users', [
            'tipe_mandor' => [
                'type' => 'ENUM',
                'constraint' => ['Panen', 'Rawat', 'Umum'],
                'default' => 'Umum',
                'after' => 'role',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('users', 'tipe_mandor');
    }
}
