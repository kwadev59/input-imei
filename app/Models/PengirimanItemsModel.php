<?php
namespace App\Models;

use CodeIgniter\Model;

class PengirimanItemsModel extends Model
{
    protected $table = 'pengiriman_items';
    protected $primaryKey = 'id';
    protected $allowedFields = ['baste_id', 'imei', 'kerusakan', 'created_by', 'created_at', 'updated_at'];
    protected $useTimestamps = true;

    /**
     * Get drafts for a specific user
     */
    public function getDraftsByUser($userId)
    {
        return $this->select('pengiriman_items.*, master_gadget.aplikasi, master_gadget.pt, master_gadget.afd, master_gadget.nama_pengguna, master_gadget.npk_pengguna, master_gadget.tipe_asset')
                    ->join('master_gadget', 'master_gadget.imei = pengiriman_items.imei', 'left')
                    ->where('pengiriman_items.baste_id', null)
                    ->where('pengiriman_items.created_by', $userId)
                    ->findAll();
    }
    
    /**
     * Get items of a specific baste
     */
    public function getItemsByBaste($basteId)
    {
         return $this->select('pengiriman_items.*, master_gadget.aplikasi, master_gadget.pt, master_gadget.afd, master_gadget.nama_pengguna, master_gadget.npk_pengguna, master_gadget.tipe_asset')
                    ->join('master_gadget', 'master_gadget.imei = pengiriman_items.imei', 'left')
                    ->where('pengiriman_items.baste_id', $basteId)
                    ->findAll();
    }
}
