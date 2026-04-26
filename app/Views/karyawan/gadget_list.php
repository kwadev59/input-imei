<?php $active_menu = $active_menu ?? 'gadget_ceker'; ?>
<?= view('partials/admin_head', ['page_title' => $page_title]) ?>
<?= view('partials/sidebar_admin', ['active_menu' => $active_menu]) ?>

<div class="content">
    <div class="page-header">
        <div>
            <h4><?= $page_title ?></h4>
            <div class="header-sub">Menampilkan informasi gadget yang dipegang oleh <?= str_replace('List Gadget ', '', $page_title) ?></div>
        </div>
    </div>

    <?= view('partials/alerts') ?>

    <div class="data-card">
        <div class="data-card-header">
            <h5><i class="bi bi-phone"></i> Gadget <?= str_replace('List Gadget ', '', $page_title) ?></h5>
            <div class="d-flex gap-2">
                <form action="" method="get" class="d-flex gap-2">
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari NIK/Nama/IMEI..." value="<?= esc($search ?? '') ?>">
                    <button type="submit" class="btn btn-primary btn-sm">Cari</button>
                    <?php if(!empty($search)): ?>
                        <a href="<?= current_url() ?>" class="btn btn-sm btn-outline-danger" title="Reset"><i class="bi bi-x-lg"></i></a>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <?php if(empty($items)): ?>
            <div class="empty-state">
                <div class="empty-state-icon"><i class="bi bi-phone"></i></div>
                <p>Data tidak ditemukan.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th class="ps-4">Karyawan</th>
                            <th>PT / Afd</th>
                            <th>IMEI Gadget</th>
                            <th>Aplikasi</th>
                            <th class="text-center">Validasi</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($items as $row): ?>
                        <?php 
                            // Logic Validasi
                            $status = 'none';
                            $status_label = 'Belum Input';
                            $status_class = 'bg-secondary';
                            
                            if (!empty($row['imei'])) {
                                if (empty($row['master_npk'])) {
                                    $status = 'not_found';
                                    $status_label = 'Tidak Terdaftar';
                                    $status_class = 'bg-dark';
                                } else {
                                    $nik1 = trim($row['nik_karyawan']);
                                    $nik2 = trim($row['master_npk']);
                                    
                                    // Logic Fuzzy Match:
                                    // 1. Sama persis
                                    // 2. 6 digit pertama sama (menangani kasus 1733751 vs 173375)
                                    $is_match = ($nik1 == $nik2) || (substr($nik1, 0, 6) == substr($nik2, 0, 6));
                                    
                                    if ($is_match) {
                                        $status = 'match';
                                        $status_label = 'Cocok';
                                        $status_class = 'bg-success';
                                    } else {
                                        $status = 'mismatch';
                                        $status_label = 'Mismatch';
                                        $status_class = 'bg-danger';
                                    }
                                }
                            }
                        ?>
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-dark"><?= esc($row['nama']) ?></div>
                                <small class="text-muted font-monospace">NIK: <?= esc($row['nik_karyawan']) ?></small>
                            </td>
                            <td>
                                <div class="small fw-bold text-primary"><?= esc($row['pt_site'] ?? '-') ?></div>
                                <div class="badge badge-soft-info"><?= esc($row['afdeling'] ?? '-') ?></div>
                            </td>
                            <td>
                                <?php if($row['imei']): ?>
                                    <span class="font-monospace fw-bold text-primary"><?= esc($row['imei']) ?></span>
                                <?php else: ?>
                                    <span class="text-muted small italic">Belum ada data</span>
                                <?php endif; ?>
                            </td>
                            <td><small><?= esc($row['aplikasi'] ?? '-') ?></small></td>
                            <td class="text-center">
                                <?php if($row['imei']): ?>
                                    <button type="button" class="btn btn-sm <?= $status_class ?> text-white border-0 shadow-sm px-3 rounded-pill btn-check-val" 
                                            style="font-size: 0.75rem;"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#valModal"
                                            data-status="<?= $status ?>"
                                            data-nama="<?= esc($row['nama']) ?>"
                                            data-nik="<?= esc($row['nik_karyawan']) ?>"
                                            data-imei="<?= esc($row['imei']) ?>"
                                            data-m-nama="<?= esc($row['master_nama'] ?? '-') ?>"
                                            data-m-npk="<?= esc($row['master_npk'] ?? '-') ?>">
                                        <i class="bi bi-shield-check me-1"></i> <?= $status_label ?>
                                    </button>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="d-flex gap-1 justify-content-center">
                                    <button type="button" class="btn btn-sm btn-outline-primary" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editGadgetModal"
                                            data-id="<?= esc($row['id']) ?>"
                                            data-nik="<?= esc($row['nik_karyawan']) ?>"
                                            data-nama="<?= esc($row['nama']) ?>"
                                            data-aplikasi="<?= esc($row['aplikasi'] ?? '') ?>"
                                            data-imei="<?= esc($row['imei'] ?? '') ?>">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <?php if($row['imei']): ?>
                                        <button type="button" class="btn btn-sm btn-success btn-print-offline" 
                                                data-nik="<?= esc($row['nik_karyawan']) ?>"
                                                data-nama="<?= esc($row['nama']) ?>"
                                                data-aplikasi="<?= esc($row['aplikasi'] ?? '') ?>"
                                                data-imei="<?= esc($row['imei'] ?? '') ?>">
                                            <i class="bi bi-printer"></i>
                                        </button>
                                        <a href="<?= base_url('karyawan/delete-gadget-karyawan/' . $row['id']) ?>" 
                                           class="btn btn-sm btn-outline-danger" 
                                           onclick="return confirm('Hapus data input gadget untuk karyawan ini?')"
                                           title="Hapus Data">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Validasi Detail -->
