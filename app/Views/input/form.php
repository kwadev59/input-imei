<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#0c4a6e">
    <title>Input Gadget - <?= $karyawan['nama'] ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #0284c7;
            --primary-soft: rgba(2,132,199,0.1);
            --success: #10b981;
            --success-soft: rgba(16,185,129,0.1);
            --danger: #ef4444;
            --danger-soft: rgba(239,68,68,0.1);
            --text-primary: #0f172a;
            --text-muted: #94a3b8;
            --card-border: #e2e8f0;
        }
        body { 
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; 
            background-color: #f1f5f9; 
            -webkit-font-smoothing: antialiased;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .main-card {
            background: #fff;
            border-radius: 20px;
            border: 1px solid var(--card-border);
            box-shadow: 0 1px 3px rgba(0,0,0,0.04), 0 1px 2px rgba(0,0,0,0.06);
            max-width: 560px;
            width: 100%;
            overflow: hidden;
        }
        .main-card-header {
            background: linear-gradient(135deg, #0c4a6e, #0284c7);
            color: #fff;
            padding: 24px;
        }
        .status-option { 
            cursor: pointer; border: 2px solid var(--card-border); padding: 20px 15px; 
            border-radius: 14px; text-align: center; transition: all 0.25s; background: white;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }
        .status-option:hover { border-color: var(--primary); background: var(--primary-soft); }
        .status-option.selected-ada { border-color: var(--success); background: var(--success-soft); }
        .status-option.selected-tidak { border-color: var(--danger); background: var(--danger-soft); }
        .hidden { display: none; }
        .btn { font-family: 'Inter', sans-serif; font-weight: 600; border-radius: 12px; }
        .btn:active { transform: scale(0.96); }
        .form-control { border-radius: 10px; border: 1px solid var(--card-border); font-family: 'Inter', sans-serif; }
        .form-control:focus { border-color: var(--primary); box-shadow: 0 0 0 3px var(--primary-soft); }
    </style>
</head>
<body>

<div class="main-card">
    <div class="main-card-header">
        <small class="d-block text-white-50 mb-1">Sisa Pekerja: <?= $remaining ?></small>
        <h5 class="mb-0 fw-bold">Form Input Gadget</h5>
    </div>
    <div style="padding: 24px;">
        <?php if(session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger border-0 shadow-sm" style="border-radius:12px;">
                <ul class="mb-0">
                <?php foreach(session()->getFlashdata('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="mb-4 text-center">
            <small class="text-muted d-block text-uppercase fw-bold" style="font-size:0.7rem;letter-spacing:0.06em;">Nama Pekerja</small>
            <h4 class="fw-bold mt-1 mb-2" style="color:var(--text-primary)"><?= $karyawan['nama'] ?></h4>
            <span class="badge" style="background:#f1f5f9;color:var(--text-muted);padding:5px 10px;border-radius:6px;"><?= $karyawan['jabatan'] ?></span>
            <span class="badge" style="background:var(--primary-soft);color:var(--primary);padding:5px 10px;border-radius:6px;"><?= $karyawan['nik_karyawan'] ?></span>
        </div>

        <form action="<?= base_url('input/store') ?>" method="post" id="inputForm">
            <?= csrf_field() ?>
            <input type="hidden" name="karyawan_id" value="<?= $karyawan['id'] ?>">
            <input type="hidden" name="status_gadget" id="status_gadget" required>

            <div class="row g-3 mb-4">
                <div class="col-6">
                    <div class="status-option" onclick="selectStatus('Ada', this)">
                        <div style="font-size:2rem;margin-bottom:8px;">📱</div>
                        <div class="fw-bold" style="color:var(--success)">Ada Gadget</div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="status-option" onclick="selectStatus('Tidak Ada', this)">
                        <div style="font-size:2rem;margin-bottom:8px;">🚫</div>
                        <div class="fw-bold" style="color:var(--danger)">Tidak Ada</div>
                    </div>
                </div>
            </div>

            <div id="section-ada" class="hidden">
                <div class="mb-3">
                    <label class="form-label fw-bold small" style="color:var(--success)">Scan / Input IMEI (15 Digit)</label>
                    <input type="number" class="form-control form-control-lg fw-bold font-monospace" name="imei" id="imei" placeholder="Contoh: 865123456789012" style="letter-spacing:1px;">
                    <div class="form-text text-muted small mt-1">Pastikan IMEI benar dan sesuai fisik HP.</div>
                </div>
            </div>

            <div id="section-tidak" class="hidden">
                <div class="mb-3">
                    <label class="form-label fw-bold small" style="color:var(--danger)">Keterangan / Alasan</label>
                    <div class="d-flex flex-wrap gap-2 mb-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary bg-white border-0 shadow-sm" style="border-radius:8px;" onclick="setKeterangan('Rusak')">Rusak</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary bg-white border-0 shadow-sm" style="border-radius:8px;" onclick="setKeterangan('Hilang')">Hilang</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary bg-white border-0 shadow-sm" style="border-radius:8px;" onclick="setKeterangan('Belum Dapat')">Belum Dapat</button>
                    </div>
                    <textarea class="form-control" name="keterangan" id="keterangan" rows="2" placeholder="Tulis alasan lain..."></textarea>
                </div>
            </div>

            <div class="d-grid mt-4">
                <button type="submit" class="btn btn-primary btn-lg py-3 fw-bold shadow-sm" id="btnSubmit" disabled style="background:var(--primary);border-color:var(--primary);font-size:1.05rem;">
                    <i class="bi bi-check-lg me-1"></i> SIMPAN DATA
                </button>
            </div>
        </form>
    </div>
    
    <div class="text-center py-3" style="border-top:1px solid var(--card-border);background:#fafbfc;">
        <a href="<?= base_url('auth/logout') ?>" class="text-muted text-decoration-none small fw-bold">
            <i class="bi bi-box-arrow-left me-1"></i> Logout Mandor
        </a>
    </div>
</div>

<script>
    function selectStatus(status, el) {
        document.getElementById('status_gadget').value = status;
        
        document.querySelectorAll('.status-option').forEach(e => {
            e.classList.remove('selected-ada', 'selected-tidak');
        });
        el.classList.add(status === 'Ada' ? 'selected-ada' : 'selected-tidak');

        if(status === 'Ada') {
            document.getElementById('section-ada').classList.remove('hidden');
            document.getElementById('section-tidak').classList.add('hidden');
            document.getElementById('imei').required = true;
            document.getElementById('keterangan').required = false;
        } else {
            document.getElementById('section-ada').classList.add('hidden');
            document.getElementById('section-tidak').classList.remove('hidden');
            document.getElementById('imei').required = false;
            document.getElementById('keterangan').required = true;
        }

        document.getElementById('btnSubmit').disabled = false;
    }
    
    function setKeterangan(text) {
        document.getElementById('keterangan').value = text;
    }
</script>
</body>
</html>
