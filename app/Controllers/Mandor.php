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

    /**
     * High Performance Import Mandor
     * Mengoptimalkan komunikasi lintas VPS (Web <-> DB via NetBird)
     */
    public function import()
    {
        $session = session();
        if(!$session->get('logged_in') || $session->get('role') != 'admin'){
            return redirect()->to('/auth');
        }

        $file = $this->request->getFile('csv_file');
        if(!$file || !$file->isValid()){
             return redirect()->back()->with('error', 'File tidak valid atau tidak ditemukan.');
        }

        // 1. Parsing CSV ke Array
        $csvData = array_map('str_getcsv', file($file->getTempName()));
        $header = array_shift($csvData); // Ambil header
        
        if (empty($csvData)) {
            return redirect()->back()->with('error', 'File CSV kosong.');
        }

        $db = \Config\Database::connect();
        $userModel = new \App\Models\UserModel();
        
        // --- PHASE 1: PRE-FETCHING (OPTIMASI UTAMA) ---
        // Ambil semua NPK mandor yang ada di DB dalam SATU query saja.
        // Kita simpan dalam array map [npk => id] untuk pengecekan cepat di memori (O(1)).
        $existingUsers = $userModel->select('id, npk')
                                   ->where('role', 'mandor')
                                   ->findAll();
        $npkMap = array_column($existingUsers, 'id', 'npk');

        $dataToInsert = [];
        $dataToUpdate = [];
        $now = date('Y-m-d H:i:s');
        $defaultPassword = password_hash('123456', PASSWORD_BCRYPT); // Hash sekali di luar loop (hemat CPU)

        // --- PHASE 2: DATA PREPARATION (IN-MEMORY PROCESSING) ---
        foreach($csvData as $row){
            if(count($row) < 3) continue; // Skip jika kolom tidak lengkap
            
            $npk = trim($row[0]);
            if(empty($npk)) continue;

            $namaLengkap = $row[1];
            $afdeling   = $row[2];
            $tipe       = 'Panen'; // Default
            
            // Logika deteksi tipe mandor
            if(isset($row[3]) && in_array(ucfirst(trim($row[3])), ['Panen', 'Rawat'])){
                $tipe = ucfirst(trim($row[3]));
            } else {
                $namaLower = strtolower($namaLengkap);
                if(strpos($namaLower, 'rawat') !== false || strpos($namaLower, 'perawatan') !== false) {
                    $tipe = 'Rawat';
                }
            }

            $ptSite = isset($row[4]) ? trim($row[4]) : '';

            // Payload dasar
            $payload = [
                'nama_lengkap' => $namaLengkap,
                'afdeling_id'  => $afdeling,
                'tipe_mandor'  => $tipe,
                'pt_site'      => $ptSite,
                'updated_at'   => $now,
            ];

            // Cek apakah NPK sudah ada di memory map (Tanpa Query ke DB)
            if(isset($npkMap[$npk])){
                // Masukkan ke antrian UPDATE
                $payload['id'] = $npkMap[$npk]; // Primary key untuk updateBatch
                $dataToUpdate[] = $payload;
            } else {
                // Masukkan ke antrian INSERT
                $payload['npk'] = $npk;
                $payload['role'] = 'mandor';
                $payload['password_hash'] = $defaultPassword;
                $payload['created_at'] = $now;
                $dataToInsert[] = $payload;
            }
        }

        // --- PHASE 3: DATABASE EXECUTION (BATCH & TRANSACTION) ---
        try {
            $db->transBegin(); // Mulai Transaksi

            // 1. Proses INSERT dalam potongan (Chunk)
            if (!empty($dataToInsert)) {
                // Batasi per 200 data untuk menghindari limit memory & paket TCP
                $chunks = array_chunk($dataToInsert, 200);
                foreach ($chunks as $chunk) {
                    $db->table('users')->insertBatch($chunk);
                }
            }

            // 2. Proses UPDATE dalam potongan (Chunk)
            if (!empty($dataToUpdate)) {
                $chunks = array_chunk($dataToUpdate, 200);
                foreach ($chunks as $chunk) {
                    // updateBatch menggunakan kolom 'id' sebagai referensi
                    $db->table('users')->updateBatch($chunk, 'id');
                }
            }

            if ($db->transStatus() === false) {
                $db->transRollback();
                return redirect()->back()->with('error', 'Terjadi kesalahan saat memproses batch data.');
            }

            $db->transCommit(); // Simpan permanen

            $totalProcessed = count($dataToInsert) + count($dataToUpdate);
            return redirect()->to('/mandor')->with('success', "Berhasil memproses $totalProcessed data Mandor (Insert: ".count($dataToInsert).", Update: ".count($dataToUpdate).").");

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->with('error', 'Database Error: ' . $e->getMessage());
        }
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
