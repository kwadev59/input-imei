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
        $builder->select('users.*, mandor_self_reports.imei, mandor_self_reports.aplikasi, mandor_self_reports.updated_at as reported_at');
        $builder->join('mandor_self_reports', 'mandor_self_reports.npk = users.npk', 'left');
        $builder->where('users.role', 'mandor');
        
        if($search){
            $builder->groupStart()
                    ->like('users.nama_lengkap', $search)
                    ->orLike('users.npk', $search)
                    ->orLike('users.afdeling_id', $search)
                    ->orLike('mandor_self_reports.imei', $search)
                    ->groupEnd();
        }

        $data['mandor_gadgets'] = $builder->orderBy('users.afdeling_id', 'ASC')->get()->getResultArray();
        
        // Get unique applications for dropdown (from master_gadget is fine as reference)
        $apps = $db->table('master_gadget')
                   ->select('aplikasi')
                   ->where('aplikasi IS NOT NULL')
                   ->where('aplikasi !=', '')
                   ->distinct()
                   ->get()->getResultArray();
        $data['applications'] = array_column($apps, 'aplikasi');

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
        $imei = trim($this->request->getVar('imei') ?? '');
        $aplikasi = $this->request->getVar('aplikasi');

        if(empty($imei)){
            return redirect()->back()->with('error', 'IMEI tidak boleh kosong.');
        }

        if(empty($aplikasi)){
            return redirect()->back()->with('error', 'Aplikasi tidak boleh kosong.');
        }

        $db = \Config\Database::connect();
        
        // Check if NPK already reported something in mandor_self_reports
        $existingReport = $db->table('mandor_self_reports')->where('npk', $npk)->get()->getRowArray();

        if($existingReport){
            // Update existing report
            $db->table('mandor_self_reports')->where('id', $existingReport['id'])->update([
                'imei' => $imei,
                'aplikasi' => $aplikasi,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        } else {
            // Insert new report
            $db->table('mandor_self_reports')->insert([
                'npk' => $npk,
                'imei' => $imei,
                'aplikasi' => $aplikasi,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }

        return redirect()->to('/mandor/gadgets')->with('success', "Data gadget mandor berhasil diperbarui di tabel laporan.");
    }
}
