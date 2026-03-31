<?php namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\KaryawanModel;

class Karyawan extends Controller
{
    public function index()
    {
        $session = session();
        if(!$session->get('logged_in') || $session->get('role') != 'admin'){
            return redirect()->to('/auth');
        }

        $karyawanModel = new KaryawanModel();
        
        $search = $this->request->getVar('search');
        if($search){
            $karyawanModel->groupStart()->like('nama', $search)->orLike('nik_karyawan', $search)->groupEnd();
        }

        $data['karyawan'] = $karyawanModel->orderBy('afdeling', 'ASC')->orderBy('nama', 'ASC')->paginate(20, 'karyawan');
        $data['pager'] = $karyawanModel->pager;
        $data['search'] = $search;
        $data['user_nama'] = $session->get('nama');

        return view('karyawan/index', $data);
    }

    public function import()
    {
        // Handle CSV POST
        $file = $this->request->getFile('csv_file');
        
        if(!$file->isValid()){
             return redirect()->back()->with('error', 'File tidak valid.');
        }

        $csv = array_map('str_getcsv', file($file->getTempName()));
        // Format: NIK, Nama, Jabatan, Afdeling, Status, PT_SITE
        
        $karyawanModel = new KaryawanModel();
        $count = 0;
        
        // Skip header
        $header = array_shift($csv); 

        foreach($csv as $row){
            if(count($row) < 4) continue;
            
            $data = [
                'nik_karyawan' => $row[0], // NIK
                'nama' => $row[1],         // Nama
                'jabatan' => $row[2],      // Jabatan
                'afdeling' => $row[3],     // Afdeling
                'status_aktif' => isset($row[4]) ? $row[4] : 'Aktif',
                'pt_site' => isset($row[5]) ? trim($row[5]) : '',
            ];
            
            // Check existing
            $exist = $karyawanModel->where('nik_karyawan', $data['nik_karyawan'])->first();
            if($exist){
                // Cek apakah ada perbedaan data
                $isChanged = ($exist['nama'] != $data['nama']) ||
                             ($exist['jabatan'] != $data['jabatan']) ||
                             ($exist['afdeling'] != $data['afdeling']) ||
                             ($exist['pt_site'] != $data['pt_site']) ||
                             ($exist['status_aktif'] != $data['status_aktif']);
                             
                if($isChanged) {
                    // Simpan data lama ke riwayat sebelum diupdate
                    $db = \Config\Database::connect();
                    $db->table('riwayat_karyawan')->insert([
                        'karyawan_id' => $exist['id'],
                        'nik_karyawan' => $exist['nik_karyawan'],
                        'nama' => $exist['nama'],
                        'jabatan' => $exist['jabatan'],
                        'afdeling' => $exist['afdeling'],
                        'pt_site' => $exist['pt_site'],
                        'status_aktif' => $exist['status_aktif'],
                        'keterangan' => 'Perubahan melalui Import CSV',
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                    
                    $karyawanModel->update($exist['id'], $data); // Update existing
                }
            } else {
                $karyawanModel->insert($data); // Insert new
            }
            $count++;
        }

        return redirect()->to('/karyawan')->with('success', "$count Data berhasil diimport/diupdate.");
    }

    public function create()
    {
        $session = session();
        if(!$session->get('logged_in') || $session->get('role') != 'admin'){
            return redirect()->to('/auth');
        }

        $data['user_nama'] = $session->get('nama');
        return view('karyawan/create', $data);
    }

    public function store()
    {
        $session = session();
        if(!$session->get('logged_in') || $session->get('role') != 'admin'){
            return redirect()->to('/auth');
        }

        $karyawanModel = new KaryawanModel();
        
        $nik = $this->request->getPost('nik_karyawan');
        // Cek duplicate NIK
        $exist = $karyawanModel->where('nik_karyawan', $nik)->first();
        if($exist){
            return redirect()->back()->with('error', 'NIK Karyawan sudah terdaftar!')->withInput();
        }

        $data = [
            'nik_karyawan' => $nik,
            'nama' => $this->request->getPost('nama'),
            'jabatan' => $this->request->getPost('jabatan'),
            'afdeling' => $this->request->getPost('afdeling'),
            'pt_site' => $this->request->getPost('pt_site'),
            'status_aktif' => $this->request->getPost('status_aktif'),
        ];

        $karyawanModel->insert($data);
        return redirect()->to('/karyawan')->with('success', 'Data karyawan baru berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $session = session();
        if(!$session->get('logged_in') || $session->get('role') != 'admin'){
            return redirect()->to('/auth');
        }

        $karyawanModel = new KaryawanModel();
        $data['karyawan'] = $karyawanModel->find($id);
        $data['user_nama'] = $session->get('nama');
        
        if(!$data['karyawan']){
            return redirect()->to('/karyawan')->with('error', 'Data tidak ditemukan');
        }

        return view('karyawan/edit', $data);
    }

    public function update($id)
    {
        $session = session();
        if(!$session->get('logged_in') || $session->get('role') != 'admin'){
            return redirect()->to('/auth');
        }

        $karyawanModel = new KaryawanModel();
        
        $data = [
            'nama' => $this->request->getPost('nama'),
            'nik_karyawan' => $this->request->getPost('nik_karyawan'),
            'jabatan' => $this->request->getPost('jabatan'),
            'afdeling' => $this->request->getPost('afdeling'),
            'pt_site' => $this->request->getPost('pt_site'),
            'status_aktif' => $this->request->getPost('status_aktif'),
        ];
        
        $exist = $karyawanModel->find($id);
        if($exist) {
            $isChanged = ($exist['nama'] != $data['nama']) ||
                         ($exist['jabatan'] != $data['jabatan']) ||
                         ($exist['afdeling'] != $data['afdeling']) ||
                         ($exist['pt_site'] != $data['pt_site']) ||
                         ($exist['status_aktif'] != $data['status_aktif']);

            if($isChanged) {
                // Simpan data lama ke riwayat sebelum diupdate
                $db = \Config\Database::connect();
                $db->table('riwayat_karyawan')->insert([
                    'karyawan_id' => $exist['id'],
                    'nik_karyawan' => $exist['nik_karyawan'],
                    'nama' => $exist['nama'],
                    'jabatan' => $exist['jabatan'],
                    'afdeling' => $exist['afdeling'],
                    'pt_site' => $exist['pt_site'],
                    'status_aktif' => $exist['status_aktif'],
                    'keterangan' => 'Perubahan Data Manual',
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }
        }
        
        $karyawanModel->update($id, $data);
        return redirect()->to('/karyawan')->with('success', 'Data karyawan berhasil diperbarui dan riwayat telah disimpan.');
    }

    public function riwayat($id)
    {
        $session = session();
        if(!$session->get('logged_in') || $session->get('role') != 'admin'){
            return redirect()->to('/auth');
        }

        $karyawanModel = new KaryawanModel();
        $data['karyawan'] = $karyawanModel->find($id);
        
        if(!$data['karyawan']){
            return redirect()->to('/karyawan')->with('error', 'Data tidak ditemukan');
        }

        $db = \Config\Database::connect();
        $data['riwayat'] = $db->table('riwayat_karyawan')
                              ->where('karyawan_id', $id)
                              ->orderBy('created_at', 'DESC')
                              ->get()->getResultArray();

        $data['user_nama'] = $session->get('nama');
        return view('karyawan/riwayat', $data);
    }

    public function export()
    {
        $session = session();
        if(!$session->get('logged_in') || $session->get('role') != 'admin'){
            return redirect()->to('/auth');
        }

        $karyawanModel = new KaryawanModel();
        
        $search = $this->request->getVar('search');
        if($search){
            $karyawanModel->groupStart()->like('nama', $search)->orLike('nik_karyawan', $search)->groupEnd();
        }

        $data = $karyawanModel->orderBy('pt_site', 'ASC')->orderBy('afdeling', 'ASC')->orderBy('nama', 'ASC')->findAll();

        $filename = "Export_Data_Karyawan_" . date('Ymd_His') . ".csv";

        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/csv; charset=UTF-8");

        $file = fopen('php://output', 'w');
        fputs($file, "\xEF\xBB\xBF"); // BOM for UTF-8 in Excel

        $header = ['NIK', 'Nama Lengkap', 'Jabatan', 'Afdeling', 'Status Aktif', 'PT_SITE'];
        fputcsv($file, $header);

        foreach ($data as $row) {
            fputcsv($file, [
                $row['nik_karyawan'],
                $row['nama'],
                $row['jabatan'],
                $row['afdeling'],
                $row['status_aktif'],
                $row['pt_site']
            ]);
        }

        fclose($file);
        exit;
    }
}
