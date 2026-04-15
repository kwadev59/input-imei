<?php $page_title = 'Riwayat Perubahan Data'; $active_menu = 'karyawan'; ?>
<?= view('partials/admin_head', ['page_title' => $page_title]) ?>
<?= view('partials/sidebar_admin', ['active_menu' => $active_menu]) ?>

<div class="content">
    <div class="page-header">
        <div>
            <h4>Riwayat Perubahan Data</h4>
            <div class="header-sub">Menampilkan rekam jejak untuk NIK: <strong><?= esc($karyawan['nik_karyawan']) ?></strong></div>
        </div>
        <a href="<?= base_url('karyawan') ?>" class="btn btn-outline-secondary btn-sm px-3">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <?= view('partials/alerts') ?>

    <!-- Info Detail Karyawan Saat Ini -->
    <div class="card mb-4 border-0 shadow-sm" style="border-radius: 12px;">
        <div class="card-body px-4 py-3 bg-light" style="border-radius: 12px;">
            <div class="row g-3">
                <div class="col-md-3">
                    <span class="text-muted small d-block mb-1">Nama Saat Ini</span>
                    <strong class="text-dark"><?= esc($karyawan['nama']) ?></strong>
                </div>
                <div class="col-md-3">
                    <span class="text-muted small d-block mb-1">Jabatan</span>
                    <strong class="text-dark"><?= esc($karyawan['jabatan']) ?></strong>
                </div>
                <div class="col-md-3">
                    <span class="text-muted small d-block mb-1">Afdeling & Site</span>
                    <strong class="text-dark"><?= esc($karyawan['afdeling']) ?> / <?= esc($karyawan['pt_site'] ?? '-') ?></strong>
                </div>
                <div class="col-md-3">
                    <span class="text-muted small d-block mb-1">Status Keaktifan</span>
                    <?php if($karyawan['status_aktif'] == 'Aktif'): ?>
                        <span class="badge bg-success">Aktif Bekerja</span>
                    <?php else: ?>
                        <span class="badge bg-danger">Non-Aktif / Resign</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Riwayat -->
    <div class="data-card">
        <div class="card-header bg-white pt-3 pb-2 border-bottom">
            <h6 class="mb-0 text-primary fw-bold"><i class="bi bi-clock-history me-1"></i> Rekam Jejak Data Sebelumnya</h6>
            <div class="small text-muted mt-1">Daftar riwayat profil profil karyawan sebelum dilakukan perubahan.</div>
        </div>
        
        <?php if(empty($riwayat)): ?>
            <div class="empty-state py-5 text-center">
                <div class="empty-state-icon" style="font-size: 3rem; color: #dee2e6;"><i class="bi bi-inbox"></i></div>
                <p class="text-muted mt-3">Karyawan ini belum memiliki riwayat perubahan data.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">No</th>
                            <th>Tgl Perubahan</th>
                            <th>Nama Sebelumnya</th>
                            <th>Jabatan</th>
                            <th>Afdeling & Site</th>
                            <th>Status</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach($riwayat as $r): ?>
                        <tr>
                            <td class="ps-4 text-muted"><?= $no++ ?></td>
                            <td>
                                <span class="badge bg-secondary mb-1">
                                    <i class="bi bi-calendar3 me-1"></i> <?= date('d M Y', strtotime($r['created_at'])) ?>
                                </span><br>
                                <span class="small text-muted"><i class="bi bi-clock me-1"></i> <?= date('H:i:s', strtotime($r['created_at'])) ?></span>
                            </td>
                            <td class="fw-bold text-dark"><?= esc($r['nama']) ?></td>
                            <td><span class="badge bg-light text-dark border"><?= esc($r['jabatan']) ?></span></td>
                            <td><span class="badge badge-soft-info"><?= esc($r['afdeling']) ?></span> <small class="text-muted">/ <?= esc($r['pt_site']) ?></small></td>
                            <td>
                                <?php if($r['status_aktif'] == 'Aktif'): ?>
                                    <span class="badge badge-soft-success">Aktif</span>
                                <?php else: ?>
                                    <span class="badge badge-soft-danger">Non-Aktif</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="small fst-italic text-muted">
                                    <?= esc($r['keterangan']) ?>
                                </span>
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
