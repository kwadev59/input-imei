<?php namespace App\Models;
use CodeIgniter\Model;

class MasterGadgetModel extends Model
{
    protected $table = 'master_gadget';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'imei', 'aplikasi', 'pt', 'afd', 'npk_pengguna', 'nama_pengguna', 
        'pos_title', 'group_asset', 'tipe_asset', 'part_asset', 
        'jumlah', 'asal_desc', 'status_desc', 'note', 'action_desc',
        'created_at', 'updated_at'
    ];
    protected $useTimestamps = true;
}
