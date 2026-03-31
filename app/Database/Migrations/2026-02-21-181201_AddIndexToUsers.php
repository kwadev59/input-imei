<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIndexToUsers extends Migration
{
    public function up()
    {
        $this->db->query('ALTER TABLE `users` ADD INDEX `idx_users_npk` (`npk`);');
    }

    public function down()
    {
        $this->db->query('ALTER TABLE `users` DROP INDEX `idx_users_npk`;');
    }
}