<div class="modal fade" id="valModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-bottom-0">
                <h6 class="modal-title fw-bold">Detail Validasi IMEI</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-0">
                <div id="val-alert" class="alert mb-4"></div>
                
                <div class="row g-3">
                    <div class="col-6">
                        <label class="small text-muted fw-bold">Data Karyawan (Input)</label>
                        <div id="v-nama" class="fw-bold text-dark"></div>
                        <div id="v-nik" class="small font-monospace"></div>
                    </div>
                    <div class="col-6 text-end border-start">
                        <label class="small text-muted fw-bold">Data Master Gadget</label>
                        <div id="v-m-nama" class="fw-bold text-dark"></div>
                        <div id="v-m-npk" class="small font-monospace"></div>
                    </div>
                </div>
                
                <div class="mt-4 p-3 bg-light rounded text-center">
                    <label class="small text-muted fw-bold d-block mb-1">Nomor IMEI</label>
                    <div id="v-imei" class="h4 fw-bold font-monospace text-primary mb-0"></div>
                </div>
            </div>
            <div class="modal-footer border-top-0 pt-0">
                <button type="button" class="btn btn-secondary btn-sm px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Gadget (Tetap Sama) -->
<div class="modal fade" id="editGadgetModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header border-bottom-0 pb-0">
                <h6 class="modal-title fw-bold">Assign Gadget Karyawan</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('karyawan/save-gadget-karyawan') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="karyawan_id" id="modal-id">
                <input type="hidden" name="nik" id="modal-nik">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted mb-1">Karyawan</label>
                        <div id="modal-nama-display" class="fw-bold text-primary"></div>
                        <div id="modal-npk-display" class="small text-muted font-monospace"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted mb-1">Aplikasi</label>
                        <select name="aplikasi" id="modal-aplikasi" class="form-select form-select-sm" required>
                            <option value="">Pilih aplikasi</option>
                            <?php foreach($applications as $app): ?>
                                <option value="<?= esc($app) ?>"><?= esc($app) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted mb-1">Nomor IMEI</label>
                        <input type="text" name="imei" id="modal-imei" class="form-control form-control-sm font-monospace" required placeholder="15 digit IMEI" maxlength="15">
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm px-3">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ====== HIDDEN POS PRINT AREA (80mm) ====== -->
<div id="posPrintArea">
    <div class="pos-receipt">
        <div class="pos-header">
            <div class="pos-title">LABEL ASET GADGET</div>
            <div class="pos-subtitle">SISTEM INPUT DATA & CHECKLIST</div>
        </div>
        <div class="pos-divider"></div>
        <div class="pos-body">
            <table class="pos-table">
                <tr><td class="label">NAMA</td><td class="value">: <strong id="print-nama" style="text-transform: uppercase;"></strong></td></tr>
                <tr><td class="label">NIK</td><td class="value">: <strong id="print-nik"></strong></td></tr>
                <tr><td class="label">APLIKASI</td><td class="value">: <span id="print-aplikasi"></span></td></tr>
            </table>
            <div class="pos-imei-box">
                <div class="imei-label">NOMOR IMEI</div>
                <div id="print-imei" class="imei-value"></div>
            </div>
            <div class="pos-qr-wrapper">
                <img id="print-qr" src="" alt="QR Code IMEI">
            </div>
        </div>
        <div class="pos-divider" style="border-top-style: solid;"></div>
        <div class="pos-footer">
            <div style="font-weight: bold; font-size: 9pt;">DICETAK: <?= date('d/m/Y H:i') ?> WIB</div>
            <div class="footer-notice">ASET PERUSAHAAN - JANGAN DIRUSAK</div>
        </div>
    </div>
</div>

