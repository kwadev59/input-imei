<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\KaryawanModel;
use App\Models\MasterGadgetModel;
use CodeIgniter\API\ResponseTrait;

class DataApi extends BaseController
{
    use ResponseTrait;

    protected $karyawanModel;
    protected $gadgetModel;

    public function __construct()
    {
        $this->karyawanModel = new KaryawanModel();
        $this->gadgetModel = new MasterGadgetModel();
    }

    /**
     * Get Karyawan Data
     * GET /api/data/karyawan
     */
    public function getKaryawan()
    {
        $search = $this->request->getGet('search');
        $nik = $this->request->getGet('nik');
        $limit = $this->request->getGet('limit');
        $page = $this->request->getGet('page');
        
        // Handle perPage: use per_page param, or limit param (if numeric), or default to a very large number
        $perPage = $this->request->getGet('per_page');
        if (!$perPage) {
            $perPage = (is_numeric($limit) && (int)$limit > 0) ? (int)$limit : 10000; // Default to 10000 to "Open Limitation"
        }

        $model = $this->karyawanModel;

        if ($nik) {
            $data = $model->where('nik_karyawan', $nik)->first();
            if (!$data) return $this->failNotFound('Karyawan dengan NIK tersebut tidak ditemukan');
            return $this->respond($data);
        }

        if ($search) {
            $model->groupStart()
                  ->like('nama', $search)
                  ->orLike('nik_karyawan', $search)
                  ->groupEnd();
        }

        // If pagination is requested (e.g. ?page=1)
        if ($page) {
            $data = $model->paginate((int)$perPage, 'default', (int)$page);
            return $this->respond([
                'status' => 200,
                'data'   => $data,
                'pager'  => [
                    'current_page' => $model->pager->getCurrentPage(),
                    'page_count'   => $model->pager->getPageCount(),
                    'total_items'  => $model->pager->getTotal(),
                    'per_page'     => $model->pager->getPerPage(),
                ]
            ]);
        }

        // Default behavior (No Page parameter)
        // If limit is 'all', '0', or not provided, we return all (null limit in findAll)
        $limitVal = (is_numeric($limit) && (int)$limit > 0) ? (int)$limit : (($limit === '0' || $limit === 'all' || $limit === null) ? null : null);
        
        $data = $model->findAll($limitVal);
        return $this->respond($data);
    }

    /**
     * Get Gadget Data
     * GET /api/data/gadget
     */
    public function getGadget()
    {
        $imei = $this->request->getGet('imei');
        $npk = $this->request->getGet('npk');
        $limit = $this->request->getGet('limit');
        $page = $this->request->getGet('page');
        
        // Handle perPage
        $perPage = $this->request->getGet('per_page');
        if (!$perPage) {
            $perPage = (is_numeric($limit) && (int)$limit > 0) ? (int)$limit : 10000;
        }

        $model = $this->gadgetModel;

        if ($imei) {
            $data = $model->where('imei', $imei)->first();
            if (!$data) return $this->failNotFound('Gadget dengan IMEI tersebut tidak ditemukan');
            return $this->respond($data);
        }

        if ($npk) {
            $model->where('npk_pengguna', $npk);
        }

        // If pagination is requested
        if ($page) {
            $data = $model->paginate((int)$perPage, 'default', (int)$page);
            return $this->respond([
                'status' => 200,
                'data'   => $data,
                'pager'  => [
                    'current_page' => $model->pager->getCurrentPage(),
                    'page_count'   => $model->pager->getPageCount(),
                    'total_items'  => $model->pager->getTotal(),
                    'per_page'     => $model->pager->getPerPage(),
                ]
            ]);
        }

        // Default behavior
        $limitVal = (is_numeric($limit) && (int)$limit > 0) ? (int)$limit : (($limit === '0' || $limit === 'all' || $limit === null) ? null : null);

        $data = $model->findAll($limitVal);
        return $this->respond($data);
    }
}
