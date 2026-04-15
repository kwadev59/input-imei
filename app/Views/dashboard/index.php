<?php $page_title = 'Dashboard Admin'; $active_menu = 'dashboard'; ?>
<?= view('partials/admin_head', ['page_title' => $page_title]) ?>
<?= view('partials/sidebar_admin', ['active_menu' => $active_menu]) ?>

<div class="content">
    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h4>Overview Distribusi</h4>
            <div class="header-sub">Selamat datang kembali, <?= esc($user_nama) ?> 👋</div>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url('dashboard/export') ?>" class="btn btn-success btn-sm px-3">
                <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
            </a>
        </div>
    </div>

    <?= view('partials/alerts') ?>

    <!-- Stats -->
    <div class="row mb-4 g-3">
        <div class="col-6 col-lg-3">
            <div class="stat-card info">
                <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
                <div class="stat-label">Total Karyawan</div>
                <div class="stat-value"><?= $total_karyawan ?></div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card success">
                <div class="stat-icon"><i class="bi bi-check-circle-fill"></i></div>
                <div class="stat-label">Sudah Input</div>
                <div class="stat-value"><?= $total_input ?></div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card warning">
                <div class="stat-icon"><i class="bi bi-clock-fill"></i></div>
                <div class="stat-label">Belum Input</div>
                <div class="stat-value"><?= $total_belum ?></div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card danger">
                <div class="stat-icon"><i class="bi bi-exclamation-triangle-fill"></i></div>
                <div class="stat-label">Duplikat IMEI</div>
                <div class="stat-value"><?= $total_duplicate ?></div>
            </div>
        </div>
    </div>

    <!-- Gadget Stats per Afdeling & PT -->
    <div class="data-card mb-4">
        <div class="data-card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-bar-chart-fill"></i> Kepemilikan Gadget Pemanen & Pekerja Rawat</h5>
            <a href="<?= base_url('dashboard/export_gadget_stats') ?>" class="btn btn-sm btn-outline-success">
                <i class="bi bi-file-earmark-excel"></i> Export
            </a>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th rowspan="2" class="align-middle text-center">Site/PT</th>
                        <th rowspan="2" class="align-middle text-center">Afdeling</th>
                        <th colspan="2" class="text-center">Pemanen</th>
                        <th colspan="2" class="text-center">Pekerja Rawat</th>
                    </tr>
                    <tr>
                        <th class="text-center text-success" style="font-size:0.85rem">Ada Gadget</th>
                        <th class="text-center text-danger" style="font-size:0.85rem">Tidak Ada</th>
                        <th class="text-center text-success" style="font-size:0.85rem">Ada Gadget</th>
                        <th class="text-center text-danger" style="font-size:0.85rem">Tidak Ada</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($gadget_stats)): ?>
                        <tr><td colspan="6" class="text-center">Belum ada data.</td></tr>
                    <?php else: ?>
                        <?php foreach($gadget_stats as $pt => $ptData): ?>
                            <?php 
                                $afdelings = $ptData['afdelings'];
                                $ptCount = count($afdelings) + 1; // +1 for the Total row
                                $firstPt = true;
                            ?>
                            <?php foreach($afdelings as $afd => $stats): ?>
                                <?php 
                                    // Calculate completion style
                                    $bgClass = '';
                                    if ($stats['total_target'] > 0) {
                                        if ($stats['total_input'] >= $stats['total_target']) {
                                            $bgClass = 'table-success'; // Hijau (100% or more)
                                        } else {
                                            $bgClass = 'table-warning'; // Orange (belum 100%)
                                        }
                                    }
                                ?>
                                <tr class="<?= $bgClass ?>">
                                    <?php if($firstPt): ?>
                                        <td rowspan="<?= $ptCount ?>" class="fw-bold align-middle text-center bg-white"><?= esc($pt) ?></td>
                                        <?php $firstPt = false; ?>
                                    <?php endif; ?>
                                    <td class="text-center fw-bold "><?= esc($afd) ?></td>
                                    
                                    <td class="text-center"><?= $stats['Pemanen']['ada'] ?></td>
                                    <td class="text-center text-muted"><?= $stats['Pemanen']['tidak'] ?></td>
                                    
                                    <td class="text-center"><?= $stats['Pekerja Rawat']['ada'] ?></td>
                                    <td class="text-center text-muted"><?= $stats['Pekerja Rawat']['tidak'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <!-- Total Row for PT -->
                            <tr class="table-secondary fw-bold">
                                <td class="text-center">TOTAL <?= esc($pt) ?></td>
                                <td class="text-center text-success"><?= $ptData['total_pt_ada_pemanen'] ?></td>
                                <td class="text-center text-danger"><?= $ptData['total_pt_tidak_pemanen'] ?></td>
                                <td class="text-center text-success"><?= $ptData['total_pt_ada_rawat'] ?></td>
                                <td class="text-center text-danger"><?= $ptData['total_pt_tidak_rawat'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="data-card mb-4">
        <div class="data-card-header">
            <h5><i class="bi bi-person-badge"></i> Daftar Mandor (Sudah Input)</h5>
        </div>
        <div class="table-responsive p-3">
            <table class="table table-hover align-middle" id="tableMandor">
                <thead>
                    <tr>
                        <th class="ps-4">Nama Mandor</th>
                        <th>Afdeling</th>
                        <th>Total Input</th>
                        <th>Terakhir Input</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($mandor_list)): ?>
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">
                                    <div class="empty-state-icon"><i class="bi bi-person-badge"></i></div>
                                    <p>Belum ada mandor yang melakukan input.</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($mandor_list as $m): ?>
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold"><?= esc($m['nama_lengkap']) ?></div>
                            </td>
                            <td><span class="badge badge-soft-info"><?= esc($m['afdeling_id']) ?></span></td>
                            <td><span class="fw-bold"><?= $m['total_input'] ?></span> <span class="text-muted">data</span></td>
                            <td><span class="text-muted" style="font-size:0.82rem"><?= date('d M Y H:i', strtotime($m['last_input'])) ?></span></td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="<?= base_url('dashboard/report/'.$m['id']) ?>" target="_blank" class="btn btn-sm btn-outline-danger px-2" title="Lihat PDF">
                                        <i class="bi bi-file-pdf"></i> PDF
                                    </a>
                                    <a href="<?= base_url('dashboard/report/'.$m['id'].'?auto_print=1') ?>" target="_blank" class="btn btn-sm btn-outline-success px-2" title="Print Semua Label Online">
                                        <i class="bi bi-printer"></i> Print
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Latest Inputs -->
    <div class="data-card">
        <div class="data-card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-clock-history"></i> Log Input Terakhir</h5>
            <a href="<?= base_url('dashboard/export_latest_inputs_txt') ?>" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-file-earmark-text"></i> Export Txt
            </a>
        </div>
        <div class="table-responsive p-3">
            <table class="table table-hover align-middle" id="tableLog">
                <thead>
                    <tr>
                        <th class="ps-4">Waktu</th>
                        <th>Karyawan</th>
                        <th>Afdeling</th>
                        <th>Status</th>
                        <th>IMEI</th>
                        <th>Diinput Oleh</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($latest_inputs)): ?>
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <div class="empty-state-icon"><i class="bi bi-inbox"></i></div>
                                    <p>Belum ada data input.</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($latest_inputs as $row): ?>
                        <tr>
                            <td class="ps-4"><span class="text-muted" style="font-size:0.82rem"><?= date('d M H:i', strtotime($row['input_at'])) ?></span></td>
                            <td>
                                <div class="fw-bold"><?= esc($row['nama_karyawan']) ?></div>
                                <small class="text-muted"><?= esc($row['nik_karyawan']) ?></small>
                            </td>
                            <td><span class="badge badge-soft-info"><?= esc($row['afdeling']) ?></span></td>
                            <td>
                                <?php if($row['status_gadget'] == 'Ada'): ?>
                                    <span class="badge badge-soft-success">Ada Gadget</span>
                                <?php else: ?>
                                    <span class="badge badge-soft-danger">Tidak Ada</span>
                                <?php endif; ?>
                            </td>
                            <td><span class="font-monospace" style="color:var(--primary); font-size:0.82rem"><?= esc($row['imei'] ?: '-') ?></span></td>
                            <td><span style="font-size:0.85rem"><?= esc($row['nama_mandor']) ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= view('partials/admin_footer') ?>

<!-- Add DataTables scripts and style if they don't exist yet in the footer -->
<!-- We can safely push it here -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function() {
        $('#tableMandor').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json"
            },
            "pageLength": 10,
            "ordering": true,
            "info": true
        });

        $('#tableLog').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json"
            },
            "pageLength": 10,
            "order": [], // Let the default ordering from controller stand
            "ordering": true,
            "info": true
        });
    });
</script>
