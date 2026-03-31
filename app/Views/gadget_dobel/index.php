<?php $page_title = $title; ?>
<?= view('partials/admin_head', ['page_title' => $page_title]) ?>
<?= view('partials/sidebar_admin', ['active_menu' => $active_menu]) ?>

<div class="content">
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h4><?= esc($title) ?></h4>
            <div class="header-sub">Karyawan (Pemanen/Pekerja Rawat) yang memiliki gadget dobel (status terpakai) di Master Gadget</div>
        </div>
    </div>

    <?= view('partials/alerts') ?>

    <!-- Dobel Data Table -->
    <div class="data-card mb-4">
        <div class="data-card-header">
            <h5 class="mb-0 text-warning"><i class="bi bi-phone-vibrate me-2"></i>Data Gadget Dobel</h5>
        </div>
        <div class="p-3 table-responsive">
            <table class="table table-hover align-middle" id="tableDobel">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Karyawan (Input Mandor)</th>
                        <th>Mandor Input</th>
                        <th>IMEI diinput Mandor</th>
                        <th>Gadget di Master (Status Terpakai)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($dobel_data as $row): ?>
                    <tr>
                        <td class="ps-3">
                            <div class="fw-bold"><?= esc($row['nama_karyawan']) ?></div>
                            <div class="text-muted small">NIK: <?= esc($row['nik_karyawan']) ?> | Job: <?= esc($row['jabatan']) ?></div>
                        </td>
                        <td><?= esc($row['nama_mandor']) ?></td>
                        <td>
                            <?php if($row['imei']): ?>
                                <span class="font-monospace text-primary"><?= esc($row['imei']) ?></span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Tidak diinput IMEInya</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($row['master_gadgets'])): ?>
                                <ul class="mb-0 ps-3">
                                    <?php foreach ($row['master_gadgets'] as $mg): ?>
                                        <li class="small mb-2">
                                            <strong>IMEI:</strong> <span class="text-danger font-monospace"><?= esc($mg['imei']) ?></span><br>
                                            NPK: <?= esc($mg['npk_pengguna']) ?> (<?= esc($mg['nama_pengguna']) ?>)
                                            <?php if (!empty($mg['dipakai_oleh_input'])): ?>
                                                <div class="mt-1 p-1 bg-light border-start border-danger border-3">
                                                    <span class="text-secondary" style="font-size:0.75rem"><i class="bi bi-info-circle"></i> Dipakai  karyawan lain (diinput mandor):</span><br>
                                                    <?php foreach ($mg['dipakai_oleh_input'] as $d_cross): ?>
                                                        &bull; <b><?= esc($d_cross['nama']) ?></b> (NIK: <?= esc($d_cross['nik']) ?>) - Input: <?= esc($d_cross['mandor']) ?><br>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php endif; ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <span class="badge bg-success">Tidak ada di Master</span>
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
        $('#tableDobel').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json"
            },
            "pageLength": 25,
            "ordering": true,
            "info": true
        });
    });
</script>
