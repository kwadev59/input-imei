<?php $page_title = 'Tambah Karyawan'; $active_menu = 'karyawan'; ?>
<?= view('partials/admin_head', ['page_title' => $page_title]) ?>
<?= view('partials/sidebar_admin', ['active_menu' => $active_menu]) ?>

<div class="content">
    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h4>Tambah Karyawan Baru</h4>
            <div class="header-sub">Input data karyawan baru secara manual</div>
        </div>
        <a href="<?= base_url('karyawan') ?>" class="btn btn-outline-secondary btn-sm px-3">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <?= view('partials/alerts') ?>

    <div class="data-card mx-auto" style="max-width: 700px;">
        <div class="data-card-body">
            <form action="<?= base_url('karyawan/store') ?>" method="post">
                <?= csrf_field() ?>
                
                <h6 class="text-muted mb-3 fw-bold text-uppercase small"><i class="bi bi-person me-1"></i> Informasi Dasar</h6>
                <div class="row g-3">
                    <div class="col-md-5">
                        <label class="form-label small fw-bold text-muted">NIK Karyawan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nik_karyawan" required placeholder="Contoh: 12345678">
                    </div>
                    <div class="col-md-7">
                        <label class="form-label small fw-bold text-muted">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nama" required placeholder="Nama lengkap karyawan">
                    </div>
                </div>

                <hr class="my-4" style="border-color: var(--card-border);">
                <h6 class="mb-3 fw-bold text-uppercase small" style="color:var(--primary)"><i class="bi bi-briefcase me-1"></i> Pekerjaan & Lokasi</h6>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted">PT / SITE <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="pt_site" id="pt_site" placeholder="Contoh: PPS1" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted">Afdeling (Lokasi Kerja) <span class="text-danger">*</span></label>
                        <select class="form-select" name="afdeling" id="afdeling" required>
                            <option value="">Pilih Afdeling</option>
                            <option value="AFD-01">AFD-01</option>
                            <option value="AFD-02">AFD-02</option>
                            <option value="AFD-03">AFD-03</option>
                            <option value="HO">HO / Kantor</option>
                        </select>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted">Jabatan <span class="text-danger">*</span></label>
                        <select class="form-select" name="jabatan" required>
                            <option value="">Pilih Jabatan</option>
                            <option value="Pemanen">Pemanen</option>
                            <option value="Pekerja Rawat">Pekerja Rawat</option>
                            <option value="Mandor">Mandor</option>
                            <option value="Kerani">Kerani</option>
                        </select>
                    </div>
                </div>

                <div class="mt-3 mb-4">
                    <label class="form-label small fw-bold text-muted">Status Keaktifan</label>
                    <div class="d-flex gap-4 mt-1">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="status_aktif" id="status1" value="Aktif" checked>
                            <label class="form-check-label fw-bold" style="color:var(--success)" for="status1">
                                <i class="bi bi-check-circle me-1"></i> Aktif Bekerja
                            </label>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between pt-3" style="border-top: 1px solid var(--card-border);">
                    <a href="<?= base_url('karyawan') ?>" class="btn btn-light border">
                        <i class="bi bi-x-lg me-1"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-check-lg me-1"></i> Simpan Karyawan
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

    function updateAfdelingOptions() {
        const ptSite = ptSiteInput.value.trim().toUpperCase();
        const selectedValue = afdelingSelect.value;
        
        afdelingSelect.innerHTML = '<option value="">Pilih Afdeling</option>';
        
        let options = [];
        if (ptSite === 'PPS1') {
            options = ['OA', 'OB', 'OC', 'OD', 'OE', 'OF'];
        } else if (ptSite === 'BIM1') {
            options = ['OA', 'OB', 'OC', 'OD', 'OE', 'OF', 'OG'];
        } else {
            options = ['AFD-01', 'AFD-02', 'AFD-03', 'HO'];
        }

        options.forEach(opt => {
            const optElem = document.createElement('option');
            optElem.value = opt;
            optElem.textContent = opt;
            afdelingSelect.appendChild(optElem);
        });
        
        for (let i = 0; i < afdelingSelect.options.length; i++) {
            if (afdelingSelect.options[i].value === selectedValue) {
                afdelingSelect.selectedIndex = i;
                break;
            }
        }
    }

    ptSiteInput.addEventListener('input', updateAfdelingOptions);
    updateAfdelingOptions();
});
</script>

<?= view('partials/admin_footer') ?>
