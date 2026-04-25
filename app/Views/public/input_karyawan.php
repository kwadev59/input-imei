<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= $title ?> | Astra Agro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        :root { --primary-color: #0056b3; --accent-color: #f8f9fa; }
        body { background-color: #f0f2f5; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; }
        .login-card { border: none; border-radius: 20px; shadow: 0 10px 25px rgba(0,0,0,0.1); overflow: hidden; }
        .card-header-custom { background: linear-gradient(135deg, #0056b3 0%, #007bff 100%); color: white; padding: 25px; text-align: center; border: none; }
        .btn-primary-custom { background-color: var(--primary-color); border: none; padding: 12px; border-radius: 10px; font-weight: 600; width: 100%; transition: all 0.3s; }
        .form-control { border-radius: 8px; padding: 12px; border: 1px solid #ddd; }
        .form-control:focus { box-shadow: 0 0 0 3px rgba(0,86,179,0.1); border-color: var(--primary-color); }
        .info-box { background-color: #e7f3ff; border-left: 4px solid #0056b3; padding: 15px; border-radius: 0 8px 8px 0; margin-bottom: 20px; font-size: 0.9rem; }
    </style>
</head>
<body>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-md-5">
            <div class="text-center mb-4">
                <img src="<?= base_url('assets/img/logo-astra.png') ?>" alt="Logo" style="height: 50px; filter: grayscale(1) brightness(0.5);">
            </div>

            <div class="card login-card shadow">
                <div class="card-header-custom">
                    <h4 class="mb-1 fw-bold"><?= $title ?></h4>
                    <p class="mb-0 opacity-75 small">Input Data Kepemilikan Gadget Kerja</p>
                </div>
                <div class="card-body p-4">
                    
                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success border-0 shadow-sm mb-4"><?= session()->getFlashdata('success') ?></div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger border-0 shadow-sm mb-4"><?= session()->getFlashdata('error') ?></div>
                    <?php endif; ?>

                    <div class="info-box">
                        <i class="bi bi-info-circle-fill me-2"></i>
                        Silakan masukkan <strong>NIK Karyawan</strong> Anda untuk memverifikasi data sebelum mengisi nomor IMEI.
                    </div>

                    <form action="<?= base_url('public/save-karyawan-gadget') ?>" method="post" id="mainForm">
                        <?= csrf_field() ?>
                        <input type="hidden" name="type" value="<?= $type ?>">

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">NIK Karyawan</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="bi bi-person-badge"></i></span>
                                <input type="text" name="npk" id="nikInput" class="form-control border-start-0" placeholder="Contoh: 123456" required>
                                <button type="button" id="btnVerify" class="btn btn-outline-primary">Cek</button>
                            </div>
                        </div>

                        <!-- Info Karyawan (Muncul setelah verifikasi) -->
                        <div id="karyawanInfo" style="display:none;" class="mb-4 p-3 border rounded bg-light">
                            <div class="small text-muted mb-1">Nama Karyawan:</div>
                            <div id="displayName" class="fw-bold fs-5 text-dark mb-2"></div>
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="small text-muted">Jabatan:</div>
                                    <div id="displayJabatan" class="fw-bold small text-secondary"></div>
                                </div>
                                <div class="col-6">
                                    <div class="small text-muted">Afdeling:</div>
                                    <div id="displayAfdeling" class="fw-bold small text-secondary"></div>
                                </div>
                            </div>
                        </div>

                        <div id="formFields" style="display:none;">
                            <div class="mb-3">
                                <label class="form-label fw-bold small text-muted">Aplikasi</label>
                                <select name="aplikasi" class="form-select" required>
                                    <option value="">-- Pilih Aplikasi --</option>
                                    <?php foreach($applications as $app): ?>
                                        <option value="<?= esc($app) ?>"><?= esc($app) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold small text-muted">Nomor IMEI</label>
                                <input type="text" name="imei" class="form-control font-monospace" placeholder="15 digit angka IMEI" maxlength="15" pattern="\d{15}" required>
                                <div class="form-text small">*Tekan *#06# di menu telepon untuk cek IMEI.</div>
                            </div>

                            <button type="submit" class="btn-primary-custom">
                                <i class="bi bi-cloud-arrow-up-fill me-2"></i> Simpan Data Gadget
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <p class="text-center text-muted small mt-4">
                &copy; <?= date('Y') ?> Astra Agro Lestari Tbk.<br>
                Sistem Input Gadget Kerja v2.0
            </p>
        </div>
    </div>
</div>

<!-- Modal Panduan -->
<div class="modal fade" id="guideModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0" style="border-radius: 15px;">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center p-4">
                <i class="bi bi-megaphone-fill text-warning display-4 mb-3 d-block"></i>
                <h5 class="fw-bold mb-3">Instruksi Penting</h5>
                <div class="text-start small text-muted mb-4"><?= $popup_instruction ?? 'Pastikan IMEI yang dimasukkan benar dan sesuai dengan gadget yang Anda gunakan untuk bekerja.' ?></div>
                <button type="button" class="btn btn-primary-custom" data-bs-dismiss="modal">Saya Mengerti</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const btnVerify = document.getElementById('btnVerify');
    const nikInput = document.getElementById('nikInput');
    const karyawanInfo = document.getElementById('karyawanInfo');
    const formFields = document.getElementById('formFields');
    
    // Show instruction on load
    <?php if(!session()->getFlashdata('success')): ?>
    new bootstrap.Modal(document.getElementById('guideModal')).show();
    <?php endif; ?>

    btnVerify.addEventListener('click', function() {
        const nik = nikInput.value.trim();
        if(!nik) return alert('Masukkan NIK terlebih dahulu.');

        btnVerify.disabled = true;
        btnVerify.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

        fetch('<?= base_url('public/check-nik') ?>?nik=' + nik + '&type=<?= $type ?>')
            .then(r => r.json())
            .then(res => {
                btnVerify.disabled = false;
                btnVerify.innerHTML = 'Cek';

                if(res.status === 'success') {
                    document.getElementById('displayName').textContent = res.nama;
                    document.getElementById('displayJabatan').textContent = res.jabatan;
                    document.getElementById('displayAfdeling').textContent = res.afdeling;
                    
                    karyawanInfo.style.display = 'block';
                    formFields.style.display = 'block';
                    nikInput.readOnly = true;
                    btnVerify.style.display = 'none';
                } else {
                    alert(res.message);
                }
            })
            .catch(err => {
                btnVerify.disabled = false;
                btnVerify.innerHTML = 'Cek';
                alert('Terjadi kesalahan jaringan.');
            });
    });
});
</script>
</body>
</html>
