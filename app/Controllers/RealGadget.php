<?php
namespace App\Controllers;

class RealGadget extends BaseController
{
    public function index()
    {
        $data = [
            'active_menu' => 'real_gadget',
        ];

        return view('real_gadget/index', $data);
    }
}
