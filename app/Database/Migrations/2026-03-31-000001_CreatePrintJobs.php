<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePrintJobs extends Migration
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
            // Referensi ke entitas yang dicetak (baste, input, dll)
            'reference_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'custom',
            ],
            'reference_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            // Payload JSON: data lengkap yang akan diformat ke ESC/POS oleh agent
            'payload' => [
                'type' => 'JSON',
            ],
            // Status antrian
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'processing', 'done', 'failed'],
                'default'    => 'pending',
            ],
            // Jumlah percobaan print
            'attempts' => [
                'type'        => 'TINYINT',
                'constraint'  => 3,
                'unsigned'    => true,
                'default'     => 0,
            ],
            'max_attempts' => [
                'type'       => 'TINYINT',
                'constraint' => 3,
                'unsigned'   => true,
                'default'    => 3,
            ],
            // Pesan error dari agent jika gagal
            'error_message' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            // ID unik agent yang sedang mengerjakan job ini (mencegah double print)
            'agent_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            // Waktu agent mengambil/lock job ini
            'locked_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            // Siapa yang meminta print
            'created_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            // Waktu selesai cetak
            'done_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        // Index utama: polling query "ambil job pending"
        $this->forge->addKey(['status', 'created_at'], false, false, 'idx_status_created');
        // Index untuk lookup per referensi
        $this->forge->addKey(['reference_type', 'reference_id'], false, false, 'idx_reference');

        $this->forge->createTable('print_jobs', true);
    }

    public function down()
    {
        $this->forge->dropTable('print_jobs', true);
    }
}
