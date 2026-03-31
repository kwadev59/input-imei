<?php namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'npk' => 'admin',
                'nama_lengkap' => 'Administrator',
                'role' => 'admin',
                'password_hash' => password_hash('admin123', PASSWORD_BCRYPT),
                'afdeling_id' => 'HO',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'npk' => '12345',
                'nama_lengkap' => 'Mandor Panen Afdeling 1',
                'role' => 'mandor',
                'password_hash' => password_hash('mandor123', PASSWORD_BCRYPT),
                'afdeling_id' => 'AFD-01',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]
        ];

        // Using Query Builder
        $this->db->table('users')->insertBatch($data);
    }
}
