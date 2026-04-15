<?php $page_title = 'Rekap MPP'; $active_menu = 'rekap_mpp'; ?>
<?= view('partials/admin_head', ['page_title' => $page_title]) ?>
<?= view('partials/sidebar_admin', ['active_menu' => $active_menu]) ?>

<!-- Berikan sedikit custom style agar tampil lebih manis -->
<style>
    .table-mpp th, .table-mpp td {
        vertical-align: middle !important;
    }
    .table-mpp thead th {
        font-weight: 600;
        letter-spacing: 0.3px;
        border-bottom-width: 2px;
    }
    .badge-count {
        display: inline-block;
        min-width: 32px;
        padding: 4px 8px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.85rem;
    }
    .bg-light-primary { background-color: rgba(13, 110, 253, 0.08); }
    .bg-light-success { background-color: rgba(25, 135, 84, 0.08); }
    .card-title-icon {
        width: 36px;
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        margin-right: 12px;
    }
    .tfoot-total {
        font-size: 1.05rem;
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }
</style>

<div class="content">
    <div class="page-header d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bolder text-dark mb-1">Rekap MPP <span class="text-muted fw-normal" style="font-size:1rem">(Man Power Planning)</span></h4>
            <div class="text-secondary small">Ringkasan total karyawan aktif BIM1 & PPS1 per Afdeling dan Jabatan</div>
        </div>
        <div>
            <button class="btn btn-sm btn-outline-secondary btn-icon shadow-sm" onclick="window.print()">
                <i class="bi bi-printer me-1"></i> Cetak / PDF
            </button>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- ======================= PT BIM1 ======================= -->
        <div class="col-xl-6">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; overflow: hidden;">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-3 px-4 d-flex align-items-center">
                    <div class="card-title-icon bg-primary text-white shadow-sm">
                        <i class="bi bi-building"></i>
                    </div>
                    <h5 class="mb-0 text-primary fw-bolder">Data Karyawan BIM1</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-mpp table-hover mb-0 text-center border-top">
                            <thead class="bg-light text-secondary">
                                <tr>
                                    <th rowspan="2" class="text-start ps-4 border-end">Afdeling</th>
                                    <th colspan="3" class="border-end">Jabatan Utama</th>
                                    <th rowspan="2" class="bg-primary text-white">Total</th>
                                </tr>
                                <tr>
                                    <th>Pemanen</th>
                                    <th>Perawatan</th>
                                    <th class="border-end">Infild</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $grandTotalBim = 0;
                                if(!empty($rekap['BIM1'])): 
                                    ksort($rekap['BIM1']);
                                    foreach($rekap['BIM1'] as $afd => $counts): 
                                        $rowTotal = $counts['Pemanen'] + $counts['Pekerja Rawat'] + $counts['Infild'];
                                        $grandTotalBim += $rowTotal;
                                ?>
                                    <tr>
                                        <td class="fw-bold text-start ps-4 border-end text-dark"><?= esc($afd) ?></td>
                                        <td>
                                            <?= $counts['Pemanen'] > 0 ? '<span class="badge-count bg-light-primary text-primary">'.$counts['Pemanen'].'</span>' : '<span class="text-muted opacity-50">-</span>' ?>
                                        </td>
                                        <td>
                                            <?= $counts['Pekerja Rawat'] > 0 ? '<span class="badge-count bg-light-primary text-primary">'.$counts['Pekerja Rawat'].'</span>' : '<span class="text-muted opacity-50">-</span>' ?>
                                        </td>
                                        <td class="border-end">
                                            <?= $counts['Infild'] > 0 ? '<span class="badge-count bg-light-primary text-primary">'.$counts['Infild'].'</span>' : '<span class="text-muted opacity-50">-</span>' ?>
                                        </td>
                                        <td class="fw-bold bg-light text-dark fs-6"><?= $rowTotal ?></td>
                                    </tr>
                                <?php endforeach; else: ?>
                                    <tr><td colspan="5" class="text-muted py-4"><i>Belum ada data karyawan aktif BIM1</i></td></tr>
                                <?php endif; ?>
                            </tbody>
                            <tfoot class="bg-primary text-white tfoot-total shadow-sm">
                                <tr>
                                    <td class="text-start ps-4 border-white">GRAND TOTAL</td>
                                    <td class="border-white"><?= $total_bim['Pemanen'] ?></td>
                                    <td class="border-white"><?= $total_bim['Pekerja Rawat'] ?></td>
                                    <td class="border-white"><?= $total_bim['Infild'] ?></td>
                                    <td class="fw-bolder fs-5 text-warning border-white shadow-sm"><?= $grandTotalBim ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- ======================= PT PPS1 ======================= -->
        <div class="col-xl-6">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; overflow: hidden;">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-3 px-4 d-flex align-items-center">
                    <div class="card-title-icon bg-success text-white shadow-sm">
                        <i class="bi bi-building"></i>
                    </div>
                    <h5 class="mb-0 text-success fw-bolder">Data Karyawan PPS1</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-mpp table-hover mb-0 text-center border-top">
                            <thead class="bg-light text-secondary">
                                <tr>
                                    <th rowspan="2" class="text-start ps-4 border-end">Afdeling</th>
                                    <th colspan="3" class="border-end">Jabatan Utama</th>
                                    <th rowspan="2" class="bg-success text-white">Total</th>
                                </tr>
                                <tr>
                                    <th>Pemanen</th>
                                    <th>Perawatan</th>
                                    <th class="border-end">Infild</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $grandTotalPps = 0;
                                if(!empty($rekap['PPS1'])): 
                                    ksort($rekap['PPS1']);
                                    foreach($rekap['PPS1'] as $afd => $counts): 
                                        $rowTotal = $counts['Pemanen'] + $counts['Pekerja Rawat'] + $counts['Infild'];
                                        $grandTotalPps += $rowTotal;
                                ?>
                                    <tr>
                                        <td class="fw-bold text-start ps-4 border-end text-dark"><?= esc($afd) ?></td>
                                        <td>
                                            <?= $counts['Pemanen'] > 0 ? '<span class="badge-count bg-light-success text-success">'.$counts['Pemanen'].'</span>' : '<span class="text-muted opacity-50">-</span>' ?>
                                        </td>
                                        <td>
                                            <?= $counts['Pekerja Rawat'] > 0 ? '<span class="badge-count bg-light-success text-success">'.$counts['Pekerja Rawat'].'</span>' : '<span class="text-muted opacity-50">-</span>' ?>
                                        </td>
                                        <td class="border-end">
                                            <?= $counts['Infild'] > 0 ? '<span class="badge-count bg-light-success text-success">'.$counts['Infild'].'</span>' : '<span class="text-muted opacity-50">-</span>' ?>
                                        </td>
                                        <td class="fw-bold bg-light text-dark fs-6"><?= $rowTotal ?></td>
                                    </tr>
                                <?php endforeach; else: ?>
                                    <tr><td colspan="5" class="text-muted py-4"><i>Belum ada data karyawan aktif PPS1</i></td></tr>
                                <?php endif; ?>
                            </tbody>
                            <tfoot class="bg-success text-white tfoot-total shadow-sm">
                                <tr>
                                    <td class="text-start ps-4 border-white">GRAND TOTAL</td>
                                    <td class="border-white"><?= $total_pps['Pemanen'] ?></td>
                                    <td class="border-white"><?= $total_pps['Pekerja Rawat'] ?></td>
                                    <td class="border-white"><?= $total_pps['Infild'] ?></td>
                                    <td class="fw-bolder fs-5 text-warning border-white shadow-sm"><?= $grandTotalPps ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= view('partials/admin_footer') ?>
