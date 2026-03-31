<?php $page_title = 'History Pengiriman Gadget'; ?>
<?= view('partials/admin_head', ['page_title' => $page_title]) ?>
<?= view('partials/sidebar_admin', ['active_menu' => $active_menu ?? 'pengiriman_gadget']) ?>

<div class="content">
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h4><i class="bi bi-truck me-2"></i>History Pengiriman / BASTE</h4>
            <div class="header-sub">Daftar BASTE yang telah dibuat pengirimannya.</div>
        </div>
        <div>
            <a href="<?= base_url('pengiriman-gadget/draft') ?>" class="btn btn-primary btn-action">
                <i class="bi bi-plus-circle me-1"></i> Buat BASTE Baru
            </a>
        </div>
    </div>

    <?= view('partials/alerts') ?>

    <div class="data-card mb-4">
        <div class="data-card-header">
            <h5 class="mb-0"><i class="bi bi-list-ul me-2"></i>Data History BASTE</h5>
        </div>
        <div class="table-responsive">
            <?php if(empty($bastes)): ?>
                <div class="text-center py-5">
                    <i class="bi bi-folder-x fs-1 text-muted mb-3 d-block"></i>
                    <h5 class="text-secondary">Belum ada history BASTE.</h5>
                    <a href="<?= base_url('pengiriman-gadget/draft') ?>" class="btn btn-outline-primary mt-2">Buat Pengiriman Pertama</a>
                </div>
            <?php else: ?>
                <table class="table table-hover align-middle mb-0" id="dataTable">
                    <thead class="table-light">
                        <tr>
                            <th width="5%" class="text-center">No</th>
                            <th width="30%">No BASTE</th>
                            <th width="20%">Tanggal</th>
                            <th width="25%">Waktu Submit</th>
                            <th width="20%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($bastes as $index => $row): ?>
                            <tr>
                                <td class="text-center"><?= $index + 1 ?></td>
                                <td><span class="fw-bold text-primary"><?= esc($row['no_baste']) ?></span></td>
                                <td><i class="bi bi-calendar-event opacity-50 me-1"></i> <?= date('d M Y', strtotime($row['tanggal'])) ?></td>
                                <td><?= date('d M Y, H:i', strtotime($row['created_at'])) ?></td>
                                <td class="text-center">
                                    <a href="<?= base_url('pengiriman-gadget/detail/'.$row['id']) ?>" class="btn btn-info btn-sm text-white" title="Lihat Detail">
                                        <i class="bi bi-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if(typeof $ !== 'undefined' && $.fn.DataTable && document.getElementById('dataTable')) {
            $('#dataTable').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json"
                }
            });
        }
    });
</script>

</div>
<?= view('partials/admin_footer') ?>
