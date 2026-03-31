<?php namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\UserModel;

class Auth extends Controller
{
    public function index()
    {
        helper(['form']);
        return view('auth/login');
    }

    public function process()
    {
        $session = session();
        $model = new UserModel();
        $npk = trim($this->request->getVar('npk'));
        $password = $this->request->getVar('password');

        $data = $model->where('npk', $npk)->first();

        if (!$data) {
            $session->setFlashdata('error', 'NPK/Username Tidak Ditemukan');
            return redirect()->to('/auth');
        }

        // Admin: wajib pakai password
        if ($data['role'] == 'admin') {
            if (!$password || !password_verify($password, $data['password_hash'])) {
                $session->setFlashdata('error', 'Password Salah');
                return redirect()->to('/auth');
            }
        }

        // Mandor: cukup NPK saja (tanpa password)
        // Admin: sudah lolos verifikasi password di atas

        $ses_data = [
            'id'    => $data['id'],
            'npk'   => $data['npk'],
            'role'  => $data['role'],
            'nama'  => $data['nama_lengkap'],
            'afdeling_id' => $data['afdeling_id'],
            'tipe_mandor' => $data['tipe_mandor'] ?? 'Panen',
            'pt_site' => $data['pt_site'] ?? '',
            'logged_in' => TRUE
        ];
        $session->set($ses_data);
        
        if($data['role'] == 'admin'){
            return redirect()->to('/dashboard');
        } else {
            return redirect()->to('/input');
        }
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/auth');
    }
}
