<?php $page_title = 'List Gadget Mandor'; $active_menu = 'mandor_gadget'; ?>
<?= view('partials/admin_head', ['page_title' => $page_title]) ?>
<?= view('partials/sidebar_admin', ['active_menu' => $active_menu]) ?>

<div class="content">
    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h4>Daftar Gadget Mandor</h4>
            <div class="header-sub">Menampilkan informasi gadget yang dipegang oleh Mandor</div>
        </div>
    </div>

    <?= view('partials/alerts') ?>

    <div id="js-alert-container"></div>

    <div class="data-card">
        <div class="data-card-header">
            <h5><i class="bi bi-phone"></i> Gadget Mandor</h5>
            <div class="d-flex gap-2">
                <form action="<?= base_url('mandor/gadgets') ?>" method="get" class="d-flex gap-2">
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari NPK/Nama/IMEI..." value="<?= esc($search) ?>">
                    <button type="submit" class="btn btn-primary btn-sm">Cari</button>
                </form>
            </div>
        </div>

        <?php if(empty($mandor_gadgets)): ?>
            <div class="empty-state">
                <div class="empty-state-icon"><i class="bi bi-phone"></i></div>
                <p>Data tidak ditemukan.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th class="ps-4">Mandor</th>
                            <th>PT / Afd</th>
                            <th>Tipe</th>
                            <th>IMEI Gadget</th>
                            <th>Aplikasi</th>
                            <th>Terakhir Update</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($mandor_gadgets as $row): ?>
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-dark"><?= esc($row['nama_lengkap']) ?></div>
                                <small class="text-muted font-monospace">NPK: <?= esc(str_pad($row['npk'], 7, '0', STR_PAD_LEFT)) ?></small>
                            </td>
                            <td>
                                <div class="small fw-bold text-primary"><?= esc($row['pt_site'] ?? '-') ?></div>
                                <div class="badge badge-soft-info"><?= esc($row['afdeling_id']) ?></div>
                            </td>
                            <td>
                                <span class="badge <?= $row['tipe_mandor'] == 'Panen' ? 'bg-warning-soft text-warning' : 'bg-success-soft text-success' ?>">
                                    <?= $row['tipe_mandor'] == 'Panen' ? '🌾 Panen' : '🌿 Rawat' ?>
                                </span>
                            </td>
                            <td>
                                <?php if($row['imei']): ?>
                                    <span class="font-monospace fw-bold text-primary"><?= esc($row['imei']) ?></span>
                                <?php else: ?>
                                    <span class="text-muted small italic">Belum ada data</span>
                                <?php endif; ?>
                            </td>
                            <td><small><?= esc($row['aplikasi'] ?? '-') ?></small></td>
                            <td>
                                <?php if($row['reported_at']): ?>
                                    <div class="small text-muted"><?= date('d/m/Y', strtotime($row['reported_at'])) ?></div>
                                    <div class="small text-muted" style="font-size: 0.7rem;"><?= date('H:i', strtotime($row['reported_at'])) ?> WIB</div>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <button type="button" class="btn btn-sm btn-outline-primary" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editGadgetModal"
                                            data-npk="<?= esc($row['npk']) ?>"
                                            data-nama="<?= esc($row['nama_lengkap']) ?>"
                                            data-aplikasi="<?= esc($row['aplikasi'] ?? '') ?>"
                                            data-imei="<?= esc($row['imei'] ?? '') ?>">
                                        <i class="bi bi-pencil-square"></i> <?= $row['imei'] ? 'Edit' : 'Input' ?>
                                    </button>
                                    <?php if($row['imei']): ?>
                                        <button type="button" class="btn btn-sm btn-success btn-print-offline" 
                                                data-npk="<?= esc($row['npk']) ?>"
                                                data-nama="<?= esc($row['nama_lengkap']) ?>"
                                                data-aplikasi="<?= esc($row['aplikasi'] ?? '') ?>"
                                                data-imei="<?= esc($row['imei'] ?? '') ?>">
                                            <i class="bi bi-printer"></i> Print
                                        </button>
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

<!-- Modal Edit Gadget -->
<div class="modal fade" id="editGadgetModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fw-bold">Assign Gadget Mandor</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('mandor/save-gadget') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="npk" id="modal-npk">
                <input type="hidden" name="nama" id="modal-nama">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Mandor</label>
                        <div id="modal-nama-display" class="fw-bold"></div>
                        <div id="modal-npk-display" class="small text-muted font-monospace"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Aplikasi</label>
                        <select name="aplikasi" id="modal-aplikasi" class="form-select" required>
                            <option value="">Pilih aplikasi</option>
                            <?php foreach($applications as $app): ?>
                                <option value="<?= esc($app) ?>"><?= esc($app) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Nomor IMEI</label>
                        <input type="text" name="imei" id="modal-imei" class="form-control" required placeholder="Masukkan 15 digit IMEI" maxlength="15">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
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
                <tr>
                    <td class="label">NAMA</td>
                    <td class="value">: <span id="print-nama"></span></td>
                </tr>
                <tr>
                    <td class="label">NPK</td>
                    <td class="value">: <span id="print-npk"></span></td>
                </tr>
                <tr>
                    <td class="label">APLIKASI</td>
                    <td class="value">: <span id="print-aplikasi"></span></td>
                </tr>
            </table>
            
            <div class="pos-imei-section">
                <div class="imei-label">NOMOR IMEI</div>
                <div id="print-imei" class="imei-value"></div>
            </div>

            <div class="pos-qr-wrapper">
                <img id="print-qr" src="" alt="QR Code IMEI">
            </div>
        </div>
        
        <div class="pos-divider"></div>
        
        <div class="pos-footer">
            <div>DICETAK: <?= date('d/m/Y H:i') ?> WIB</div>
            <div class="footer-bold">MILIK PERUSAHAAN - JANGAN DIRUSAK</div>
        </div>
    </div>
