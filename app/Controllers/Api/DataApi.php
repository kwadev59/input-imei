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
     * GET /api/karyawan
     */
    public function getKaryawan()
    {
        $search = $this->request->getGet('search');
        $nik = $this->request->getGet('nik');

        $query = $this->karyawanModel;

        if ($nik) {
            $data = $query->where('nik_karyawan', $nik)->first();
            if (!$data) return $this->failNotFound('Karyawan dengan NIK tersebut tidak ditemukan');
            return $this->respond($data);
        }

        if ($search) {
            $query->like('nama', $search)->orLike('nik_karyawan', $search);
        }

        $data = $query->findAll(100); // Limit 100 for safety
        return $this->respond($data);
    }

    /**
     * Get Gadget Data
     * GET /api/gadget
     */
    public function getGadget()
    {
        $imei = $this->request->getGet('imei');
        $npk = $this->request->getGet('npk');

        $query = $this->gadgetModel;

        if ($imei) {
            $data = $query->where('imei', $imei)->first();
            if (!$data) return $this->failNotFound('Gadget dengan IMEI tersebut tidak ditemukan');
            return $this->respond($data);
        }

        if ($npk) {
            $query->where('npk_pengguna', $npk);
        }

        $data = $query->findAll(100); // Limit 100 for safety
        return $this->respond($data);
    }
}
