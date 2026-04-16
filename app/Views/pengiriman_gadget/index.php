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
                                    <div class="btn-group">
                                        <a href="<?= base_url('pengiriman-gadget/detail/'.$row['id']) ?>" class="btn btn-info btn-sm text-white" title="Lihat Detail">
                                            <i class="bi bi-eye"></i> Detail
                                        </a>
                                        <?php if(empty($row['no_resi'])): ?>
                                            <button type="button" class="btn btn-success btn-sm upload-resi-btn" 
                                                    data-baste-id="<?= $row['id'] ?>" 
                                                    data-baste-no="<?= esc($row['no_baste']) ?>"
                                                    title="Upload Resi">
                                                <i class="bi bi-upload"></i> Upload Resi
                                            </button>
                                        <?php else: ?>
                                            <button type="button" class="btn btn-warning btn-sm view-resi-btn" 
                                                    data-baste-id="<?= $row['id'] ?>" 
                                                    data-baste-no="<?= esc($row['no_baste']) ?>"
                                                    data-resi-no="<?= esc($row['no_resi']) ?>"
                                                    data-file-name="<?= !empty($row['foto_resi']) ? esc($row['foto_resi']) : '' ?>"
                                                    title="Lihat Resi">
                                                <i class="bi bi-file-earmark-pdf"></i> Lihat Resi
                                            </button>
                                            <form action="<?= base_url('pengiriman-gadget/aksi-hapus-resi/'.$row['id']) ?>" method="get" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus resi ini? Nomor resi akan dihapus dan dapat digunakan kembali.')">
                                                <button type="submit" class="btn btn-danger btn-sm" title="Hapus Resi">
                                                    <i class="bi bi-trash"></i> HAPUS RESI
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                    <?php if(!empty($row['no_resi'])): ?>
                                        <div class="mt-1 small">
                                            <span class="badge bg-light text-dark border">
                                                <i class="bi bi-truck me-1"></i> <?= esc($row['no_resi']) ?>
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal Upload Resi -->
<div class="modal fade" id="uploadResiModal" tabindex="-1" aria-labelledby="uploadResiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadResiModalLabel">Upload Resi BASTE</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="uploadResiForm" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Perhatian:</strong> File resi harus dalam format PDF dan maksimal 5MB.
                    </div>
                    
                    <input type="hidden" name="baste_id" id="modalBasteId">
                    
                    <div class="mb-3">
                        <label for="modalNoResi" class="form-label fw-bold">Nomor Resi / Bukti Pengiriman <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="modalNoResi" name="no_resi" required>
                        <div class="form-text">Masukkan nomor resi ekspedisi atau kode bukti pengiriman.</div>
                    </div>

                    <div class="mb-3">
                        <label for="modalFotoResi" class="form-label fw-bold">File Resi (PDF) <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" id="modalFotoResi" name="foto_resi" accept=".pdf" required>
                        <div class="form-text">Hanya format PDF yang diperbolehkan. Maksimal 5MB.</div>
                    </div>
                    
                    <div id="uploadProgress" class="d-none">
                        <div class="progress mb-3">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
                        </div>
                        <div class="text-center text-muted small">Mengupload file...</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="submitUploadBtn">
                        <i class="bi bi-cloud-arrow-up me-1"></i> Upload Resi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal View Resi -->
