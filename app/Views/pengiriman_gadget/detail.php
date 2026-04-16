<?php $page_title = 'Detail BASTE Pengiriman Gadget'; ?>
<?= view('partials/admin_head', ['page_title' => $page_title]) ?>
<?= view('partials/sidebar_admin', ['active_menu' => $active_menu ?? 'pengiriman_gadget']) ?>

<div class="content">
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h4><i class="bi bi-file-text me-2"></i>Detail BASTE Pengiriman</h4>
            <div class="header-sub">BASTE Register: <strong><?= esc($baste['no_baste']) ?></strong></div>
        </div>
        <div>
            <a href="<?= base_url('pengiriman-gadget/edit/' . $baste['id']) ?>" class="btn btn-warning btn-action text-dark me-2">
                <i class="bi bi-pencil-square me-1"></i> Edit
            </a>
            <form action="<?= base_url('pengiriman-gadget/delete/' . $baste['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus BASTE ini? Data pengiriman gadget yang terkait akan kembali berstatus draft.');">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-danger btn-action me-2">
                    <i class="bi bi-trash me-1"></i> Hapus
                </button>
            </form>
            <a href="<?= base_url('pengiriman-gadget') ?>" class="btn btn-outline-secondary btn-action me-2">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
            <a href="<?= base_url('pengiriman-gadget/print/' . $baste['id']) ?>" class="btn btn-secondary btn-action" target="_blank">
                <i class="bi bi-printer me-1"></i> Cetak BASTE PDF
            </a>
        </div>
    </div>

    <?= view('partials/alerts') ?>

    <div class="data-card mb-4">
        <div class="data-card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-primary"><i class="bi bi-info-circle me-2"></i>Informasi BASTE</h5>
            <span class="badge bg-success px-3 py-2"><i class="bi bi-check-circle me-1"></i>SUBMITTED</span>
        </div>
        <div class="card-body p-4">
            
            <div class="row mb-4 p-4 rounded-3 border bg-light">
                <div class="col-md-6 border-end">
                    <table class="table table-borderless table-sm mb-0">
                        <tr><td width="35%" class="text-muted fw-bold">No Register BASTE</td><td width="5%">:</td><td class="fw-bold fs-5 text-primary"><?= esc($baste['no_baste']) ?></td></tr>
                        <tr><td class="text-muted fw-bold">Tanggal</td><td>:</td><td class="fw-bold"><?= date('d F Y', strtotime($baste['tanggal'])) ?></td></tr>
                    </table>
                </div>
                <div class="col-md-6 ps-md-4">
                    <table class="table table-borderless table-sm mb-0">
                        <tr><td width="35%" class="text-muted fw-bold">Waktu Submit</td><td width="5%">:</td><td><?= date('d M Y, H:i:s', strtotime($baste['created_at'])) ?></td></tr>
                        <tr><td class="text-muted fw-bold">Total Gadget</td><td>:</td><td><span class="badge bg-primary px-3 fs-6 rounded-pill"><?= count($items) ?> unit</span></td></tr>
                        <?php if(!empty($baste['no_resi'])): ?>
                        <tr>
                            <td class="text-muted fw-bold">No Resi</td>
                            <td>:</td>
                            <td>
                                <span class="badge bg-success border"><?= esc($baste['no_resi']) ?></span>
                                <form action="<?= base_url('pengiriman-gadget/aksi-hapus-resi/'.$baste['id']) ?>" method="get" class="d-inline" onsubmit="return confirm('Hapus resi ini?')">
                                    <button type="submit" class="btn btn-link btn-sm text-danger p-0 ms-2" title="Hapus Resi">
                                        <i class="bi bi-trash"></i> Hapus Resi
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </table>
                </div>
            </div>

            <h5 class="mb-3 text-secondary border-bottom pb-2"><i class="bi bi-box-seam me-2"></i>Daftar Gadget Dikirim</h5>
            <div class="table-responsive border rounded-3 shadow-sm">
                <table class="table table-hover table-striped align-middle mb-0" style="font-size: 0.95rem;">
                    <thead class="table-dark text-center">
                        <tr>
                            <th width="5%" class="py-3">No</th>
                            <th width="12%">IMEI</th>
                            <th width="5%">PT</th>
                            <th width="5%">AFD</th>
                            <th width="10%">NPK</th>
                            <th width="15%">Nama Pengguna</th>
                            <th width="10%">Aplikasi</th>
                            <th width="18%">Kerusakan</th>
                            <th width="20%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($items)): ?>
                            <tr><td colspan="9" class="text-center text-muted py-5"><i class="bi bi-inbox fs-1 d-block mb-3 text-secondary opacity-50"></i>Tidak ada data item.</td></tr>
                        <?php else: ?>
                            <?php foreach ($items as $index => $item): ?>
                                <tr>
                                    <td class="text-center fw-bold text-muted"><?= $index + 1 ?></td>
                                    <td><span class="fw-bold text-primary"><i class="bi bi-phone me-1"></i><?= esc($item['imei']) ?></span></td>
                                    <td class="text-center"><span class="badge bg-secondary"><?= esc($item['pt'] ?? '-') ?></span></td>
                                    <td class="text-center"><span class="badge border border-secondary text-secondary"><?= esc($item['afd'] ?? '-') ?></span></td>
                                    <td class="text-center font-monospace text-muted"><?= esc($item['npk_pengguna'] ?? '-') ?></td>
                                    <td class="fw-medium"><?= esc($item['nama_pengguna'] ?? '-') ?></td>
                                    <td class="text-center"><span class="badge bg-info text-dark"><i class="bi bi-grid-fill me-1"></i><?= esc($item['aplikasi'] ?? '-') ?></span></td>
                                    <td class="text-dark"><small><?= nl2br(esc($item['kerusakan'])) ?></small></td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1">
                                            <a href="<?= base_url('pengiriman-gadget/edit-item/' . $item['id']) ?>" class="btn btn-sm btn-outline-warning" title="Edit Item">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <form action="<?= base_url('pengiriman-gadget/delete-item/' . $item['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus item ini dari BASTE?');">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus Item">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
<?= view('partials/admin_footer') ?>
<!-- Refresh timestamp: Thu Apr 16 10:11:00 PM WITA 2026 -->
