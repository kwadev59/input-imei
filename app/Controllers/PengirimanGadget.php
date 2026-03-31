<?php
namespace App\Controllers;

use App\Models\MasterGadgetModel;
use App\Models\PengirimanItemsModel;
use App\Models\PengirimanBasteModel;
use Mpdf\Mpdf;

class PengirimanGadget extends BaseController
{
    protected $masterGadgetModel;
    protected $itemsModel;
    protected $basteModel;

    public function __construct()
    {
        $this->masterGadgetModel = new MasterGadgetModel();
        $this->itemsModel = new PengirimanItemsModel();
        $this->basteModel = new PengirimanBasteModel();
    }

    /**
     * List of all Submitted Baste Pengiriman
     */
    public function index()
    {
        $bastes = $this->basteModel->orderBy('created_at', 'DESC')->findAll();
        
        $data = [
            'active_menu' => 'pengiriman_gadget',
            'bastes' => $bastes
        ];

        return view('pengiriman_gadget/index', $data);
    }

    /**
     * Draft View to input Gadget Pengiriman
     */
    public function draft()
    {
        $userId = session()->get('id');
        $drafts = $this->itemsModel->getDraftsByUser($userId);
        
        $data = [
            'active_menu' => 'buat_pengiriman',
            'drafts' => $drafts
        ];

        return view('pengiriman_gadget/draft', $data);
    }
    
    /**
     * Ajax - Check IMEI in Master Gadget
     */
    public function checkImei()
    {
        $imei = $this->request->getPost('imei');
        $gadget = $this->masterGadgetModel->where('imei', $imei)->first();
        if ($gadget) {
            return $this->response->setJSON(['status' => 'success', 'data' => $gadget]);
        }
        return $this->response->setJSON(['status' => 'error', 'message' => 'IMEI tidak ditemukan di Master Gadget']);
    }

    /**
     * Action to Save item into Draft
     */
    public function saveDraft()
    {
        $userId = session()->get('id');
        if (!$userId) {
            return redirect()->to('/auth')->with('error', 'Sesi login telah berakhir. Silakan login kembali.');
        }

        $imei = $this->request->getPost('imei');
        $kerusakan = $this->request->getPost('kerusakan');
        
        // Verify IMEI
        $gadget = $this->masterGadgetModel->where('imei', $imei)->first();
        if (!$gadget) {
            return redirect()->to('pengiriman-gadget/draft')->with('error', 'Gagal menambahkan draft, IMEI tidak valid.');
        }

        // Check if already in draft
        $existingDraft = $this->itemsModel->where('imei', $imei)
            ->where('baste_id', null)
            ->where('created_by', $userId)
            ->first();
        
        if ($existingDraft) {
            return redirect()->to('pengiriman-gadget/draft')->with('error', 'Gadget dengan IMEI ini sudah ada di Draft Anda.');
        }

        $this->itemsModel->save([
            'imei' => $imei,
            'kerusakan' => $kerusakan,
            'baste_id' => null,
            'created_by' => $userId
        ]);

        return redirect()->to('pengiriman-gadget/draft')->with('success', 'Gadget berhasil ditambahkan ke Draft.');
    }

    /**
     * Action to Save BATCH items into Draft (from the temporary list)
     */
    public function saveDraftBatch()
    {
        $userId    = session()->get('id');
        if (!$userId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Sesi login telah berakhir. Silakan login kembali.']);
        }
        
        $itemsJson = $this->request->getPost('items');
        $items     = json_decode($itemsJson, true);

        if (empty($items) || !is_array($items)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Tidak ada item untuk disimpan.']);
        }

        $saved   = 0;
        $skipped = 0;

        foreach ($items as $item) {
            $imei      = trim($item['imei'] ?? '');
            $kerusakan = trim($item['kerusakan'] ?? '');

            if (empty($imei)) { $skipped++; continue; }

            // Verify IMEI exists in master
            $gadget = $this->masterGadgetModel->where('imei', $imei)->first();
            if (!$gadget) { $skipped++; continue; }

            // Skip if already saved as draft
            $existing = $this->itemsModel->where('imei', $imei)
                ->where('baste_id', null)
                ->where('created_by', $userId)
                ->first();
            if ($existing) { $skipped++; continue; }

            $this->itemsModel->save([
                'imei'      => $imei,
                'kerusakan' => $kerusakan,
                'baste_id'  => null,
                'created_by'=> $userId
            ]);
            $saved++;
        }

        $message = "Berhasil menyimpan {$saved} item ke Draft.";
        if ($skipped > 0) {
            $message .= " {$skipped} item dilewati (IMEI tidak valid atau sudah ada di draft).";
        }

        return $this->response->setJSON(['status' => 'success', 'message' => $message, 'saved' => $saved, 'skipped' => $skipped]);
    }