<style>
@media print {
    body > *:not(#posPrintArea) { display: none !important; }
    #posPrintArea { display: block !important; width: 80mm; margin: 0; padding: 0; background: #fff; }
    .pos-receipt { width: 76mm; margin: 0 auto; padding: 5mm 0; font-family: 'Courier New', Courier, monospace; color: #000; line-height: 1.3; }
    .pos-header { text-align: center; margin-bottom: 10px; border: 2px solid #000; padding: 5px; }
    .pos-title { font-size: 14pt; font-weight: bold; margin-bottom: 2px; }
    .pos-subtitle { font-size: 8pt; font-weight: normal; }
    .pos-divider { border-top: 3px solid #000; margin: 12px 0; width: 100%; }
    .pos-body { margin: 10px 0; }
    .pos-table { width: 100%; margin-bottom: 15px; }
    .pos-table td { font-size: 11pt; padding: 4px 0; vertical-align: top; }
    .pos-table td.label { width: 32%; font-weight: bold; }
    .pos-imei-box { text-align: center; border: 4px solid #000; padding: 10px 5px; margin: 15px 0; }
    .imei-label { font-size: 9pt; font-weight: bold; text-decoration: underline; margin-bottom: 5px; }
    .imei-value { font-size: 18pt; font-weight: bold; letter-spacing: 1px; }
    .pos-qr-wrapper { text-align: center; margin: 20px 0; }
    .pos-qr-wrapper img { width: 40mm; height: 40mm; }
    .pos-footer { text-align: center; font-size: 8pt; }
    .footer-notice { font-weight: bold; margin-top: 6px; font-size: 9pt; text-transform: uppercase; border-top: 1px solid #000; padding-top: 4px; }
}
@media screen { #posPrintArea { display: none; } }
</style>

<?= view('partials/admin_footer') ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle Validation Modal
    document.querySelectorAll('.btn-check-val').forEach(btn => {
        btn.addEventListener('click', function() {
            const status = this.getAttribute('data-status');
            const alert = document.getElementById('val-alert');
            
            // Set Alert
            if (status === 'match') {
                alert.className = 'alert alert-success mb-4';
                alert.innerHTML = '<i class="bi bi-check-circle-fill me-2"></i> <strong>DATA COCOK</strong><br><small>IMEI ini terdaftar sesuai dengan pemiliknya di Master Data.</small>';
            } else if (status === 'mismatch') {
                alert.className = 'alert alert-danger mb-4';
                alert.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-2"></i> <strong>DATA TIDAK COCOK</strong><br><small>IMEI ini terdaftar atas nama orang lain di Master Data.</small>';
            } else {
                alert.className = 'alert alert-dark mb-4';
                alert.innerHTML = '<i class="bi bi-question-circle-fill me-2"></i> <strong>IMEI TIDAK TERDAFTAR</strong><br><small>IMEI ini tidak ditemukan di Master Data Gadget perusahaan.</small>';
            }

            document.getElementById('v-nama').textContent = this.getAttribute('data-nama');
            document.getElementById('v-nik').textContent = 'NIK: ' + this.getAttribute('data-nik');
            document.getElementById('v-m-nama').textContent = this.getAttribute('data-m-nama');
            document.getElementById('v-m-npk').textContent = 'NIK: ' + this.getAttribute('data-m-npk');
            document.getElementById('v-imei').textContent = this.getAttribute('data-imei');
        });
    });

    const editModal = document.getElementById('editGadgetModal');
    if(editModal) {
        editModal.addEventListener('show.bs.modal', function(event) {
            const btn = event.relatedTarget;
            const id = btn.getAttribute('data-id');
            const nik = btn.getAttribute('data-nik');
            const nama = btn.getAttribute('data-nama');
            const imei = btn.getAttribute('data-imei');
            const aplikasi = btn.getAttribute('data-aplikasi');
            
            document.getElementById('modal-id').value = id;
            document.getElementById('modal-nik').value = nik;
            document.getElementById('modal-imei').value = imei;
            document.getElementById('modal-aplikasi').value = aplikasi;
            document.getElementById('modal-nama-display').textContent = nama;
            document.getElementById('modal-npk-display').textContent = 'NIK: ' + nik;
        });
    }

    document.querySelectorAll('.btn-print-offline').forEach(btn => {
        btn.addEventListener('click', function() {
            const nik = this.getAttribute('data-nik');
            const nama = this.getAttribute('data-nama');
            const imei = this.getAttribute('data-imei');
            const aplikasi = this.getAttribute('data-aplikasi');
            if (!imei) { alert('IMEI tidak ditemukan.'); return; }
            document.getElementById('print-nama').textContent = nama;
            document.getElementById('print-nik').textContent = nik;
            document.getElementById('print-aplikasi').textContent = aplikasi || '-';
            document.getElementById('print-imei').textContent = imei;
            const qrUrl = `https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl=${imei}&choe=UTF-8`;
            document.getElementById('print-qr').src = qrUrl;
            document.getElementById('print-qr').onload = function() { window.print(); };
            setTimeout(() => { window.print(); }, 1000);
        });
    });
});
</script>
