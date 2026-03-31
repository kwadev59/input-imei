<?php $page_title = 'Edit BASTE Pengiriman Gadget'; ?>
<?= view('partials/admin_head', ['page_title' => $page_title]) ?>
<?= view('partials/sidebar_admin', ['active_menu' => $active_menu ?? 'pengiriman_gadget']) ?>

<div class="content">
    <div class="page-header d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4><i class="bi bi-pencil-square me-2"></i>Edit BASTE</h4>
            <div class="header-sub">Mengubah register dan tanggal BASTE.</div>
        </div>
        <div>
            <a href="<?= base_url('pengiriman-gadget/detail/' . $baste['id']) ?>" class="btn btn-outline-secondary btn-action">
                <i class="bi bi-arrow-left me-1"></i> Batal
            </a>
        </div>
    </div>

    <?= view('partials/alerts') ?>

    <div class="data-card p-4 mx-auto" style="max-width: 600px;">
        <form action="<?= base_url('pengiriman-gadget/update/' . $baste['id']) ?>" method="post">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label for="no_baste" class="form-label fw-bold">No Register BASTE</label>
                <input type="text" class="form-control" id="no_baste" name="no_baste" value="<?= esc($baste['no_baste']) ?>" required>
            </div>
            <div class="mb-4">
                <label for="tanggal" class="form-label fw-bold">Tanggal BASTE</label>
                <input type="date" class="form-control" id="tanggal" name="tanggal" value="<?= date('Y-m-d', strtotime($baste['tanggal'])) ?>" required>
            </div>
            
            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-save me-1"></i> Simpan Perubahan
            </button>
        </form>
    </div>
</div>
<?= view('partials/admin_footer') ?>
