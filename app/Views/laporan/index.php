<?php $page_title = 'Rekap Input'; $active_menu = 'laporan'; ?>
<?= view('partials/admin_head', ['page_title' => $page_title]) ?>
<?= view('partials/sidebar_admin', ['active_menu' => $active_menu]) ?>

<div class="content">
    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h4>Rekapitulasi Input Harian</h4>
            <div class="header-sub">Data hasil input mandor di lapangan</div>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url('dashboard/export') ?>" class="btn btn-success btn-sm px-3">
                <i class="bi bi-file-earmark-excel me-1"></i> Download Excel
            </a>
        </div>
    </div>

    <?= view('partials/alerts') ?>

    <!-- Filters -->
    <div class="data-card mb-4">
        <div class="data-card-body py-3">
            <form action="" method="get" class="row gx-3 gy-2 align-items-center">
                <div class="col-auto">
                    <span class="fw-bold text-muted small d-flex align-items-center gap-1">
                        <i class="bi bi-funnel"></i> FILTER:
                    </span>
                </div>
                <!-- Search Box -->
                <div class="col-auto">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white text-muted border-end-0"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control border-start-0 ps-0" name="search" placeholder="Cari Nama/NIK/IMEI..." value="<?= esc($search ?? '') ?>">
                    </div>
                </div>
                <div class="col-auto">
                    <select class="form-select form-select-sm" name="afdeling">
                        <option value="">Semua Afdeling</option>
                        <?php foreach($afdeling_list as $afd): ?>
                            <option value="<?= esc($afd['afdeling']) ?>" <?= ($filter_afdeling == $afd['afdeling']) ? 'selected' : '' ?>>
                                <?= esc($afd['afdeling']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-auto">
                    <select class="form-select form-select-sm" name="status">
                        <option value="">Semua Status</option>
                        <option value="Ada" <?= ($filter_status == 'Ada') ? 'selected' : '' ?>>Ada Gadget</option>
                        <option value="Tidak Ada" <?= ($filter_status == 'Tidak Ada') ? 'selected' : '' ?>>Tidak Ada</option>
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-check2 me-1"></i> Terapkan</button>
                </div>
                <?php if(!empty($filter_afdeling) || !empty($filter_status) || !empty($search)): ?>
                <div class="col-auto">
                    <a href="<?= base_url('laporan') ?>" class="btn btn-sm btn-outline-danger"><i class="bi bi-x-lg me-1"></i> Reset</a>
                </div>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <div class="data-card">
        <?php if(isset($total_laporan) && $total_laporan > 0): ?>
        <div class="data-card-header">
            <h5><i class="bi bi-file-earmark-text"></i> <?= $total_laporan ?> data ditemukan</h5>
            <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteAllModal">
                <i class="bi bi-trash3 me-1"></i> Hapus Semua
            </button>
        </div>
        <?php endif; ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th class="ps-4">Timestamp</th>
                        <th>Mandor (Penginput)</th>
                        <th>Karyawan</th>
                        <th>Jabatan</th>
                        <th>Site PT</th>
                        <th>Afdeling</th>
                        <th>Status Gadget</th>
                        <th>Detail (IMEI / Keterangan)</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($laporan)): ?>
                        <tr>
                            <td colspan="9">
                                <div class="empty-state">
                                    <div class="empty-state-icon"><i class="bi bi-file-earmark-text"></i></div>
                                    <p>Belum ada data input sesuai filter.</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($laporan as $row): ?>
                        <tr>
                            <td class="ps-4"><span class="text-muted" style="font-size:0.82rem"><?= date('d/m/Y H:i', strtotime($row['input_at'])) ?></span></td>
                            <td>
                                <div class="fw-bold"><?= esc($row['nama_mandor']) ?></div>
                                <small class="text-muted"><?= esc($row['mandor_afdeling']) ?></small>
                            </td>
                            <td>
                                <div><?= esc($row['nama_karyawan']) ?></div>
                                <small class="text-muted"><?= esc($row['nik_karyawan']) ?></small>
                            </td>
                            <td><span class="badge badge-soft-warning"><?= esc($row['jabatan'] ?? '-') ?></span></td>
                            <td><span class="badge badge-soft-primary"><?= esc($row['pt_site'] ?? '-') ?></span></td>
                            <td><span class="badge badge-soft-info"><?= esc($row['afdeling']) ?></span></td>
                            <td>
                                <?php if($row['status_gadget'] == 'Ada'): ?>
                                    <span class="badge badge-soft-success">Ada</span>
                                <?php else: ?>
                                    <span class="badge badge-soft-danger">Tidak Ada</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($row['status_gadget'] == 'Ada'): ?>
                                    <span class="font-monospace" style="color:var(--primary); font-size:0.82rem"><?= esc($row['imei']) ?></span>
                                <?php else: ?>
                                    <span class="text-muted fst-italic" style="font-size:0.85rem"><?= esc($row['keterangan']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-outline-danger border-0" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#deleteModal"
                                        data-id="<?= $row['id'] ?>"
                                        data-nama="<?= esc($row['nama_karyawan']) ?>"
                                        title="Hapus">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination Links -->
        <?php if(isset($pager) && $total_laporan > 0): ?>
        <div class="data-card-footer d-flex justify-content-end">
            <?= $pager->links('laporan', 'default_full') ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Hapus Satu -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-bold"><i class="bi bi-trash3 text-danger me-2"></i>Hapus Data</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="deleteForm" method="post" action="">
                <?= csrf_field() ?>
                <div class="modal-body px-4">
                    <p class="mb-0">Yakin hapus inputan untuk <strong id="deleteNama"></strong>?</p>
                    <small class="text-muted">Data yang dihapus tidak bisa dikembalikan.</small>
                </div>
                <div class="modal-footer border-0 pt-0 px-4 pb-4">
                    <button type="button" class="btn btn-light flex-fill" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger flex-fill fw-bold">
                        <i class="bi bi-trash3 me-1"></i> Hapus
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Hapus Semua -->
<div class="modal fade" id="deleteAllModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-bold"><i class="bi bi-exclamation-triangle text-danger me-2"></i>Hapus Semua</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="post" action="<?= base_url('laporan/delete-all') ?>">
                <?= csrf_field() ?>
                <div class="modal-body px-4">
                    <div class="text-center mb-3">
                        <div style="width:56px;height:56px;border-radius:14px;background:var(--danger-soft);display:inline-flex;align-items:center;justify-content:center;">
                            <i class="bi bi-exclamation-triangle" style="font-size:1.5rem;color:var(--danger)"></i>
                        </div>
                    </div>
                    <p class="text-center fw-bold mb-1">Hapus SEMUA data inputan?</p>
                    <p class="text-center text-muted small mb-0">Seluruh <?= $total_laporan ?? count($laporan ?? []) ?> data akan dihapus permanen.</p>
                </div>
                <div class="modal-footer border-0 pt-0 px-4 pb-4">
                    <button type="button" class="btn btn-light flex-fill" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger flex-fill fw-bold">
                        <i class="bi bi-trash3 me-1"></i> Ya, Hapus Semua
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= view('partials/admin_footer') ?>

<script>
    // Pass data to delete modal
    const deleteModal = document.getElementById('deleteModal');
    if(deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function(event) {
            const btn = event.relatedTarget;
            document.getElementById('deleteNama').textContent = btn.getAttribute('data-nama');
            document.getElementById('deleteForm').action = '<?= base_url("laporan/delete/") ?>' + btn.getAttribute('data-id');
        });
    }
</script>