</div>

<style>
/* === POS 80mm Print Media === */
@media print {
    /* Hide everything else */
    body > *:not(#posPrintArea) { display: none !important; }
    
    #posPrintArea { 
        display: block !important; 
        width: 80mm; 
        margin: 0;
        padding: 0;
        background: #fff;
    }

    .pos-receipt {
        width: 74mm; /* safe margin for 80mm paper */
        margin: 0 auto;
        padding: 5mm 0;
        font-family: 'Arial', sans-serif;
        color: #000;
    }

    .pos-header {
        text-align: center;
        margin-bottom: 8px;
    }

    .pos-title {
        font-size: 14pt;
        font-weight: 900;
        letter-spacing: 1px;
        margin-bottom: 2px;
    }

    .pos-subtitle {
        font-size: 8pt;
        font-weight: normal;
        text-transform: uppercase;
    }

    .pos-divider {
        border-top: 2px dashed #000;
        margin: 10px 0;
        width: 100%;
    }

    .pos-body {
        margin: 10px 0;
    }

    .pos-table {
        width: 100%;
        margin-bottom: 12px;
    }

    .pos-table td {
        font-size: 10pt;
        padding: 3px 0;
        vertical-align: top;
    }

    .pos-table td.label {
        width: 25%;
        font-weight: bold;
    }

    .pos-table td.value {
        width: 75%;
        font-weight: normal;
    }

    .pos-imei-section {
        text-align: center;
        background: #000;
        color: #fff;
        padding: 8px 0;
        margin: 15px 0;
        border-radius: 4px;
        -webkit-print-color-adjust: exact;
    }

    .imei-label {
        font-size: 8pt;
        font-weight: normal;
        margin-bottom: 2px;
    }

    .imei-value {
        font-size: 16pt;
        font-weight: 900;
        letter-spacing: 1.5px;
        font-family: 'Courier New', Courier, monospace;
    }

    .pos-qr-wrapper {
        text-align: center;
        margin: 15px 0;
    }

    .pos-qr-wrapper img {
        width: 35mm;
        height: 35mm;
    }

    .pos-footer {
        text-align: center;
        font-size: 8pt;
        line-height: 1.4;
    }

    .footer-bold {
        font-weight: bold;
        margin-top: 4px;
        border: 1px solid #000;
        display: inline-block;
        padding: 2px 8px;
    }
}

@media screen {
    #posPrintArea { display: none; }
}
</style>

<?= view('partials/admin_footer') ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const editModal = document.getElementById('editGadgetModal');
    if(editModal) {
        editModal.addEventListener('show.bs.modal', function(event) {
            const btn = event.relatedTarget;
            const npk = btn.getAttribute('data-npk');
            const nama = btn.getAttribute('data-nama');
            const imei = btn.getAttribute('data-imei');
            const aplikasi = btn.getAttribute('data-aplikasi');
            
            document.getElementById('modal-npk').value = npk;
            document.getElementById('modal-nama').value = nama;
            document.getElementById('modal-imei').value = imei;
            document.getElementById('modal-aplikasi').value = aplikasi;
            
            document.getElementById('modal-nama-display').textContent = nama;
            document.getElementById('modal-npk-display').textContent = 'NPK: ' + npk;
        });
    }

    // Handle Print Offline
    document.querySelectorAll('.btn-print-offline').forEach(btn => {
        btn.addEventListener('click', function() {
            const npk = this.getAttribute('data-npk');
            const nama = this.getAttribute('data-nama');
            const imei = this.getAttribute('data-imei');
            const aplikasi = this.getAttribute('data-aplikasi');

            if (!imei) {
                alert('IMEI tidak ditemukan.');
                return;
            }

            // Fill the hidden POS area
            document.getElementById('print-nama').textContent = nama;
            document.getElementById('print-npk').textContent = npk;
            document.getElementById('print-aplikasi').textContent = aplikasi || '-';
            document.getElementById('print-imei').textContent = imei;
            
            // Generate QR Code via Google Charts API
            const qrUrl = `https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl=${imei}&choe=UTF-8`;
            document.getElementById('print-qr').src = qrUrl;

            // Wait for image to load before printing
            document.getElementById('print-qr').onload = function() {
                window.print();
            };
            
            // Fallback for print if image takes too long
            setTimeout(() => {
                window.print();
            }, 1000);
        });
    });
});
</script>
