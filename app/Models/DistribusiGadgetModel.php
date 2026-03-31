<?php namespace App\Models;
use CodeIgniter\Model;

class DistribusiGadgetModel extends Model
{
    protected $table = 'distribusi_gadget';
    protected $primaryKey = 'id';
    protected $allowedFields = ['karyawan_id', 'status_gadget', 'imei', 'keterangan', 'input_by', 'input_at', 'is_verified', 'status_pengajuan'];
    protected $useTimestamps = true;

    public function getWithDetails()
    {
        return $this->select('distribusi_gadget.*, karyawan.nama as nama_karyawan, karyawan.nik_karyawan, karyawan.afdeling, karyawan.pt_site, karyawan.jabatan, users.nama_lengkap as nama_mandor')
                    ->join('karyawan', 'karyawan.id = distribusi_gadget.karyawan_id')
                    ->join('users', 'users.id = distribusi_gadget.input_by')
                    ->findAll();
    }
}
