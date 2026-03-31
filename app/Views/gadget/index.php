<?php $page_title = 'Master Gadget'; $active_menu = 'gadget'; ?>
<?= view('partials/admin_head', ['page_title' => $page_title]) ?>
<?= view('partials/sidebar_admin', ['active_menu' => $active_menu]) ?>

<div class="content">
    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h4>Master Gadget</h4>
            <div class="header-sub">Database aset gadget perusahaan</div>
        </div>
    </div>

    <?= view('partials/alerts') ?>

    <div class="data-card">
        <div class="data-card-header">
            <h5><i class="bi bi-phone"></i> Daftar Aset Gadget</h5>
            <div class="d-flex gap-2 flex-wrap align-items-center">
                <a href="<?= base_url('download/template?type=gadget') ?>" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-download me-1"></i> Template
                </a>
                <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#importCard">
                    <i class="bi bi-upload me-1"></i> Import CSV
                </button>
                <form action="" method="get" class="d-flex gap-1">
                    <div class="input-group input-group-sm" style="max-width: 240px;">
                        <input type="text" name="search" class="form-control" placeholder="Cari IMEI / Pengguna..." value="<?= esc($search ?? '') ?>">
                        <button class="btn btn-outline-secondary" type="submit" style="border-radius: 0 10px 10px 0;"><i class="bi bi-search"></i></button>
                    </div>
                    <?php if(!empty($search)): ?>
                        <a href="<?= base_url('gadget') ?>" class="btn btn-sm btn-outline-danger" title="Reset"><i class="bi bi-x-lg"></i></a>
                    <?php endif; ?>
                </form>
            </div>
        </div>
        
        <!-- Import CSV Collapse -->
        <div class="collapse" id="importCard">
            <div class="data-card-body" style="background:#f8fafc; border-bottom: 1px solid var(--card-border);">
                <h6 class="mb-3 fw-bold"><i class="bi bi-upload me-1 text-primary"></i> Import Master Gadget</h6>
                <form action="<?= base_url('gadget/import') ?>" method="post" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <div class="row align-items-end g-3">
                        <div class="col-md-5">
                            <label class="form-label small fw-bold text-muted">Upload CSV Gadget</label>
                            <input type="file" name="csv_file" class="form-control form-control-sm" required accept=".csv">
                            <div class="form-text small text-muted mt-1">Gunakan template yang disediakan.</div>
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

        <?php if(empty($gadgets)): ?>
            <div class="empty-state">
                <div class="empty-state-icon"><i class="bi bi-phone"></i></div>
                <p>Belum ada data gadget.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th class="ps-4">IMEI</th>
                            <th>Aplikasi</th>
                            <th>PT / AFD</th>
                            <th>Pengguna</th>
                            <th>Tipe Asset</th>
                            <th>Status</th>
                            <th>Note</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($gadgets as $row): ?>
                        <tr>
                            <td class="ps-4"><span class="font-monospace fw-bold" style="color:var(--primary)"><?= esc($row['imei']) ?></span></td>
                            <td><?= esc($row['aplikasi']) ?></td>
                            <td>
                                <div><?= esc($row['pt']) ?></div>
                                <small class="text-muted"><?= esc($row['afd']) ?></small>
                            </td>
                            <td>
                                <div class="fw-bold"><?= esc($row['nama_pengguna']) ?></div>
                                <small class="text-muted"><?= esc($row['npk_pengguna']) ?> - <?= esc($row['pos_title']) ?></small>
                            </td>
                            <td>
                                <div><?= esc($row['tipe_asset']) ?></div>
                                <small class="text-muted"><?= esc($row['part_asset']) ?></small>
                            </td>
                            <td><span class="badge badge-soft-info"><?= esc($row['status_desc']) ?></span></td>
                            <td><span class="text-muted fst-italic" style="font-size:0.82rem"><?= esc(substr($row['note'] ?? '', 0, 30)) ?><?= strlen($row['note'] ?? '') > 30 ? '...' : '' ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="data-card-footer">
                <?= $pager->links('gadget', 'default_full') ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= view('partials/admin_footer') ?>