    /**
     * Action to Update kerusakan of a single Draft item (AJAX)
     */
    public function updateDraftKerusakan($id)
    {
        $userId    = session()->get('id');
        if (!$userId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Sesi login telah berakhir.']);
        }
        
        $kerusakan = $this->request->getPost('kerusakan');
        $draft     = $this->itemsModel->where('id', $id)->where('created_by', $userId)->where('baste_id', null)->first();

        if (!$draft) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Draft tidak ditemukan.']);
        }

        $this->itemsModel->update($id, ['kerusakan' => $kerusakan]);
        return $this->response->setJSON(['status' => 'success', 'message' => 'Keterangan berhasil diperbarui.']);
    }

    /**
     * Action to Delete Draft Item
     */
    public function deleteDraft($id)
    {
        $userId = session()->get('id');
        if (!$userId) {
            return redirect()->to('/auth')->with('error', 'Sesi login telah berakhir.');
        }

        $draft = $this->itemsModel->where('id', $id)->where('created_by', $userId)->where('baste_id', null)->first();
        
        if ($draft) {
             $this->itemsModel->delete($id);
             return redirect()->to('pengiriman-gadget/draft')->with('success', 'Draft berhasil dihapus.');
        }
        
        return redirect()->to('pengiriman-gadget/draft')->with('error', 'Draft tidak valid atau tidak dapat dihapus.');
    }
    
    /**
     * Submit all Drafts into A BASTE
     */
    public function submitBaste()
    {
        $userId = session()->get('id');
        if (!$userId) {
            return redirect()->to('/auth')->with('error', 'Sesi login telah berakhir.');
        }
        
        $drafts = $this->itemsModel->where('created_by', $userId)->where('baste_id', null)->findAll();
        
        if (empty($drafts)) {
            return redirect()->to('pengiriman-gadget/draft')->with('error', 'Draft kosong, tidak dapat membuat BASTE.');
        }

        // Create BASTE Parent
        $noBaste = $this->basteModel->generateNoBaste();
        $this->basteModel->save([
            'no_baste' => $noBaste,
            'tanggal' => date('Y-m-d'),
            'created_by' => $userId
        ]);
        
        $basteId = $this->basteModel->insertID();
        
        // Update all drafts to have baste_id
        foreach ($drafts as $draft) {
            $this->itemsModel->update($draft['id'], [
                'baste_id' => $basteId
            ]);
        }
        
        return redirect()->to('pengiriman-gadget')->with('success', 'Berhasil melakukan pengiriman gadget dari draft. No Baste: ' . $noBaste);
    }

    /**
     * View specific Baste detail
     */
    public function detail($id)
    {
        $baste = $this->basteModel->find($id);
        if (!$baste) {
            return redirect()->to('pengiriman-gadget')->with('error', 'BASTE tidak ditemukan.');
        }
        
        $items = $this->itemsModel->getItemsByBaste($id);
        
        $data = [
            'active_menu' => 'pengiriman_gadget',
            'baste' => $baste,
            'items' => $items
        ];
        
        return view('pengiriman_gadget/detail', $data);
    }

    /**
     * Print specific Baste to PDF using DomPDF
     */
    public function printPdf($id)
    {
        $baste = $this->basteModel->find($id);
        if (!$baste) {
            return redirect()->to('pengiriman-gadget')->with('error', 'BASTE tidak ditemukan.');
        }
        
        $items = $this->itemsModel->getItemsByBaste($id);
        
        $data = [
            'baste' => $baste,
            'items' => $items,
            'admin_name' => session()->get('nama')
        ];

        // Load HTML view for PDF
        $html = view('pengiriman_gadget/print_pdf', $data);

        // Configure Mpdf
        $mpdf = new Mpdf([
            'format' => 'A4-L', // A4 Landscape
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 10,
            'margin_bottom' => 10,
            'tempDir' => WRITEPATH . 'tmp'
        ]);

        $mpdf->SetTitle('BASTE_' . str_replace('/', '_', $baste['no_baste']));
        
        $mpdf->WriteHTML($html);
        
        // Generate PDF content
        $pdfContent = $mpdf->Output('', \Mpdf\Output\Destination::STRING_RETURN);
        $filename = 'BASTE_' . str_replace('/', '_', $baste['no_baste']) . '.pdf';
        
        // Return via CodeIgniter Response
        return $this->response->setContentType('application/pdf')
                              ->setHeader('Content-Disposition', 'inline; filename="' . $filename . '"')
                              ->setBody($pdfContent);
     }
     /**
      * Edit specific BASTE
      */
     public function edit($id)
     {
         $baste = $this->basteModel->find($id);
         if (!$baste) {
             return redirect()->to('pengiriman-gadget')->with('error', 'BASTE tidak ditemukan.');
         }

         $data = [
             'active_menu' => 'pengiriman_gadget',
             'baste' => $baste
         ];

         return view('pengiriman_gadget/edit', $data);
     }

     /**
      * Update specific BASTE
      */
     public function update($id)
     {
         $baste = $this->basteModel->find($id);
         if (!$baste) {
             return redirect()->to('pengiriman-gadget')->with('error', 'BASTE tidak ditemukan.');
         }

         $this->basteModel->update($id, [
             'no_baste' => $this->request->getPost('no_baste'),
             'tanggal' => $this->request->getPost('tanggal')
         ]);

         return redirect()->to('pengiriman-gadget/detail/' . $id)->with('success', 'Data BASTE berhasil diperbarui.');
     }

     /**
      * Delete specific BASTE
      */
     public function delete($id)
     {
         $baste = $this->basteModel->find($id);
         if (!$baste) {
             return redirect()->to('pengiriman-gadget')->with('error', 'BASTE tidak ditemukan.');
         }

         // Kembalikan items BASTE ini ke draft
         $this->itemsModel->where('baste_id', $id)->set(['baste_id' => null])->update();

         // Hapus BASTE
         $this->basteModel->delete($id);

         return redirect()->to('pengiriman-gadget')->with('success', 'BASTE berhasil dihapus dan item terkait dikembalikan sebagai draft.');
     }

     /**
      * Edit item inside a BASTE
      */
     public function editItem($id)
     {
         $item = $this->itemsModel->find($id);
         if (!$item) {
             return redirect()->back()->with('error', 'Item tidak ditemukan.');
         }

         $baste = $this->basteModel->find($item['baste_id']);
         
         $data = [
             'active_menu' => 'pengiriman_gadget',
             'item' => $item,
             'baste' => $baste
         ];

         return view('pengiriman_gadget/edit_item', $data);
     }

     /**
      * Update item inside a BASTE
      */
     public function updateItem($id)
     {
         $item = $this->itemsModel->find($id);
         if (!$item) {
             return redirect()->back()->with('error', 'Item tidak ditemukan.');
         }

         $imei = $this->request->getPost('imei');
         
         // Check valid IMEI
         $gadget = $this->masterGadgetModel->where('imei', $imei)->first();
         if (!$gadget) {
             return redirect()->back()->with('error', 'IMEI tidak valid atau tidak ditemukan di Master Gadget.');
         }

         $this->itemsModel->update($id, [
             'imei' => $imei,
             'kerusakan' => $this->request->getPost('kerusakan')
         ]);

         return redirect()->to('pengiriman-gadget/detail/' . $item['baste_id'])->with('success', 'Item berhasil diperbarui.');
     }

     /**
      * Delete item inside a BASTE
      */
     public function deleteItem($id)
     {
         $item = $this->itemsModel->find($id);
         if (!$item) {
             return redirect()->back()->with('error', 'Item tidak ditemukan.');
         }

         $baste_id = $item['baste_id'];
         
         // Hapus item dari database (atau bisa juga di-null-kan baste_id-nya jika ingin dikembalikan ke draft)
         $this->itemsModel->delete($id);

         return redirect()->to('pengiriman-gadget/detail/' . $baste_id)->with('success', 'Item berhasil dihapus dari BASTE.');
     }
}
