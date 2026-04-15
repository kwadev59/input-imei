<?php $page_title = 'Upload Resi BASTE'; ?>
<?= view('partials/admin_head', ['page_title' => $page_title]) ?>
<?= view('partials/sidebar_admin', ['active_menu' => 'pengiriman_gadget']) ?>

<div class="content">
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h4><i class="bi bi-upload me-2"></i>Upload Resi</h4>
            <div class="header-sub">Upload bukti pengiriman untuk BASTE: <strong><?= esc($baste['no_baste']) ?></strong></div>
        </div>
        <div>
            <a href="<?= base_url('pengiriman-gadget') ?>" class="btn btn-outline-secondary btn-action">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="data-card mb-4">
                <div class="data-card-header">
                    <h5 class="mb-0"><i class="bi bi-file-earmark-image me-2"></i>Form Upload Resi</h5>
                </div>
                <div class="card-body p-4">
                    <form action="<?= base_url('pengiriman-gadget/do-upload-resi/' . $baste['id']) ?>" method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="no_resi" class="form-label fw-bold">Nomor Resi / Bukti Pengiriman</label>
                            <input type="text" class="form-control" id="no_resi" name="no_resi" value="<?= esc($baste['no_resi']) ?>" placeholder="Contoh: JNE-12345678" required>
                            <div class="form-text">Masukkan nomor resi ekspedisi atau kode bukti pengiriman lainnya.</div>
                        </div>

                        <div class="mb-4">
                            <label for="foto_resi" class="form-label fw-bold">Foto / Scan Resi (Opsional)</label>
                            <?php if(!empty($baste['foto_resi'])): ?>
                                <div class="mb-2">
                                    <div class="text-muted small mb-1">File saat ini:</div>
                                    <a href="<?= base_url('uploads/resi/' . $baste['foto_resi']) ?>" target="_blank" class="btn btn-sm btn-outline-info">
                                        <i class="bi bi-image me-1"></i> Lihat Foto Resi
                                    </a>
                                </div>
                            <?php endif; ?>
                            <input type="file" class="form-control" id="foto_resi" name="foto_resi" accept="image/*,.pdf">
                            <div class="form-text">Format yang didukung: JPG, PNG, PDF. Maksimal 2MB.</div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-cloud-arrow-up me-1"></i> Simpan Data Resi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?= view('partials/admin_footer') ?>
