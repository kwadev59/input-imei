<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\PrintJobModel;

/**
 * API Controller untuk sistem Print Queue (ESC/POS via Agent).
 *
 * Endpoint:
 *   POST /api/print           → Tambah job ke antrian (dari frontend)
 *   GET  /api/print/queue     → Agent ambil job berikutnya
 *   POST /api/print/done/{id} → Agent lapor status selesai/gagal
 *   GET  /api/print/status    → Status antrian (untuk dashboard)
 */
class PrintQueue extends BaseController
{
    protected PrintJobModel $jobModel;

    public function __construct()
    {
        $this->jobModel = new PrintJobModel();
    }

    // =========================================================
    // MIDDLEWARE: Validasi header auth
    // =========================================================

    /**
     * Cek apakah request memiliki Bearer Token yang valid.
     * Tidak mengirim response error (hanya return boolean).
     */
    private function is_valid_token(): bool
    {
        $authHeader = $this->request->getHeaderLine('Authorization');

        if (empty($authHeader) || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            return false;
        }

        $token = $matches[1];
        $expected = env('PRINT_AGENT_KEY', '');

        return !empty($expected) && hash_equals($expected, $token);
    }

    /**
     * Helper response untuk Unauthorized.
     */
    private function failUnauthorized(string $message = 'Unauthorized')
    {
        return $this->response
            ->setStatusCode(401)
            ->setJSON(['status' => 'error', 'message' => $message]);
    }

    /**
     * Autentikasi User menggunakan Session.
     */
    private function is_logged_in(): bool
    {
        return (bool) session()->get('logged_in');
    }

    // =========================================================
    // [1] POST /api/print
    // Dipanggil dari frontend saat user klik "PRINT ONLINE"
    // =========================================================

    public function submit()
    {
        // Izinkan jika ada session (frontend) ATAU token valid (agent)
        if (!$this->is_logged_in() && !$this->is_valid_token()) {
             return $this->failUnauthorized('Unauthorized. Silakan login atau gunakan API Token.');
        }

        // Ambil body JSON
        $body = $this->request->getJSON(true);

        // Validasi field wajib
        $required = ['imei', 'nama_pengguna', 'aplikasi', 'kerusakan'];
        foreach ($required as $field) {
            if (empty($body[$field] ?? null)) {
                return $this->response->setStatusCode(422)->setJSON([
                    'status'  => 'error',
                    'message' => "Field '{$field}' wajib diisi.",
                ]);
            }
        }

        // Sanitasi & normalisasi data
        $imei        = preg_replace('/\D/', '', trim($body['imei']));
        $namaGadget  = strip_tags(trim($body['nama_pengguna']));
        $aplikasi    = strip_tags(trim($body['aplikasi']));
        $kerusakan   = strip_tags(trim($body['kerusakan']));
        $npk         = strip_tags(trim($body['npk_pengguna'] ?? '-'));
        $pt          = strip_tags(trim($body['pt'] ?? '-'));
        $afd         = strip_tags(trim($body['afd'] ?? '-'));
        $tipeAsset   = strip_tags(trim($body['tipe_asset'] ?? '-'));
        $groupAsset  = strip_tags(trim($body['group_asset'] ?? '-'));
        $partAsset   = strip_tags(trim($body['part_asset'] ?? '-'));
        $jumlah      = strip_tags(trim($body['jumlah'] ?? '-'));
        $asal        = strip_tags(trim($body['asal_desc'] ?? $body['asal'] ?? '-'));
        $statusDesc  = strip_tags(trim($body['status_desc'] ?? '-'));
        $posTitle    = strip_tags(trim($body['pos_title'] ?? '-'));

        // Validasi IMEI (15 digit angka)
        if (strlen($imei) !== 15) {
            return $this->response->setStatusCode(422)->setJSON([
                'status'  => 'error',
                'message' => 'IMEI harus 15 digit angka.',
            ]);
        }

        // Cegah duplikat: cek apakah IMEI yang sama masih antri/diproses
        $existingPending = $this->jobModel
            ->where('status', 'pending')
            ->orWhere('status', 'processing')
            ->groupStart()
                ->where('JSON_UNQUOTE(JSON_EXTRACT(payload, "$.imei"))', $imei)
            ->groupEnd()
            ->first();

        if ($existingPending) {
            return $this->response->setStatusCode(409)->setJSON([
                'status'  => 'duplicate',
                'message' => "IMEI {$imei} sudah ada di antrian cetak (status: {$existingPending['status']}).",
                'job_id'  => $existingPending['id'],
            ]);
        }

        // Susun payload JSON untuk agent ESC/POS
        $payload = [
            'imei'        => $imei,
            'nama'        => $namaGadget,
            'npk'         => $npk,
            'aplikasi'    => $aplikasi,
            'pt'          => $pt,
            'afd'         => $afd,
            'pos_title'   => $posTitle,
            'group_asset' => $groupAsset,
            'tipe_asset'  => $tipeAsset,
            'part_asset'  => $partAsset,
            'jumlah'      => $jumlah,
            'asal'        => $asal,
            'status_desc' => $statusDesc,
            'kerusakan'   => $kerusakan,
            'dicetak_oleh'=> session()->get('nama') ?? 'Sistem',
            'timestamp'   => date('d/m/Y H:i:s'),
        ];

        // Simpan ke tabel antrian
        $this->jobModel->save([
            'reference_type' => 'gadget_label',
            'reference_id'   => null,
            'payload'        => json_encode($payload, JSON_UNESCAPED_UNICODE),
            'status'         => 'pending',
            'attempts'       => 0,
            'max_attempts'   => 3,
            'created_by'     => session()->get('id'),
        ]);

        $jobId = $this->jobModel->insertID();

        return $this->response->setStatusCode(201)->setJSON([
            'status'  => 'queued',
            'message' => 'Job berhasil ditambahkan ke antrian cetak.',
            'job_id'  => $jobId,
        ]);
    }

