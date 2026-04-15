<?php $page_title = 'Detail History Pengiriman / BASTE'; ?>
<?= view('partials/admin_head', ['page_title' => $page_title]) ?>
<?= view('partials/sidebar_admin', ['active_menu' => $active_menu ?? 'detail_pengiriman']) ?>

<div class="content">
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h4><i class="bi bi-list-check me-2"></i>Detail History Pengiriman / BASTE</h4>
            <div class="header-sub">Daftar lengkap IMEI gadget yang sudah dikirim melalui semua BASTE.</div>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url('pengiriman-gadget/export-detail') ?>" class="btn btn-success btn-action">
                <i class="bi bi-file-earmark-spreadsheet me-1"></i> Export Excel
            </a>
            <a href="<?= base_url('pengiriman-gadget') ?>" class="btn btn-outline-secondary btn-action">
                <i class="bi bi-arrow-left me-1"></i> History BASTE
            </a>
        </div>
    </div>

    <?= view('partials/alerts') ?>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3 mb-md-0">
            <div class="data-card h-100">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center me-3" style="width: 52px; height: 52px;">
                        <i class="bi bi-phone-fill fs-4 text-primary"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Total IMEI Dikirim</div>
                        <h3 class="mb-0 fw-bold"><?= count($items) ?></h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3 mb-md-0">
            <div class="data-card h-100">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="rounded-circle bg-success bg-opacity-10 d-flex align-items-center justify-content-center me-3" style="width: 52px; height: 52px;">
                        <i class="bi bi-file-earmark-text-fill fs-4 text-success"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Total BASTE</div>
                        <h3 class="mb-0 fw-bold"><?= $total_bastes ?></h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="data-card h-100">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="rounded-circle bg-warning bg-opacity-10 d-flex align-items-center justify-content-center me-3" style="width: 52px; height: 52px;">
                        <i class="bi bi-truck fs-4 text-warning"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Dengan Resi</div>
                        <h3 class="mb-0 fw-bold"><?= count(array_filter($items, fn($i) => !empty($i['no_resi']))) ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="data-card mb-4">
        <div class="data-card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-table me-2"></i>Daftar IMEI yang Telah Dikirim</h5>
        </div>
        <div class="table-responsive">
            <?php if(empty($items)): ?>
                <div class="text-center py-5">
                    <i class="bi bi-inbox fs-1 text-muted mb-3 d-block"></i>
                    <h5 class="text-secondary">Belum ada data pengiriman.</h5>
                    <p class="text-muted">Data akan tampil setelah BASTE pertama disubmit.</p>
                </div>
            <?php else: ?>
                <table class="table table-hover table-striped align-middle mb-0" id="detailAllTable" style="font-size: 0.92rem;">
                    <thead class="table-dark text-center">
                        <tr>
                            <th width="4%" class="py-3">No</th>
                            <th width="13%">IMEI</th>
                            <th width="14%">No BASTE</th>
                            <th width="9%">Tanggal</th>
                            <th width="5%">PT</th>
                            <th width="5%">AFD</th>
                            <th width="8%">NPK</th>
                            <th width="13%">Nama Pengguna</th>
                            <th width="8%">Aplikasi</th>
                            <th width="12%">Kerusakan</th>
                            <th width="9%">No Resi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($items as $index => $item): ?>
                            <tr>
                                <td class="text-center fw-bold text-muted"><?= $index + 1 ?></td>
                                <td>
                                    <span class="fw-bold text-primary font-monospace">
                                        <i class="bi bi-phone me-1 opacity-50"></i><?= esc($item['imei']) ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="<?= base_url('pengiriman-gadget/detail/' . $item['baste_id']) ?>" class="text-decoration-none fw-semibold">
                                        <i class="bi bi-file-earmark-text me-1 opacity-50"></i><?= esc($item['no_baste']) ?>
                                    </a>
                                </td>
                                <td class="text-center">
                                    <span class="small"><?= date('d M Y', strtotime($item['tanggal_baste'])) ?></span>
                                </td>
                                <td class="text-center"><span class="badge bg-secondary"><?= esc($item['pt'] ?? '-') ?></span></td>
                                <td class="text-center"><span class="badge border border-secondary text-secondary"><?= esc($item['afd'] ?? '-') ?></span></td>
                                <td class="text-center font-monospace text-muted small"><?= esc($item['npk_pengguna'] ?? '-') ?></td>
                                <td class="fw-medium"><?= esc($item['nama_pengguna'] ?? '-') ?></td>
                                <td class="text-center">
                                    <span class="badge bg-info text-dark"><i class="bi bi-grid-fill me-1"></i><?= esc($item['aplikasi'] ?? '-') ?></span>
                                </td>
                                <td>
                                    <?php if(!empty($item['kerusakan'])): ?>
                                        <small class="text-dark"><?= nl2br(esc($item['kerusakan'])) ?></small>
                                    <?php else: ?>
                                        <small class="text-muted fst-italic">-</small>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if(!empty($item['no_resi'])): ?>
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success">
                                            <i class="bi bi-truck me-1"></i><?= esc($item['no_resi']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-light text-muted border">Belum ada</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if(typeof $ !== 'undefined' && $.fn.DataTable && document.getElementById('detailAllTable')) {
            $('#detailAllTable').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json"
                },
                "pageLength": 25,
                "order": [[3, 'desc'], [0, 'asc']],
                "dom": '<"row mb-3"<"col-sm-6"l><"col-sm-6"f>>rtip'
            });
        }
    });
</script>

<?= view('partials/admin_footer') ?>
