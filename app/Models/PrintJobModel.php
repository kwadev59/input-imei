<?php

namespace App\Models;

use CodeIgniter\Model;

class PrintJobModel extends Model
{
    protected $table      = 'print_jobs';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'reference_type', 'reference_id', 'payload',
        'status', 'attempts', 'max_attempts',
        'error_message', 'agent_id', 'locked_at',
        'created_by', 'done_at',
    ];

    protected $useTimestamps = true;

    public function claimNextJob(string $agentId): ?array
    {
        $db = $this->db;

        // Buat lock token unik (gabungan agent ID + microtime) agar tidak tertukar jika agent restart cepat
        $lockToken = $agentId . ':' . microtime(true);

        // 1. Coba klaim satu job secara UPDATE-FIRST (Sangat aman dari double-print)
        // Kita gunakan subquery atau LIMIT 1 untuk mengambil SATU job pending tertua.
        // Catatan: MySQL tidak support LIMIT di subquery UPDATE secara langsung tanpa trik.
        // Kita gunakan pendekatan transaksi yang lebih ketat.
        
        $db->transStart();

        // Cari ID yang menganggur
        $jobIdRow = $db->query("
            SELECT id FROM print_jobs 
            WHERE status = 'pending' 
              AND attempts < max_attempts 
            ORDER BY created_at ASC 
            LIMIT 1 
            FOR UPDATE
        ")->getRowArray();

        if (!$jobIdRow) {
            $db->transRollback();
            return null;
        }

        $jobId = $jobIdRow['id'];

        // 2. Langsung update status ke 'processing'
        // Kita sertakan ID di WHERE agar sangat spesifik
        $db->table($this->table)
           ->where('id', $jobId)
           ->where('status', 'pending')
           ->set('status', 'processing')
           ->set('agent_id', $agentId)
           ->set('locked_at', date('Y-m-d H:i:s'))
           ->set('attempts', 'attempts + 1', false) // Tambah percobaan secara atomic
           ->set('updated_at', date('Y-m-d H:i:s'))
           ->update();

        $db->transComplete();

        if ($db->transStatus() === false || $db->affectedRows() === 0) {
            return null;
        }

        // 3. Ambil data lengkap job yang sudah berhasil di-lock
        return $this->find($jobId);
    }

    /**
     * Tandai job sebagai selesai (done atau failed).
     *
     * @param int    $jobId    ID job
     * @param string $agentId  Agent ID yang mengklaim job
     * @param string $status   'done' atau 'failed'
     * @param string $errorMsg Pesan error jika gagal
     */
    public function completeJob(int $jobId, string $agentId, string $status, string $errorMsg = ''): bool
    {
        $validStatus = ['done', 'failed'];
        if (!in_array($status, $validStatus)) {
            $status = 'failed';
        }

        $result = $this->db->query("
            UPDATE print_jobs
            SET status        = ?,
                error_message = ?,
                done_at       = IF(? = 'done', NOW(), NULL),
                locked_at     = NULL,
                updated_at    = NOW()
            WHERE id      = ?
              AND agent_id = ?
              AND status   = 'processing'
        ", [$status, $errorMsg, $status, $jobId, $agentId]);

        return $result && $this->db->affectedRows() > 0;
    }

    /**
     * Reset job yang stuck (processing terlalu lama tanpa selesai).
     * Dipanggil periodik atau saat agent restart.
     *
     * @param int $timeoutSeconds Batas waktu dalam detik (default 120)
     */
    public function resetStuckJobs(int $timeoutSeconds = 120): int
    {
        $this->db->query("
            UPDATE print_jobs
            SET status    = 'pending',
                agent_id  = NULL,
                locked_at = NULL,
                updated_at = NOW()
            WHERE status    = 'processing'
              AND locked_at < DATE_SUB(NOW(), INTERVAL ? SECOND)
              AND attempts  < max_attempts
        ", [$timeoutSeconds]);

        return $this->db->affectedRows();
    }

    /**
     * Ambil statistik antrian (untuk dashboard admin).
     */
    public function getQueueStats(): array
    {
        $row = $this->db->query("
            SELECT
                SUM(status = 'pending')    AS pending,
                SUM(status = 'processing') AS processing,
                SUM(status = 'done')       AS done,
                SUM(status = 'failed')     AS failed
            FROM print_jobs
        ")->getRowArray();

        return $row ?? ['pending' => 0, 'processing' => 0, 'done' => 0, 'failed' => 0];
    }
}
