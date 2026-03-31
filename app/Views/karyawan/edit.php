<?php $page_title = 'Edit Karyawan'; $active_menu = 'karyawan'; ?>
<?= view('partials/admin_head', ['page_title' => $page_title]) ?>
<?= view('partials/sidebar_admin', ['active_menu' => $active_menu]) ?>

<div class="content">
    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h4>Edit & Mutasi Karyawan</h4>
            <div class="header-sub">Perbarui data atau mutasi karyawan</div>
        </div>
        <a href="<?= base_url('karyawan') ?>" class="btn btn-outline-secondary btn-sm px-3">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <?= view('partials/alerts') ?>

    <div class="data-card mx-auto" style="max-width: 700px;">
        <div class="data-card-body">
            <form action="<?= base_url('karyawan/update/'.$karyawan['id']) ?>" method="post">
                <?= csrf_field() ?>
                
                <h6 class="text-muted mb-3 fw-bold text-uppercase small"><i class="bi bi-person me-1"></i> Informasi Dasar</h6>
                <div class="row g-3">
                    <div class="col-md-5">
                        <label class="form-label small fw-bold text-muted">NIK Karyawan</label>
                        <input type="text" class="form-control bg-light" name="nik_karyawan" value="<?= esc($karyawan['nik_karyawan']) ?>" readonly style="opacity:0.7">
                    </div>
                    <div class="col-md-7">
                        <label class="form-label small fw-bold text-muted">Nama Lengkap</label>
                        <input type="text" class="form-control" name="nama" value="<?= esc($karyawan['nama']) ?>" required>
                    </div>
                </div>

                <hr class="my-4" style="border-color: var(--card-border);">
                <h6 class="mb-3 fw-bold text-uppercase small" style="color:var(--primary)"><i class="bi bi-arrow-left-right me-1"></i> Mutasi & Status</h6>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted">Afdeling (Lokasi Kerja)</label>
                        <select class="form-select" name="afdeling" id="afdeling">
                            <option value="<?= esc($karyawan['afdeling']) ?>" selected><?= esc($karyawan['afdeling']) ?> (Saat Ini)</option>
                            <option value="AFD-01">AFD-01</option>
                            <option value="AFD-02">AFD-02</option>
                            <option value="AFD-03">AFD-03</option>
                            <option value="HO">HO / Kantor</option>
                        </select>
                        <div class="form-text text-muted small mt-1">Ubah jika karyawan dimutasi lokasi.</div>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted">Jabatan</label>
                        <select class="form-select" name="jabatan">
                            <option value="<?= esc($karyawan['jabatan']) ?>" selected><?= esc($karyawan['jabatan']) ?></option>
                            <option value="Pemanen">Pemanen</option>
                            <option value="Perawatan">Perawatan</option>
                            <option value="Mandor">Mandor</option>
                            <option value="Kerani">Kerani</option>
                        </select>
                        <div class="form-text text-muted small mt-1">Ubah jika ada promosi/demosi.</div>
                    </div>
                </div>

                <div class="mt-3">
                    <label class="form-label small fw-bold text-muted">PT / SITE</label>
                    <input type="text" class="form-control" name="pt_site" id="pt_site" value="<?= esc($karyawan['pt_site'] ?? '') ?>" placeholder="Contoh: PPS1">
                    <div class="form-text text-muted small mt-1">Ubah jika karyawan dimutasi PT/Site.</div>
                </div>

                <div class="mt-3 mb-4">
                    <label class="form-label small fw-bold text-muted">Status Keaktifan</label>
                    <div class="d-flex gap-4 mt-1">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="status_aktif" id="status1" value="Aktif" <?= ($karyawan['status_aktif'] == 'Aktif') ? 'checked' : '' ?>>
                            <label class="form-check-label fw-bold" style="color:var(--success)" for="status1">
                                <i class="bi bi-check-circle me-1"></i> Aktif Bekerja
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="status_aktif" id="status2" value="Resign" <?= ($karyawan['status_aktif'] == 'Resign') ? 'checked' : '' ?>>
                            <label class="form-check-label fw-bold" style="color:var(--danger)" for="status2">
                                <i class="bi bi-x-circle me-1"></i> Resign / Non-Aktif
                            </label>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between pt-3" style="border-top: 1px solid var(--card-border);">
                    <a href="<?= base_url('karyawan') ?>" class="btn btn-light border">
                        <i class="bi bi-x-lg me-1"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-check-lg me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ptSiteInput = document.getElementById('pt_site');
    const afdelingSelect = document.getElementById('afdeling');
    const currentAfdeling = '<?= esc($karyawan['afdeling']) ?>';

    function updateAfdelingOptions() {
        const ptSite = ptSiteInput.value.trim().toUpperCase();
        const selectedValue = afdelingSelect.value;
        
        afdelingSelect.innerHTML = '';
        
        let options = [];
        if (ptSite === 'PPS1') {
            options = ['OA', 'OB', 'OC', 'OD', 'OE', 'OF'];
        } else if (ptSite === 'BIM1') {
            options = ['OA', 'OB', 'OC', 'OD', 'OE', 'OF', 'OG'];
        } else {
            options = ['AFD-01', 'AFD-02', 'AFD-03', 'HO'];
        }

        if (currentAfdeling) {
            const currentOpt = document.createElement('option');
            currentOpt.value = currentAfdeling;
            currentOpt.textContent = currentAfdeling + ' (Saat Ini)';
            afdelingSelect.appendChild(currentOpt);
        }

        options.forEach(opt => {
            if (opt !== currentAfdeling) {
                const optElem = document.createElement('option');
                optElem.value = opt;
                optElem.textContent = opt;
                afdelingSelect.appendChild(optElem);
            }
        });
        
        let restored = false;
        for (let i = 0; i < afdelingSelect.options.length; i++) {
            if (afdelingSelect.options[i].value === selectedValue) {
                afdelingSelect.selectedIndex = i;
                restored = true;
                break;
            }
        }
        
        if (!restored && afdelingSelect.options.length > 0 && selectedValue !== currentAfdeling) {
            afdelingSelect.selectedIndex = 0;
        }
    }

    ptSiteInput.addEventListener('input', updateAfdelingOptions);
    updateAfdelingOptions();
});
</script>

<?= view('partials/admin_footer') ?>
