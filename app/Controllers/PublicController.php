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

    public function save_gadget()
    {
        $npk = trim($this->request->getVar('npk') ?? '');
        $aplikasi = $this->request->getVar('aplikasi');
        $imei = trim($this->request->getVar('imei') ?? '');

        if(empty($npk) || empty($aplikasi) || empty($imei)){
            return redirect()->back()->withInput()->with('error', 'Semua field wajib diisi.');
        }

        $db = \Config\Database::connect();

        // Check if NPK already reported something
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

        return redirect()->to('/public/input-gadget')->with('success', "Terima kasih, data gadget Anda berhasil disimpan.");
    }
}
