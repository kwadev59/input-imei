<?php namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\UserModel;

class Mandor extends Controller
{
    public function index()
    {
        $session = session();
        if(!$session->get('logged_in') || $session->get('role') != 'admin'){
            return redirect()->to('/auth');
        }

        $userModel = new UserModel();
        
        $search = $this->request->getVar('search');
        
        $builder = $userModel->where('role', 'mandor');
        
        if($search){
            $builder->groupStart()
                    ->like('nama_lengkap', $search)
                    ->orLike('npk', $search)
                    ->orLike('afdeling_id', $search)
                    ->groupEnd();
        }

        $data['mandor'] = $builder->orderBy('afdeling_id', 'ASC')->paginate(10, 'mandor');
        $data['pager'] = $userModel->pager;
        $data['search'] = $search;
        $data['user_nama'] = $session->get('nama');
        $data['menu_active'] = 'mandor';

        return view('mandor/index', $data);
    }

    public function import()
    {
        $session = session();
        if(!$session->get('logged_in') || $session->get('role') != 'admin'){
            return redirect()->to('/auth');
        }

        $file = $this->request->getFile('csv_file');
        
        if(!$file->isValid()){
             return redirect()->back()->with('error', 'File tidak valid.');
        }

        // CSV Format: NPK, Nama Lengkap, Afdeling, Tipe (Optional), PT/SITE (Optional)
        $csv = array_map('str_getcsv', file($file->getTempName()));
        $userModel = new \App\Models\UserModel();
        $count = 0;
        
        // Skip header
        $header = array_shift($csv); 

        foreach($csv as $row){
            if(count($row) < 3) continue;
            
            $npk = trim($row[0]);
            
            // Assume Type is in 4th column, or default to Panen
            // Or try to detect from Name
            $tipe = 'Panen';
            if(isset($row[3]) && in_array(ucfirst(trim($row[3])), ['Panen', 'Rawat'])){
                $tipe = ucfirst(trim($row[3]));
            } else {
                // Auto-detect from name
                $nama = strtolower($row[1]);
                if(strpos($nama, 'rawat') !== false || strpos($nama, 'perawatan') !== false || strpos($nama, 'maintenance') !== false) {
                    $tipe = 'Rawat';
                }
                // Default stays 'Panen' if no match
            }

            $pt_site = isset($row[4]) ? trim($row[4]) : '';

            // Check if user exists
            $exist = $userModel->where('npk', $npk)->first();
            
            if(!$exist){
                $data = [
                    'npk' => $npk,
                    'nama_lengkap' => $row[1],
                    'afdeling_id' => $row[2],
                    'role' => 'mandor',
                    'tipe_mandor' => $tipe,
                    'pt_site' => $pt_site,
                    'password_hash' => password_hash('123456', PASSWORD_BCRYPT), // Default password
                    'created_at' => date('Y-m-d H:i:s'),
                ];
                $userModel->insert($data);
                $count++;
            } else {
                // Update existing mandor info (name/afdeling/tipe)
                $data = [
                    'nama_lengkap' => $row[1],
                    'afdeling_id' => $row[2],
                    'tipe_mandor' => $tipe,
                    'pt_site' => $pt_site,
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
                $userModel->update($exist['id'], $data);
            }
        }

        return redirect()->to('/mandor')->with('success', "$count Mandor baru berhasil ditambahkan! Default Password: 123456");
    }

    public function changePassword($id)
    {
        $session = session();
        if(!$session->get('logged_in') || $session->get('role') != 'admin'){
            return redirect()->to('/auth');
        }

        $newPassword = $this->request->getVar('new_password');
        
        if(!$newPassword || strlen($newPassword) < 6){
            return redirect()->back()->with('error', 'Password minimal 6 karakter.');
        }

        $userModel = new UserModel();
        $user = $userModel->find($id);
        
        if(!$user || $user['role'] != 'mandor'){
            return redirect()->back()->with('error', 'Mandor tidak ditemukan.');
        }

        $userModel->update($id, [
            'password_hash' => password_hash($newPassword, PASSWORD_BCRYPT),
        ]);

        return redirect()->to('/mandor')->with('success', 'Password untuk ' . $user['nama_lengkap'] . ' berhasil diubah.');
    }

    public function changeTipe($id)
    {
        $session = session();
        if(!$session->get('logged_in') || $session->get('role') != 'admin'){
            return redirect()->to('/auth');
        }

        $userModel = new UserModel();
        $user = $userModel->find($id);
        
        if(!$user || $user['role'] != 'mandor'){
            return redirect()->back()->with('error', 'Mandor tidak ditemukan.');
        }

        $newTipe = $this->request->getVar('tipe_mandor');
        if(!in_array($newTipe, ['Panen', 'Rawat'])){
            return redirect()->back()->with('error', 'Tipe mandor tidak valid. Harus Panen atau Rawat.');
        }

        $userModel->update($id, [
            'tipe_mandor' => $newTipe,
        ]);

        return redirect()->to('/mandor')->with('success', 'Tipe mandor ' . $user['nama_lengkap'] . ' diubah menjadi ' . $newTipe . '.');
    }
}
