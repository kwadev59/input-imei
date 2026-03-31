<?php namespace App\Controllers;

use CodeIgniter\Controller;

class Rekap extends Controller
{
    public function index()
    {
        $session = session();
        if(!$session->get('logged_in') || $session->get('role') != 'admin'){
            return redirect()->to('/auth');
        }

        $db = \Config\Database::connect();

        $pt_map = [
            'BIM1' => 'PT BORNEO INDAH MARJAYA',
            'PPS1' => 'PT PALMA PLANTASINDO',
        ];

        // 1. Get raw statistics per PT and Afdeling in one query
        $rawStats = $db->query("
            SELECT 
                pt_site,
                afdeling,
                COUNT(id) as total_karyawan,
                SUM(CASE WHEN id IN (SELECT karyawan_id FROM distribusi_gadget) THEN 1 ELSE 0 END) as sudah_input
            FROM karyawan
            WHERE status_aktif = 'Aktif'
              AND (jabatan LIKE '%PEMANEN%' OR jabatan LIKE '%PANEN%' OR jabatan LIKE '%GANDENG%' OR jabatan LIKE '%RAWAT%' OR jabatan LIKE '%PERAWATAN%' OR jabatan LIKE '%PUPUK%' OR jabatan LIKE '%SEMPROT%')
            GROUP BY pt_site, afdeling
            ORDER BY pt_site ASC, afdeling ASC
        ")->getResultArray();

        // 2. We need details of who hasn't been inputted yet. Get them in one query.
        $belumDetails = $db->query("
            SELECT pt_site, afdeling, nik_karyawan, nama, jabatan
            FROM karyawan
            WHERE status_aktif = 'Aktif'
              AND (jabatan LIKE '%PEMANEN%' OR jabatan LIKE '%PANEN%' OR jabatan LIKE '%GANDENG%' OR jabatan LIKE '%RAWAT%' OR jabatan LIKE '%PERAWATAN%' OR jabatan LIKE '%PUPUK%' OR jabatan LIKE '%SEMPROT%')
              AND id NOT IN (SELECT karyawan_id FROM distribusi_gadget)
            ORDER BY pt_site ASC, afdeling ASC, nama ASC
        ")->getResultArray();

        // Group the $belumDetails by pt_site and afdeling
        $belumGrouped = [];
        foreach ($belumDetails as $b) {
            $key = $b['pt_site'] . '|' . $b['afdeling'];
            if (!isset($belumGrouped[$key])) $belumGrouped[$key] = [];
            $belumGrouped[$key][] = $b;
        }

        // 3. Transform into the expected output structure
        $rekapMap = [];
        $grandTotal = 0;
        $grandSudah = 0;
        $grandBelum = 0;

        foreach ($rawStats as $stat) {
            $ptSite = $stat['pt_site'];
            if (!isset($rekapMap[$ptSite])) {
                $rekapMap[$ptSite] = [
                    'pt_site' => $ptSite,
                    'afdelings' => [],
                    'total' => 0,
                    'sudah' => 0,
                    'belum' => 0,
                ];
            }

            $key = $ptSite . '|' . $stat['afdeling'];
            $belumList = $belumGrouped[$key] ?? [];
            $belumCount = $stat['total_karyawan'] - $stat['sudah_input'];

            $rekapMap[$ptSite]['afdelings'][] = [
                'afdeling' => $stat['afdeling'],
                'total' => (int)$stat['total_karyawan'],
                'sudah' => (int)$stat['sudah_input'],
                'belum' => $belumCount,
                'belum_list' => $belumList
            ];

            $rekapMap[$ptSite]['total'] += $stat['total_karyawan'];
            $rekapMap[$ptSite]['sudah'] += $stat['sudah_input'];
            $rekapMap[$ptSite]['belum'] += $belumCount;

            $grandTotal += $stat['total_karyawan'];
            $grandSudah += $stat['sudah_input'];
            $grandBelum += $belumCount;
        }

        $rekap = array_values($rekapMap);
        $pt_map = [
            'BIM1' => 'PT BORNEO INDAH MARJAYA',
            'PPS1' => 'PT PALMA PLANTASINDO',
        ];

        $data = [
            'rekap' => $rekap,
            'pt_map' => $pt_map,
            'grand_total' => $grandTotal,
            'grand_sudah' => $grandSudah,
            'grand_belum' => $grandBelum,
            'active_menu' => 'rekap',
            'user_nama' => $session->get('nama'),
        ];

        return view('rekap/index', $data);
    }
}
