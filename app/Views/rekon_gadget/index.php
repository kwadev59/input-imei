<?php $page_title = $title; ?>
<?= view('partials/admin_head', ['page_title' => $page_title]) ?>
<?= view('partials/sidebar_admin', ['active_menu' => $active_menu]) ?>

<div class="content">
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h4><?= esc($title) ?></h4>
            <div class="header-sub">Rekonsiliasi data gadget lapangan dengan Master Gadget</div>
        </div>
    </div>

    <?= view('partials/alerts') ?>

    <!-- Summary Stats -->
    <div class="row mb-4 g-3">
        <div class="col-6 col-lg-3">
            <div class="stat-card info">
                <div class="stat-icon"><i class="bi bi-phone-fill"></i></div>
                <div class="stat-label">Total Input IMEI</div>
                <div class="stat-value"><?= $total_input ?></div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card success">
                <div class="stat-icon"><i class="bi bi-shield-check"></i></div>
                <div class="stat-label">Cocok Master</div>
                <div class="stat-value"><?= $total_match ?></div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card danger">
                <div class="stat-icon"><i class="bi bi-shield-exclamation"></i></div>
                <div class="stat-label">Tidak Cocok</div>
                <div class="stat-value"><?= $total_mismatch ?></div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <?php 
                $pctClass = 'success';
                if ($percentage_match < 50) $pctClass = 'danger';
                elseif ($percentage_match < 100) $pctClass = 'warning';
            ?>
            <div class="stat-card <?= $pctClass ?>">
                <div class="stat-icon"><i class="bi bi-percent"></i></div>
                <div class="stat-label">Persentase Cocok</div>
                <div class="stat-value"><?= $percentage_match ?>%</div>
            </div>
        </div>
    </div>

    <!-- Mismatch Data Table -->
    <div class="data-card mb-4">
        <div class="data-card-header">
            <h5 class="mb-0 text-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i>Data Tidak Cocok</h5>
        </div>
        <div class="p-3 table-responsive">
            <table class="table table-hover align-middle" id="tableMismatch">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Karyawan (Input Mandor)</th>
                        <th>Mandor Input</th>
                        <th>IMEI diinput</th>
                        <th>Keterangan / Alasan Tidak Cocok</th>
                        <th>Data Master Gadget</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($mismatch_data as $row): ?>
                    <tr>
                        <td class="ps-3">
                            <div class="fw-bold"><?= esc($row['nama_karyawan']) ?></div>
                            <div class="text-muted small">NIK: <?= esc($row['nik_karyawan']) ?> | Afd: <?= esc($row['afdeling']) ?></div>
                        </td>
                        <td><?= esc($row['nama_mandor']) ?></td>
                        <td><span class="font-monospace text-primary"><?= esc($row['imei']) ?></span></td>
                        <td class="text-danger small"><?= esc($row['mismatch_reason']) ?></td>
                        <td>
                            <?php if($row['master_info']): ?>
                                <div class="small">
                                    NPK: <?= esc($row['master_info']['npk_pengguna']) ?><br>
                                    Nama: <?= esc($row['master_info']['nama_pengguna']) ?>
                                </div>
                            <?php else: ?>
                                <span class="badge bg-secondary">Tidak ada di Master</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= view('partials/admin_footer') ?>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        $('#tableMismatch').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json"
            },
            "pageLength": 25,
            "ordering": true,
            "info": true
        });
    });
</script>
