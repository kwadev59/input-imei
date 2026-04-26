<?php namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\SettingsModel;

class PublicController extends Controller
{
    public function input_gadget()
    {
        $db = \Config\Database::connect();
        $settingsModel = new SettingsModel();

        // Get unique applications from master_gadget
        $apps = $db->table('master_gadget')
                   ->select('aplikasi')
                   ->where('aplikasi IS NOT NULL')
                   ->where('aplikasi !=', '')
                   ->distinct()
                   ->get()->getResultArray();

        $data['applications'] = array_column($apps, 'aplikasi');
        $data['popup_instruction'] = $settingsModel->get_value('mandor_popup_instruction');

        return view('public/input_gadget', $data);
    }

    // AJAX: Validasi IMEI
    public function validate_imei()
    {
        $imei = trim($this->request->getVar('imei') ?? '');
        $npk = trim($this->request->getVar('npk') ?? '');

        if(empty($imei) || strlen($imei) !== 15 || !ctype_digit($imei)){
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'IMEI harus 15 digit angka'
            ]);
        }

        $db = \Config\Database::connect();

        // Cek IMEI di master_gadget
        $gadget = $db->table('master_gadget')
                     ->where('imei', $imei)
                     ->get()->getRowArray();

        if(!$gadget){
            // IMEI tidak terdaftar
            return $this->response->setJSON([
                'status' => 'not_registered',
                'message' => 'IMEI tidak terdaftar di Master Gadget'
            ]);
        }

        // IMEI terdaftar - cek kepemilikan
        $ownerNpk = trim($gadget['npk_pengguna'] ?? '');
        $ownerName = $gadget['nama_pengguna'] ?? 'Tidak Diketahui';
        $statusDesc = $gadget['status_desc'] ?? '-';

        // Cek apakah NPK yang input mirip/cocok dengan owner (fuzzy matching)
        if($ownerNpk && !$this->isNpkMatch($npk, $ownerNpk)){
            // IMEI terdaftar tapi milik orang lain
            return $this->response->setJSON([
                'status' => 'owned_by_other',
                'owner_name' => $ownerName,
                'owner_npk' => $ownerNpk,
                'status_desc' => $statusDesc,
                'message' => "IMEI terdaftar atas nama {$ownerName} (NPK: {$ownerNpk})"
            ]);
        }

        // IMEI terdaftar dan NPK cocok (exact atau fuzzy match)
        $matchType = ($ownerNpk && $ownerNpk === $npk) ? 'exact' : 'fuzzy';
        return $this->response->setJSON([
            'status' => 'matched',
            'match_type' => $matchType,
            'owner_name' => $ownerName,
            'owner_npk' => $ownerNpk,
            'status_desc' => $statusDesc,
            'message' => "IMEI cocok - {$ownerName} (NPK: {$ownerNpk})"
        ]);
    }

    // Fuzzy NPK Matching - cek apakah NPK mirip (6 digit pertama atau substring)
    private function isNpkMatch($inputNpk, $ownerNpk)
    {
        // Exact match
        if($inputNpk === $ownerNpk){
            return true;
        }

        // Ambil 6 digit pertama dari input (jika 7 digit)
        $input6 = substr($inputNpk, 0, 6);
        $owner6 = substr($ownerNpk, 0, 6);

        // 6 digit pertama cocok
        if($input6 === $owner6){
            return true;
        }

        // Substring matching - salah satu mengandung yang lain
        if(strpos($inputNpk, $ownerNpk) !== false || strpos($ownerNpk, $inputNpk) !== false){
            return true;
        }

        // Similarity check - hitung persentase kemiripan
        similar_text($inputNpk, $ownerNpk, $percent);
        if($percent >= 80){
            return true;
        }

        return false;
    }

    public function input_ceker()
    {
        return $this->render_public_input('Ceker', 'List Gadget Ceker');
    }

    public function input_mtrp()
    {
        return $this->render_public_input('MTRP', 'List Gadget MTRP');
    }

    private function render_public_input($type, $title)
    {
        $db = \Config\Database::connect();
        $settingsModel = new SettingsModel();

        $apps = $db->table('master_gadget')
                   ->select('aplikasi')
                   ->where('aplikasi IS NOT NULL')
                   ->where('aplikasi !=', '')
                   ->distinct()
                   ->get()->getResultArray();

        $data['applications'] = array_column($apps, 'aplikasi');
        $data['title'] = $title;
        $data['type'] = $type;
        $data['popup_instruction'] = $settingsModel->get_value('mandor_popup_instruction');

        return view('public/input_karyawan', $data);
    }

    // AJAX: Cek NIK Karyawan
    public function ajax_check_nik()
    {
        $nik = trim($this->request->getVar('nik') ?? '');
        $type = $this->request->getVar('type'); // Ceker atau MTRP

        if(empty($nik)){
            return $this->response->setJSON(['status' => 'error', 'message' => 'NIK tidak boleh kosong']);
        }

        $db = \Config\Database::connect();
        $builder = $db->table('karyawan');
        $builder->where('nik_karyawan', $nik);

        if($type === 'Ceker'){
            $builder->like('jabatan', 'KRANI');
            $builder->whereIn('afdeling', ['OA', 'OB', 'OC', 'OD', 'OE', 'OF', 'OG']);
        } else {
            $builder->where('jabatan', 'MANDOR TRANSPORT');
        }

        $karyawan = $builder->get()->getRowArray();

        if(!$karyawan){
            return $this->response->setJSON(['status' => 'error', 'message' => "NIK {$nik} tidak terdaftar sebagai {$type} yang valid."]);
        }

        return $this->response->setJSON([
            'status' => 'success', 
            'nama' => $karyawan['nama'],
            'jabatan' => $karyawan['jabatan'],
            'afdeling' => $karyawan['afdeling']
        ]);
    }

    public function save_karyawan_gadget()
    {
        $nik = trim($this->request->getVar('npk') ?? '');
        $aplikasi = $this->request->getVar('aplikasi');
        $imei = trim($this->request->getVar('imei') ?? '');
        $type = $this->request->getVar('type');

        if(empty($nik) || empty($aplikasi) || empty($imei)){
            return redirect()->back()->withInput()->with('error', 'Semua field wajib diisi.');
        }

        $db = \Config\Database::connect();
        
        // 1. Validasi ulang karyawan
        $builder = $db->table('karyawan');
        $builder->where('nik_karyawan', $nik);
        if($type === 'Ceker'){
            $builder->like('jabatan', 'KRANI');
        } else {
            $builder->where('jabatan', 'MANDOR TRANSPORT');
        }
        $karyawan = $builder->get()->getRowArray();

        if(!$karyawan){
            return redirect()->back()->withInput()->with('error', 'Data karyawan tidak valid.');
        }

        // 2. Simpan ke distribusi_gadget
        $exist = $db->table('distribusi_gadget')->where('karyawan_id', $karyawan['id'])->get()->getRowArray();

        // Cari ID admin valid untuk memenuhi constraint input_by
        $admin = $db->table('users')->where('role', 'admin')->get()->getRowArray();
        $adminId = $admin ? $admin['id'] : 1;

        $data = [
            'karyawan_id'   => $karyawan['id'],
            'imei'          => $imei,
            'status_gadget' => 'Ada',
            'input_by'      => $adminId, 
            'input_at'      => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s'),
            'status_pengajuan' => 'Submitted'
        ];

        if ($exist) {
            $db->table('distribusi_gadget')->where('id', $exist['id'])->update($data);
        } else {
            $data['created_at'] = date('Y-m-d H:i:s');
            $db->table('distribusi_gadget')->insert($data);
        }

        // 3. Sync ke master_gadget
        $master = $db->table('master_gadget')->where('imei', $imei)->get()->getRowArray();
        if ($master) {
            $db->table('master_gadget')->where('imei', $imei)->update(['aplikasi' => $aplikasi]);
        } else {
            $db->table('master_gadget')->insert([
                'imei' => $imei,
                'aplikasi' => $aplikasi,
                'pt' => $karyawan['pt_site'] ?? '',
                'afd' => $karyawan['afdeling'] ?? '',
                'nama_pengguna' => $karyawan['nama'] ?? '',
                'npk_pengguna' => $karyawan['nik_karyawan'] ?? '',
                'status_desc' => 'Aktif'
            ]);
        }

        $redirectUrl = ($type === 'Ceker') ? '/public/input-ceker' : '/public/input-mtrp';
        return redirect()->to($redirectUrl)->with('success', "Terima kasih, data gadget Anda ({$karyawan['nama']}) berhasil disimpan.");
    }
}
