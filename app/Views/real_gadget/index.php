<?php $page_title = 'Real Gadget'; ?>
<?= view('partials/admin_head', ['page_title' => $page_title]) ?>
<?= view('partials/sidebar_admin', ['active_menu' => $active_menu ?? 'real_gadget']) ?>

<style>
/* Responsive tweaks specifically for wide datatables */
.table-data-wrapper {
    overflow-x: auto;
    width: 100%;
}
.table-data-wrapper table {
    min-width: 1500px; /* Force scroll for so many columns */
    font-size: 0.8rem;
}
.table-data-wrapper th, .table-data-wrapper td {
    white-space: nowrap;
}
</style>

<div class="content">
    <div class="page-header d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4><i class="bi bi-router-fill me-2"></i>Real Gadget API Data</h4>
            <div class="header-sub">Data Master Gadget dimuat secara real-time melalui API Eksternal (SimaD2 - BIM).</div>
        </div>
        <div>
            <!-- Button API Manual Refresh -->
            <button class="btn btn-primary btn-action" id="btn-sync-api">
                <i class="bi bi-cloud-arrow-down me-1"></i> Muat Data Gadget
            </button>
        </div>
    </div>

    <?= view('partials/alerts') ?>

    <div class="data-card p-4">
        <div class="d-flex justify-content-between align-items-center mb-3 text-secondary border-bottom pb-2">
            <h5 class="mb-0"><i class="bi bi-database-check me-2"></i>Master Gadget Assignment</h5>
            <span class="badge bg-info text-dark px-3 py-2 fs-6 d-none" id="total-badge">
                Total: <span id="total-gadget">0</span> Gadget
            </span>
        </div>
        
        <div id="loading-indicator" class="text-center py-5 d-none">
            <div class="spinner-border text-primary fs-3 mb-3" role="status"></div>
            <h6 class="text-muted">Mengambil rentetan data gadget melalui API...</h6>
        </div>
        
        <div id="error-indicator" class="alert alert-danger d-none">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> <strong>Ups!</strong> <span id="error-message">Terjadi kesalahan.</span>
        </div>

        <div class="table-data-wrapper rounded-3 shadow-sm d-none" id="api-table-container">
            <table class="table table-hover table-bordered table-striped align-middle mb-0" id="table-gadget">
                <thead class="table-dark text-center">
                    <tr>
                        <th width="3%">NO</th>
                        <th>IMEI</th>
                        <th>APLIKASI</th>
                        <th>PT</th>
                        <th>AFD</th>
                        <th>NPK PENGGUNA</th>
                        <th>NAMA</th>
                        <th>POS TITLE</th>
                        <th>GROUP ASSET</th>
                        <th>TIPE ASSET</th>
                        <th>PART ASSET</th>
                        <th>JUMLAH</th>
                        <th>ASAL</th>
                        <th>STATUS</th>
                        <th>NOTE</th>
                        <th>ACTION</th>
                    </tr>
                </thead>
                <tbody id="api-data-body">
                    <!-- Data will be loaded using JS / AJAX here -->
                </tbody>
            </table>
        </div>
        
        <div id="empty-state" class="text-center py-5 text-muted">
             <i class="bi bi-router fs-1 text-secondary d-block mb-3 opacity-50"></i>
             <p>Data gadget belum dimuat, silakan klik tombol "Muat Data Gadget".</p>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const btnSync = document.getElementById('btn-sync-api');
    const tableContainer = document.getElementById('api-table-container');
    const dataBody = document.getElementById('api-data-body');
    const loading = document.getElementById('loading-indicator');
    const emptyState = document.getElementById('empty-state');
    const errorState = document.getElementById('error-indicator');
    const errorMsg = document.getElementById('error-message');
    
    // Store datatable instance
    let dataTable = null;

    btnSync.addEventListener('click', function() {
        // Reset state
        emptyState.classList.add('d-none');
        tableContainer.classList.add('d-none');
        errorState.classList.add('d-none');
        loading.classList.remove('d-none');
        
        btnSync.disabled = true;
        
        // Destroy existing datatable if exists
        if(dataTable) {
            $('#table-gadget').DataTable().destroy();
            dataTable = null;
        }

        // Call our CI Controller Proxy API
        fetch('<?= base_url("api_npk/get_gadget") ?>')
        .then(response => {
            if(!response.ok) {
                return response.json().then(err => { throw err; });
            }
            return response.json();
        })
        .then(res => {
            loading.classList.add('d-none');
            
            if(res.status === 'success' && res.data && res.data.data && res.data.data.length > 0) {
                let html = '';
                const gadgets = res.data.data;
                
                gadgets.forEach((item, index) => {
                    let badgeStatus = 'bg-secondary';
                    if(item.STATUS_DESC === 'TERPAKAI' || item.STATUS_DESC === 'NORMAL') badgeStatus = 'bg-success';
                    if(item.STATUS_DESC === 'RUSAK' || item.STATUS_DESC === 'HILANG') badgeStatus = 'bg-danger';
                    if(item.STATUS_DESC === 'CADANGAN' || item.STATUS_DESC === 'DRAFT') badgeStatus = 'bg-warning text-dark';
                    
                    html += `
                        <tr>
                            <td class="text-center text-muted fw-bold">${index + 1}</td>
                            <td class="fw-bold text-primary font-monospace">${item.IMEI || '-'}</td>
                            <td class="fw-medium">${item.APLIKASI || '-'}</td>
                            <td class="text-center"><span class="badge border border-secondary text-secondary">${item.PT || '-'}</span></td>
                            <td class="text-center"><span class="badge border border-secondary text-secondary">${item.AFD || '-'}</span></td>
                            <td class="font-monospace text-center text-muted">${item.NPK_PENGGUNA || '-'}</td>
                            <td class="fw-bold">${item.NAMA || '-'}</td>
                            <td>${item.POS_TITLE || '-'}</td>
                            <td class="text-center">${item.GROUP_ASSET || '-'}</td>
                            <td class="text-center">${item.TIPE_ASSET || '-'}</td>
                            <td class="text-center">${item.PART_ASSET || '-'}</td>
                            <td class="text-center fw-bold">${item.JUMLAH || '-'}</td>
                            <td class="text-center"><span class="badge bg-light text-dark border">${item.ASAL_DESC || '-'}</span></td>
                            <td class="text-center"><span class="badge ${badgeStatus}">${item.STATUS_DESC || '-'}</span></td>
                            <td><span class="text-muted fst-italic">${item.NOTE || '-'}</span></td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="alert('Action Placeholder for IMEI: ${item.IMEI || '-'}')">
                                    <i class="bi bi-three-dots"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                });
                
                dataBody.innerHTML = html;
                tableContainer.classList.remove('d-none');
                
                // Show Total
                document.getElementById('total-gadget').textContent = gadgets.length;
                document.getElementById('total-badge').classList.remove('d-none');
                
                // Init Datatable
                if(typeof $ !== 'undefined' && $.fn.DataTable) {
                    dataTable = $('#table-gadget').DataTable({
                        "language": {
                            "url": "//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json"
                        },
                        "scrollX": true
                    });
                }
            } else {
                emptyState.querySelector('p').textContent = 'API tidak mengembalikan data gadget (kosong)';
                emptyState.classList.remove('d-none');
            }
        })
        .catch(err => {
            loading.classList.add('d-none');
            errorMsg.textContent = err.message || 'Gagal tersambung ke API. Pastikan internet dan endpoint dapat diakses.';
            errorState.classList.remove('d-none');
            console.error(err);
        })
        .finally(() => {
             btnSync.disabled = false;
        });
    });
});
</script>

<?= view('partials/admin_footer') ?>

<!-- Add DataTables scripts and style -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
