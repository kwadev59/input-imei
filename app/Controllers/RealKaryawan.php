<?php
namespace App\Controllers;

class RealKaryawan extends BaseController
{
    public function index()
    {
        $data = [
            'active_menu' => 'real_karyawan',
        ];

        return view('real_karyawan/index', $data);
    }
}
