<?php

namespace App\Controllers;

use App\Models\KaryawanModel;
use App\Models\DistribusiGadgetModel;

class KaryawanGadget extends BaseController
{
    public function ceker()
    {
        return $this->renderList('ceker', 'KRANI', 'List Gadget Ceker');
    }

    public function mtrp()
    {
        return $this->renderList('mtrp', 'MANDOR TRANSPORT', 'List Gadget MTRP');
    }

    private function renderList($type, $jabatanKeyword, $title)
    {
        $session = session();
        if(!$session->get('logged_in') || $session->get('role') != 'admin'){
            return redirect()->to('/auth');
        }

        $db = \Config\Database::connect();
        $search = $this->request->getVar('search');

        $builder = $db->table('karyawan k');
        $builder->select('k.*, dg.imei, dg.aplikasi, dg.updated_at as reported_at');
        $builder->join('distribusi_gadget dg', 'dg.karyawan_id = k.id', 'left');
        
        // Filter berdasarkan jabatan
        $builder->like('k.jabatan', $jabatanKeyword);

        if($search){
            $builder->groupStart()
                    ->like('k.nama', $search)
                    ->orLike('k.nik_karyawan', $search)
                    ->orLike('k.afdeling', $search)
                    ->orLike('dg.imei', $search)
                    ->groupEnd();
        }

        $data['items'] = $builder->orderBy('k.afdeling', 'ASC')->get()->getResultArray();
        $data['page_title'] = $title;
        $data['active_menu'] = 'gadget_' . $type;
        $data['search'] = $search;
        $data['user_nama'] = $session->get('nama');

        return view('karyawan/gadget_list', $data);
    }
}
