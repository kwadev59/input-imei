<?php $page_title = 'Pengaturan Pop-up'; $active_menu = 'settings_popup'; ?>
<?= view('partials/admin_head', ['page_title' => $page_title]) ?>
<?= view('partials/sidebar_admin', ['active_menu' => $active_menu]) ?>

<div class="content">
    <div class="page-header">
        <div>
            <h4>Pengaturan Pop-up Mandor</h4>
            <div class="header-sub">Atur pesan instruksi yang muncul saat mandor mengakses halaman input publik.</div>
        </div>
        <div>
            <a href="<?= base_url('public/input-gadget') ?>" target="_blank" class="btn btn-success">
                <i class="bi bi-box-arrow-up-right me-1"></i> Buka Halaman Input Public
            </a>
        </div>
    </div>

    <?= view('partials/alerts') ?>

    <div class="row">
        <div class="col-md-6">
            <div class="data-card">
                <div class="data-card-header">
                    <h5><i class="bi bi-chat-left-dots"></i> Isi Instruksi</h5>
                </div>
                <div class="data-card-body p-4">
                    <form action="<?= base_url('settings/popup') ?>" method="post">
                        <?= csrf_field() ?>
                        <div class="mb-4">
                            <label class="form-label fw-bold">Pesan Instruksi Pop-up</label>
                            <textarea name="instruction" class="form-control" rows="5" required><?= esc($instruction) ?></textarea>
                            <div class="form-text mt-2">Pesan ini akan muncul sebagai pop-up pertama kali saat halaman input mandor dibuka.</div>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-save me-1"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="data-card">
                <div class="data-card-header bg-light">
                    <h5><i class="bi bi-eye"></i> Preview</h5>
                </div>
                <div class="data-card-body p-4 bg-light">
                    <div class="p-3 border rounded bg-white shadow-sm">
                        <div class="d-flex justify-content-between align-items-center mb-2 border-bottom pb-2">
                            <h6 class="mb-0 fw-bold">Instruksi Penting</h6>
                            <i class="bi bi-x-lg small text-muted"></i>
                        </div>
                        <p class="mb-0 text-muted italic"><?= nl2br(esc($instruction)) ?></p>
                        <div class="mt-3 text-end">
                            <button class="btn btn-primary btn-sm px-3">Saya Mengerti</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- API Access Information -->
            <div class="data-card mt-4">
                <div class="data-card-header bg-dark text-white">
                    <h5><i class="bi bi-key"></i> API Access Data (Dev/Prod)</h5>
                </div>
                <div class="data-card-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted small">X-API-KEY</label>
                        <div class="input-group">
                            <input type="text" class="form-control bg-light" value="<?= esc($api_key) ?>" readonly id="apiKeyInput">
                            <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('apiKeyInput')">
                                <i class="bi bi-clipboard"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted small">ENDPOINT KARYAWAN</label>
                        <div class="input-group">
                            <input type="text" class="form-control bg-light" value="<?= esc($api_endpoint_karyawan) ?>" readonly id="endpointKaryawan">
                            <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('endpointKaryawan')">
                                <i class="bi bi-clipboard"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-bold text-muted small">ENDPOINT GADGET</label>
                        <div class="input-group">
                            <input type="text" class="form-control bg-light" value="<?= esc($api_endpoint_gadget) ?>" readonly id="endpointGadget">
                            <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('endpointGadget')">
                                <i class="bi bi-clipboard"></i>
                            </button>
                        </div>
                    </div>
                    <div class="form-text mt-3 text-info">
                        <i class="bi bi-info-circle me-1"></i> Gunakan <code>X-API-KEY</code> pada header request untuk mengakses data.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copyToClipboard(id) {
    var copyText = document.getElementById(id);
    copyText.select();
    copyText.setSelectionRange(0, 99999);
    navigator.clipboard.writeText(copyText.value);
    alert("Copied to clipboard: " + copyText.value);
}
</script>

<?= view('partials/admin_footer') ?>
