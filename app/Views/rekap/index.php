<?php $page_title = 'Rekap Afdeling'; $active_menu = 'rekap'; ?>
<?= view('partials/admin_head', ['page_title' => $page_title]) ?>
<?= view('partials/sidebar_admin', ['active_menu' => $active_menu]) ?>

<div class="content">
    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h4>Rekap Per Afdeling <span class="badge bg-secondary" style="font-size: 0.5em; vertical-align: middle;">Pemanen & Pekerja Rawat</span></h4>
            <div class="header-sub">Jumlah karyawan (khusus Pemanen dan Pekerja Rawat) yang sudah dan belum diinput per PT/SITE dan Afdeling</div>
        </div>
    </div>

    <?= view('partials/alerts') ?>

    <!-- Grand Total Stats -->
    <?php $pctGlobal = $grand_total > 0 ? round(($grand_sudah / $grand_total) * 100, 1) : 0; ?>
    <div class="row mb-4 g-3">
        <div class="col-6 col-lg-3">
            <div class="stat-card info">
                <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
                <div class="stat-label">Total Karyawan</div>
                <div class="stat-value"><?= $grand_total ?></div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card success">
                <div class="stat-icon"><i class="bi bi-check-circle-fill"></i></div>
                <div class="stat-label">Sudah Input</div>
                <div class="stat-value"><?= $grand_sudah ?></div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card warning">
                <div class="stat-icon"><i class="bi bi-clock-fill"></i></div>
                <div class="stat-label">Belum Input</div>
                <div class="stat-value"><?= $grand_belum ?></div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card info">
                <div class="stat-icon"><i class="bi bi-speedometer2"></i></div>
                <div class="stat-label">Progress</div>
                <div class="stat-value"><?= $pctGlobal ?>%</div>
            </div>
        </div>
    </div>

    <!-- Progress Bar Global -->
    <div class="data-card mb-4">
        <div class="data-card-header d-flex justify-content-between align-items-center">
            <h5><i class="bi bi-bar-chart-line"></i> Progress Keseluruhan</h5>
            <span class="fw-bold" style="color:var(--accent-color);font-size:1rem;"><?= $grand_sudah ?> / <?= $grand_total ?></span>
        </div>
        <div style="padding:16px 20px 20px;">
            <div class="progress" style="height: 18px; border-radius: 10px; background: var(--border-color);">
                <div class="progress-bar" role="progressbar" 
                     style="width: <?= $pctGlobal ?>%; background: linear-gradient(90deg, #10b981, #34d399); border-radius: 10px; font-weight: 700; font-size: 0.75rem;"
                     aria-valuenow="<?= $pctGlobal ?>" aria-valuemin="0" aria-valuemax="100">
                    <?= $pctGlobal > 8 ? $pctGlobal.'%' : '' ?>
                </div>
            </div>
            <div class="d-flex justify-content-between mt-2">
                <small class="text-muted"><strong style="color:var(--success-color);"><?= $grand_sudah ?></strong> karyawan sudah diinput</small>
                <small class="text-muted">Sisa: <strong style="color:var(--danger-color);"><?= $grand_belum ?></strong></small>
            </div>
        </div>
    </div>

    <!-- Per PT/SITE Sections -->
    <?php foreach($rekap as $pt): ?>
        <?php 
            $ptName = $pt_map[strtoupper($pt['pt_site'])] ?? $pt['pt_site'];
            $ptPct = $pt['total'] > 0 ? round(($pt['sudah'] / $pt['total']) * 100, 1) : 0;
        ?>
        <div class="data-card mb-4">
            <div class="data-card-header">
                <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap:8px;">
                    <h5 class="mb-0">
                        <i class="bi bi-building"></i>
                        <span class="badge" style="background:var(--accent-color);font-size:0.7rem;vertical-align:middle;border-radius:5px;"><?= esc($pt['pt_site']) ?></span>
                        <?= esc($ptName) ?>
                    </h5>
                    <div class="d-flex align-items-center" style="gap:10px;">
                        <span class="badge badge-soft-success" style="padding:5px 10px;"><?= $pt['sudah'] ?> sudah</span>
                        <span class="badge badge-soft-danger" style="padding:5px 10px;"><?= $pt['belum'] ?> belum</span>
                        <strong style="color:var(--accent-color);font-size:0.9rem;"><?= $ptPct ?>%</strong>
                    </div>
                </div>
                <div class="progress mt-3" style="height: 5px; border-radius: 3px; background: var(--border-color);">
                    <div class="progress-bar" role="progressbar" 
                         style="width: <?= $ptPct ?>%; background: linear-gradient(90deg, #10b981, #34d399); border-radius: 3px;"
                         aria-valuenow="<?= $ptPct ?>" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4" style="width:45px;">No</th>
                            <th>Afdeling</th>
                            <th style="text-align:center;">Total</th>
                            <th style="text-align:center;">Sudah Input</th>
                            <th style="text-align:center;">Belum Input</th>
                            <th>Progress</th>
                            <th style="text-align:center;width:55px;">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach($pt['afdelings'] as $afd): ?>
                            <?php $afdPct = $afd['total'] > 0 ? round(($afd['sudah'] / $afd['total']) * 100, 1) : 0; ?>
                            <tr>
                                <td class="ps-4 text-muted"><?= $no++ ?></td>
                                <td><strong><?= esc($afd['afdeling']) ?></strong></td>
                                <td style="text-align:center;"><span class="fw-bold"><?= $afd['total'] ?></span></td>
                                <td style="text-align:center;">
                                    <?php if($afd['sudah'] > 0): ?>
                                        <span class="badge badge-soft-success" style="padding:4px 10px;font-weight:700;"><?= $afd['sudah'] ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">0</span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align:center;">
                                    <?php if($afd['belum'] > 0): ?>
                                        <button type="button" class="btn btn-sm btn-outline-danger" style="padding:2px 8px; font-weight:700; border-radius:4px;" data-bs-toggle="modal" data-bs-target="#modalBelum<?= md5($pt['pt_site'] . '_' . $afd['afdeling']) ?>">
                                            Lihat (<span style="font-weight:700;"><?= $afd['belum'] ?></span>)
                                        </button>
                                    <?php else: ?>
                                        <span class="badge badge-soft-success" style="padding:4px 10px;"><i class="bi bi-check-lg"></i> Selesai</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="progress" style="height: 7px; border-radius: 4px; background: var(--border-color);">
                                        <div class="progress-bar" role="progressbar" 
                                             style="width: <?= $afdPct ?>%; border-radius: 4px;
                                                    background: <?= $afdPct >= 100 ? '#10b981' : ($afdPct >= 50 ? '#f59e0b' : '#ef4444') ?>;"
                                             aria-valuenow="<?= $afdPct ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </td>
                                <td style="text-align:center;">
                                    <strong style="font-size:0.82rem; color: <?= $afdPct >= 100 ? '#059669' : ($afdPct >= 50 ? '#d97706' : '#dc2626') ?>;"><?= $afdPct ?>%</strong>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr style="background:var(--card-bg);border-top:2px solid var(--border-color);">
                            <td class="ps-4 fw-bold" colspan="2">Total <?= esc($pt['pt_site']) ?></td>
                            <td style="text-align:center;" class="fw-bold"><?= $pt['total'] ?></td>
                            <td style="text-align:center;color:#059669;" class="fw-bold"><?= $pt['sudah'] ?></td>
                            <td style="text-align:center;color:#dc2626;" class="fw-bold"><?= $pt['belum'] ?></td>
                            <td colspan="2" style="text-align:center;" class="fw-bold" ><?= $ptPct ?>%</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    <?php endforeach; ?>

    <!-- Modals for Belum Input List -->
    <?php foreach($rekap as $pt): ?>
        <?php foreach($pt['afdelings'] as $afd): ?>
            <?php if($afd['belum'] > 0): ?>
                <div class="modal fade" id="modalBelum<?= md5($pt['pt_site'] . '_' . $afd['afdeling']) ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-scrollable">
                        <div class="modal-content">
                            <div class="modal-header" style="background:var(--card-bg); border-bottom:1px solid var(--border-color);">
                                <h5 class="modal-title"><i class="bi bi-people"></i> Daftar Belum Input - <?= esc($pt['pt_site']) ?> <?= esc($afd['afdeling']) ?></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead style="background:var(--body-bg);">
                                            <tr>
                                                <th class="ps-4">No</th>
                                                <th>NIK</th>
                                                <th>Nama Karyawan</th>
                                                <th>Jabatan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $mno = 1; foreach($afd['belum_list'] as $bk): ?>
                                            <tr>
                                                <td class="ps-4 text-muted"><?= $mno++ ?></td>
                                                <td><code><?= esc($bk['nik_karyawan']) ?></code></td>
                                                <td><span class="fw-bold"><?= esc($bk['nama']) ?></span></td>
                                                <td><?= esc($bk['jabatan']) ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="modal-footer" style="border-top:1px solid var(--border-color);">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endforeach; ?>

</div>

<?= view('partials/admin_footer') ?>
