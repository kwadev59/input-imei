<?php

namespace App\Controllers;

use App\Models\DistribusiGadgetModel;
use App\Models\MasterGadgetModel;

class GadgetDobel extends BaseController
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
        
        // 1. Ambil data inputan mandor karyawan yang memiliki gadget
        //    untuk Karyawan Pemanen dan Pekerja Rawat
        $distribusiFiltered = array_filter($distribusiData, function($item) {
            $jabatan = strtolower(trim($item['jabatan']));
            $hasGadget = ($item['status_gadget'] === 'Ada' || !empty(trim((string)$item['imei'])));
            $isTargetJob = (strpos($jabatan, 'pemanen') !== false || strpos($jabatan, 'rawat') !== false);
            return $hasGadget && $isTargetJob;
        });

        $masterGadgets = $this->masterGadgetModel->findAll();
        
        // Only consider master gadgets with status "terpakai"
        $masterTerpakai = array_filter($masterGadgets, function($mg) {
            return stripos($mg['status_desc'], 'terpakai') !== false;
        });

        $dobelData = [];

        foreach ($distribusiFiltered as $item) {
            $nikClean = ltrim(trim((string)$item['nik_karyawan']), '0');
            $mandorImei = trim((string)$item['imei']);
            
            // Find all matching master gadgets
            $matchedMaster = [];
            $allImeis = [];
            
            if ($mandorImei !== '') {
                $allImeis[] = $mandorImei;
            }

            foreach ($masterTerpakai as $mg) {
                $npkClean = ltrim(trim((string)$mg['npk_pengguna']), '0');
                
                $isMatch = false;
                if ($nikClean === $npkClean) {
                    $isMatch = true;
                } elseif ($nikClean !== '' && $npkClean !== '') {
                    if (strpos($nikClean, $npkClean) !== false || strpos($npkClean, $nikClean) !== false) {
                        $isMatch = true;
                    }
                }
                
                if ($isMatch) {
                    $matchedMaster[] = $mg;
                    $mgImei = trim((string)$mg['imei']);
                    if ($mgImei !== '' && !in_array($mgImei, $allImeis)) {
                         $allImeis[] = $mgImei;
                    }
                }
            }

            // A user has "gadget dobel" if they have more than 1 unique IMEI in total 
            // OR if they have more than 1 terpakai gadgets in Master.
            if (count($allImeis) > 1 || count($matchedMaster) > 1) {
                
                // Cross-check with distribusiData to see if the master gadgets' IMEIs
                // are currently inputted by a mandor for ANY other employee.
                foreach ($matchedMaster as &$mgadget) {
                    $mgImeiCheck = trim((string)$mgadget['imei']);
                    $mgadget['dipakai_oleh_input'] = []; // To store who holds this IMEI from Mandor input

                    if ($mgImeiCheck !== '') {
                        foreach ($distribusiData as $dCross) {
                            if (trim((string)$dCross['imei']) === $mgImeiCheck && 
                                $dCross['karyawan_id'] !== $item['karyawan_id']) { // Check people other than the current person
                                $mgadget['dipakai_oleh_input'][] = [
                                    'nama' => $dCross['nama_karyawan'],
                                    'nik' => $dCross['nik_karyawan'],
                                    'mandor' => $dCross['nama_mandor']
                                ];
                            }
                        }
                    }
                }

                $item['master_gadgets'] = $matchedMaster;
                $dobelData[] = $item;
            }
        }

        $data = [
            'title' => 'List Gadget Dobel',
            'active_menu' => 'gadget_dobel',
            'dobel_data' => $dobelData
        ];

        return view('gadget_dobel/index', $data);
    }
}
