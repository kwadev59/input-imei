<?php $active_menu = $active_menu ?? 'gadget_ceker'; ?>
<?= view('partials/admin_head', ['page_title' => $page_title]) ?>
<?= view('partials/sidebar_admin', ['active_menu' => $active_menu]) ?>

<div class="content">
    <!-- Page Header -->
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
                            <th>Jabatan</th>
                            <th>IMEI Gadget</th>
                            <th>Aplikasi</th>
                            <th>Terakhir Update</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($items as $row): ?>
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
                                <span class="badge bg-light text-dark border">
                                    <?= esc($row['jabatan']) ?>
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
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= view('partials/admin_footer') ?>