<div class="modal fade" id="viewResiModal" tabindex="-1" aria-labelledby="viewResiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewResiModalLabel">Lihat Resi BASTE</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-sm-6">
                        <label class="form-label fw-bold mb-1">Nomor BASTE:</label>
                        <p class="mb-0 text-primary fw-semibold" id="viewBasteNo"></p>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label fw-bold mb-1">Nomor Resi:</label>
                        <p class="mb-0" id="viewResiNo"></p>
                    </div>
                </div>
                
                <div>
                    <label class="form-label fw-bold">File Resi:</label>
                    <div id="resiViewerContainer" class="border rounded bg-light" style="min-height: 200px; position: relative;">
                        <!-- Loading state -->
                        <div class="text-center py-5" id="resiLoading">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2 text-muted mb-0">Memuat file resi...</p>
                        </div>
                        <!-- PDF viewer (embed) -->
                        <embed id="resiPdfEmbed" type="application/pdf" class="w-100 d-none" style="height: 500px;">
                        <!-- Image viewer -->
                        <div id="resiImageContainer" class="text-center p-3 d-none">
                            <img id="resiImage" src="" alt="Resi" class="img-fluid rounded" style="max-height: 500px; cursor: pointer;" onclick="window.open(this.src, '_blank')">
                        </div>
                        <!-- Error state -->
                        <div id="resiError" class="text-center py-5 d-none">
                            <i class="bi bi-file-earmark-x fs-1 text-danger d-block mb-2"></i>
                            <p class="text-danger mb-1">Gagal memuat file resi.</p>
                            <p class="text-muted small mb-0" id="resiErrorDetail"></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <a href="#" class="btn btn-outline-primary" id="openNewTabBtn" target="_blank">
                    <i class="bi bi-box-arrow-up-right me-1"></i> Buka di Tab Baru
                </a>
                <a href="#" class="btn btn-primary" id="downloadResiBtn" download>
                    <i class="bi bi-download me-1"></i> Download
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize DataTable
        if(typeof $ !== 'undefined' && $.fn.DataTable && document.getElementById('dataTable')) {
            $('#dataTable').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json"
                }
            });
        }
        
        // ============================================================
        // Upload Resi Modal Handler
        // ============================================================
        const uploadResiModalEl = document.getElementById('uploadResiModal');
        const uploadResiModal = new bootstrap.Modal(uploadResiModalEl);
        const uploadResiForm = document.getElementById('uploadResiForm');
        const uploadProgress = document.getElementById('uploadProgress');
        const progressBar = uploadProgress.querySelector('.progress-bar');
        const submitUploadBtn = document.getElementById('submitUploadBtn');
        
        // Handle upload resi button clicks
        document.querySelectorAll('.upload-resi-btn').forEach(button => {
            button.addEventListener('click', function() {
                const basteId = this.getAttribute('data-baste-id');
                const basteNo = this.getAttribute('data-baste-no');
                
                document.getElementById('modalBasteId').value = basteId;
                document.getElementById('uploadResiModalLabel').textContent = `Upload Resi BASTE: ${basteNo}`;
                
                // Reset form & state
                uploadResiForm.reset();
                uploadProgress.classList.add('d-none');
                progressBar.style.width = '0%';
                submitUploadBtn.disabled = false;
                
                // Remove any existing alert inside modal
                const existingAlert = uploadResiModalEl.querySelector('.modal-body > .alert-danger');
                if (existingAlert) existingAlert.remove();
                
                uploadResiModal.show();
            });
        });
        
        // Handle form submission with real AJAX progress
        uploadResiForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const basteId = formData.get('baste_id');
            
            // Validate file type client-side
            const fileInput = document.getElementById('modalFotoResi');
            const file = fileInput.files[0];
            
            if (!file) {
                showUploadError('File resi harus dipilih!');
                return;
            }
            
            if (file.type !== 'application/pdf') {
                showUploadError('File harus dalam format PDF!');
                return;
            }
            
            if (file.size > 5 * 1024 * 1024) {
                showUploadError('File terlalu besar! Maksimal 5MB.');
                return;
            }
            
            // Show progress
            uploadProgress.classList.remove('d-none');
            submitUploadBtn.disabled = true;
            clearUploadError();
            
            // Use XMLHttpRequest for real progress tracking
            const xhr = new XMLHttpRequest();
            
            xhr.upload.addEventListener('progress', function(e) {
                if (e.lengthComputable) {
                    const percent = Math.round((e.loaded / e.total) * 100);
                    progressBar.style.width = percent + '%';
                    progressBar.textContent = percent + '%';
                }
            });
            
            xhr.addEventListener('load', function() {
                progressBar.style.width = '100%';
                progressBar.textContent = '100%';

                let data;
                try {
                    data = JSON.parse(xhr.responseText);
                } catch (parseErr) {
                    // Server returned non-JSON (e.g. redirect HTML)
                    showUploadError('Terjadi kesalahan pada server. Silakan refresh halaman dan coba lagi.');
                    resetUploadState();
                    return;
                }
                
                setTimeout(() => {
                    if (data.status === 'success') {
                        // Show success message on the page
                        const alertDiv = document.createElement('div');
                        alertDiv.className = 'alert alert-success alert-dismissible fade show';
                        alertDiv.innerHTML = `
                            <i class="bi bi-check-circle me-2"></i>
                            ${data.message}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        `;
                        document.querySelector('.content').insertBefore(alertDiv, document.querySelector('.data-card'));
                        
                        // Close modal and reload page
                        uploadResiModal.hide();
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    } else {
                        showUploadError(data.message || 'Terjadi kesalahan saat upload.');
                        resetUploadState();
                    }
                }, 300);
            });
            
            xhr.addEventListener('error', function() {
                showUploadError('Terjadi kesalahan jaringan. Periksa koneksi Anda.');
                resetUploadState();
            });
            
            xhr.addEventListener('timeout', function() {
                showUploadError('Upload timeout. File mungkin terlalu besar atau koneksi lambat.');
                resetUploadState();
            });
            
            xhr.open('POST', `<?= base_url('pengiriman-gadget/do-upload-resi/') ?>${basteId}`);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.timeout = 60000; // 60 second timeout
            xhr.send(formData);
        });
        
        function showUploadError(message) {
            clearUploadError();
            const modalBody = uploadResiModalEl.querySelector('.modal-body');
            const alertEl = document.createElement('div');
            alertEl.className = 'alert alert-danger alert-dismissible fade show mb-3';
            alertEl.id = 'uploadErrorAlert';
            alertEl.innerHTML = `
                <i class="bi bi-exclamation-triangle me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            modalBody.insertBefore(alertEl, modalBody.firstChild);
        }
        
        function clearUploadError() {
            const existing = document.getElementById('uploadErrorAlert');
            if (existing) existing.remove();
        }
        
        function resetUploadState() {
            uploadProgress.classList.add('d-none');
            submitUploadBtn.disabled = false;
            progressBar.style.width = '0%';
            progressBar.textContent = '';
        }
        
        // File validation on change
        document.getElementById('modalFotoResi').addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                if (file.type !== 'application/pdf') {
                    showUploadError('File harus dalam format PDF!');
                    this.value = '';
                } else if (file.size > 5 * 1024 * 1024) {
                    showUploadError('File terlalu besar! Maksimal 5MB.');
                    this.value = '';
                } else {
                    clearUploadError();
                }
            }
        });
        
        // ============================================================
        // View Resi Modal Handler
        // ============================================================
        const viewResiModalEl = document.getElementById('viewResiModal');
        const viewResiModal = new bootstrap.Modal(viewResiModalEl);
        
        document.querySelectorAll('.view-resi-btn').forEach(button => {
            button.addEventListener('click', function() {
                const basteId = this.getAttribute('data-baste-id');
                const basteNo = this.getAttribute('data-baste-no');
                const resiNo = this.getAttribute('data-resi-no');
                const fileName = this.getAttribute('data-file-name');
                
                document.getElementById('viewBasteNo').textContent = basteNo;
                document.getElementById('viewResiNo').textContent = resiNo;
                document.getElementById('viewResiModalLabel').textContent = `Resi BASTE: ${basteNo}`;
                
                const resiLoading = document.getElementById('resiLoading');
                const resiPdfEmbed = document.getElementById('resiPdfEmbed');
                const resiImageContainer = document.getElementById('resiImageContainer');
                const resiImage = document.getElementById('resiImage');
                const resiError = document.getElementById('resiError');
                const resiErrorDetail = document.getElementById('resiErrorDetail');
                const openNewTabBtn = document.getElementById('openNewTabBtn');
                const downloadBtn = document.getElementById('downloadResiBtn');
                
                // Reset all states
                resiLoading.classList.remove('d-none');
                resiPdfEmbed.classList.add('d-none');
                resiPdfEmbed.removeAttribute('src');
                resiImageContainer.classList.add('d-none');
                resiImage.src = '';
                resiError.classList.add('d-none');
                resiErrorDetail.textContent = '';
                
                if (!fileName) {
                    resiLoading.classList.add('d-none');
                    resiError.classList.remove('d-none');
                    resiErrorDetail.textContent = 'File resi belum diupload.';
                    openNewTabBtn.classList.add('d-none');
                    downloadBtn.classList.add('d-none');
                    viewResiModal.show();
                    return;
                }
                
                // Use the controller route to serve the file with proper headers
                const fileUrl = `<?= base_url('pengiriman-gadget/view-resi-file/') ?>${basteId}`;
                openNewTabBtn.href = fileUrl;
                openNewTabBtn.classList.remove('d-none');
                downloadBtn.href = fileUrl;
                downloadBtn.classList.remove('d-none');
                
                // Detect file type from extension
                const ext = fileName.split('.').pop().toLowerCase();
                const isImage = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'].includes(ext);
                const isPdf = ext === 'pdf';
                
                viewResiModal.show();
                
                if (isImage) {
                    // Load as image
                    resiImage.onload = function() {
                        resiLoading.classList.add('d-none');
                        resiImageContainer.classList.remove('d-none');
                    };
                    resiImage.onerror = function() {
                        resiLoading.classList.add('d-none');
                        resiError.classList.remove('d-none');
                        resiErrorDetail.textContent = 'Gagal memuat gambar resi.';
                    };
                    // Set src after modal is shown
                    setTimeout(() => {
                        resiImage.src = fileUrl;
                    }, 150);
                } else if (isPdf) {
                    // Load PDF via embed
                    setTimeout(() => {
                        resiPdfEmbed.src = fileUrl;
                        resiPdfEmbed.classList.remove('d-none');
                        resiLoading.classList.add('d-none');
                    }, 150);
                } else {
                    // Unknown file type - just offer download
                    resiLoading.classList.add('d-none');
                    resiError.classList.remove('d-none');
                    resiErrorDetail.textContent = 'Tipe file tidak dapat ditampilkan. Silakan download file.';
                }
            });
        });
        
        // Cleanup when view resi modal is closed
        viewResiModalEl.addEventListener('hidden.bs.modal', function() {
            const resiPdfEmbed = document.getElementById('resiPdfEmbed');
            const resiImage = document.getElementById('resiImage');
            
            resiPdfEmbed.removeAttribute('src');
            resiPdfEmbed.classList.add('d-none');
            resiImage.src = '';
            resiImage.onload = null;
            resiImage.onerror = null;
            document.getElementById('resiImageContainer').classList.add('d-none');
            document.getElementById('resiLoading').classList.remove('d-none');
            document.getElementById('resiError').classList.add('d-none');
        });
    });
</script>

<?= view('partials/admin_footer') ?>
<!-- Refresh timestamp: Thu Apr 16 10:11:00 PM WITA 2026 -->
