<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Auth::index');
$routes->get('/auth', 'Auth::index');
$routes->post('/auth/process', 'Auth::process');
$routes->get('/auth/logout', 'Auth::logout');

$routes->get('/dashboard', 'Dashboard::index');
$routes->get('/dashboard/export', 'Dashboard::export');
$routes->get('/dashboard/export_gadget_stats', 'Dashboard::export_gadget_stats');
$routes->get('/dashboard/export_latest_inputs_txt', 'Dashboard::export_latest_inputs_txt');
$routes->get('/dashboard/report/(:num)', 'Dashboard::report/$1'); // New Route for PDF Report

$routes->get('/mandor', 'Mandor::index'); // New Route for List Mandor
$routes->get('/mandor/gadgets', 'Mandor::gadgets'); // New Route for List Gadget Mandor
$routes->post('/mandor/save-gadget', 'Mandor::save_gadget'); // Save/Assign Gadget to Mandor
$routes->post('/mandor/import', 'Mandor::import'); // Import Mandor Feature
$routes->post('/mandor/change-password/(:num)', 'Mandor::changePassword/$1'); // Change Password
$routes->post('/mandor/change-tipe/(:num)', 'Mandor::changeTipe/$1'); // Change Tipe Mandor

// Settings
$routes->get('/settings/popup', 'Settings::popup');
$routes->post('/settings/popup', 'Settings::popup');

// Public Mandor Input
$routes->get('/public/input-gadget', 'PublicController::input_gadget');
$routes->post('/public/input-gadget', 'PublicController::save_gadget');
$routes->post('/public/validate-imei', 'PublicController::validate_imei');

// Karyawan Management
$routes->get('/karyawan', 'Karyawan::index');
$routes->get('/karyawan/create', 'Karyawan::create');
$routes->post('/karyawan/store', 'Karyawan::store');
$routes->get('/karyawan/export', 'Karyawan::export');
$routes->get('/karyawan/edit/(:num)', 'Karyawan::edit/$1');
$routes->get('/karyawan/riwayat/(:num)', 'Karyawan::riwayat/$1');
$routes->post('/karyawan/update/(:num)', 'Karyawan::update/$1');
$routes->post('/karyawan/import', 'Karyawan::import');

// Master Gadget
$routes->get('/api_npk/get_npk', 'Api_npk::get_npk');
$routes->get('/api_npk/get_gadget', 'Api_npk::get_gadget');
$routes->get('/real-karyawan', 'RealKaryawan::index');
$routes->get('/real-gadget', 'RealGadget::index');
$routes->get('/gadget', 'Gadget::index');
$routes->post('/gadget/import', 'Gadget::import');
$routes->get('/download/template', 'Gadget::downloadTemplate'); // Generic Template Downloader

// Rekap Hasil Input Mandor
$routes->get('/laporan', 'Laporan::index'); // Detailed Recap
$routes->get('/laporan/export', 'Laporan::export'); // Filtered Export
$routes->post('/laporan/delete/(:num)', 'Laporan::delete/$1'); // Delete single input
$routes->post('/laporan/delete-all', 'Laporan::deleteAll'); // Delete all inputs

// Rekap Afdeling
$routes->get('/rekap', 'Rekap::index');

// Rekap MPP
$routes->get('/rekap-mpp', 'RekapMpp::index');

// Rekon Gadget
$routes->get('/rekon-gadget', 'RekonGadget::index');

// Gadget Dobel
$routes->get('/gadget-dobel', 'GadgetDobel::index');

$routes->get('/input', 'Input::index');
$routes->get('/input/create', 'Input::create'); // New Route
$routes->post('/input/check-imei', 'Input::checkImei'); // Real-time IMEI Check
$routes->post('/input/store', 'Input::store');
$routes->get('/input/edit/(:num)', 'Input::edit/$1'); // Edit Draft
$routes->post('/input/update/(:num)', 'Input::update/$1'); // Update Draft

// Pengiriman Gadget
$routes->get('/pengiriman-gadget', 'PengirimanGadget::index');
$routes->get('/pengiriman-gadget/draft', 'PengirimanGadget::draft');
$routes->post('/pengiriman-gadget/check-imei', 'PengirimanGadget::checkImei');
$routes->post('/pengiriman-gadget/save-draft', 'PengirimanGadget::saveDraft');
$routes->post('/pengiriman-gadget/save-draft-batch', 'PengirimanGadget::saveDraftBatch');
$routes->get('/pengiriman-gadget/delete-draft/(:num)', 'PengirimanGadget::deleteDraft/$1');
$routes->post('/pengiriman-gadget/update-draft-kerusakan/(:num)', 'PengirimanGadget::updateDraftKerusakan/$1');
$routes->post('/pengiriman-gadget/submit-baste', 'PengirimanGadget::submitBaste');
$routes->get('/pengiriman-gadget/detail/(:num)', 'PengirimanGadget::detail/$1');
$routes->get('/pengiriman-gadget/edit/(:num)', 'PengirimanGadget::edit/$1');
$routes->post('/pengiriman-gadget/update/(:num)', 'PengirimanGadget::update/$1');
$routes->post('/pengiriman-gadget/delete/(:num)', 'PengirimanGadget::delete/$1');
$routes->get('/pengiriman-gadget/print/(:num)', 'PengirimanGadget::printPdf/$1');
$routes->get('/pengiriman-gadget/edit-item/(:num)', 'PengirimanGadget::editItem/$1');
$routes->post('/pengiriman-gadget/update-item/(:num)', 'PengirimanGadget::updateItem/$1');
$routes->post('/pengiriman-gadget/delete-item/(:num)', 'PengirimanGadget::deleteItem/$1');

// =====================================================
// API Print Queue (ESC/POS via Bash Agent)
// =====================================================
$routes->group('api/print', function ($routes) {
    // [Frontend] Kirim job ke antrian
    $routes->post('', 'Api\PrintQueue::submit');

    // [Agent] Ambil job berikutnya (polling)
    $routes->get('queue', 'Api\PrintQueue::queue');

    // [Agent] Lapor status selesai/gagal
    $routes->post('done/(:num)', 'Api\PrintQueue::done/$1');

    // [Frontend/Admin] Cek status antrian
    $routes->get('status', 'Api\PrintQueue::status');
});
