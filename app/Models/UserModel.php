<?php namespace App\Models;
use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $allowedFields = ['npk', 'nama_lengkap', 'role', 'password_hash', 'afdeling_id', 'tipe_mandor', 'pt_site', 'created_at', 'updated_at'];
    protected $useTimestamps = true;
}
