<?php namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\DistribusiGadgetModel;

class Laporan extends Controller
{
    public function index()
    {
        $session = session();
        if(!$session->get('logged_in') || $session->get('role') != 'admin'){
            return redirect()->to('/auth');
        }

        $model = new DistribusiGadgetModel();
        $db = \Config\Database::connect();
        
        $filter_afdeling = $this->request->getVar('afdeling');
        $filter_status = $this->request->getVar('status');
        $search = $this->request->getVar('search');

        $builder = $model->select('distribusi_gadget.*, karyawan.nama as nama_karyawan, karyawan.nik_karyawan, karyawan.afdeling, karyawan.pt_site, karyawan.jabatan, users.nama_lengkap as nama_mandor, users.afdeling_id as mandor_afdeling')
                          ->join('karyawan', 'karyawan.id = distribusi_gadget.karyawan_id')
                          ->join('users', 'users.id = distribusi_gadget.input_by');

        if($filter_afdeling){
            $builder->where('karyawan.afdeling', $filter_afdeling);
        }
        if($filter_status){
            $builder->where('distribusi_gadget.status_gadget', $filter_status);
        }
        if($search){
            $builder->groupStart()
                    ->like('karyawan.nama', $search)
                    ->orLike('karyawan.nik_karyawan', $search)
                    ->orLike('users.nama_lengkap', $search)
                    ->orLike('distribusi_gadget.imei', $search)
                    ->groupEnd();
        }

        $perPage = 15;
        $data['laporan'] = $builder->orderBy('input_at', 'DESC')->paginate($perPage, 'laporan');
        $data['pager'] = $model->pager;
        $data['total_laporan'] = $data['pager']->getTotal('laporan');
        
        $data['user_nama'] = $session->get('nama');
        $data['afdeling_list'] = $db->table('karyawan')->select('afdeling')->distinct()->get()->getResultArray();
        
        // Pass filter/search values back to view
        $data['filter_afdeling'] = $filter_afdeling;
        $data['filter_status'] = $filter_status;
        $data['search'] = $search;

        return view('laporan/index', $data);
    }

    public function export()
    {
        return redirect()->to('/dashboard/export'); 
    }

    public function delete($id)
    {
        $session = session();
        if(!$session->get('logged_in') || $session->get('role') != 'admin'){
            return redirect()->to('/auth');
        }

        $model = new DistribusiGadgetModel();
        $record = $model->find($id);
        
        if(!$record){
            return redirect()->to('/laporan')->with('error', 'Data tidak ditemukan.');
        }

        $model->delete($id);
        return redirect()->to('/laporan')->with('success', 'Data inputan berhasil dihapus.');
    }

    public function deleteAll()
    {
        $session = session();
        if(!$session->get('logged_in') || $session->get('role') != 'admin'){
            return redirect()->to('/auth');
        }

        $model = new DistribusiGadgetModel();
        $model->truncate();
        
        return redirect()->to('/laporan')->with('success', 'Semua data inputan berhasil dihapus.');
    }
}
