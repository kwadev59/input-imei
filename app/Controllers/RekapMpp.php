<?php namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\KaryawanModel;

class RekapMpp extends Controller
{
    public function index()
    {
        $session = session();
        if(!$session->get('logged_in') || $session->get('role') != 'admin'){
            return redirect()->to('/auth');
        }

        $db = \Config\Database::connect();
        
        $sql = "
            SELECT 
                pt_site,
                afdeling,
                jabatan,
                COUNT(id) as total
            FROM karyawan 
            WHERE status_aktif = 'Aktif'
            GROUP BY pt_site, afdeling, jabatan
            ORDER BY pt_site ASC, afdeling ASC
        ";
        
        $query = $db->query($sql);
        $results = $query->getResultArray();
        
        $rekap = [
            'BIM1' => [],
            'PPS1' => []
        ];
        
        $total_bim = ['Pemanen' => 0, 'Pekerja Rawat' => 0, 'Infild' => 0];
        $total_pps = ['Pemanen' => 0, 'Pekerja Rawat' => 0, 'Infild' => 0];
        
        foreach($results as $row) {
            $site = strtolower(trim($row['pt_site']));
            $actual_site = null;
            if(strpos($site, 'bim') !== false) {
                $actual_site = 'BIM1';
            } elseif(strpos($site, 'pps') !== false) {
                $actual_site = 'PPS1';
            }
            
            if(!$actual_site) continue;
            
            $afd = $row['afdeling'];
            $jab = strtolower(trim($row['jabatan']));
            
            $kat = null;
            if(strpos($jab, 'pemanen') !== false) {
                $kat = 'Pemanen';
            } elseif(strpos($jab, 'rawat') !== false || strpos($jab, 'perawatan') !== false) {
                $kat = 'Pekerja Rawat';
            } elseif(strpos($jab, 'infild') !== false || strpos($jab, 'infield') !== false) {
                $kat = 'Infild';
            }
            
            // Only aggregate if category is recognized
            if(!$kat) continue;
            
            if(!isset($rekap[$actual_site][$afd])) {
                $rekap[$actual_site][$afd] = ['Pemanen' => 0, 'Pekerja Rawat' => 0, 'Infild' => 0];
            }
            
            $rekap[$actual_site][$afd][$kat] += (int) $row['total'];
            
            if($actual_site == 'BIM1') $total_bim[$kat] += (int) $row['total'];
            if($actual_site == 'PPS1') $total_pps[$kat] += (int) $row['total'];
        }
        
        $data = [
            'rekap' => $rekap,
            'total_bim' => $total_bim,
            'total_pps' => $total_pps,
            'user_nama' => $session->get('nama')
        ];
        
        return view('rekap_mpp/index', $data);
    }
}
