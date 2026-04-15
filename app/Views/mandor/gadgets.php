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
                                        <button type="button" class="btn btn-sm btn-success btn-print-online" 
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

    // Handle Print Online
    document.querySelectorAll('.btn-print-online').forEach(btn => {
        btn.addEventListener('click', function() {
            const npk = this.getAttribute('data-npk');
            const nama = this.getAttribute('data-nama');
            const imei = this.getAttribute('data-imei');
            const aplikasi = this.getAttribute('data-aplikasi');

            if (!imei) {
                showJsAlert('IMEI tidak ditemukan.', 'danger');
                return;
            }

            // Lock button
            const originalHtml = this.innerHTML;
            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

            const payload = {
                imei: imei,
                nama_pengguna: nama,
                npk_pengguna: npk,
                aplikasi: aplikasi,
                kerusakan: 'Label Mandor'
            };

            fetch('<?= base_url("api/print") ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(payload)
            })
            .then(response => response.json())
            .then(data => {
                this.disabled = false;
                this.innerHTML = originalHtml;

                if (data.status === 'queued') {
                    showJsAlert(`<i class="bi bi-check-circle-fill me-2"></i> Job print <strong>#${data.job_id}</strong> masuk antrian.`, 'success');
                } else {
                    showJsAlert(`<i class="bi bi-exclamation-triangle me-2"></i> ${data.message || 'Gagal menambahkan antrian.'}`, 'danger');
                }
            })
            .catch(error => {
                this.disabled = false;
                this.innerHTML = originalHtml;
                console.error('Error:', error);
                showJsAlert('Terjadi kesalahan jaringan.', 'danger');
            });
        });
    });

    function showJsAlert(message, type = 'success') {
        const container = document.getElementById('js-alert-container');
        const alert = document.createElement('div');
        alert.className = `alert alert-${type} alert-dismissible fade show shadow-sm border-0 rounded-3 mb-4`;
        alert.role = 'alert';
        alert.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        container.appendChild(alert);

        // Auto-dismiss after 5s
        setTimeout(() => {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
            if (bsAlert) bsAlert.close();
        }, 5000);
    }
});
</script>