    // =========================================================
    // [2] GET /api/print/queue
    // Dipanggil oleh Bash agent di VPS lokal (polling)
    // =========================================================

    public function queue()
    {
        if (!$this->is_valid_token()) {
            return $this->failUnauthorized('Invalid or missing API Token.');
        }

        // Ambil Agent ID dari header sebagai pengenal unik (untuk locking)
        $agentId = $this->request->getHeaderLine('X-Agent-Id');
        if (empty($agentId)) {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 'error',
                'message' => 'Header X-Agent-Id wajib diisi.',
            ]);
        }

        // Panggil resetStuckJobs secara periodik (misal 10% probability) untuk menghemat resource
        if (rand(1, 10) === 5) {
            $this->jobModel->resetStuckJobs(120);
        }

        // Claim job berikutnya secara atomic
        $job = $this->jobModel->claimNextJob($agentId);

        if (!$job) {
            // User minta [] jika kosong
            return $this->response->setJSON([]);
        }

        // User minta [ { id, text } ] jika ada job
        return $this->response->setJSON([[
            'id'      => (int)$job['id'],
            'text'    => $job['payload'], // Payload asli (JSON string)
            'attempts'=> (int)$job['attempts'],
        ]]);
    }

    // =========================================================
    // [3] POST /api/print/done/{id}
    // Dipanggil Bash agent setelah selesai atau gagal mencetak
    // =========================================================

    public function done($jobId)
    {
        if (!$this->is_valid_token()) {
            return $this->failUnauthorized('Invalid or missing API Token.');
        }

        $agentId  = $this->request->getHeaderLine('X-Agent-Id');
        $body     = $this->request->getJSON(true) ?? [];
        $status   = $body['status'] ?? 'failed';
        $errorMsg = $body['error_message'] ?? '';

        if (!in_array($status, ['done', 'failed'])) {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 'error',
                'message' => "Status tidak valid. Gunakan 'done' atau 'failed'.",
            ]);
        }

        $updated = $this->jobModel->completeJob((int)$jobId, $agentId, $status, $errorMsg);

        if (!$updated) {
            return $this->response->setStatusCode(404)->setJSON([
                'status'  => 'error',
                'message' => 'Job tidak ditemukan atau tidak dimiliki oleh agent ini.',
            ]);
        }

        return $this->response->setJSON([
            'status'  => 'ok',
            'message' => "Job #{$jobId} ditandai sebagai {$status}.",
        ]);
    }

    // =========================================================
    // [4] GET /api/print/status
    // Untuk dashboard admin (cek kondisi antrian)
    // =========================================================

    public function status()
    {
        if (!$this->is_logged_in()) {
            return $this->failUnauthorized('Silakan login terlebih dahulu.');
        }

        $stats = $this->jobModel->getQueueStats();

        // Ambil 10 job terbaru
        $recent = $this->jobModel
            ->orderBy('created_at', 'DESC')
            ->limit(10)
            ->findAll();

        // Decode payload untuk readability
        foreach ($recent as &$job) {
            $job['payload'] = json_decode($job['payload'], true);
        }
        unset($job);

        return $this->response->setJSON([
            'status' => 'ok',
            'stats'  => $stats,
            'recent' => $recent,
        ]);
    }
}
