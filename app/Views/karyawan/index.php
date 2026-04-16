<?php $page_title = 'Data Karyawan'; $active_menu = 'karyawan'; ?>
<?= view('partials/admin_head', ['page_title' => $page_title]) ?>
<?= view('partials/sidebar_admin', ['active_menu' => $active_menu]) ?>

<div class="content">
    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h4>Data Karyawan</h4>
            <div class="header-sub">Kelola data karyawan kebun</div>
        </div>
    </div>

    <?= view('partials/alerts') ?>

    <!-- Import CSV Collapse -->
    <div class="data-card mb-4 collapse" id="importCard">
        <div class="data-card-body" style="background:#f8fafc;">
            <h6 class="mb-3 fw-bold"><i class="bi bi-upload me-1 text-primary"></i> Import Karyawan via CSV</h6>
            <form action="<?= base_url('karyawan/import') ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="row align-items-end g-3">
                    <div class="col-md-5">
                        <label class="form-label small fw-bold text-muted">Upload CSV (NIK, Nama, Jabatan, Afdeling, Status, PT_SITE)</label>
                        <input type="file" name="csv_file" class="form-control form-control-sm" required accept=".csv">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-sm btn-primary w-100">
                            <i class="bi bi-cloud-upload me-1"></i> Upload
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="data-card">
        <div class="data-card-header">
            <h5><i class="bi bi-people"></i> Daftar Karyawan</h5>
            <div class="d-flex gap-2 flex-wrap align-items-center">
                <a href="<?= base_url('karyawan/create') ?>" class="btn btn-success btn-sm">
                    <i class="bi bi-plus-lg me-1"></i> Tambah Karyawan
                </a>
                <a href="<?= base_url('karyawan/export') ?><?= !empty($search) ? '?search='.urlencode($search) : '' ?>" class="btn btn-outline-success btn-sm">
                    <i class="bi bi-file-earmark-excel me-1"></i> Export 
                </a>
                <a href="<?= base_url('download/template?type=karyawan') ?>" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-download me-1"></i> Template
                </a>
                <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#importCard">
                    <i class="bi bi-upload me-1"></i> Import CSV
                </button>
                <form action="" method="get" class="d-flex gap-1">
                    <div class="input-group input-group-sm" style="max-width: 240px;">
                        <input type="text" name="search" class="form-control" placeholder="Cari NIK / Nama..." value="<?= esc($search ?? '') ?>">
                        <button class="btn btn-outline-secondary" type="submit" style="border-radius: 0 10px 10px 0;"><i class="bi bi-search"></i></button>
                    </div>
                    <?php if(!empty($search)): ?>
                        <a href="<?= base_url('karyawan') ?>" class="btn btn-sm btn-outline-danger" title="Reset"><i class="bi bi-x-lg"></i></a>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <?php if(empty($karyawan)): ?>
            <div class="empty-state">
                <div class="empty-state-icon"><i class="bi bi-people"></i></div>
                <p>
                    <?= !empty($search) ? 'Tidak ditemukan karyawan dengan pencarian "' . esc($search) . '".' : 'Belum ada data karyawan. Import CSV untuk menambahkan.' ?>
                </p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th class="ps-4">NIK</th>
                            <th>Nama</th>
                            <th>Jabatan</th>
                            <th>PT / SITE</th>
                            <th>Afdeling</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($karyawan as $row): ?>
                        <tr>
                            <td class="ps-4"><span class="font-monospace text-muted fw-bold"><?= esc($row['nik_karyawan']) ?></span></td>
                            <td class="fw-bold"><?= esc($row['nama']) ?></td>
                            <td><span class="badge" style="background:#f1f5f9; color:var(--text-secondary);"><?= esc($row['jabatan']) ?></span></td>
                            <td><span class="badge badge-soft-primary"><?= esc($row['pt_site'] ?? '-') ?></span></td>
                            <td><span class="badge badge-soft-info"><?= esc($row['afdeling']) ?></span></td>
                            <td>
                                <?php if($row['status_aktif'] == 'Aktif'): ?>
                                    <span class="badge badge-soft-success">Aktif</span>
                                <?php else: ?>
                                    <span class="badge badge-soft-danger">Tidak Aktif</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-primary mutasi-btn" 
                                            data-id="<?= $row['id'] ?>" 
                                            title="Edit & Mutasi">
                                        <i class="bi bi-pencil-square"></i> Mutasi
                                    </button>
                                    <a href="<?= base_url('karyawan/riwayat/'.$row['id']) ?>" class="btn btn-outline-info" title="Riwayat">
                                        <i class="bi bi-clock-history"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="data-card-footer">
                <?= $pager->links('karyawan', 'default_full') ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Edit & Mutasi Karyawan -->
