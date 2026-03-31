<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveUmumFromTipeMandor extends Migration
{
    public function up()
    {
        // Update existing 'Umum' records to 'Panen'
        $this->db->query("UPDATE users SET tipe_mandor = 'Panen' WHERE tipe_mandor = 'Umum' OR tipe_mandor IS NULL");
        
        // Change ENUM to only allow Panen and Rawat
        $this->db->query("ALTER TABLE users MODIFY COLUMN tipe_mandor ENUM('Panen', 'Rawat') DEFAULT 'Panen'");
    }

    public function down()
    {
        // Revert to include Umum
        $this->db->query("ALTER TABLE users MODIFY COLUMN tipe_mandor ENUM('Panen', 'Rawat', 'Umum') DEFAULT 'Umum'");
    }
}
