<?php $page_title = 'Real Karyawan'; ?>
<?= view('partials/admin_head', ['page_title' => $page_title]) ?>
<?= view('partials/sidebar_admin', ['active_menu' => $active_menu ?? 'real_karyawan']) ?>

<div class="content">
    <div class="page-header d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4><i class="bi bi-person-vcard-fill me-2"></i>Real Karyawan API Data</h4>
            <div class="header-sub">Data karyawan yang dimuat secara real-time melalui API NPK external.</div>
        </div>
        <div>
            <!-- Button API Manual Refresh -->
            <button class="btn btn-primary btn-action" id="btn-sync-api">
                <i class="bi bi-cloud-arrow-down me-1"></i> Muat Data Karyawan
            </button>
        </div>
    </div>

    <?= view('partials/alerts') ?>

    <div class="data-card p-4">
        <div class="d-flex justify-content-between align-items-center mb-3 text-secondary border-bottom pb-2">
            <h5 class="mb-0"><i class="bi bi-database-check me-2"></i>Daftar Real Karyawan NPK (BIM)</h5>
            <span class="badge bg-info text-dark px-3 py-2 fs-6 d-none" id="total-badge">
                Total: <span id="total-karyawan">0</span> Karyawan
            </span>
        </div>
        
        <div id="loading-indicator" class="text-center py-5 d-none">
            <div class="spinner-border text-primary fs-3 mb-3" role="status"></div>
            <h6 class="text-muted">Mengambil data karyawan melalui API...</h6>
        </div>
        
        <div id="error-indicator" class="alert alert-danger d-none">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> <strong>Ups!</strong> <span id="error-message">Terjadi kesalahan.</span>
        </div>

        <div class="table-responsive border rounded-3 shadow-sm d-none" id="api-table-container">
            <table class="table table-hover table-striped align-middle mb-0" id="table-karyawan">
                <thead class="table-dark text-center">
                    <tr>
                        <th width="5%" class="py-3">No</th>
                        <th width="20%">NPK (CODE)</th>
                        <th width="75%">Nama Karyawan & Jabatan (DESCRIPTION)</th>
                    </tr>
                </thead>
                <tbody id="api-data-body">
                    <!-- Data will be loaded using JS / AJAX here -->
                </tbody>
            </table>
        </div>
        
        <div id="empty-state" class="text-center py-5 text-muted">
             <i class="bi bi-inbox fs-1 text-secondary d-block mb-3 opacity-50"></i>
             <p>Data belum dimuat, silakan klik tombol "Muat Data Karyawan".</p>
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
            $('#table-karyawan').DataTable().destroy();
            dataTable = null;
        }

        // Call our CI Controller Proxy API
        fetch('<?= base_url("api_npk/get_npk") ?>')
        .then(response => {
            if(!response.ok) {
                return response.json().then(err => { throw err; });
            }
            return response.json();
        })
        .then(res => {
            loading.classList.add('d-none');
            
            if(res.status === 'success' && res.data && res.data.length > 0) {
                let html = '';
                res.data.forEach((item, index) => {
                    html += `
                        <tr>
                            <td class="text-center text-muted fw-bold">${index + 1}</td>
                            <td class="fw-bold font-monospace text-primary text-center">${item.CODE}</td>
                            <td class="fw-medium">${item.DESCRIPTION}</td>
                        </tr>
                    `;
                });
                
                dataBody.innerHTML = html;
                tableContainer.classList.remove('d-none');
                
                // Show Total
                document.getElementById('total-karyawan').textContent = res.data.length;
                document.getElementById('total-badge').classList.remove('d-none');
                
                // Init Datatable
                if(typeof $ !== 'undefined' && $.fn.DataTable) {
                    dataTable = $('#table-karyawan').DataTable({
                        "language": {
                            "url": "//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json"
                        }
                    });
                }
            } else {
                emptyState.querySelector('p').textContent = 'API tidak mengembalikan data karyawan (kosong)';
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
