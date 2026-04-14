<?php namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\SettingsModel;

class Settings extends Controller
{
    public function popup()
    {
        $session = session();
        if(!$session->get('logged_in') || $session->get('role') != 'admin'){
            return redirect()->to('/auth');
        }

        $settingsModel = new SettingsModel();
        
        if ($this->request->getMethod() === 'POST') {
            $instruction = $this->request->getVar('instruction');
            $settingsModel->set_value('mandor_popup_instruction', $instruction);
            return redirect()->back()->with('success', 'Pengaturan Pop-up berhasil diperbarui.');
        }

        $data['instruction'] = $settingsModel->get_value('mandor_popup_instruction');
        $data['user_nama'] = $session->get('nama');
        $data['active_menu'] = 'settings_popup';

        return view('settings/popup', $data);
    }
}
