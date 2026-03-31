<?php namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\KaryawanModel;
use App\Models\DistribusiGadgetModel;
use App\Models\MasterGadgetModel;

class Input extends Controller
{
    // Mandor Dashboard: List of submissions
    public function index()
    {
        $session = session();
        if(!$session->get('logged_in')){
            return redirect()->to('/auth');
        }
        
        $session_id = $session->get('id');
        $model = new DistribusiGadgetModel();
        
        $data['submissions'] = $model->select('distribusi_gadget.*, karyawan.nama as nama_karyawan, karyawan.nik_karyawan')
                                     ->join('karyawan', 'karyawan.id = distribusi_gadget.karyawan_id')
                                     ->where('input_by', $session_id)
                                     ->orderBy('updated_at', 'DESC')
                                     ->findAll();
                                     
        $data['user_nama'] = $session->get('nama');
        
        return view('input/dashboard', $data);
    }

    // Unified method to prepare data for the view
    private function prepareData($session)
    {
        $afdeling = $session->get('afdeling_id');
        $pt_site = $session->get('pt_site');
        $karyawanModel = new KaryawanModel();
        $distribusiModel = new DistribusiGadgetModel();

        // 1. Get all submissions (to map status)
        $done = $distribusiModel->select('distribusi_gadget.karyawan_id, distribusi_gadget.id as submission_id, distribusi_gadget.status_pengajuan, distribusi_gadget.input_at, distribusi_gadget.input_by, users.nama_lengkap as nama_mandor')
                                ->join('users', 'users.id = distribusi_gadget.input_by', 'left')
                                ->findAll();
        
        $submissionMap = [];
        foreach($done as $d){
            $submissionMap[$d['karyawan_id']] = $d;
        }

        // 2. Get All Active Karyawan - filter by Afdeling
        $builder = $karyawanModel->where('afdeling', $afdeling)->where('status_aktif', 'Aktif');

        // 3. Filter by PT/SITE (jika mandor punya pt_site)
        if(!empty($pt_site)){
            $builder->where('pt_site', $pt_site);
        }

        // 4. Filter by Mandor Type (Panen/Rawat)
        $mandorType = $session->get('tipe_mandor');
        if(!$mandorType){
             $userModel = new \App\Models\UserModel();
             $user = $userModel->find($session->get('id'));
             $mandorType = $user['tipe_mandor'] ?? 'Panen';
        }

        // Jabatan Filter Logic (Reverted to Flexible Search to fix missing data)
        if($mandorType == 'Panen'){
            $builder->groupStart()
                ->like('jabatan', 'PEMANEN') // Mencari kata PEMANEN (besar/kecil)
                ->orLike('jabatan', 'PANEN') // Mencari kata PANEN
                ->orLike('jabatan', 'GANDENG') // Mencari kata GANDENG
            ->groupEnd();
        } elseif ($mandorType == 'Rawat') {
            $builder->groupStart()
                ->like('jabatan', 'RAWAT') // Mencari kata RAWAT
                ->orLike('jabatan', 'PERAWATAN')
                ->orLike('jabatan', 'PUPUK')
                ->orLike('jabatan', 'SEMPROT')
            ->groupEnd();
        } 
        
        // Tetap gunakan optimasi select agar ringan
        $allKaryawan = $builder->select('id, nama, nik_karyawan, jabatan, status_aktif')->findAll();

        return [
            'karyawan_list' => $allKaryawan,
            'submission_map' => empty($submissionMap) ? new \stdClass() : (object)$submissionMap,
            'mandor_type' => $mandorType
        ];
    }

    // Add Form: Select Karyawan & Input Gadget
    public function create()
    {
        $session = session();
        if(!$session->get('logged_in')){
            return redirect()->to('/auth');
        }

        $data = $this->prepareData($session);
        // 'submission' is null for new input
        $data['submission'] = null;

        return view('input/create', $data);
    }
    
