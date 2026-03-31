<?php namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class KaryawanSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'nik_karyawan' => 'KRY001',
                'nama' => 'Budi Santoso',
                'jabatan' => 'Pemanen',
                'afdeling' => 'AFD-01',
                'status_aktif' => 'Aktif',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nik_karyawan' => 'KRY002',
                'nama' => 'Slamet Riyadi',
                'jabatan' => 'Pemanen',
                'afdeling' => 'AFD-01',
                'status_aktif' => 'Aktif',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nik_karyawan' => 'KRY003',
                'nama' => 'Joko Widodo',
                'jabatan' => 'Perawatan',
                'afdeling' => 'AFD-01',
                'status_aktif' => 'Aktif',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]
        ];

        // Using Query Builder
        $this->db->table('karyawan')->insertBatch($data);
    }
}
