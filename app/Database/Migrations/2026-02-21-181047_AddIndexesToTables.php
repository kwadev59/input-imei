<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIndexesToTables extends Migration
{
    public function up()
    {
        // Indexes on master_gadget
        $this->db->query('ALTER TABLE `master_gadget` ADD INDEX `idx_master_imei` (`imei`);');
        
        // Indexes on karyawan
        $this->db->query('ALTER TABLE `karyawan` ADD INDEX `idx_karyawan_afdeling` (`afdeling`);');
        $this->db->query('ALTER TABLE `karyawan` ADD INDEX `idx_karyawan_status_aktif` (`status_aktif`);');
        $this->db->query('ALTER TABLE `karyawan` ADD INDEX `idx_karyawan_pt_site` (`pt_site`);');

        // Indexes on distribusi_gadget 
        $this->db->query('ALTER TABLE `distribusi_gadget` ADD INDEX `idx_distribusi_imei` (`imei`);');
        $this->db->query('ALTER TABLE `distribusi_gadget` ADD INDEX `idx_distribusi_input_by` (`input_by`);');
    }

    public function down()
    {
        // Remove Indexes on master_gadget
        $this->db->query('ALTER TABLE `master_gadget` DROP INDEX `idx_master_imei`;');
        
        // Remove Indexes on karyawan
        $this->db->query('ALTER TABLE `karyawan` DROP INDEX `idx_karyawan_afdeling`;');
        $this->db->query('ALTER TABLE `karyawan` DROP INDEX `idx_karyawan_status_aktif`;');
        $this->db->query('ALTER TABLE `karyawan` DROP INDEX `idx_karyawan_pt_site`;');

        // Remove Indexes on distribusi_gadget
        $this->db->query('ALTER TABLE `distribusi_gadget` DROP INDEX `idx_distribusi_imei`;');
        $this->db->query('ALTER TABLE `distribusi_gadget` DROP INDEX `idx_distribusi_input_by`;');
    }
}
