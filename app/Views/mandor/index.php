<?php $page_title = 'Manajemen Mandor'; $active_menu = 'mandor'; ?>
<?= view('partials/admin_head', ['page_title' => $page_title]) ?>
<?= view('partials/sidebar_admin', ['active_menu' => $active_menu]) ?>

<div class="content">
    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h4>Data Mandor / User</h4>
            <div class="header-sub"><?= count($mandor) ?> mandor terdaftar</div>
        </div>
    </div>

    <?= view('partials/alerts') ?>

    <div class="data-card">
        <div class="data-card-header">
            <h5><i class="bi bi-person-badge"></i> Daftar Mandor</h5>
            <div class="d-flex gap-2 flex-wrap">
                <a href="<?= base_url('download/template?type=mandor') ?>" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-download me-1"></i> Template CSV
                </a>
                <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#importCard">
                    <i class="bi bi-upload me-1"></i> Import CSV
                </button>
            </div>
        </div>

        <!-- Import CSV Collapse -->
        <div class="collapse" id="importCard">
            <div class="data-card-body" style="background:#f8fafc; border-bottom: 1px solid var(--card-border);">
                <h6 class="mb-3 fw-bold"><i class="bi bi-upload me-1 text-primary"></i> Import Mandor via CSV</h6>
                <form action="<?= base_url('mandor/import') ?>" method="post" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <div class="row align-items-end g-3">
                        <div class="col-md-5">
                            <label class="form-label small fw-bold text-muted">Upload CSV (NPK, Nama, Afdeling, Tipe, PT/Site)</label>
                            <input type="file" name="csv_file" class="form-control form-control-sm" required accept=".csv">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-sm btn-primary w-100">
                                <i class="bi bi-cloud-upload me-1"></i> Upload
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <?php if(empty($mandor)): ?>
            <div class="empty-state">
                <div class="empty-state-icon"><i class="bi bi-person-badge"></i></div>
                <p>Belum ada data mandor. Import CSV untuk menambahkan.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th class="ps-4">NPK</th>
                            <th>Nama Lengkap</th>
                            <th>PT / SITE</th>
                            <th>Afdeling</th>
                            <th>Tipe Mandor</th>
                            <th>Password</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($mandor as $row): ?>
                        <tr>
                            <td class="ps-4">
                                <span class="font-monospace fw-bold" style="color:var(--primary)"><?= esc(str_pad($row['npk'], 7, '0', STR_PAD_LEFT)) ?></span>
                            </td>
                            <td>
                                <div class="fw-bold"><?= esc($row['nama_lengkap']) ?></div>
                                <small class="text-muted">ID: <?= $row['id'] ?></small>
                            </td>
                            <td><span class="badge badge-soft-primary"><?= esc($row['pt_site'] ?? '-') ?></span></td>
                            <td><span class="badge badge-soft-info"><?= esc($row['afdeling_id']) ?></span></td>
                            <td>
                                <form method="post" action="<?= base_url('mandor/change-tipe/' . $row['id']) ?>" class="d-inline">
                                    <?= csrf_field() ?>
                                    <select name="tipe_mandor" class="form-select form-select-sm fw-bold <?= $row['tipe_mandor'] == 'Rawat' ? 'text-success' : 'text-warning' ?>" 
                                            style="width: auto; background-color: <?= $row['tipe_mandor'] == 'Rawat' ? 'var(--success-soft)' : 'var(--warning-soft)' ?>; cursor: pointer; border: none; border-radius: 8px;"
                                            onchange="this.form.submit()">
                                        <option value="Panen" <?= $row['tipe_mandor'] == 'Panen' ? 'selected' : '' ?>>🌾 Panen</option>
                                        <option value="Rawat" <?= $row['tipe_mandor'] == 'Rawat' ? 'selected' : '' ?>>🌿 Rawat</option>
                                    </select>
                                </form>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline-warning px-3"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#passwordModal"
                                        data-id="<?= $row['id'] ?>"
                                        data-nama="<?= esc($row['nama_lengkap']) ?>"
                                        data-npk="<?= esc(str_pad($row['npk'], 7, '0', STR_PAD_LEFT)) ?>">
                                    <i class="bi bi-key me-1"></i> Ganti
                                </button>
                            </td>
                            <td><span class="badge badge-soft-success">Aktif</span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="data-card-footer">
                <?= $pager->links('mandor', 'default_full') ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Ganti Password -->
<div class="modal fade" id="passwordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-bold"><i class="bi bi-key text-warning me-2"></i>Ganti Password</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="passwordForm" method="post" action="">
                <?= csrf_field() ?>
                <div class="modal-body px-4 pt-3">
                    <div class="text-center mb-3">
                        <div class="fw-bold text-dark" id="modalNama"></div>
                        <small class="text-muted font-monospace" id="modalNpk"></small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Password Baru</label>
                        <div class="input-group">
                            <input type="password" class="form-control" name="new_password" id="inputPassword" 
                                   required minlength="6" placeholder="Minimal 6 karakter">
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword" style="border-radius: 0 10px 10px 0;">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <div class="form-text small">Minimal 6 karakter</div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 px-4 pb-4">
                    <button type="button" class="btn btn-light flex-fill" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning flex-fill fw-bold">
                        <i class="bi bi-check-lg me-1"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= view('partials/admin_footer') ?>

<script>
    // Pass data to modal
    const passwordModal = document.getElementById('passwordModal');
    if(passwordModal) {
        passwordModal.addEventListener('show.bs.modal', function(event) {
            const btn = event.relatedTarget;
            document.getElementById('modalNama').textContent = btn.getAttribute('data-nama');
            document.getElementById('modalNpk').textContent = 'NPK: ' + btn.getAttribute('data-npk');
            document.getElementById('passwordForm').action = '<?= base_url("mandor/change-password/") ?>' + btn.getAttribute('data-id');
            document.getElementById('inputPassword').value = '';
        });
    }

    // Toggle password visibility
    const toggleBtn = document.getElementById('togglePassword');
    if(toggleBtn) {
        toggleBtn.addEventListener('click', function() {
            const input = document.getElementById('inputPassword');
            const icon = this.querySelector('i');
            if(input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('bi-eye', 'bi-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('bi-eye-slash', 'bi-eye');
            }
        });
    }
</script>