<div class="modal fade" id="mutasiModal" tabindex="-1" aria-labelledby="mutasiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="mutasiModalLabel"><i class="bi bi-pencil-square me-2"></i>Edit & Mutasi Karyawan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="mutasiForm" action="" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">NIK Karyawan</label>
                            <input type="text" name="nik_karyawan" id="m_nik" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Nama Lengkap</label>
                            <input type="text" name="nama" id="m_nama" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Jabatan</label>
                            <input type="text" name="jabatan" id="m_jabatan" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Status Aktif</label>
                            <select name="status_aktif" id="m_status" class="form-select">
                                <option value="Aktif">Aktif</option>
                                <option value="Tidak Aktif">Tidak Aktif</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">PT / SITE</label>
                            <input type="text" name="pt_site" id="m_pt" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Afdeling</label>
                            <input type="text" name="afdeling" id="m_afd" class="form-control">
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="bg-light p-3 rounded border">
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="is_mutation" id="isMutation" value="1">
                            <label class="form-check-label fw-bold text-primary" for="isMutation">Aktifkan Mode Mutasi</label>
                        </div>
                        <div id="mutationNoteArea" style="display:none;">
                            <label class="form-label small fw-bold">Keterangan Mutasi / Perubahan</label>
                            <textarea name="keterangan_mutasi" class="form-control" rows="2" placeholder="Contoh: Mutasi dari AFD A ke AFD B atau promosi jabatan..."></textarea>
                            <div class="form-text text-danger small"><i class="bi bi-info-circle me-1"></i> Mengaktifkan mode mutasi akan mencatat keterangan ini secara khusus ke riwayat karyawan.</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4"><i class="bi bi-floppy me-1"></i> Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const mutasiModal = new bootstrap.Modal(document.getElementById('mutasiModal'));
    const mutasiForm = document.getElementById('mutasiForm');
    const isMutationCheck = document.getElementById('isMutation');
    const mutationNoteArea = document.getElementById('mutationNoteArea');

    // Toggle Mutation Note
    isMutationCheck.addEventListener('change', function() {
        mutationNoteArea.style.display = this.checked ? 'block' : 'none';
    });

    // Click Mutasi Button
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('mutasi-btn') || e.target.closest('.mutasi-btn')) {
            const btn = e.target.classList.contains('mutasi-btn') ? e.target : e.target.closest('.mutasi-btn');
            const id = btn.getAttribute('data-id');
            
            // Show loading state or something if needed
            btn.disabled = true;
            const originalContent = btn.innerHTML;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

            fetch('<?= base_url('karyawan/ajax-detail') ?>/' + id)
                .then(r => r.json())
                .then(res => {
                    btn.disabled = false;
                    btn.innerHTML = originalContent;

                    if (res.status === 'success') {
                        const d = res.data;
                        document.getElementById('m_nik').value = d.nik_karyawan;
                        document.getElementById('m_nama').value = d.nama;
                        document.getElementById('m_jabatan').value = d.jabatan;
                        document.getElementById('m_status').value = d.status_aktif;
                        document.getElementById('m_pt').value = d.pt_site || '';
                        document.getElementById('m_afd').value = d.afdeling || '';
                        
                        // Reset mutation mode
                        isMutationCheck.checked = false;
                        mutationNoteArea.style.display = 'none';
                        mutasiForm.querySelector('textarea').value = '';

                        // Set Action URL
                        mutasiForm.action = '<?= base_url('karyawan/update-mutasi') ?>/' + id;
                        
                        mutasiModal.show();
                    } else {
                        alert('Gagal mengambil data: ' + res.message);
                    }
                })
                .catch(err => {
                    btn.disabled = false;
                    btn.innerHTML = originalContent;
                    alert('Terjadi kesalahan jaringan.');
                });
        }
    });
});
</script>

<?= view('partials/admin_footer') ?>
