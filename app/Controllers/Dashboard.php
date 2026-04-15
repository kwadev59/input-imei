<?php namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\KaryawanModel;
use App\Models\DistribusiGadgetModel;

class Dashboard extends Controller
{
    public function index()
    {
        $session = session();
        if(!$session->get('logged_in') || $session->get('role') != 'admin'){
            return redirect()->to('/auth');
        }

        $karyawanModel = new KaryawanModel();
        $distribusiModel = new DistribusiGadgetModel();
        $db = \Config\Database::connect();

        // Stats
        $data['total_karyawan'] = $karyawanModel->where('status_aktif', 'Aktif')->countAllResults();
        $data['total_input'] = $distribusiModel->countAllResults();
        $data['total_belum'] = max(0, $data['total_karyawan'] - $data['total_input']);
        
        $query = $db->query("SELECT imei, COUNT(*) as c FROM distribusi_gadget WHERE imei IS NOT NULL AND imei != '' GROUP BY imei HAVING c > 1");
        $data['total_duplicate'] = count($query->getResult());

        $data['latest_inputs'] = $distribusiModel->getWithDetails();
        $data['user_nama'] = $session->get('nama');
        
        // --- NEW: Gadget Stats (Pemanen/Rawat) per Afdeling & PT_SITE ---
        $raw_stats = $db->query("
            SELECT 
                k.pt_site,
                k.afdeling,
                COUNT(k.id) as total_karyawan_target,
                SUM(CASE WHEN dg.id IS NOT NULL THEN 1 ELSE 0 END) as total_sudah_input,
                CASE 
                    WHEN k.jabatan LIKE '%RAWAT%' OR k.jabatan LIKE '%PERAWATAN%' OR k.jabatan LIKE '%PUPUK%' OR k.jabatan LIKE '%SEMPROT%' THEN 'Pekerja Rawat'
                    ELSE 'Pemanen'
                END as kategori_jabatan,
                SUM(CASE WHEN dg.status_gadget = 'Ada' THEN 1 ELSE 0 END) as total_ada,
                SUM(CASE WHEN dg.status_gadget != 'Ada' OR dg.status_gadget IS NULL THEN 1 ELSE 0 END) as total_tidak_ada
            FROM karyawan k
            LEFT JOIN distribusi_gadget dg ON dg.karyawan_id = k.id
            WHERE k.status_aktif = 'Aktif'
              AND (k.jabatan LIKE '%PEMANEN%' OR k.jabatan LIKE '%PANEN%' OR k.jabatan LIKE '%GANDENG%' OR k.jabatan LIKE '%RAWAT%' OR k.jabatan LIKE '%PERAWATAN%' OR k.jabatan LIKE '%PUPUK%' OR k.jabatan LIKE '%SEMPROT%')
            GROUP BY k.pt_site, k.afdeling, kategori_jabatan
            ORDER BY k.pt_site ASC, k.afdeling ASC, kategori_jabatan ASC
        ")->getResultArray();

        $gadget_stats = [];
        foreach($raw_stats as $st) {
            $pt = $st['pt_site'] ?: 'Tidak Ada PT';
            $afd = $st['afdeling'] ?: 'Tidak Ada Afd';
            $kat = $st['kategori_jabatan'];
            
            if(!isset($gadget_stats[$pt])) {
                $gadget_stats[$pt] = [
                    'afdelings' => [],
                    'total_pt_ada_pemanen' => 0,
                    'total_pt_tidak_pemanen' => 0,
                    'total_pt_ada_rawat' => 0,
                    'total_pt_tidak_rawat' => 0,
                ];
            }
            if(!isset($gadget_stats[$pt]['afdelings'][$afd])) {
                $gadget_stats[$pt]['afdelings'][$afd] = [
                    'Pemanen' => ['ada' => 0, 'tidak' => 0],
                    'Pekerja Rawat' => ['ada' => 0, 'tidak' => 0],
                    'total_target' => 0,
                    'total_input' => 0,
                ];
            }
            $gadget_stats[$pt]['afdelings'][$afd][$kat]['ada'] += $st['total_ada'];
            $gadget_stats[$pt]['afdelings'][$afd][$kat]['tidak'] += $st['total_tidak_ada'];
            
            // accumulate targets and inputs for completion percentage
            $gadget_stats[$pt]['afdelings'][$afd]['total_target'] += $st['total_karyawan_target'];
            $gadget_stats[$pt]['afdelings'][$afd]['total_input'] += $st['total_sudah_input'];

            // Totals per PT
            if($kat == 'Pemanen') {
                $gadget_stats[$pt]['total_pt_ada_pemanen'] += $st['total_ada'];
                $gadget_stats[$pt]['total_pt_tidak_pemanen'] += $st['total_tidak_ada'];
            } else {
                $gadget_stats[$pt]['total_pt_ada_rawat'] += $st['total_ada'];
                $gadget_stats[$pt]['total_pt_tidak_rawat'] += $st['total_tidak_ada'];
            }
        }
        $data['gadget_stats'] = $gadget_stats;

        // --- NEW: List Mandor yg sudah input ---
        $builder = $db->table('distribusi_gadget');
        $builder->select('users.id, users.nama_lengkap, users.afdeling_id, COUNT(distribusi_gadget.id) as total_input, MAX(distribusi_gadget.input_at) as last_input');
        $builder->join('users', 'users.id = distribusi_gadget.input_by');
        $builder->groupBy('users.id');
        $data['mandor_list'] = $builder->get()->getResultArray();

        return view('dashboard/index', $data);
    }

    public function export()
    {
        // Simple Excel Export Logic (restored)
        $distribusiModel = new DistribusiGadgetModel();
        $data['laporan'] = $distribusiModel->getWithDetails();
        
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=Laporan_Gadget_".date('Ymd').".xls");
        header("Pragma: no-cache"); 
        header("Expires: 0");

        return view('dashboard/export_excel', $data);
    }
    
    public function export_gadget_stats()
    {
        $db = \Config\Database::connect();
        $raw_stats = $db->query("
            SELECT 
                k.pt_site,
                k.afdeling,
                COUNT(k.id) as total_karyawan_target,
                SUM(CASE WHEN dg.id IS NOT NULL THEN 1 ELSE 0 END) as total_sudah_input,
                CASE 
                    WHEN k.jabatan LIKE '%RAWAT%' OR k.jabatan LIKE '%PERAWATAN%' OR k.jabatan LIKE '%PUPUK%' OR k.jabatan LIKE '%SEMPROT%' THEN 'Pekerja Rawat'
                    ELSE 'Pemanen'
                END as kategori_jabatan,
                SUM(CASE WHEN dg.status_gadget = 'Ada' THEN 1 ELSE 0 END) as total_ada,
                SUM(CASE WHEN dg.status_gadget != 'Ada' OR dg.status_gadget IS NULL THEN 1 ELSE 0 END) as total_tidak_ada
            FROM karyawan k
            LEFT JOIN distribusi_gadget dg ON dg.karyawan_id = k.id
            WHERE k.status_aktif = 'Aktif'
              AND (k.jabatan LIKE '%PEMANEN%' OR k.jabatan LIKE '%PANEN%' OR k.jabatan LIKE '%GANDENG%' OR k.jabatan LIKE '%RAWAT%' OR k.jabatan LIKE '%PERAWATAN%' OR k.jabatan LIKE '%PUPUK%' OR k.jabatan LIKE '%SEMPROT%')
            GROUP BY k.pt_site, k.afdeling, kategori_jabatan
            ORDER BY k.pt_site ASC, k.afdeling ASC, kategori_jabatan ASC
        ")->getResultArray();

        $gadget_stats = [];
        foreach($raw_stats as $st) {
            $pt = $st['pt_site'] ?: 'Tidak Ada PT';
            $afd = $st['afdeling'] ?: 'Tidak Ada Afd';
            $kat = $st['kategori_jabatan'];
            
            if(!isset($gadget_stats[$pt])) {
                $gadget_stats[$pt] = [
                    'afdelings' => [],
                    'total_pt_ada_pemanen' => 0,
                    'total_pt_tidak_pemanen' => 0,
                    'total_pt_ada_rawat' => 0,
                    'total_pt_tidak_rawat' => 0,
                ];
            }
            if(!isset($gadget_stats[$pt]['afdelings'][$afd])) {
                $gadget_stats[$pt]['afdelings'][$afd] = [
                    'Pemanen' => ['ada' => 0, 'tidak' => 0],
                    'Pekerja Rawat' => ['ada' => 0, 'tidak' => 0],
                    'total_target' => 0,
                    'total_input' => 0,
                ];
            }
            $gadget_stats[$pt]['afdelings'][$afd][$kat]['ada'] += $st['total_ada'];
            $gadget_stats[$pt]['afdelings'][$afd][$kat]['tidak'] += $st['total_tidak_ada'];
            
            // accumulate targets and inputs for completion percentage
            $gadget_stats[$pt]['afdelings'][$afd]['total_target'] += $st['total_karyawan_target'];
            $gadget_stats[$pt]['afdelings'][$afd]['total_input'] += $st['total_sudah_input'];

            // Totals per PT
            if($kat == 'Pemanen') {
                $gadget_stats[$pt]['total_pt_ada_pemanen'] += $st['total_ada'];
                $gadget_stats[$pt]['total_pt_tidak_pemanen'] += $st['total_tidak_ada'];
            } else {
                $gadget_stats[$pt]['total_pt_ada_rawat'] += $st['total_ada'];
                $gadget_stats[$pt]['total_pt_tidak_rawat'] += $st['total_tidak_ada'];
            }
        }
        $data['gadget_stats'] = $gadget_stats;

        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=Kepemilikan_Gadget_".date('Ymd').".xls");
        header("Pragma: no-cache"); 
        header("Expires: 0");

        return view('dashboard/export_excel_stats', $data);
    }
    
    public function export_latest_inputs_txt()
    {
        $distribusiModel = new DistribusiGadgetModel();
        $latest = $distribusiModel->getWithDetails();
        
        $filename = "Log_Input_Terakhir_".date('Ymd_His').".txt";
        
        header("Content-Type: text/plain");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Pragma: no-cache");
        header("Expires: 0");

        echo "--- LOG INPUT TERAKHIR ---\r\n";
        echo "Dicetak pada: " . date('d M Y H:i:s') . "\r\n\r\n";
        
        foreach($latest as $row) {
            echo "Waktu       : " . $row['input_at'] . "\r\n";
            echo "Karyawan    : " . $row['nama_karyawan'] . " (".$row['nik_karyawan'].")\r\n";
            echo "Afdeling    : " . $row['afdeling'] . "\r\n";
            echo "Status      : " . $row['status_gadget'] . "\r\n";
            echo "IMEI        : " . ($row['imei'] ?: '-') . "\r\n";
            echo "Diinput Oleh: " . $row['nama_mandor'] . "\r\n";
            echo "--------------------------\r\n";
        }
        exit;
    }
    
    // NEW: PDF Report Generator (Using Print View)
    public function report($mandor_id)
    {
        $session = session();
        if(!$session->get('logged_in') || $session->get('role') != 'admin'){
            return redirect()->to('/auth');
        }

        $db = \Config\Database::connect();
        
        // Get Mandor Info
        $mandor = $db->table('users')->where('id', $mandor_id)->get()->getRowArray();
        if(!$mandor){
            return redirect()->back()->with('error', 'Data Mandor tidak ditemukan');
        }

        // Get Input Data
        $inputs = $db->table('distribusi_gadget')
                     ->select('distribusi_gadget.*, karyawan.nama as nama_karyawan, karyawan.nik_karyawan, karyawan.jabatan, karyawan.afdeling')
                     ->join('karyawan', 'karyawan.id = distribusi_gadget.karyawan_id')
                     ->where('input_by', $mandor_id)
                     ->orderBy('input_at', 'DESC')
                     ->get()->getResultArray();

        // Verifikasi IMEI: cocokkan dengan master_gadget
        foreach($inputs as &$row) {
            $row['verifikasi'] = '-';       // default
            $row['pemilik_master'] = '';
            $row['npk_master'] = '';
            
            if($row['status_gadget'] == 'Ada' && !empty($row['imei'])) {
                $gadget = $db->table('master_gadget')
                             ->where('imei', $row['imei'])
                             ->get()->getRowArray();
                
                if(!$gadget) {
                    $row['verifikasi'] = 'not_found';
                    $row['aplikasi'] = '-';
                } else {
                    $row['aplikasi'] = $gadget['aplikasi'] ?? '-';
                    $row['pt_master'] = $gadget['pt'] ?? '-';
                    $row['afd_master'] = $gadget['afd'] ?? '-';
                    $row['pos_title_master'] = $gadget['pos_title'] ?? '-';
                    $row['tipe_asset_master'] = $gadget['tipe_asset'] ?? '-';
                    $row['group_asset_master'] = $gadget['group_asset'] ?? '-';
                    $row['part_asset_master'] = $gadget['part_asset'] ?? '-';

                    $npkOwner = trim($gadget['npk_pengguna'] ?? '');
                    $nikTarget = trim($row['nik_karyawan']);
                    $row['pemilik_master'] = $gadget['nama_pengguna'] ?? '';
                    $row['npk_master'] = $npkOwner;
                    
                    // Substring match (NIK = NPK + digit tambahan)
                    if($npkOwner && $nikTarget) {
                        if(str_contains($nikTarget, $npkOwner) || str_contains($npkOwner, $nikTarget)) {
                            $row['verifikasi'] = 'cocok';
                        } else {
                            $row['verifikasi'] = 'tidak_cocok';
                        }
                    } else {
                        $row['verifikasi'] = 'no_owner';
                    }
                }
            }
        }
        unset($row);

        $data = [
            'mandor' => $mandor,
            'inputs' => $inputs,
            'generated_at' => date('d F Y H:i:s'),
        ];
        
        return view('dashboard/report_pdf', $data);
    }
}
