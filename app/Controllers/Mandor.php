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

    public function gadgets()
    {
        $session = session();
        if(!$session->get('logged_in') || $session->get('role') != 'admin'){
            return redirect()->to('/auth');
        }

        $db = \Config\Database::connect();
        
        $search = $this->request->getVar('search');
        
        $builder = $db->table('users');
        $builder->select('users.*, master_gadget.imei, master_gadget.status_desc as status_gadget, master_gadget.aplikasi, master_gadget.pt as gadget_pt, master_gadget.afd as gadget_afd');
        $builder->join('master_gadget', 'master_gadget.npk_pengguna = users.npk', 'left');
        $builder->where('users.role', 'mandor');
        
        if($search){
            $builder->groupStart()
                    ->like('users.nama_lengkap', $search)
                    ->orLike('users.npk', $search)
                    ->orLike('users.afdeling_id', $search)
                    ->orLike('master_gadget.imei', $search)
                    ->groupEnd();
        }

        $data['mandor_gadgets'] = $builder->orderBy('users.afdeling_id', 'ASC')->get()->getResultArray();
        $data['search'] = $search;
        $data['user_nama'] = $session->get('nama');
        $data['active_menu'] = 'mandor_gadget';

        return view('mandor/gadgets', $data);
    }

    public function save_gadget()
    {
        $session = session();
        if(!$session->get('logged_in') || $session->get('role') != 'admin'){
            return redirect()->to('/auth');
        }

        $npk = $this->request->getVar('npk');
        $nama = $this->request->getVar('nama');
        $imei = trim($this->request->getVar('imei') ?? '');

        if(empty($imei)){
            return redirect()->back()->with('error', 'IMEI tidak boleh kosong.');
        }

        $db = \Config\Database::connect();
        
        // 1. Check if this IMEI is already used by someone else in master_gadget
        $existingGadget = $db->table('master_gadget')->where('imei', $imei)->get()->getRowArray();
        
        // 2. Check if this Mandor already has another IMEI assigned
        $currentGadget = $db->table('master_gadget')->where('npk_pengguna', $npk)->get()->getRowArray();

        if($currentGadget && $currentGadget['imei'] !== $imei) {
            // Unassign current gadget first
            $db->table('master_gadget')->where('id', $currentGadget['id'])->update([
                'npk_pengguna' => null,
                'nama_pengguna' => null,
                'status_desc' => 'CADANGAN',
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }

        if($existingGadget){
            // Update existing gadget to this mandor
            $db->table('master_gadget')->where('id', $existingGadget['id'])->update([
                'npk_pengguna' => $npk,
                'nama_pengguna' => $nama,
                'status_desc' => 'TERPAKAI',
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        } else {
            // Create new record in master_gadget
            $db->table('master_gadget')->insert([
                'imei' => $imei,
                'npk_pengguna' => $npk,
                'nama_pengguna' => $nama,
                'status_desc' => 'TERPAKAI',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }

        return redirect()->to('/mandor/gadgets')->with('success', "IMEI untuk Mandor $nama berhasil diperbarui.");
    }
}
