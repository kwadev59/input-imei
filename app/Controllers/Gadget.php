<?php namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\MasterGadgetModel;

class Gadget extends Controller
{
    public function index()
    {
        $session = session();
        if(!$session->get('logged_in') || $session->get('role') != 'admin'){
            return redirect()->to('/auth');
        }

        $model = new MasterGadgetModel();
        $search = $this->request->getVar('search');

        if($search){
            $model->groupStart()
                  ->like('imei', $search)
                  ->orLike('nama_pengguna', $search)
                  ->orLike('npk_pengguna', $search)
                  ->groupEnd();
        }

        $data['gadgets'] = $model->paginate(20, 'gadget');
        $data['pager'] = $model->pager;
        $data['search'] = $search;
        $data['user_nama'] = $session->get('nama');

        return view('gadget/index', $data);
    }

    public function downloadTemplate()
    {
        $type = $this->request->getVar('type');
        
        $headers = [];
        $filename = "";

        if($type == 'karyawan'){
            $filename = "template_karyawan_import.csv";
            $headers = ['NIK', 'NAMA', 'JABATAN', 'AFDELING', 'STATUS', 'PT_SITE']; 
            // example: 12345, Budi, Pemanen, AFD-01, Aktif
        } elseif ($type == 'mandor'){
            $filename = "template_mandor_import.csv";
            $headers = ['NPK', 'NAMA_LENGKAP', 'AFDELING', 'TIPE', 'PT_SITE'];
        } elseif ($type == 'gadget'){
            $filename = "template_master_gadget.csv";
            $headers = ['IMEI', 'APLIKASI', 'PT', 'AFD', 'NPK_PENGGUNA', 'NAMA', 'POS_TITLE', 'GROUP_ASSET', 'TIPE_ASSET', 'PART_ASSET', 'JUMLAH', 'ASAL_DESC', 'STATUS_DESC', 'NOTE', 'ACTION'];
        }

        if(empty($headers)) return redirect()->back();

        $output = fopen('php://temp', 'w');
        fputcsv($output, $headers);
        
        // Add sample data
        if($type == 'karyawan'){
            fputcsv($output, ['KRY001', 'Contoh Nama', 'Pemanen', 'AFD-01', 'Aktif', 'PT ABC SITE 1']);
        } elseif ($type == 'mandor'){
            fputcsv($output, ['MANDOR01', 'Contoh Mandor', 'AFD-01', 'Panen', 'PT ABC SITE 1']);
        } elseif ($type == 'gadget'){
            fputcsv($output, ['865123456789012', 'E-Harvest', 'PT. ABC', 'AFD-01', '12345', 'User A', 'Asisten', 'Gadget', 'Smartphone', 'Samsung A10', '1', 'HO', 'Baik', '-', '-']);
        }
        
        rewind($output);
        $content = stream_get_contents($output);
        fclose($output);

        return $this->response
            ->setHeader('Content-Type', 'text/csv')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($content);
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

        $csv = array_map('str_getcsv', file($file->getTempName()));
        $model = new MasterGadgetModel();
        $count = 0;
        
        $header = array_shift($csv); // Skip header

        foreach($csv as $row){
            if(count($row) < 14) continue; // Basic validation
            
            // Clean IMEI specifically in case of scientific notation from Excel (e.g. 3.5653E+14)
            $imeiRaw = trim((string)$row[0]);
            if (stripos($imeiRaw, 'E+') !== false) {
                // Konversi scientific notation menjadi full digit (meskipun kehilangan precision detail dari CSV asalnya)
                $imeiRaw = number_format((float)$imeiRaw, 0, '', '');
            }

            $data = [
                'imei' => $imeiRaw,
                'aplikasi' => $row[1] ?? '',
                'pt' => $row[2] ?? '',
                'afd' => $row[3] ?? '',
                'npk_pengguna' => $row[4] ?? '',
                'nama_pengguna' => $row[5] ?? '',
                'pos_title' => $row[6] ?? '',
                'group_asset' => $row[7] ?? '',
                'tipe_asset' => $row[8] ?? '',
                'part_asset' => $row[9] ?? '',
                'jumlah' => $row[10] ?? 1,
                'asal_desc' => $row[11] ?? '',
                'status_desc' => $row[12] ?? '',
                'note' => $row[13] ?? '',
                'action_desc' => $row[14] ?? '',
            ];

            // Update or Insert based on IMEI
            $exist = $model->where('imei', $data['imei'])->first();
            if($exist){
                $model->update($exist['id'], $data);
            } else {
                $model->insert($data);
            }
            $count++;
        }

        return redirect()->to('/gadget')->with('success', "$count Data Gadget berhasil diimport.");
    }
}
