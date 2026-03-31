<?php

namespace App\Controllers;

use App\Models\DistribusiGadgetModel;
use App\Models\MasterGadgetModel;

class RekonGadget extends BaseController
{
    protected $distribusiModel;
    protected $masterGadgetModel;

    public function __construct()
    {
        $this->distribusiModel = new DistribusiGadgetModel();
        $this->masterGadgetModel = new MasterGadgetModel();
    }

    public function index()
    {
        // Must be admin
        if(session()->get('role') !== 'admin') {
            return redirect()->to('/auth');
        }

        $distribusiData = $this->distribusiModel->getWithDetails();
        
        // Filter out those without IMEI
        $distribusiWithImei = array_filter($distribusiData, function($item) {
            return !empty(trim((string)$item['imei']));
        });

        // Get all master gadgets
        $masterGadgets = $this->masterGadgetModel->findAll();
        // Index by IMEI for fast lookup
        $masterImeis = [];
        foreach ($masterGadgets as $mg) {
            $masterImeis[$mg['imei']] = $mg;
        }

        $totalInput = count($distribusiWithImei);
        $totalMatch = 0;
        $totalMismatch = 0;
        
        $mismatchData = [];

        foreach ($distribusiWithImei as $item) {
            $imei = trim((string)$item['imei']);
            if (isset($masterImeis[$imei])) {
                $mg = $masterImeis[$imei];
                $nik = trim((string)$item['nik_karyawan']);
                $npk = trim((string)$mg['npk_pengguna']);
                
                $isMatch = false;
                $nikClean = ltrim($nik, '0');
                $npkClean = ltrim($npk, '0');

                if ($nik === $npk) {
                    $isMatch = true;
                } elseif ($nikClean !== '' && $npkClean !== '') {
                    // Check if one contains the other (e.g., 142396 matching 1423962)
                    if (strpos($nikClean, $npkClean) !== false || strpos($npkClean, $nikClean) !== false) {
                        $isMatch = true;
                    }
                }

                if ($isMatch) {
                    $totalMatch++;
                } else {
                    $totalMismatch++;
                    $item['master_info'] = $mg;
                    $item['mismatch_reason'] = 'IMEI terdaftar di Master, tetapi NPK (' . $npk . ') berbeda dengan NIK Karyawan (' . $nik . ').';
                    $mismatchData[] = $item;
                }
            } else {
                $totalMismatch++;
                $item['master_info'] = null;
                $item['mismatch_reason'] = 'IMEI tidak ditemukan di Master Gadget.';
                $mismatchData[] = $item;
            }
        }

        $percentageMatch = $totalInput > 0 ? round(($totalMatch / $totalInput) * 100, 2) : 0;

        $data = [
            'title' => 'Rekon Gadget',
            'active_menu' => 'rekon_gadget',
            'total_input' => $totalInput,
            'total_match' => $totalMatch,
            'total_mismatch' => $totalMismatch,
            'percentage_match' => $percentageMatch,
            'mismatch_data' => $mismatchData
        ];

        return view('rekon_gadget/index', $data);
    }
}
