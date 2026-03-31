<?php $page_title = 'Dashboard Mandor'; $active_menu = 'dashboard'; ?>
<?= view('partials/mandor_head', ['page_title' => $page_title]) ?>
<?= view('partials/sidebar_mandor', ['active_menu' => $active_menu]) ?>

<div class="content">
    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h4>Dashboard Mandor</h4>
            <div class="header-sub">Ringkasan aktivitas input data gadget</div>
        </div>
        <a href="<?= base_url('input/create') ?>" class="btn btn-primary btn-sm px-4 d-none d-md-inline-flex align-items-center gap-1">
            <i class="bi bi-plus-lg"></i> Input Baru
        </a>
    </div>

    <!-- Stats -->
    <div class="row g-3 mb-4">
        <div class="col-6">
            <div class="stat-card primary">
                <div class="stat-icon"><i class="bi bi-send-fill"></i></div>
                <div class="stat-label">Terkirim</div>
                <div class="stat-value">
                    <?= array_reduce($submissions, function($c, $i){ return $c + ($i['status_pengajuan'] == 'Submitted' ? 1 : 0); }, 0) ?>
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="stat-card warning">
                <div class="stat-icon"><i class="bi bi-file-earmark-fill"></i></div>
                <div class="stat-label">Draft</div>
                <div class="stat-value">
                    <?= array_reduce($submissions, function($c, $i){ return $c + ($i['status_pengajuan'] == 'Draft' ? 1 : 0); }, 0) ?>
                </div>
            </div>
        </div>
    </div>

    <?= view('partials/alerts') ?>

    <!-- Mobile FAB -->
    <div class="d-md-none position-fixed bottom-0 end-0 p-4" style="z-index: 1050;">
        <a href="<?= base_url('input/create') ?>" class="btn btn-primary rounded-circle shadow-lg d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
            <i class="bi bi-plus-lg h4 mb-0 text-white"></i>
        </a>
    </div>

    <!-- Submission History -->
    <div class="data-card">
        <div class="data-card-header">
            <h6><i class="bi bi-clock-history" style="color:var(--primary)"></i> Riwayat Input Terbaru</h6>
        </div>
        <?php if(empty($submissions)): ?>
            <div class="empty-state">
                <div class="empty-state-icon"><i class="bi bi-people"></i></div>
                <p class="mb-3">Belum ada data input. Mulai checklist gadget karyawan Anda hari ini.</p>
                <a href="<?= base_url('input/create') ?>" class="btn btn-primary btn-sm px-4">
                    <i class="bi bi-plus-lg me-1"></i> Mulai Sekarang
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle" style="min-width: 650px;">
                    <thead>
                        <tr>
                            <th class="ps-4" style="width: 5%;">No</th>
                            <th style="width: 25%;">Karyawan</th>
                            <th style="width: 18%;">Status</th>
                            <th style="width: 25%;">Keterangan / IMEI</th>
                            <th style="width: 15%;">Waktu</th>
                            <th class="text-end pe-4" style="width: 12%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no=1; foreach($submissions as $row): ?>
                        <tr>
                            <td class="ps-4 text-muted"><?= $no++ ?></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div style="width:36px;height:36px;border-radius:10px;background:var(--primary-soft);color:var(--primary);display:flex;align-items:center;justify-content:center;flex-shrink:0;font-weight:700;font-size:0.8rem;">
                                        <?= strtoupper(substr($row['nama_karyawan'], 0, 1)) ?>
                                    </div>
                                    <div>
                                        <div class="fw-bold" style="max-width:160px;"><?= esc($row['nama_karyawan']) ?></div>
                                        <small class="text-muted"><?= esc($row['nik_karyawan']) ?></small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <?php if($row['status_gadget'] == 'Ada'): ?>
                                    <span class="badge badge-soft-success">Ada Gadget</span>
                                <?php else: ?>
                                    <span class="badge badge-soft-danger">Tidak Ada</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($row['status_gadget'] == 'Ada'): ?>
                                    <div class="d-flex align-items-center gap-1">
                                        <i class="bi bi-upc-scan text-muted"></i>
                                        <span class="font-monospace" style="color:var(--primary);font-size:0.82rem;background:var(--primary-soft);padding:3px 8px;border-radius:6px;"><?= esc($row['imei']) ?></span>
                                    </div>
                                <?php else: ?>
                                    <span class="text-muted fst-italic" style="font-size:0.85rem; max-width:150px; display:inline-block;"><?= esc($row['keterangan']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="fw-bold text-muted" style="font-size:0.85rem"><?= date('H:i', strtotime($row['input_at'])) ?></div>
                                <small class="text-muted" style="font-size:0.7rem"><?= date('d M Y', strtotime($row['input_at'])) ?></small>
                            </td>
                            <td class="text-end pe-4">
                                <?php if($row['status_pengajuan'] == 'Draft'): ?>
                                    <a href="<?= base_url('input/edit/'.$row['id']) ?>" class="btn btn-sm btn-outline-primary px-3">
                                        <i class="bi bi-pencil me-1"></i> Edit
                                    </a>
                                <?php else: ?>
                                    <span class="badge badge-soft-success"><i class="bi bi-check-circle-fill me-1"></i>Terkirim</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="text-center mt-4 mb-5 pb-5">
        <small class="text-muted fst-italic">Data yang sudah 'Terkirim' tidak dapat diedit lagi.</small>
    </div>
</div>

<?= view('partials/mandor_footer') ?>