    // Edit Function
    public function edit($id)
    {
        $session = session();
        if(!$session->get('logged_in')){
            return redirect()->to('/auth');
        }
        
        $model = new DistribusiGadgetModel();
        $submission = $model->find($id);
        
        if(!$submission || $submission['input_by'] != $session->get('id')){
             return redirect()->to('/input')->with('error', 'Data tidak ditemukan atau bukan milik Anda.');
        }

        // Prepare same data as Create (Checklist)
        $data = $this->prepareData($session);
        $data['submission'] = $submission;
        
        return view('input/create', $data);
    }

    // Store Submission (Draft or Submit)
    public function store()
    {
        $session = session();
        $model = new DistribusiGadgetModel();
        
        $action = $this->request->getVar('action');
        $status_pengajuan = ($action === 'draft') ? 'Draft' : 'Submitted';
        $redirect_next = ($action === 'submit_next');

        $rules = [
            'karyawan_id' => 'required',
            'status_gadget' => 'required',
        ];

        if($this->request->getVar('status_gadget') == 'Ada' && $status_pengajuan == 'Submitted'){
             $rules['imei'] = 'required|numeric|exact_length[15]';
        } elseif ($this->request->getVar('status_gadget') == 'Tidak Ada' && $status_pengajuan == 'Submitted') {
             $rules['keterangan'] = 'required';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Prevent duplicate input for same karyawan by any mandor
        $karyawan_id = $this->request->getVar('karyawan_id');
        $existingInput = $model->select('distribusi_gadget.*, users.nama_lengkap as nama_mandor')
                               ->join('users', 'users.id = distribusi_gadget.input_by', 'left')
                               ->where('distribusi_gadget.karyawan_id', $karyawan_id)
                               ->first();
        if($existingInput){
            $namaMandor = $existingInput['nama_mandor'] ?? 'Tidak diketahui';
            return redirect()->back()->withInput()->with('errors', ['Karyawan ini sudah pernah diinput oleh Mandor: ' . $namaMandor . '.']);
        }

        $imei = $this->request->getVar('imei');
        $keterangan = $this->request->getVar('keterangan') ?? '';

        // --- IMEI Validation & Ownership Check ---
        if($this->request->getVar('status_gadget') == 'Ada' && $imei){
            $masterGadget = new MasterGadgetModel();
            $gadgetRef = $masterGadget->where('imei', $imei)->first();
            
            if(!$gadgetRef){
                // Block if not in master
                return redirect()->back()->withInput()->with('errors', ['IMEI ' . $imei . ' TIDAK TERDAFTAR di Master Gadget.']);
            }

            // Check duplicate IMEI in distribusi_gadget
            $existingImei = $model->where('imei', $imei)->where('karyawan_id !=', $karyawan_id)->first();
            if ($existingImei) {
                 return redirect()->back()->withInput()->with('errors', ['IMEI ' . $imei . ' sudah digunakan oleh karyawan lain pada aplikasi ini.']);
            }

            // Check Ownership
            $karyawanModel = new KaryawanModel();
            $targetKaryawan = $karyawanModel->find($karyawan_id);
            
            if($targetKaryawan){
                $nikTarget = trim($targetKaryawan['nik_karyawan']);
                $npkOwner  = trim($gadgetRef['npk_pengguna'] ?? '');

                // Only warn if owner is set in master and differs from target
                if($npkOwner && $nikTarget !== $npkOwner){
                    $ownerName = $gadgetRef['nama_pengguna'] ?: 'Tidak Diketahui';
                    $statusDesc = $gadgetRef['status_desc'] ?: '-';
                    
                    $keterangan .= " [WARNING: Di Master Gadget, IMEI ini milik: $ownerName ($npkOwner), Status: $statusDesc]";
                }
            }
        }
        // -----------------------------------------

        if($status_pengajuan == 'Submitted' && $imei){
            $exists = $model->where('imei', $imei)->first();
            if($exists){
                $keterangan .= " [DUPLICATE DETECTED]";
            }
        }

        $data = [
            'karyawan_id' => $this->request->getVar('karyawan_id'),
            'status_gadget' => $this->request->getVar('status_gadget'),
            'imei' => $imei,
            'keterangan' => $keterangan,
            'input_by' => $session->get('id'),
            'input_at' => date('Y-m-d H:i:s'),
            'is_verified' => 0,
            'status_pengajuan' => $status_pengajuan
        ];

        $model->save($data);
        
        $msg = $status_pengajuan == 'Draft' ? 'Disimpan sebagai Draft' : 'Data Berhasil Disimpan';
        
        if($redirect_next){
            return redirect()->to('/input/create')->with('success', $msg);
        } else {
            return redirect()->to('/input')->with('success', $msg);
        }
    }

    public function update($id)
    {
        $session = session();
        $model = new DistribusiGadgetModel();
        
        $submission = $model->find($id);
        if(!$submission || $submission['input_by'] != $session->get('id')){
            return redirect()->to('/input');
        }

        $action = $this->request->getVar('action');
        $status_pengajuan = ($action === 'draft') ? 'Draft' : 'Submitted';
        // Note: Update doesn't usually use submit_next, but we can support it if needed.
        // For simplicity, update returns to dashboard or create list.
        
        $rules = [
            'karyawan_id' => 'required',
            'status_gadget' => 'required',
        ];

        if($this->request->getVar('status_gadget') == 'Ada' && $status_pengajuan == 'Submitted'){
             $rules['imei'] = 'required|numeric|exact_length[15]';
        } elseif ($this->request->getVar('status_gadget') == 'Tidak Ada' && $status_pengajuan == 'Submitted') {
             $rules['keterangan'] = 'required';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $imei = $this->request->getVar('imei');
        $keterangan = $this->request->getVar('keterangan') ?? '';
        $karyawan_id = $this->request->getVar('karyawan_id');

        // Prevent duplicate input if karyawan_id changed
        $existingInput = $model->select('distribusi_gadget.*, users.nama_lengkap as nama_mandor')
                               ->join('users', 'users.id = distribusi_gadget.input_by', 'left')
                               ->where('distribusi_gadget.karyawan_id', $karyawan_id)
                               ->where('distribusi_gadget.id !=', $id)
                               ->first();
        if($existingInput){
            $namaMandor = $existingInput['nama_mandor'] ?? 'Tidak diketahui';
            return redirect()->back()->withInput()->with('errors', ['Karyawan ini sudah pernah diinput oleh Mandor: ' . $namaMandor . '.']);
        }

        // --- IMEI Validation & Ownership Check ---
        if($this->request->getVar('status_gadget') == 'Ada' && $imei){
            $masterGadget = new MasterGadgetModel();
            $gadgetRef = $masterGadget->where('imei', $imei)->first();
            
            if(!$gadgetRef){
                return redirect()->back()->withInput()->with('errors', ['IMEI ' . $imei . ' TIDAK TERDAFTAR di Master Gadget.']);
            }

            // Check duplicate IMEI in distribusi_gadget
            $existingImei = $model->where('imei', $imei)->where('karyawan_id !=', $karyawan_id)->first();
            if ($existingImei) {
                 return redirect()->back()->withInput()->with('errors', ['IMEI ' . $imei . ' sudah digunakan oleh karyawan lain pada aplikasi ini.']);
            }

            $karyawanModel = new KaryawanModel();
            $targetKaryawan = $karyawanModel->find($karyawan_id);
            
            if($targetKaryawan){
                $nikTarget = trim($targetKaryawan['nik_karyawan']);
                $npkOwner  = trim($gadgetRef['npk_pengguna'] ?? '');

                if($npkOwner && $nikTarget !== $npkOwner){
                    $ownerName = $gadgetRef['nama_pengguna'] ?: 'Tidak Diketahui';
                    $statusDesc = $gadgetRef['status_desc'] ?: '-';
                    $keterangan .= " [WARNING: Di Master Gadget, IMEI ini milik: $ownerName ($npkOwner), Status: $statusDesc]";
                }
            }
        }
        // -----------------------------------------

        if($status_pengajuan == 'Submitted' && $imei){
            $exists = $model->where('imei', $imei)->where('id !=', $id)->first();
            if($exists){
                $keterangan .= " [DUPLICATE DETECTED]";
            }
        }

        $data = [
            'id' => $id,
            'karyawan_id' => $this->request->getVar('karyawan_id'),
            'status_gadget' => $this->request->getVar('status_gadget'),
            'imei' => $imei,
            'keterangan' => $keterangan,
            'status_pengajuan' => $status_pengajuan
        ];

        $model->save($data);
        
        if($action === 'submit_next'){
             return redirect()->to('/input/create')->with('success', 'Data diperbarui. Lanjut input berikutnya.');
        }

        return redirect()->to('/input')->with('success', $status_pengajuan == 'Draft' ? 'Draft Diperbarui' : 'Permohonan Berhasil Dikirim');
    }

    // AJAX Handler for Real-time Check
    public function checkImei()
    {
        $imei = $this->request->getVar('imei');
        $karyawan_id = $this->request->getVar('karyawan_id');
        
        if(strlen($imei) !== 15){
            return $this->response->setJSON(['status' => 'invalid', 'message' => 'IMEI harus 15 digit']);
        }

        $masterGadget = new MasterGadgetModel();
        $gadget = $masterGadget->where('imei', $imei)->first();
        
        if(!$gadget){
            return $this->response->setJSON(['status' => 'error', 'message' => '❌ IMEI TIDAK TERDAFTAR di Master Data!']);
        }

        $distribusiModel = new DistribusiGadgetModel();
        $existingImei = $distribusiModel->select('distribusi_gadget.*, karyawan.nama as nama_karyawan')
                                        ->join('karyawan', 'karyawan.id = distribusi_gadget.karyawan_id', 'left')
                                        ->where('imei', $imei)
                                        ->where('karyawan_id !=', $karyawan_id)
                                        ->first();
        if ($existingImei) {
            $namaPenggunaLain = $existingImei['nama_karyawan'] ?? 'Karyawan Lain';
            return $this->response->setJSON(['status' => 'error', 'message' => '❌ IMEI sudah digunakan oleh: ' . $namaPenggunaLain]);
        }

        $karyawanModel = new KaryawanModel();
        $karyawan = $karyawanModel->find($karyawan_id);
        
        $warning = null;
        $matched = false;
        $ownerName = $gadget['nama_pengguna'] ?: '';
        $npkOwner = trim($gadget['npk_pengguna'] ?? '');
        $statusDesc = $gadget['status_desc'] ?: '-';
        $assetType = $gadget['tipe_asset'] ?? '-';
        
        if($karyawan){
            $nikTarget = trim($karyawan['nik_karyawan']);
            
            // NPK/NIK matching: cek substring (NIK = NPK + digit tambahan)
            $npkMatch = false;
            if($npkOwner && $nikTarget){
                $npkMatch = (str_contains($nikTarget, $npkOwner) || str_contains($npkOwner, $nikTarget));
            }
            
            if($npkMatch){
                // Data Cocok!
                $matched = true;
            } elseif($npkOwner || $ownerName){
                // Ada pemilik lain — tampilkan warning
                $ownerDisplay = $ownerName ?: 'Tidak Diketahui';
                $npkDisplay = $npkOwner ?: '-';
                $warning = "⚠️ Peringatan: IMEI ini milik <b>$ownerDisplay ($npkDisplay)</b> dengan Status: <b>$statusDesc</b>, bukan karyawan yang dipilih.";
            }
        }
        
        if($matched){
            return $this->response->setJSON([
                'status' => 'matched', 
                'warning' => null,
                'message' => "✅ DATA COCOK — IMEI ini terdaftar atas nama <b>$ownerName</b> ($npkOwner)",
                'gadget' => [
                    'nama_pengguna' => $ownerName,
                    'npk_pengguna' => $npkOwner,
                    'status_desc' => $statusDesc,
                    'jenis_aset' => $assetType,
                ]
            ]);
        }
        
        return $this->response->setJSON([
            'status' => 'ok', 
            'warning' => $warning,
            'message' => '✅ IMEI Terdaftar Valid'
        ]);
    }
}
