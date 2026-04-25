<?php $active_menu = $active_menu ?? 'gadget_ceker'; ?>
<?= view('partials/admin_head', ['page_title' => $page_title]) ?>
<?= view('partials/sidebar_admin', ['active_menu' => $active_menu]) ?>

<div class="content">
    <div class="page-header">
        <div>
            <h4><?= $page_title ?></h4>
            <div class="header-sub">Menampilkan daftar gadget yang dipegang oleh karyawan berdasarkan jabatan</div>
        </div>
    </div>

    <?= view('partials/alerts') ?>

    <div class="data-card">
        <div class="data-card-header">
            <h5><i class="bi bi-phone"></i> Daftar Kepemilikan Gadget</h5>
            <div class="d-flex gap-2">
                <form action="" method="get" class="d-flex gap-1">
                    <div class="input-group input-group-sm" style="max-width: 250px;">
                        <input type="text" name="search" class="form-control" placeholder="Cari NIK / Nama / IMEI..." value="<?= esc($search ?? '') ?>">
                        <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
                    </div>
                    <?php if(!empty($search)): ?>
                        <a href="<?= current_url() ?>" class="btn btn-sm btn-outline-danger" title="Reset"><i class="bi bi-x-lg"></i></a>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th class="ps-4">NIK</th>
                        <th>Nama Karyawan</th>
                        <th>Jabatan</th>
                        <th>Afdeling</th>
                        <th>IMEI Gadget</th>
                        <th>Terakhir Update</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($items)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-info-circle fs-2 d-block mb-2"></i>
                                Tidak ada data ditemukan.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($items as $row): ?>
                        <tr>
                            <td class="ps-4"><span class="font-monospace fw-bold"><?= esc($row['nik_karyawan']) ?></span></td>
                            <td class="fw-bold"><?= esc($row['nama']) ?></td>
                            <td><span class="badge bg-light text-dark border"><?= esc($row['jabatan']) ?></span></td>
                            <td><span class="badge badge-soft-info"><?= esc($row['afdeling'] ?? '-') ?></span></td>
                            <td>
                                <?php if($row['imei']): ?>
                                    <span class="badge bg-primary font-monospace"><?= esc($row['imei']) ?></span>
                                <?php else: ?>
                                    <span class="text-muted small"><em>Belum ada data</em></span>
                                <?php endif; ?>
                            </td>
                            <td class="small text-muted">
                                <?= $row['reported_at'] ? date('d M Y, H:i', strtotime($row['reported_at'])) : '-' ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= view('partials/admin_footer') ?>
