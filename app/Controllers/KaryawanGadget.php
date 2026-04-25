<?php

namespace App\Controllers;

use App\Models\KaryawanModel;
use App\Models\DistribusiGadgetModel;

class KaryawanGadget extends BaseController
{
    public function ceker()
    {
        $afdelings = ['OA', 'OB', 'OC', 'OD', 'OE', 'OF', 'OG'];
        return $this->renderList('ceker', 'KRANI', 'List Gadget Ceker', $afdelings);
    }

    public function mtrp()
    {
        return $this->renderList('mtrp', 'MANDOR TRANSPORT', 'List Gadget MTRP');
    }

    private function renderList($type, $jabatanKeyword, $title, $afdelingFilter = [])
    {
        $session = session();
        if(!$session->get('logged_in') || $session->get('role') != 'admin'){
            return redirect()->to('/auth');
        }

        $db = \Config\Database::connect();
        $search = $this->request->getVar('search');

        $builder = $db->table('karyawan k');
        $builder->select('k.*, dg.imei, dg.updated_at as reported_at, mg.aplikasi');
        $builder->join('distribusi_gadget dg', 'dg.karyawan_id = k.id', 'left');
        $builder->join('master_gadget mg', 'mg.imei = dg.imei', 'left');
        
        // Filter berdasarkan jabatan
        $builder->like('k.jabatan', $jabatanKeyword);

        // Filter berdasarkan afdeling jika ada
        if(!empty($afdelingFilter)){
            $builder->whereIn('k.afdeling', $afdelingFilter);
        }

        if($search){
            $builder->groupStart()
                    ->like('k.nama', $search)
                    ->orLike('k.nik_karyawan', $search)
                    ->orLike('k.afdeling', $search)
                    ->orLike('dg.imei', $search)
                    ->orLike('mg.aplikasi', $search)
                    ->groupEnd();
        }

        $data['items'] = $builder->orderBy('k.afdeling', 'ASC')->get()->getResultArray();

        // Ambil daftar aplikasi untuk dropdown di modal
        $apps = $db->table('master_gadget')
                   ->select('aplikasi')
                   ->where('aplikasi IS NOT NULL')
                   ->where('aplikasi !=', '')
                   ->distinct()
                   ->get()->getResultArray();
        $data['applications'] = array_column($apps, 'aplikasi');

        $data['page_title'] = $title;
        $data['active_menu'] = 'gadget_' . $type;
        $data['search'] = $search;
        $data['user_nama'] = $session->get('nama');

        return view('karyawan/gadget_list', $data);
    }

    /**
     * Save/Update gadget assignment for Ceker/MTRP
     */
    public function save_gadget_karyawan()
    {
        $session = session();
        if(!$session->get('logged_in') || $session->get('role') != 'admin'){
            return redirect()->to('/auth');
        }

        $karyawanId = $this->request->getPost('karyawan_id');
        $imei = $this->request->getPost('imei');
        $aplikasi = $this->request->getPost('aplikasi');

        if (!$karyawanId || !$imei) {
            return redirect()->back()->with('error', 'Data tidak lengkap.');
        }

        $db = \Config\Database::connect();
        
        // 1. Ambil data karyawan untuk sinkronisasi
        $karyawan = $db->table('karyawan')->where('id', $karyawanId)->get()->getRowArray();

        // 2. Simpan/Update ke distribusi_gadget
        $exist = $db->table('distribusi_gadget')->where('karyawan_id', $karyawanId)->get()->getRowArray();

        $data = [
            'karyawan_id'   => $karyawanId,
            'imei'          => $imei,
            'status_gadget' => 'Ada',
            'input_by'      => $session->get('id'),
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

        // 3. Update/Insert ke master_gadget agar data aplikasi tersinkron
        $master = $db->table('master_gadget')->where('imei', $imei)->get()->getRowArray();
        if ($master) {
            $db->table('master_gadget')->where('imei', $imei)->update(['aplikasi' => $aplikasi]);
        } else {
            // Jika belum ada di master, buatkan record baru agar relasi join berjalan
            $db->table('master_gadget')->insert([
                'imei' => $imei,
                'aplikasi' => $aplikasi,
                'pt' => $karyawan['pt_site'] ?? '',
                'afdeling' => $karyawan['afdeling'] ?? '',
                'nama_pengguna' => $karyawan['nama'] ?? '',
                'npk' => $karyawan['nik_karyawan'] ?? '',
                'status' => 'Aktif'
            ]);
        }

        return redirect()->back()->with('success', 'Data gadget berhasil disimpan.');
    }
}
