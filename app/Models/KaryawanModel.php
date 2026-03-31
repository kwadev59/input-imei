<?php namespace App\Models;
use CodeIgniter\Model;

class KaryawanModel extends Model
{
    protected $table = 'karyawan';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nik_karyawan', 'nama', 'jabatan', 'afdeling', 'pt_site', 'status_aktif', 'created_at', 'updated_at'];
    protected $useTimestamps = true;
}
